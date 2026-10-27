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

            {{-- 2. TAMPILAN JIKA MENUNGGU PEMBAYARAN (PENDING + COUNTDOWN) --}}
            @elseif ($booking->status === 'pending')
                <div class="mb-3 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Menunggu Pembayaran" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <!-- COUNTDOWN TIMER -->
                <div class="mb-3">
                    <h2 class="fw-bold text-dark display-6 mb-1" id="countdown-display">00m : 00d</h2>
                    <p class="text-danger fw-semibold small mb-2">Selesaikan Pembayaranmu Sebelum Waktunya Habis</p>
                    <p class="text-secondary small mx-auto" style="max-width: 460px;">
                        Tinggal selangkah lagi kamu bisa bawa motor keliling! Selesaikan transaksimu sekarang sebelum motor disewa ke penyewa lain.
                    </p>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-4">
                    <button type="button" id="pay-now-btn" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Bayar Sekarang
                    </button>

                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-semibold w-100">
                            Batalkan Pesanan
                        </button>
                    </form>

                    @php
                        $waHelpMsg = "Halo Admin, saya butuh bantuan untuk pembayaran pesanan ID #{$booking->id}. Mohon dibantu ya kak 🙏";
                    @endphp
                    <a href="https://wa.me/6288905604619?text={{ urlencode($waHelpMsg) }}" 
                       target="_blank" 
                       class="btn btn-outline-success px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> Chat Admin
                    </a>
                </div>

            {{-- 3. TAMPILAN JIKA GAGAL / BATAL / KADALUARSA --}}
            @else
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Waktu Habis / Gagal" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <h3 class="fw-bold text-dark mb-3">Pesanan Batal / Kadaluarsa</h3>
                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Masa pembayaran pesananmu sudah lewat atau pesanan telah dibatalkan. Kamu bisa membuat pesanan baru dari halaman daftar pesanan.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('bookings.index') }}" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Lihat Daftar Pesanan
                    </a>

                    @php
                        $waHelpMsg = "Halo Admin, saya butuh bantuan untuk pesanan ID #{$booking->id} yang dibatalkan. Mohon dibantu ya kak 🙏";
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

@if ($booking->status === 'pending')
    @php
        $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        $snapUrl = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN)
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));

        // FIX: Batas waktu: 5 menit setelah booking dibuat
        $expiryTime = \Carbon\Carbon::parse($booking->created_at)->addMinutes(5)->timestamp * 1000;
    @endphp

    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- LOGIKA COUNTDOWN TIMER ---
            const expiryTimestamp = {{ $expiryTime }};
            const countdownEl = document.getElementById('countdown-display');

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = expiryTimestamp - now;

                if (distance < 0) {
                    if (countdownEl) countdownEl.innerHTML = "Waktu Habis";
                    window.location.reload();
                    return;
                }

                // FIX: Ubah format ke menit dan detik saja
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (countdownEl) {
                    countdownEl.innerHTML = `${minutes < 10 ? '0' : ''}${minutes}m : ${seconds < 10 ? '0' : ''}${seconds}d`;
                }
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);

            // --- LOGIKA TOMBOL BAYAR SEKARANG ---
            const payBtn = document.getElementById('pay-now-btn');
            const snapToken = "{{ $snapToken ?? '' }}";

            if (payBtn) {
                payBtn.addEventListener('click', function () {
                    if (!snapToken) {
                        alert('Token pembayaran tidak ditemukan. Halaman akan dimuat ulang.');
                        window.location.reload();
                        return;
                    }

                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '');
                        },
                        onPending: function(result) {
                            window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '');
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                        },
                        onClose: function() {
                            // Tetap di halaman ini saat ditutup
                        }
                    });
                });
            }
        });
    </script>
@endif
@endsection