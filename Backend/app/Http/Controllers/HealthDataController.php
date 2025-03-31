<?php

namespace App\Http\Controllers;

use App\Events\HealthDataUpdated;
use App\Models\HealthData;
use Illuminate\Http\Request;
use League\Csv\Reader;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class HealthDataController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv']);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $path = $request->file('file')->store('health_data');
        $csv = Reader::createFromPath(storage_path("app/$path"), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            HealthData::updateOrCreate(
                ['user_id' => $user->id, 'date' => $record['date']],
                [
                    'steps' => $record['steps'],
                    'distance_km' => $record['distance_km'],
                    'active_minutes' => $record['active_minutes'],
                ]
            );
        }

        event(new HealthDataUpdated($user));
        return response()->json(['success' => true]);
    }

    public function getData(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = HealthData::where('user_id', $user->id)
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}
