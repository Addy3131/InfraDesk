<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['db' => 'Connected successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});