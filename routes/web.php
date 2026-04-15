<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

// Register
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/dashboard', function () {
    return view('dashboards.admin');
})->middleware('auth');

    
