<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        // FIKSASI: Mengabaikan motor yang berstatus 'maintenance' maupun 'pending_maintenance'
        $query = Motorcycle::query()->whereNotIn('status', ['maintenance', 'pending_maintenance']);

        // Filter berdasarkan pencarian kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tipe/model kendaraan
        if ($request->filled('model') && $request->model !== 'semua') {
            $query->where('model', $request->model);
        }

        $motorcycles = $query->latest()->paginate(12)->withQueryString();

        return view('user.motorcycles.index', compact('motorcycles'));
    }

    public function show(Motorcycle $motorcycle): View
    {
        // FIKSASI: Jika motor berstatus maintenance atau pending_maintenance, cegah akses langsung
        if (in_array($motorcycle->status, ['maintenance', 'pending_maintenance'])) {
            abort(404, 'Kendaraan sedang dalam perawatan atau tidak tersedia.');
        }

        return view('user.motorcycles.show', compact('motorcycle'));
    }
}