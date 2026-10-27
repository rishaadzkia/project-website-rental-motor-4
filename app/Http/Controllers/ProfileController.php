<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            
            // Format WhatsApp Indonesia: diawali 08/628, total 10-13 digit
            'no_hp' => [
                'required',
                'numeric',
                'regex:/^(08|628)[0-9]{8,11}$/',
                Rule::unique('users', 'no_hp')->ignore($user->id),
            ],
            
            // Format NIK KTP Indonesia: Tepat 16 digit angka
            'no_ktp' => [
                'required',
                'numeric',
                'digits:16',
                Rule::unique('users', 'no_ktp')->ignore($user->id),
            ],
            
            // Format Nomor SIM C Indonesia: Tepat 12 digit angka
            'no_sim' => [
                'required',
                'numeric',
                'digits:12',
                Rule::unique('users', 'no_sim')->ignore($user->id),
            ],
            
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB
        ], [
            'name.required'   => 'Nama lengkap wajib diisi.',
            
            'no_hp.required'  => 'Nomor WhatsApp wajib diisi.',
            'no_hp.numeric'   => 'Nomor WhatsApp harus berupa angka.',
            'no_hp.regex'     => 'Nomor WhatsApp harus diawali 08 atau 628 (total 10-13 digit).',
            'no_hp.unique'    => 'Nomor WhatsApp ini sudah digunakan akun lain.',
            
            'no_ktp.required' => 'Nomor KTP (NIK) wajib diisi.',
            'no_ktp.numeric'  => 'Nomor KTP harus berupa angka.',
            'no_ktp.digits'   => 'Nomor KTP harus berjumlah tepat 16 digit.',
            'no_ktp.unique'   => 'Nomor KTP ini sudah digunakan akun lain.',
            
            'no_sim.required' => 'Nomor SIM wajib diisi.',
            'no_sim.numeric'  => 'Nomor SIM harus berupa angka.',
            'no_sim.digits'   => 'Nomor SIM C harus berjumlah tepat 12 digit.',
            'no_sim.unique'   => 'Nomor SIM ini sudah digunakan akun lain.',
            
            'avatar.image'    => 'File avatar harus berupa gambar.',
            'avatar.mimes'    => 'Format gambar avatar harus jpeg, png, jpg, atau gif.',
            'avatar.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = [
            'name'   => $request->name,
            'no_hp'  => $request->no_hp,
            'no_ktp' => $request->no_ktp,
            'no_sim' => $request->no_sim,
        ];

        // Jika user mengunggah foto baru
        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke folder storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return back()->with('profile_updated', 'Data berhasil diperbarui.');
    }
}