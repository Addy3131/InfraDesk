<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::get('/debug', function () {
    return 'working';
});
Route::middleware(['auth.token'])->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
});