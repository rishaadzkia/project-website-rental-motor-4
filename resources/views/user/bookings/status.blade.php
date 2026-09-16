@extends('layouts.app')

@section('title', 'Status Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">

            {{-- 1. TAMPILAN JIKA PEMBAYARAN BERHASIL --}}
            @if (in_array($booking->status, ['confirmed', 'paid', 'success']))
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
    
    // Konversi ke lowercase untuk mengantisipasi perbedaan kapitalisasi
    $method = strtolower($booking->pickup_method ?? 'pool');

    // Format keterangan lokasi berdasarkan pilihan user
    $lokasiInfo = ($method === 'delivery') 
        ? "Diantar ke Lokasi\n📍 *Alamat Pengantaran:* " . ($booking->pickup_address ?? 'Belum diisi')
        : "Ambil Sendiri di Pool / Garasi";

    $pesanWA = "Halo Admin, selamat pagi/siang 😊\n\n"
             . "Saya mau konfirmasi booking motor yang baru saja saya selesaikan via web. Berikut rincian pemesanan dan pengambilannya ya kak:\n\n"
             . "📌 *Detail Pemesanan:*\n"
             . "• ID Booking: #" . $booking->id . "\n"
             . "• Nama Pemesan: " . ($booking->user->name ?? '-') . "\n"
             . "• Unit Motor: " . ($booking->motorcycle->name ?? '-') . "\n"
             . "• Tanggal Sewa: " . \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d F Y') . "\n"
             . "• Pengambilan: " . $lokasiInfo . "\n\n"
             . "Mohon dikonfirmasi ya kak, terima kasih banyak! 🙏";

    $urlWA = "https://wa.me/" . $nomorAdmin . "?text=" . urlencode($pesanWA);
@endphp

<!-- Gunakan $urlWA di tag <a> tombol WhatsApp Anda -->
<a href="{{ $urlWA }}" target="_blank" class="btn btn-primary">
    Konfirmasi WhatsApp
</a>

                {{-- <div class="d-flex justify-content-center">
                    <a href="https://wa.me/{{ $nomorAdmin }}?text={{ rawurlencode($pesanWA) }}" 
                       target="_blank" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Chat Admin
                    </a>
                </div> --}}

            {{-- 2. TAMPILAN JIKA WAKTU HABIS / BATAL / GAGAL --}}
            @elseif (in_array($booking->status, ['cancelled', 'failed', 'expire']))
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Waktu Habis" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <h3 class="fw-bold text-dark mb-3">Ups, Waktunya Habis!</h3>
                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Masa pembayaran pesananmu sudah lewat nih. Kamu masih bisa gabung kok, daftar ulang sekarang atau hubungi admin kami untuk bantuan.
                </p>

                <div class="d-flex justify-content-center">
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Ulangi Pembayaran
                    </a>
                </div>

            {{-- 3. TAMPILAN JIKA MASIH MENUNGGU PEMBAYARAN (PENDING) --}}
            @else
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ asset('images/failed_avatar.png') }}" 
                         alt="Menunggu Pembayaran" 
                         class="img-fluid mx-auto d-block" 
                         style="max-height: 180px; width: auto;">
                </div>

                <div id="countdown-timer" class="display-6 fw-bold text-dark mb-2">
                    24<span class="fs-5 fw-normal">j</span> : 00<span class="fs-5 fw-normal">m</span> 00<span class="fs-5 fw-normal">d</span>
                </div>

                <h6 class="fw-bold text-danger mb-3">
                    Selesaikan Pembayaranmu Sebelum Waktunya Habis
                </h6>

                <p class="text-secondary mb-4 mx-auto" style="font-size: 0.95rem; line-height: 1.6; max-width: 480px;">
                    Tinggal selangkah lagi kamu bisa bawa motor keliling Jogja! Selesaikan transaksimu sekarang. Jika ada kendala, jangan ragu hubungi admin kami.
                </p>

                <div class="d-flex justify-content-center">
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        Bayar Sekarang
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection