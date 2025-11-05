<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::post('/chatbot/respond', [ChatbotController::class, 'respond']);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
