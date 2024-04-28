<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RunController;

Route::middleware('hmac_auth')->group(function ()
{
    Route::post('run', [RunController::class, 'store']);
    Route::get('runs/min-results', [RunController::class, 'minResults']);
});
