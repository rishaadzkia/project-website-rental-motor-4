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
        $query = Motorcycle::query()->where('status', 'available');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $motorcycles = $query->latest()->paginate(12)->withQueryString();

        return view('user.motorcycles.index', compact('motorcycles'));
    }

    public function show(Motorcycle $motorcycle): View
    {
        return view('user.motorcycles.show', compact('motorcycle'));
    }
}