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

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            //get original filename and sanitize it
            $originalName = $request->file('file')->getClientOriginalName();
            $safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $originalName);

            //manual file storage
            $directory = storage_path('app/health_data');
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $path = $directory . '/' . $safeName;

            //move the file manually
            if (!move_uploaded_file($request->file('file')->getPathname(), $path)) {
                throw new \Exception("Failed to move uploaded file to: $path");
            }

            //verify file exists
            if (!file_exists($path)) {
                throw new \Exception("File not found at: $path");
            }

            //process CSV
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);

            $processed = 0;
            foreach ($csv as $record) {
                HealthData::updateOrCreate(
                    ['user_id' => $user->id, 'date' => $record['date']],
                    [
                        'steps' => $record['steps'],
                        'distance_km' => $record['distance_km'],
                        'active_minutes' => $record['active_minutes'],
                    ]
                );
                $processed++;
            }

            event(new HealthDataUpdated($user));

            return response()->json([
                'success' => true,
                'message' => "$processed records processed",
                'stored_path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'temp_path' => $request->file('file')->getPathname(),
                'storage_dir' => storage_path('app/health_data'),
                'dir_perms' => substr(sprintf('%o', fileperms(storage_path('app/health_data'))), -4)
            ], 500);
        }
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
