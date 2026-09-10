<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Coderflex\LaravelTurnstile\Rules\TurnstileCheck;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validasi data sesuai UI Buat Akun
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'no_hp'    => 'required|string|max:15|unique:users,no_hp',
            'no_ktp'   => 'required|string|max:16|unique:users,no_ktp',
            'no_sim'   => 'required|string|max:20',
            'email'    => 'nullable|email|unique:users,email', // Opsional, diisi jika UI nanti ada email
            'alamat'   => 'nullable|string',                  // Opsional
            'password' => 'required|string|min:8|confirmed',
            'cf-turnstile-response' => ['required', new TurnstileCheck()],
        ],
        [
        'cf-turnstile-response.required' => 'Verifikasi keamanan wajib diisi.',
    ]);

        // 2. Buat user baru (Password otomatis di-hash oleh Cast di Model)
        $user = User::create([
            'name'     => $validated['name'],
            'no_hp'    => $validated['no_hp'],
            'no_ktp'   => $validated['no_ktp'],
            'no_sim'   => $validated['no_sim'],
            'email'    => $validated['email'] ?? null,
            'alamat'   => $validated['alamat'] ?? null,
            'password' => $validated['password'],
            'role'     => 'customer',
        ]);

        // 3. Auto login setelah registrasi
        Auth::login($user);

        // 4. Redirect ke dashboard/home
        return redirect('/')->with('success', 'Registrasi berhasil!');
    }
}