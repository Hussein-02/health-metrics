<?php

namespace App\Http\Controllers;

use App\Models\HealthData;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class PredictionController extends Controller
{
    public function getPredictions(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //retrieve the users activity data from the database
        $healthData = HealthData::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        if ($healthData->isEmpty()) {
            return response()->json(['error' => 'No activity data found'], 404);
        }

        //prepare data for Gemini
        $formattedData = $healthData->map(function ($data) {
            return [
                'date' => $data->date,
                'steps' => $data->steps,
                'distance_km' => $data->distance_km,
                'active_minutes' => $data->active_minutes,
            ];
        });

        //call Gemini api for predictions
        $predictions = $this->getActivityPredictions($formattedData);

        if (!$predictions) {
            return response()->json(['error' => 'AI prediction failed'], 500);
        }

        $prediction = Prediction::updateOrCreate(
            ['user_id' => $user->id],
            ['insights' => json_encode($predictions)]
        );

        return response()->json($prediction->insights);
    }

    private function getActivityPredictions($activityData)
    {
        $apiKey = config('services.gemini.api_key');
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro-002:generateContent?key=$apiKey";

        $prompt = "Analyze this Apple Watch activity data and predict the user's steps, distance, and active minutes for the next 7 days. Identify patterns and suggest improvements. Data: " . json_encode($activityData);

        $response = Http::timeout(120)->post($url, [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ]
        ]);

        if (!$response->successful()) {
            \Log::error("Gemini API Error: " . $response->body());
            return null;
        }

        return $response->json();
    }
}
