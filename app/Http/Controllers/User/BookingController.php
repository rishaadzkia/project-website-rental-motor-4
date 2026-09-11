<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Motorcycle $motorcycle): View
    {
        if ($motorcycle->status !== 'available') {
            abort(404);
        }

        return view('user.bookings.create', compact('motorcycle'));
    }

    public function store(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
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

        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $totalDays * (float) $motorcycle->price_per_day;

        $hasConflict = Booking::where('motorcycle_id', $motorcycle->id)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query
                            ->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'start_date' => 'Motor sudah dibooking pada tanggal tersebut.',
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
            ->with('success', 'Booking berhasil dibuat.');
    }

    public function index(): View
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('motorcycle')
            ->latest()
            ->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['motorcycle', 'user']);

        $snapToken = $booking->snap_token;

        if ($booking->status === 'pending' && empty($snapToken)) {
            
            $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
            $isProduction = filter_var(config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);

            $endpoint = $isProduction 
                ? 'https://app.midtrans.com/snap/v1/transactions' 
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $grossAmount = (int) round($booking->total_price);

            $params = [
                'transaction_details' => [
                    'order_id' => 'RENTAL-' . $booking->id . '-' . time(),
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $booking->user->name ?? 'Pelanggan',
                    'email' => $booking->user->email ?? 'customer@example.com',
                    'phone' => $booking->user->no_hp ?? '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => 'MOTOR-' . $booking->motorcycle_id,
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => substr('Sewa ' . ($booking->motorcycle->name ?? 'Motor'), 0, 50),
                    ]
                ]
            ];

            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    ])
                    ->post($endpoint, $params);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $snapToken = $responseData['token'] ?? null;

                    if ($snapToken) {
                        $booking->update(['snap_token' => $snapToken]);
                    }
                } else {
                    Log::error('Midtrans API Error Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Midtrans Snap Exception: ' . $e->getMessage());
            }
        }

        return view('user.bookings.show', compact('booking', 'snapToken'));
    }

    public function status(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        // Fallback update status lokal berdasarkan parameter URL dari JS
        if (request()->has('transaction_status')) {
            $trxStatus = request('transaction_status');
            if (in_array($trxStatus, ['capture', 'settlement'])) {
                $booking->update(['status' => 'confirmed']);
            } elseif (in_array($trxStatus, ['deny', 'expire', 'cancel'])) {
                $booking->update(['status' => 'cancelled']);
            }
        }

        return view('user.bookings.status', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        if ($booking->status !== 'pending') {
            return back()->withErrors([
                'booking' => 'Booking yang sudah diproses tidak dapat dibatalkan.',
            ]);
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    public function notificationHandler(Request $request)
    {
        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        
        $payload = $request->all();
        
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $type = $payload['payment_type'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $validSignatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $validSignatureKey) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderParts = explode('-', $orderId);
        $bookingId = $orderParts[1] ?? null;

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $booking->update(['status' => 'pending']);
                } else {
                    $booking->update(['status' => 'confirmed']);
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $booking->update(['status' => 'confirmed']);
        } else if ($transactionStatus == 'pending') {
            $booking->update(['status' => 'pending']);
        } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $booking->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }
}