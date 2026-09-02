<?php

use App\Http\Controllers\Api\V1\OAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Google OAuth Callback Handler for http://localhost:8000/google_callback.php
Route::match(['get', 'post'], '/google_callback.php', function (Request $request, OAuthController $controller) {
    return $controller->callback($request, 'google');
});
