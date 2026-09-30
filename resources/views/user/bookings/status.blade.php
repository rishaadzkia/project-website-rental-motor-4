@extends('layouts.app')

@section('title', 'Status Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">

            {{-- 1. TAMPILAN JIKA PEMBAYARAN BERHASIL --}}
            @if (in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']))
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/success_avatar.png') }}" 
                         alt="Pembayaran Berhasil" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <h3 class="fw-bold text-dark mb-3">Yeay! Pembayaran Berhasil</h3>
                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Selamat, kamu berhasil booking motornya! Cek status transaksimu dan chat admin untuk konfirmasi pengambilan motor!
                </p>

                @php
                    $nomorAdmin = '6288905604619';
                    $method = strtolower($booking->pickup_method ?? 'pool');

                    $startDate = \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y');
                    $endDate   = \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d F Y');
                    $pickupTime =$booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') . ' WIB' : '-';$returnTime = $booking->return_time ? \Carbon\Carbon::parse($booking->return_time)->format('H:i') . ' WIB' : '-';

                    $lokasiInfo = ($method === 'delivery') 
                        ? "Diantar ke Lokasi (Delivery)\n📍 *Alamat Pengantaran:* " . ($booking->pickup_address ?? 'Belum diisi')
                        : "Ambil Sendiri di Pool / Garasi";

                    $pesanWA = "Halo Admin, selamat pagi/siang 😊\n\n"
                             . "Saya mau konfirmasi booking motor yang baru saja saya selesaikan via web. Berikut rincian pemesanan dan pengambilannya ya kak:\n\n"
                             . "📌 *Detail Pemesanan:*\n"
                             . "• ID Booking: #" . $booking->id . "\n"
                             . "• Nama Pemesan: " . ($booking->user->name ?? '-') . "\n"
                             . "• Unit Motor: " . ($booking->motorcycle->name ?? '-') . "\n\n"
                             . "📅 *Jadwal Sewa:*\n"
                             . "• Ambil: " . $startDate . " (" . $pickupTime . ")\n"
                             . "• Kembali: " . $endDate . " (" . $returnTime . ")\n"
                             . "• Durasi: " . ($booking->total_days ?? 1) . " Hari\n\n"
                             . "🚚 *Pengambilan:*\n"
                             . "• " . $lokasiInfo . "\n\n"
                             . "💰 *Total Biaya:* Rp " . number_format(($booking->total_price ?? 0) + ($booking->delivery_fee ?? 0), 0, ',', '.') . "\n\n"
                             . "Mohon dikonfirmasi ya kak, terima kasih banyak! 🙏";

                    $urlWA = "https://wa.me/" . $nomorAdmin . "?text=" . urlencode($pesanWA);
                @endphp

                <div class="d-flex justify-content-center">
                    <a href="{{ $urlWA }}" target="_blank" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-whatsapp me-2"></i> Konfirmasi WhatsApp
                    </a>
                </div>

            {{-- 2. TAMPILAN JIKA STATUS PENDING (MENUNGGU PEMBAYARAN + COUNTDOWN) --}}
            @elseif ($booking->status === 'pending')
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Menunggu Pembayaran" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 200px; width: auto;">
                </div>

                <!-- Tampilan Timer Countdown -->
                <div class="mb-3">
                    <h2 class="fw-bold text-dark display-5 mb-1" id="countdown-timer">00<sub>j</sub> : 00<sub>m</sub> 00<sub>d</sub></h2>
                    <p class="text-danger fw-semibold small mb-0">Selesaikan Pembayaranmu Sebelum Waktunya Habis</p>
                </div>

                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Tinggal selangkah lagi kamu bisa bawa motor pilihanmu! Klik tombol di bawah untuk melanjutkan proses pembayaran.
                </p>

                @php
                    $waPendingMsg = "Halo Admin, saya mengalami kendala saat pembayaran untuk booking ID #{$booking->id}. Mohon dibantu ya kak 🙏";
                @endphp

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <!-- Tombol Lanjutkan Pembayaran -->
                    <button type="button" id="pay-button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                    </button>

                    <!-- Tombol Batalkan Pesanan -->
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger px-3 py-2 rounded-3 fw-semibold shadow-sm">
                            Batalkan
                        </button>
                    </form>

                    <!-- Tombol Bantuan WA -->
                    <a href="https://wa.me/6288905604619?text={{ urlencode($waPendingMsg) }}" 
                       target="_blank" 
                       class="btn btn-outline-success px-3 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>

                @php
                    $expiryTime = \Carbon\Carbon::parse($booking->created_at)->addHours(24)->timestamp * 1000;
                @endphp

                <!-- INTEGRASI MIDTRANS SNAP -->
                @php
                    $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
                    $snapUrl = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN)
                        ? 'https://app.midtrans.com/snap/snap.js' 
                        : 'https://app.sandbox.midtrans.com/snap/snap.js';
                    $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
                @endphp

                <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function () {
                        const expiryTimestamp = {{ $expiryTime }};
                        const timerElement = document.getElementById('countdown-timer');
                        const payButton = document.getElementById('pay-button');
                        const snapToken = "{{ $snapToken ?? $booking->snap_token ?? '' }}";

                        // Trigger Midtrans Snap Popup
                        if (payButton) {
                            payButton.addEventListener('click', function () {
                                if (!snapToken) {
                                    alert('Token pembayaran tidak ditemukan. Silakan muat ulang halaman.');
                                    return;
                                }

                                snap.pay(snapToken, {
                                    onSuccess: function(result) { 
                                        window.location.href = "{{ route('bookings.status', $booking) }}"; 
                                    },
                                    onPending: function(result) { 
                                        window.location.href = "{{ route('bookings.status', $booking) }}";
                                    },
                                    onError: function(result) { 
                                        window.location.href = "{{ route('bookings.status', $booking) }}";
                                    },
                                    onClose: function() { 
                                        window.location.reload();
                                    }
                                });
                            });
                        }

                        // Countdown Timer Real-time
                        function updateCountdown() {
                            const now = new Date().getTime();
                            const distance = expiryTimestamp - now;

                            if (distance <= 0) {
                                timerElement.innerHTML = "<span class='text-danger fs-3'>Waktu Pembayaran Habis</span>";
                                // Otomatis refresh agar sistem membatalkan pesanan
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                                return;
                            }

                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            timerElement.innerHTML = `${hours}<sub>j</sub> : ${minutes < 10 ? '0' : ''}${minutes}<sub>m</sub> ${seconds < 10 ? '0' : ''}${seconds}<sub>d</sub>`;
                        }

                        updateCountdown();
                        setInterval(updateCountdown, 1000);
                    });
                </script>

            {{-- 3. TAMPILAN JIKA GAGAL / BATAL / EXPIRED --}}
            @else
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Waktu Habis / Gagal" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <h3 class="fw-bold text-dark mb-3">Pesanan Dibatalkan / Kadaluarsa</h3>
                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Masa berlaku pembayaran untuk pesanan ini telah habis atau dibatalkan. Kamu dapat membuat pemesanan baru.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('motorcycles.index') }}" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Cari Motor Lain
                    </a>

                    @php
                        $waHelpMsg = "Halo Admin, saya butuh bantuan terkait booking ID #{$booking->id} yang sudah dibatalkan. Mohon dibantu ya kak 🙏";
                    @endphp
                    <a href="https://wa.me/6288905604619?text={{ urlencode($waHelpMsg) }}" 
                       target="_blank" 
                       class="btn btn-outline-success px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> Chat Admin
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection