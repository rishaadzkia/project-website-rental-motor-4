<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Carbon\Carbon;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Motorcycle::query();

        // Filter Cari Nama Motor[cite: 16]
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Tipe / Model[cite: 16]
        if ($request->filled('model') && $request->model !== 'semua') {
            $query->where('model', $request->model);
        }

        // Tentukan waktu acuan pengecekan[cite: 16]
        $checkTime = $request->filled('rental_time') 
            ? Carbon::parse($request->rental_time) 
            : Carbon::now();

        $checkDate = $checkTime->toDateString();

        // Status booking terkonfirmasi yang dipakai sistem
        $activeBookingStatuses = ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'];

        // Filter Status Dinamis
        if ($request->filled('status') && $request->status !== 'semua') {
            $status = $request->status;

            if ($status === 'maintenance') {
                $query->where('status', 'maintenance');
            } elseif ($status === 'rented') {
                $query->where('status', '!=', 'maintenance')
                      ->whereHas('bookings', function ($q) use ($checkDate, $activeBookingStatuses) {
                          $q->whereIn('status', $activeBookingStatuses)
                            ->whereDate('start_date', '<=', $checkDate)
                            ->whereDate('end_date', '>=', $checkDate);
                      });
            } elseif ($status === 'available') {
                $query->where('status', '!=', 'maintenance')
                      ->whereDoesntHave('bookings', function ($q) use ($checkDate, $activeBookingStatuses) {
                          $q->whereIn('status', $activeBookingStatuses)
                            ->whereDate('start_date', '<=', $checkDate)
                            ->whereDate('end_date', '>=', $checkDate);
                      });
            }
        }

        $motorcycles = $query->latest()->paginate(4)->withQueryString();

        return view('admin.motorcycles.index', compact('motorcycles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'brand'         => ['required', 'string', 'max:255'],
            'model'         => ['required', 'string', 'in:Matic (Skutik),Sport,Cub (Bebek),EV (Kendaraan Listrik),Big Bike (Moge)'],
            'plate_number'  => ['required', 'string', 'max:20', 'unique:motorcycles,plate_number'],
            'year'          => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'tank_capacity' => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required', 'in:available,maintenance'],
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('motorcycles', 'public');
        }

        Motorcycle::create($validated);

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil ditambahkan.');
    }

    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'brand'         => ['required', 'string', 'max:255'],
            'model'         => ['required', 'string', 'in:Matic (Skutik),Sport,Cub (Bebek),EV (Kendaraan Listrik),Big Bike (Moge)'],
            'plate_number'  => ['required', 'string', 'max:20', 'unique:motorcycles,plate_number,' . $motorcycle->id],
            'year'          => ['required', 'integer', 'min:2000', 'max:' . date('Y')],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'tank_capacity' => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required', 'in:available,maintenance'],
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($motorcycle->image) {
                Storage::disk('public')->delete($motorcycle->image);
            }
            $validated['image'] = $request->file('image')->store('motorcycles', 'public');
        }

        $motorcycle->update($validated);

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil diperbarui.');
    }

    public function edit(Motorcycle $motorcycle): View
    {
        return view('admin.motorcycles.edit', compact('motorcycle'));
    }

    public function show(Motorcycle $motorcycle): View
    {
        return view('admin.motorcycles.show', compact('motorcycle'));
    }
}