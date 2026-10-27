<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MotorcycleController as AdminMotorcycleController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\User\MotorcycleController as UserMotorcycleController;
use App\Http\Controllers\User\BookingController;
use App\Models\Motorcycle;

/*
|--------------------------------------------------------------------------
| Public & Landing Page Routes
|--------------------------------------------------------------------------
*/

// Landing Page (Katalog Singkat)
Route::get('/', function () {
    $motorcycles = Motorcycle::where('status', 'available')
        ->latest()
        ->take(4)
        ->get();

    return view('welcome', compact('motorcycles'));
})->name('home');

// Midtrans Webhook (Wajib Publik, Tanpa Auth & CSRF)
Route::post('/midtrans/notification', [BookingController::class, 'notificationHandler'])
    ->name('midtrans.notification');


/*
|--------------------------------------------------------------------------
| Guest Only Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Registrasi Manual
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Login Manual
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth 2.0
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});


/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Customer & General Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');

    // Handling Route /dashboard untuk Customer (Redirect ke Home)
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    // Edit Profil User
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Katalog Motor (Customer)
    Route::get('/motorcycles', [UserMotorcycleController::class, 'index'])->name('motorcycles.index');
    Route::get('/motorcycles/{motorcycle}', [UserMotorcycleController::class, 'show'])->name('motorcycles.show');

    // Pemesanan / Booking (Customer)
    Route::get('/motorcycles/{motorcycle}/booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/motorcycles/{motorcycle}/booking', [BookingController::class, 'store'])->name('bookings.store');

    // Riwayat & Detail Booking (Customer)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/pickup', [BookingController::class, 'updatePickup'])->name('bookings.update-pickup');
    Route::get('/bookings/{booking}/status', [BookingController::class, 'status'])->name('bookings.status');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Khusus Role Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () { 

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Management Data Motor (CRUD Admin)
        Route::resource('motorcycles', AdminMotorcycleController::class);

        // Management Bookings Customer
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');

        // Cetak Dokumen Admin (Invoice & Surat Jalan)
        Route::get('/bookings/{booking}/print-invoice', [AdminBookingController::class, 'printInvoice'])->name('bookings.print-invoice');
        Route::get('/bookings/{booking}/print-surat-jalan', [AdminBookingController::class, 'printSuratJalan'])->name('bookings.print-surat-jalan');
    });
});