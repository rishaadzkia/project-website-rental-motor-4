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
        // Validasi ketat sesuai ketentuan standar Indonesia
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            
            // Format WhatsApp Indonesia: diawali 08/628, panjang total 10-13 digit
            'no_hp' => [
                'required',
                'numeric',
                'regex:/^(08|628)[0-9]{8,11}$/',
                'unique:users,no_hp'
            ],
            
            // Format NIK KTP Indonesia: Tepat 16 digit angka
            'no_ktp' => [
                'required',
                'numeric',
                'digits:16',
                'unique:users,no_ktp'
            ],
            
            // Format Nomor SIM C Indonesia: Tepat 12 digit angka
            'no_sim' => [
                'required',
                'numeric',
                'digits:12',
                'unique:users,no_sim'
            ],
            
            'email'    => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'alamat'   => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => ['required', new TurnstileCheck()],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            
            'no_hp.required'    => 'Nomor WhatsApp wajib diisi.',
            'no_hp.numeric'     => 'Nomor WhatsApp harus berupa angka.',
            'no_hp.regex'       => 'Nomor WhatsApp harus diawali 08 atau 628 (total 10-13 digit).',
            'no_hp.unique'      => 'Nomor WhatsApp ini sudah terdaftar.',
            
            'no_ktp.required'   => 'Nomor KTP (NIK) wajib diisi.',
            'no_ktp.numeric'    => 'Nomor KTP harus berupa angka.',
            'no_ktp.digits'     => 'Nomor KTP harus berjumlah tepat 16 digit.',
            'no_ktp.unique'     => 'Nomor KTP ini sudah terdaftar.',
            
            'no_sim.required'   => 'Nomor SIM wajib diisi.',
            'no_sim.numeric'    => 'Nomor SIM harus berupa angka.',
            'no_sim.digits'     => 'Nomor SIM C harus berjumlah tepat 12 digit.',
            'no_sim.unique'     => 'Nomor SIM ini sudah terdaftar.',
            
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            
            'cf-turnstile-response.required' => 'Verifikasi keamanan wajib diisi.',
        ]);

        // Buat user baru (Password otomatis di-hash oleh $casts di Model User)
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

        // Auto login setelah registrasi
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang di ED.RENT.');
    }
}