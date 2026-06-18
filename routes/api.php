<?php

use App\Http\Controllers\Api\LeadController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:lead-api')->post('/leads', LeadController::class);
