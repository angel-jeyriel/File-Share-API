<?php

use App\Http\Controllers\Api\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/upload', [UploadController::class, 'upload']);
Route::get('/download/{token}', [UploadController::class, 'download']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');