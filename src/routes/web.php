<?php

use Illuminate\Support\Facades\Route;
use Fhsinchy\Inspire\Controllers\{
    InspirationController
};

Route::get('inspire', [InspirationController::class, 'getLatestQuot']);