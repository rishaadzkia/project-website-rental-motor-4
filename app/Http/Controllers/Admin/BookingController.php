<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::with(['user', 'motorcycle'])
            ->latest()
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'motorcycle']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        // Default action type ke 'update_status' jika tidak dikirim dari form
        $actionType = $request->input('action_type', 'update_status');

        switch ($actionType) {
            // 1. Toggle Verifikasi Berkas Fisik
            case 'toggle_verification':
                $booking->update([
                    'is_verified' => !$booking->is_verified,
                ]);
                $msg = $booking->is_verified ? 'Identitas berhasil diverifikasi!' : 'Status verifikasi dibatalkan.';
                break;

            // 2. Simpan Data Driver / Staf Bertugas
            case 'update_driver':
                $request->validate([
                    'driver_name' => 'nullable|string|max:255',
                    'driver_phone' => 'nullable|string|max:20',
                ]);

                $booking->update([
                    'driver_name' => $request->driver_name,
                    'driver_phone' => $request->driver_phone,
                ]);
                $msg = 'Data driver / staf bertugas berhasil disimpan!';
                break;

            // 3. Simpan Catatan Internal Admin
            case 'update_notes':
                $booking->update([
                    'admin_notes' => $request->admin_notes,
                ]);
                $msg = 'Catatan internal admin berhasil disimpan!';
                break;

            // 4. Record Actual Pickup & Return
            case 'record_pickup':
                $booking->update([
                    'actual_pickup' => now(),
                ]);
                $msg = 'Waktu penyerahan (pickup) berhasil dicatat!';
                break;

            case 'record_return':
                $booking->update([
                    'actual_return' => now(),
                ]);
                $msg = 'Waktu pengembalian (return) berhasil dicatat!';
                break;

            // 5. Update Status Utama (Modal Index & Detail)
            case 'update_status':
            default:
                $request->validate([
                    'status' => 'required|in:pending,confirmed,ongoing,completed,cancelled',
                ]);

                $booking->update([
                    'status' => $request->status,
                ]);
                $msg = 'Status pesanan berhasil diperbarui!';
                break;
        }

        return redirect()->back()->with('success', $msg);
    }

    public function printInvoice(Booking $booking): View
    {
        $booking->load(['user', 'motorcycle']);
        return view('admin.bookings.print-invoice', compact('booking'));
    }

    public function printSuratJalan(Booking $booking): View
    {
        $booking->load(['user', 'motorcycle']);
        return view('admin.bookings.print-surat-jalan', compact('booking'));
    }
}