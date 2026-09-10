<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Motorcycle $motorcycle): View
    {
        if ($motorcycle->status !== 'available') {
            abort(404);
        }

        return view(
            'user.bookings.create',
            compact('motorcycle')
        );
    }

    public function store(
        Request $request,
        Motorcycle $motorcycle
    ): RedirectResponse {
        $validated = $request->validate([
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        if ($motorcycle->status !== 'available') {
            return back()->withErrors([
                'motorcycle' => 'Motor ini sedang tidak tersedia.',
            ]);
        }

        $startDate = \Carbon\Carbon::parse(
            $validated['start_date']
        );

        $endDate = \Carbon\Carbon::parse(
            $validated['end_date']
        );

        $totalDays = $startDate->diffInDays($endDate) + 1;

        $totalPrice =
            $totalDays * (float) $motorcycle->price_per_day;

        $hasConflict = Booking::where(
            'motorcycle_id',
            $motorcycle->id
        )
            ->whereIn('status', [
                'pending',
                'confirmed',
                'ongoing',
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->whereBetween('start_date', [
                        $startDate,
                        $endDate,
                    ])
                    ->orWhereBetween('end_date', [
                        $startDate,
                        $endDate,
                    ])
                    ->orWhere(function ($query) use (
                        $startDate,
                        $endDate
                    ) {
                        $query
                            ->where(
                                'start_date',
                                '<=',
                                $startDate
                            )
                            ->where(
                                'end_date',
                                '>=',
                                $endDate
                            );
                    });
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'start_date' =>
                    'Motor sudah dibooking pada tanggal tersebut.',
            ]);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'motorcycle_id' => $motorcycle->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with(
                'success',
                'Booking berhasil dibuat.'
            );
    }

    public function index(): View
    {
        $bookings = Booking::where(
            'user_id',
            Auth::id()
        )
            ->with('motorcycle')
            ->latest()
            ->paginate(10);

        return view(
            'user.bookings.index',
            compact('bookings')
        );
    }

    public function show(Booking $booking): View
    {
        abort_unless(
            $booking->user_id === Auth::id(),
            403
        );

        $booking->load('motorcycle');

        return view(
            'user.bookings.show',
            compact('booking')
        );
    }

    public function cancel(
        Booking $booking
    ): RedirectResponse {
        abort_unless(
            $booking->user_id === Auth::id(),
            403
        );

        if ($booking->status !== 'pending') {
            return back()->withErrors([
                'booking' =>
                    'Booking yang sudah diproses tidak dapat dibatalkan.',
            ]);
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('bookings.index')
            ->with(
                'success',
                'Booking berhasil dibatalkan.'
            );
    }
}