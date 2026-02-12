<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth.token'])->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user_id' => $request->auth_user_id
        ]);
    });
});