<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Motorcycle::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('model') && $request->model !== 'semua') {
            $query->where('model', $request->model);
        }

        $checkTime = $request->filled('rental_time') 
            ? Carbon::parse($request->rental_time) 
            : Carbon::now();

        $checkDate = $checkTime->toDateString();
        $activeBookingStatuses = ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'];

        if ($request->filled('status') && $request->status !== 'semua') {
            $status = $request->status;

            if (in_array($status, ['maintenance', 'pending_maintenance'])) {
                $query->where('status', $status);
            } elseif ($status === 'rented') {
                $query->whereNotIn('status', ['maintenance', 'pending_maintenance'])
                      ->whereHas('bookings', function ($q) use ($checkDate, $activeBookingStatuses) {
                          $q->whereIn('status', $activeBookingStatuses)
                            ->whereDate('start_date', '<=', $checkDate)
                            ->whereDate('end_date', '>=', $checkDate);
                      });
            } elseif ($status === 'available') {
                $query->whereNotIn('status', ['maintenance', 'pending_maintenance'])
                      ->whereDoesntHave('bookings', function ($q) use ($checkDate, $activeBookingStatuses) {
                          $q->whereIn('status', $activeBookingStatuses)
                            ->whereDate('start_date', '<=', $checkDate)
                            ->whereDate('end_date', '>=', $checkDate);
                      });
            }
        }

        $motorcycles = $query->latest()->paginate(10)->withQueryString();

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
            'status'        => ['required', 'in:available,maintenance,pending_maintenance'],
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
            'status'        => ['required', 'in:available,maintenance,pending_maintenance'],
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $today = Carbon::today()->toDateString();
        $activeStatuses = ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'];
        $upcomingBookings = null;

        if ($validated['status'] === 'maintenance') {
            $isCurrentlyRented = $motorcycle->bookings()
                ->whereIn('status', $activeStatuses)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->exists();

            if ($isCurrentlyRented) {
                return redirect()
                    ->back()
                    ->with('error', 'Gagal mengubah status! Motor sedang digunakan oleh penyewa saat ini.');
            }

            $upcomingBookings = $motorcycle->bookings()
                ->with('user')
                ->whereIn('status', $activeStatuses)
                ->whereDate('end_date', '>=', $today)
                ->orderBy('start_date', 'asc')
                ->get();

            if ($upcomingBookings->count() > 0) {
                $validated['status'] = 'pending_maintenance';
                $validated['maintenance_requested_at'] = now();
            } else {
                $validated['maintenance_requested_at'] = null;
            }
        } elseif ($validated['status'] === 'available') {
            $validated['maintenance_requested_at'] = null;
        }

        if ($request->hasFile('image')) {
            if ($motorcycle->image) {
                Storage::disk('public')->delete($motorcycle->image);
            }
            $validated['image'] = $request->file('image')->store('motorcycles', 'public');
        }

        $motorcycle->update($validated);

        if ($upcomingBookings && $upcomingBookings->count() > 0) {
            return redirect()
                ->route('admin.motorcycles.index')
                ->with('warning_upcoming_bookings', $upcomingBookings)
                ->with('success', 'Motor diset ke Pending Perawatan. Diberikan batas waktu 2 hari untuk konfirmasi pelanggan.');
        }

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil diperbarui.');
    }

    public function destroy(Motorcycle $motorcycle): RedirectResponse
    {
        $today = Carbon::today()->toDateString();
        $activeStatuses = ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'];

        // Ambil data pemesanan aktif atau di masa mendatang milik motor ini
        $activeBookings = $motorcycle->bookings()
            ->with('user')
            ->whereIn('status', $activeStatuses)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->get();

        // FIKSASI: Jika ada transaksi berjalan/mendatang, tampilkan modal daftar WA pelanggan
        if ($activeBookings->count() > 0) {
            return redirect()
                ->back()
                ->with('warning_delete_bookings', $activeBookings)
                ->with('deleted_motor_name', $motorcycle->name);
        }

        // Hapus file gambar jika ada
        if ($motorcycle->image) {
            Storage::disk('public')->delete($motorcycle->image);
        }

        $motorcycle->delete();

        return redirect()
            ->route('admin.motorcycles.index')
            ->with('success', 'Motor berhasil dihapus.');
    }
}