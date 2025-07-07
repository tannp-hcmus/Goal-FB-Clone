<?php

use App\Http\Controllers\UserSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('/search-users', [UserSearchController::class, 'search']);
