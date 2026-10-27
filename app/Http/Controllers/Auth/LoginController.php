<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi format input login No. WhatsApp
        $credentials = $request->validate([
            'no_hp' => [
                'required',
                'string',
                'regex:/^(08|628)[0-9]{8,11}$/',
            ],
            'password' => ['required', 'string'],
        ], [
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex'    => 'Format nomor WhatsApp tidak valid (harus diawali 08 atau 628).',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['no_hp' => $credentials['no_hp'], 'password' => $credentials['password']], $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Pengecekan eksplisit role Admin
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin!');
            }

            // Untuk customer biasa
            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'no_hp' => 'No. WhatsApp atau password salah.',
        ])->onlyInput('no_hp');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil keluar akun.');
    }
}