<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MotorcycleController as AdminMotorcycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\MotorcycleController as UserMotorcycleController;

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Profile
Route::middleware(['auth'])->group(function () {
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Guest Only
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Auth Users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard biasa (untuk user)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User - Daftar Motor
    Route::get('/motorcycles', [UserMotorcycleController::class, 'index'])->name('motorcycles.index');
    Route::get('/motorcycles/{motorcycle}', [UserMotorcycleController::class, 'show'])->name('motorcycles.show');

    // Khusus Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource Motorcycles (CRUD Admin)
        Route::resource('motorcycles', AdminMotorcycleController::class);

        // Orders (sementara)
        Route::get('/orders', function () {
            return view('admin.orders.index');
        })->name('orders.index');
    });
});