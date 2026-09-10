<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MotorcycleController as AdminMotorcycleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\MotorcycleController as UserMotorcycleController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

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

Route::post('/logout', function () {
    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

// Auth Users
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        UserDashboardController::class,
        'index'
    ])->name('dashboard');

    Route::get(
        '/motorcycles',
        [UserMotorcycleController::class, 'index']
    )->name('motorcycles.index');

    Route::get(
        '/motorcycles/{motorcycle}',
        [UserMotorcycleController::class, 'show']
    )->name('motorcycles.show');

    Route::get(
        '/motorcycles/{motorcycle}/booking',
        [BookingController::class, 'create']
    )->name('bookings.create');

    Route::post(
        '/motorcycles/{motorcycle}/booking',
        [BookingController::class, 'store']
    )->name('bookings.store');

    Route::get(
        '/bookings',
        [BookingController::class, 'index']
    )->name('bookings.index');

    Route::get(
        '/bookings/{booking}',
        [BookingController::class, 'show']
    )->name('bookings.show');

    Route::patch(
        '/bookings/{booking}/cancel',
        [BookingController::class, 'cancel']
    )->name('bookings.cancel');
    

    // Khusus Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Resource Motorcycles (CRUD Admin)
        Route::resource('motorcycles', AdminMotorcycleController::class);
        
        Route::get('/dashboard', [
            AdminDashboardController::class,
            'index'
        ])->name('dashboard');

        Route::resource(
            'motorcycles',
            AdminMotorcycleController::class
        );

        Route::get(
            '/bookings',
            [AdminBookingController::class, 'index']
        )->name('bookings.index');

        Route::get(
            '/bookings/{booking}',
            [AdminBookingController::class, 'show']
        )->name('bookings.show');

        Route::patch(
            '/bookings/{booking}',
            [AdminBookingController::class, 'update']
        )->name('bookings.update');

        // Orders (sementara)
        Route::get('/orders', function () {
            return view('admin.orders.index');
        })->name('orders.index');
    });
});