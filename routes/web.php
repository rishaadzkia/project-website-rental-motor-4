<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// Landing Page (Akses Utama Customer & Publik)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Only (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Auth Users (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Khusus Admin (Akses /admin/* dan /dashboard)
    Route::middleware('admin')->group(function () {
        
        // Jika ketik /dashboard, lempar ke dashboard admin
        Route::get('/dashboard', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::prefix('admin')->as('admin.')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('dashboard');

            Route::get('/orders', function () {
                return view('admin.orders.index');
            })->name('orders.index');

            Route::get('/motors', function () {
                return view('admin.motors.index');
            })->name('motors.index');
        });
    });
});