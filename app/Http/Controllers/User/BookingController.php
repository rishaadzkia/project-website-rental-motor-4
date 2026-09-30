<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Motorcycle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
        // 1. Validasi Input
        $validated = $request->validate([
            'start_date'     => ['required', 'date', 'after_or_equal:today'],
            'start_time'     => ['required', 'date_format:H:i'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'end_time'       => ['required', 'date_format:H:i'],
            'pickup_method'  => ['nullable', 'in:pool,delivery'],
            'pickup_address' => ['nullable', 'string', 'required_if:pickup_method,delivery'],
            'delivery_fee'   => ['nullable', 'numeric'],
        ]);

        // 2. Cek Status Ketersediaan Unit Motor
        if ($motorcycle->status !== 'available') {
            return back()->withErrors([
                'motorcycle' => 'Motor ini sedang tidak tersedia untuk disewa.',
            ])->withInput();
        }

        // Gabungkan tanggal dan jam dengan Timezone Asia/Jakarta secara eksplisit
        $startDateTime = Carbon::createFromFormat(
            'Y-m-d H:i', 
            $validated['start_date'] . ' ' . $validated['start_time'], 
            'Asia/Jakarta'
        );

        $endDateTime = Carbon::createFromFormat(
            'Y-m-d H:i', 
            $validated['end_date'] . ' ' . $validated['end_time'], 
            'Asia/Jakarta'
        );

        if ($endDateTime->lte($startDateTime)) {
            return back()->withErrors([
                'end_date' => 'Waktu pengembalian harus lebih lambat dari waktu pengambilan.',
            ])->withInput();
        }

        // 3. Hitung Total Hari Sewa
        $diffInHours = $startDateTime->diffInHours($endDateTime);
        $totalDays   = (int) ceil($diffInHours / 24);
        $totalDays   = $totalDays > 0 ? $totalDays : 1;
        $totalPrice  = $totalDays * (float) $motorcycle->price_per_day;

        // 4. Cek Bentrok Jadwal Booking
        $hasConflict = Booking::where('motorcycle_id', $motorcycle->id)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('start_date', [$startDateTime->toDateTimeString(), $endDateTime->toDateTimeString()])
                    ->orWhereBetween('end_date', [$startDateTime->toDateTimeString(), $endDateTime->toDateTimeString()])
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->where('start_date', '<=', $startDateTime->toDateTimeString())
                          ->where('end_date', '>=', $endDateTime->toDateTimeString());
                    });
            })
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'start_date' => 'Motor sudah dibooking pada rentang tanggal & jam tersebut.',
            ])->withInput();
        }

        // 5. Simpan Data Booking dengan Waktu Lengkap
        $pickupMethod = $validated['pickup_method'] ?? 'pool';

        $booking = Booking::create([
            'user_id'        => Auth::id(),
            'motorcycle_id'  => $motorcycle->id,
            'start_date'     => $startDateTime->format('Y-m-d H:i:s'),
            'end_date'       => $endDateTime->format('Y-m-d H:i:s'),
            'pickup_time'    => $validated['start_time'],
            'return_time'    => $validated['end_time'],
            'total_days'     => $totalDays,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
            'pickup_method'  => $pickupMethod,
            'pickup_address' => $pickupMethod === 'delivery' ? ($validated['pickup_address'] ?? null) : null,
            'delivery_fee'   => $pickupMethod === 'delivery' ? ($validated['delivery_fee'] ?? 0) : 0,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking berhasil dibuat. Silakan selesaikan pembayaran.');
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
            $snapToken = $this->generateSnapToken($booking);
        }

        return view('user.bookings.show', compact('booking', 'snapToken'));
    }

    public function status(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['motorcycle', 'user']);

        $orderId = request('order_id');

        // Jika ada order_id, cek detail pembayaran langsung ke API Midtrans
        if ($orderId) {
            $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
            $isProduction = filter_var(config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);

            $endpoint = $isProduction 
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                    ])
                    ->get($endpoint);

                if ($response->successful()) {
                    $trxData = $response->json();
                    $realStatus = $trxData['transaction_status'] ?? '';
                    $realPaymentType = $trxData['payment_type'] ?? null;

                    // Format metode bayar spesifik dari respons API Midtrans
                    $paymentMethod = $this->formatPaymentMethod($realPaymentType, $trxData);

                    if (in_array($realStatus, ['capture', 'settlement'])) {
                        $booking->update([
                            'status'         => 'confirmed',
                            'payment_method' => $paymentMethod,
                            'paid_at'        => $booking->paid_at ?? Carbon::now('Asia/Jakarta'),
                        ]);
                    } elseif (in_array($realStatus, ['deny', 'expire', 'cancel'])) {
                        $booking->update([
                            'status'         => 'cancelled',
                            'payment_method' => $paymentMethod,
                        ]);
                    } elseif ($realStatus === 'pending') {
                        $booking->update([
                            'status'         => 'pending',
                            'payment_method' => $paymentMethod,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Check Midtrans Status Error: ' . $e->getMessage());
            }
        }

        return view('user.bookings.status', compact('booking'));
    }

    public function notificationHandler(Request $request): JsonResponse
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid order ID'], 400);
        }

        // Ekstrak ID Booking dari Order ID format: "RENTAL-{id}"
        $bookingId = str_replace('RENTAL-', '', $orderId);

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $paymentMethod = $this->formatPaymentMethod($paymentType, $payload);

        // Update Database berdasarkan status transaksi
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $booking->update([
                    'status'         => 'pending',
                    'payment_method' => $paymentMethod,
                ]);
            } else if ($fraudStatus == 'accept') {
                $booking->update([
                    'status'         => 'confirmed',
                    'payment_method' => $paymentMethod,
                    'paid_at'        => $booking->paid_at ?? Carbon::now('Asia/Jakarta'),
                ]);
            }
        } else if ($transactionStatus == 'settlement') {
            $booking->update([
                'status'         => 'confirmed',
                'payment_method' => $paymentMethod,
                'paid_at'        => $booking->paid_at ?? Carbon::now('Asia/Jakarta'),
            ]);
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $booking->update([
                'status'         => 'cancelled',
                'payment_method' => $paymentMethod,
            ]);
        } else if ($transactionStatus == 'pending') {
            $booking->update([
                'status'         => 'pending',
                'payment_method' => $paymentMethod,
            ]);
        }

        return response()->json(['message' => 'Notification processed successfully']);
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

    public function updatePickup(Request $request, Booking $booking): JsonResponse|RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $request->validate([
            'pickup_method'  => 'required|in:pool,delivery',
            'pickup_address' => 'required_if:pickup_method,delivery|nullable|string|min:10',
            'delivery_fee'   => 'nullable|numeric',
        ], [
            'pickup_address.required_if' => 'Alamat pengantaran wajib diisi jika memilih metode Diantar ke Lokasi.',
            'pickup_address.min'         => 'Alamat pengantaran minimal 10 karakter agar lokasi jelas.',
        ]);

        $isDelivery = $request->pickup_method === 'delivery';

        $booking->update([
            'pickup_method'  => $request->pickup_method,
            'pickup_address' => $isDelivery ? $request->pickup_address : null,
            'delivery_fee'   => $isDelivery ? ($request->delivery_fee ?? 0) : 0,
            'snap_token'     => null, 
        ]);

        $newSnapToken = $this->generateSnapToken($booking);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'snap_token' => $newSnapToken,
                'message'    => 'Detail pengantaran berhasil diperbarui.'
            ]);
        }

        return redirect()->back()->with('success', 'Metode pengantaran berhasil diperbarui.');
    }

    private function generateSnapToken(Booking $booking): ?string
    {
        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        $isProduction = filter_var(config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);

        $endpoint = $isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $deliveryFee = $booking->pickup_method === 'delivery' ? (float) ($booking->delivery_fee ?? 0) : 0;
        $grossAmount = (int) round($booking->total_price + $deliveryFee);

        $itemDetails = [
            [
                'id'       => 'MOTOR-' . $booking->motorcycle_id,
                'price'    => (int) round($booking->total_price),
                'quantity' => 1,
                'name'     => substr('Sewa ' . ($booking->motorcycle->name ?? 'Motor'), 0, 50),
            ]
        ];

        if ($booking->pickup_method === 'delivery' && $deliveryFee > 0) {
            $itemDetails[] = [
                'id'       => 'DELIVERY-FEE',
                'price'    => (int) round($deliveryFee),
                'quantity' => 1,
                'name'     => 'Biaya Pengantaran',
            ];
        }

        $params = [
            'transaction_details' => [
                // Order ID statis agar timer tidak ter-reset ulang
                'order_id'     => 'RENTAL-' . $booking->id,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name ?? 'Pelanggan',
                'email'      => $booking->user->email ?? 'customer@example.com',
                'phone'      => $booking->user->no_hp ?? '08123456789',
            ],
            'item_details' => $itemDetails
        ];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                ])
                ->post($endpoint, $params);

            if ($response->successful()) {
                $responseData = $response->json();
                $snapToken = $responseData['token'] ?? null;

                if ($snapToken) {
                    $booking->update(['snap_token' => $snapToken]);
                    return $snapToken;
                }
            } else {
                Log::error('Midtrans API Error Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Helper privat untuk mengekstrak dan merapikan nama metode pembayaran Midtrans
     */
    private function formatPaymentMethod(?string $paymentType, array $payloadData = []): string
    {
        if (empty($paymentType)) {
            return 'PAYMENT GATEWAY';
        }

        $type = strtolower($paymentType);

        if ($type === 'bank_transfer') {
            $bank = null;

            if (isset($payloadData['va_numbers'][0]['bank'])) {
                $bank = $payloadData['va_numbers'][0]['bank'];
            } elseif (isset($payloadData['va_numbers'][0]->bank)) {
                $bank = $payloadData['va_numbers'][0]->bank;
            }

            if ($bank) {
                return 'VA ' . strtoupper($bank);
            } elseif (!empty($payloadData['permata_va_number'])) {
                return 'VA PERMATA';
            }

            return 'BANK TRANSFER';
        } elseif ($type === 'echannel') {
            return 'MANDIRI BILL';
        } elseif ($type === 'cstore') {
            $store = $payloadData['store'] ?? '';
            return 'RETAIL ' . strtoupper($store);
        } elseif ($type === 'credit_card') {
            return 'CREDIT CARD';
        } elseif (in_array($type, ['gopay', 'qris', 'shopeepay'])) {
            return strtoupper($type);
        }

        return strtoupper($paymentType);
    }
}