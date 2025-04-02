<?php

namespace App\Listeners;

use App\Events\HealthDataUpdated;
use App\Http\Controllers\PredictionController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GeneratePrediction
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HealthDataUpdated $event): void
    {
        $request = new \Illuminate\Http\Request();

        $controller = new PredictionController();

        $controller->getPredictions($request);
    }
}
