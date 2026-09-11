@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold text-dark mb-4">Detail Pemesanan</h3>

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Pemesan -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-4">Pemesan</h5>

                <div class="d-flex flex-column gap-3 fs-6">
                    <div class="row">
                        <div class="col-4 text-muted">Nama</div>
                        <div class="col-8 fw-semibold text-dark">: {{ $booking->user->name ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-muted">No. Whatsapp</div>
                        <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_hp ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-muted">No. KTP</div>
                        <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_ktp ?? '-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-muted">No. SIM</div>
                        <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_sim ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <small class="text-muted" style="font-size: 0.8rem;">
                        Kamu bisa mengubah data di atas lewat profilmu di menu Akun.
                    </small>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Card Durasi & Ringkasan Transaksi -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">

                <!-- Card 1: Durasi & Kendaraan -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">
                            Durasi sewa : <strong>{{ $booking->total_days }} hari</strong>
                        </span>
                        @if ($booking->status === 'pending')
                            <a href="#" class="text-primary text-decoration-none small fw-semibold">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        @endif
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Tanggal sewa</span>
                            <strong class="d-block text-dark small">
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('D, d M Y') }}
                            </strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Selesai sewa</span>
                            <strong class="d-block text-dark small">
                                {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('D, d M Y') }}
                            </strong>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-2">

                    <!-- Info Kendaraan -->
                    <div class="d-flex align-items-center gap-3 pt-2">
                        <img src="{{ $booking->motorcycle && $booking->motorcycle->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                             alt="{{ $booking->motorcycle->name ?? 'Motor' }}" 
                             class="img-fluid rounded-3 object-fit-cover" 
                             style="width: 80px; height: 60px;">
                        
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $booking->motorcycle->name ?? 'Nama Motor' }}</h6>
                            <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">
                                {{ $booking->motorcycle->type ?? 'Matic' }}
                            </span>
                            <span class="badge bg-light text-muted border border-secondary border-opacity-25 mt-1" style="font-size: 0.7rem;">
                                {{ $booking->motorcycle->plate_number ?? 'B 1234 CD' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Ringkasan Transaksi -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-dark mb-3">Ringkasan transaksi</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Harga</span>
                        <span class="fw-medium text-dark small">
                            Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Diskon</span>
                        <span class="text-muted small">-</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-semibold text-dark small">Total harga</span>
                        <strong class="text-dark fs-6">
                            Rp{{ number_format($booking->total_price, 0, ',', '.') }}
                        </strong>
                    </div>

                    <!-- Tombol Aksi / Bayar -->
                    <div class="mt-4">
                        @if ($booking->status === 'pending')
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="mb-2" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-semibold btn-sm">
                                    Batalkan Pesanan
                                </button>
                            </form>

                            <button type="button" id="pay-button" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                Bayar Sekarang
                            </button>
                        @elseif (in_array($booking->status, ['confirmed', 'paid', 'success']))
                            <a href="{{ route('bookings.status', $booking) }}" class="btn btn-success w-100 rounded-3 py-2 fw-semibold text-decoration-none d-block text-center">
                                <i class="bi bi-check-circle-fill me-1"></i> Lihat Status Pembayaran
                            </a>
                        @elseif ($booking->status === 'cancelled')
                            <div class="alert alert-danger text-center fw-bold rounded-3 mb-0 py-2">
                                Pesanan Dibatalkan
                            </div>
                        @else
                            <button class="btn bg-light text-muted w-100 rounded-3 py-2 fw-semibold" disabled>
                                Status: {{ ucfirst($booking->status) }}
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- MIDTRANS SCRIPT INTEGRATION -->
@php
    $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
    $snapUrl = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN)
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        const payButton = document.getElementById('pay-button');
        const snapToken = @json($snapToken ?? null);

        if (payButton) {
            payButton.addEventListener('click', function (e) {
                e.preventDefault();
                
                if (!snapToken || snapToken === "") {
                    alert("Gagal Membuka Pembayaran!\n\nSnap Token tidak ditemukan di database.");
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    alert("Gagal Memuat Midtrans SDK!\n\nScript snap.js gagal diunduh. Cek koneksi internet.");
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=settlement";
                    },
                    onPending: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=pending";
                    },
                    onError: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=deny";
                    },
                    onClose: function () {
                        // Jika pop-up ditutup tanpa menyelesaikan pembayaran, alihkan ke status untuk cek update
                        window.location.href = "{{ route('bookings.status', $booking) }}";
                    }
                });
            });
        }
    });
</script>
@endsection