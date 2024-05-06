<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RunController;

Route::middleware('hmac_auth')->group(function ()
{
    Route::post('run', [RunController::class, 'store']);
    Route::get('runs/results', [RunController::class, 'results']);
    Route::get('runs/gap-results', [RunController::class, 'gapResults']);
    Route::get('runs/compare-diffs', [RunController::class, 'compareDiffs']);
    Route::get('runs/compare-values', [RunController::class, 'compareValues']);
});
