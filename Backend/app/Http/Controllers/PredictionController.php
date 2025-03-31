<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class PredictionController extends Controller
{
    public function getPredictions(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $predictions = Prediction::where('user_id', $user->id)->first();

        if (!$predictions) {
            return response()->json(['error' => 'No predictions yet'], 404);
        }

        return response()->json($predictions->insights);
    }
}
