<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RunController;

Route::middleware(['set_x_requested_with', 'throttle:500,1'])->group(function ()
{
    Route::post('run', [RunController::class, 'store']);
    Route::get('runs/results', [RunController::class, 'results']);
    Route::get('runs/gap-results', [RunController::class, 'gapResults']);
    Route::get('runs/compare-diffs', [RunController::class, 'compareDiffs']);
    Route::get('runs/compare-values', [RunController::class, 'compareValues']);
    Route::get('runs/accuracy', [RunController::class, 'verticesClassificationAccuracy']);
    Route::get('runs/distances', [RunController::class, 'distancesFromOptimal']);
    Route::get('runs/values', [RunController::class, 'valuesFromAlgorithms']);
});
