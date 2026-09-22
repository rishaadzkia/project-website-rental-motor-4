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

    // Tanggal & Jam Formatted
    $startDate = \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y');
    $endDate   = \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d F Y');
    $pickupTime = $booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') . ' WIB' : '-';
    $returnTime = $booking->return_time ? \Carbon\Carbon::parse($booking->return_time)->format('H:i') . ' WIB' : '-';

    // Info Metode Pengambilan
    $lokasiInfo = ($method === 'delivery') 
        ? "Diantar ke Lokasi (Delivery)\n📍 *Alamat Pengantaran:* " . ($booking->pickup_address ?? 'Belum diisi')
        : "Ambil Sendiri di Pool / Garasi";

    // Format Pesan WhatsApp
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

            {{-- 2. TAMPILAN JIKA GAGAL / BATAL / KADALUARSA / PENDING --}}
            @else
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Waktu Habis / Gagal" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <h3 class="fw-bold text-dark mb-3">Yahh, Pesananmu Gagal!</h3>
                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Masa pembayaran pesananmu sudah lewat nih. Kamu masih bisa booking ulang sekarang atau hubungi admin kami untuk bantuan.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Ulangi Pembayaran
                    </a>

                    @php
                        $waHelpMsg = "Halo Admin, saya butuh bantuan untuk booking ID #{$booking->id}. Mohon dibantu ya kak 🙏";
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