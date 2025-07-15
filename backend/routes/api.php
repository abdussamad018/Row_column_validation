<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route for debugging
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working',
        'php_version' => PHP_VERSION,
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit'),
    ]);
});

// Excel Import Routes
Route::prefix('imports')->group(function () {
    Route::post('/upload', [ImportController::class, 'upload']);
    Route::get('/{id}', [ImportController::class, 'status']);
    Route::get('/{id}/records', [ImportController::class, 'records']);
    Route::post('/{id}/download-errors', [ImportController::class, 'downloadErrors']);
    Route::get('/{id}/download-file', [ImportController::class, 'downloadFile']);
}); 