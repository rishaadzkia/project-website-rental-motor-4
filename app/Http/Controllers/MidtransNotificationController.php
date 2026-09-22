<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransNotificationController extends Controller
{
    public function handleNotification(Request $request): JsonResponse
    {
        // 1. Konfigurasi Server Key Midtrans
        Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        Config::$isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        Config::$isSanitized = true;

        try {
            // 2. Ambil payload notifikasi resmi dari Midtrans
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $type = $notif->payment_type; // contoh: bank_transfer, gopay, qris, cstore
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status;

            // Format nama metode pembayaran agar lebih rapi di tampilan
            $paymentMethod = $type;
            if ($type == 'bank_transfer') {
                if (isset($notif->va_numbers[0]->bank)) {
                    $paymentMethod = 'VA ' . strtoupper($notif->va_numbers[0]->bank);
                } elseif (isset($notif->permata_va_number)) {
                    $paymentMethod = 'VA PERMATA';
                }
            } elseif ($type == 'cstore') {
                $paymentMethod = isset($notif->store) ? strtoupper($notif->store) : 'MINIMARKET';
            } elseif ($type == 'echannel') {
                $paymentMethod = 'MANDIRI BILL';
            }

            // Ekstrak ID Booking (Format: "RENTAL-15-17123456" atau "BOOKING-15")
            $orderParts = explode('-', $orderId);
            $bookingId = $orderParts[1] ?? $orderId;
            
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json(['message' => 'Booking not found'], 404);
            }

            // 3. Update status booking, payment_method, dan paid_at
            if ($transactionStatus == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraudStatus == 'challenge') {
                        $booking->update(['status' => 'pending']);
                    } else {
                        $booking->update([
                            'status'         => 'confirmed',
                            'payment_method' => strtoupper($paymentMethod),
                            'paid_at'        => now(),
                        ]);
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                // Pembayaran Berhasil
                $booking->update([
                    'status'         => 'confirmed',
                    'payment_method' => strtoupper($paymentMethod),
                    'paid_at'        => now(),
                ]);
            } else if ($transactionStatus == 'pending') {
                $booking->update([
                    'status'         => 'pending',
                    'payment_method' => strtoupper($paymentMethod),
                ]);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                // Pembayaran Ditolak, Kadaluarsa, atau Dibatalkan
                $booking->update([
                    'status' => 'cancelled',
                ]);
            }

            return response()->json(['message' => 'Notification processed successfully']);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing notification',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}