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
        $bookings = Booking::with([
            'user',
            'motorcycle',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'admin.bookings.index',
            compact('bookings')
        );
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'motorcycle',
        ]);

        return view(
            'admin.bookings.show',
            compact('booking')
        );
    }

    public function update(
        Request $request,
        Booking $booking
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,confirmed,ongoing,completed,cancelled',
            ],
        ]);

        $booking->update([
            'status' => $validated['status'],
        ]);

        return back()->with(
            'success',
            'Status booking berhasil diperbarui.'
        );
    }
}