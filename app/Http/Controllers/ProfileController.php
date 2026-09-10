<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'no_hp'  => 'nullable|string|max:20',
            'no_ktp' => 'nullable|string|max:20',
            'no_sim' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
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

        return back()->with('profile_updated', 'Data profil berhasil diperbarui.');
    }
}