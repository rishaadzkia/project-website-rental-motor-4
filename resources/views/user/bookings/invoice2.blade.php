@extends('layouts.app')

@section('title', 'Detail Pesanan #' . ($booking->booking_code ?? 'RM' . sprintf('\%05d', $booking->id)))

@section('content')
<style>
    /* Base Invoice Styling */
    .invoice-card {
        background: #ffffff;
        border-radius: 1rem;
    }

    .table-invoice th {
        background-color: #f8f9fa;
        color: #333333;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .table-invoice td {
        color: #212529;
    }

    /* Print CSS Rules */
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }

        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .container {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }

        @page {
            size: A4;
            margin: 1.2cm;
        }
    }
</style>

<div class="container py-4" style="max-width: 820px;">

    <!-- WADAH UTAMA INVOICE SERBA PUTIH -->
    <div class="invoice-card border-0 shadow-sm rounded-4 p-4 p-md-5">

        <!-- Navigasi & Tombol Cetak (Layar Monitor) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
            <a href="{{ route('bookings.index') }}" class="btn btn-light border rounded-3 text-dark fw-medium btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-chevron-left"></i> Kembali
            </a>
            <div class="d-flex align-items-center gap-2">
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-3 fw-medium d-inline-flex align-items-center gap-1">
                    <i class="bi bi-printer-fill"></i> Cetak Invoice
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 no-print" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- COP PERUSAHAAN & JUDUL INVOICE -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1">RENTAL MOTOR</h3>
                <p class="text-secondary small mb-0">Jl. Pemuda No. 123, Jakarta Timur</p>
                <p class="text-secondary small mb-0">
                    <i class="bi bi-whatsapp text-success me-1"></i>+62 812-3456-7890 
                    <span class="mx-1">|</span> 
                    <i class="bi bi-envelope text-primary me-1"></i>info@rentalmotor.com
                </p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-uppercase text-dark mb-1">INVOICE</h4>
                <p class="mb-1 text-secondary small">No. Referensi: <strong class="text-dark">#{{ $booking->booking_code ?? 'RM' . sprintf('\%05d', $booking->id) }}</strong></p>
                <p class="mb-0 text-secondary small">Tanggal: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- 1. KARTU BANNER STATUS DINAMIS (WARNA PASTEL & KONTRAS BERSIH) -->
        <div class="no-print">
            @if ($booking->status === 'pending')
                <div class="card border-0 rounded-4 p-3.5 mb-4" style="background-color: #FFF9E6; border: 1px solid #FFE599 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-warning bg-white shadow-sm" style="width: 44px; height: 44px;">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Pending</h6>
                                <p class="text-secondary small mb-0">
                                    Pesanan Anda sedang kami verifikasi. Mohon lakukan pembayaran sesuai tagihan.
                                </p>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 extra-small fw-bold">
                            Menunggu Konfirmasi
                        </span>
                    </div>
                </div>
            @elseif (in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']))
                <div class="card border-0 rounded-4 p-3.5 mb-4" style="background-color: #EBFBEE; border: 1px solid #B2F2BB !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-white shadow-sm" style="width: 44px; height: 44px;">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Confirmed</h6>
                                <p class="text-secondary small mb-0">
                                    Pembayaran Anda telah berhasil. Pesanan Anda sedang kami siapkan.
                                </p>
                            </div>
                        </div>
                        <span class="badge bg-success text-white rounded-pill px-3 py-1.5 extra-small fw-bold">
                            Lunas / Terkonfirmasi
                        </span>
                    </div>
                </div>
            @elseif ($booking->status === 'cancelled')
                <div class="card border-0 rounded-4 p-3.5 mb-4" style="background-color: #FFF5F5; border: 1px solid #FFC9C9 !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-danger bg-white shadow-sm" style="width: 44px; height: 44px;">
                                <i class="bi bi-x-circle-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Cancelled</h6>
                                <p class="text-secondary small mb-0">
                                    Pesanan Anda telah dibatalkan.
                                </p>
                            </div>
                        </div>
                        <span class="badge bg-danger text-white rounded-pill px-3 py-1.5 extra-small fw-bold">
                            Dibatalkan
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. INFORMASI PELANGGAN & DETAIL PENGAMBILAN -->
        <div class="row mb-4 g-3">
            <div class="col-6">
                <h6 class="fw-bold text-dark text-uppercase small mb-2" style="letter-spacing: 0.5px;">
                    <i class="bi bi-person-fill text-primary me-1 no-print"></i> Data Penyewa
                </h6>
                <h5 class="fw-bold text-dark mb-1">{{ $booking->user->name ?? '-' }}</h5>
                <p class="mb-1 text-secondary small"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $booking->user->no_hp ?? '-' }}</p>
                <p class="mb-1 text-secondary small"><i class="bi bi-card-heading me-1 text-muted"></i>KTP: {{ $booking->user->no_ktp ?? '-' }}</p>
                <p class="mb-0 text-secondary small"><i class="bi bi-credit-card-2-front me-1 text-muted"></i>SIM: {{ $booking->user->no_sim ?? '-' }}</p>
            </div>

            <div class="col-6 text-end">
                <h6 class="fw-bold text-dark text-uppercase small mb-2" style="letter-spacing: 0.5px;">
                    Detail Pengambilan
                </h6>
                <p class="mb-1 fw-medium text-dark small">
                    Metode: 
                    <span class="badge bg-light text-dark border ms-1 fw-semibold">
                        {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool Utama' }}
                    </span>
                </p>
                @if (($booking->pickup_method ?? '') === 'delivery')
                    <p class="mb-1 text-secondary small">Alamat: {{ $booking->pickup_address ?? '-' }}</p>
                @endif
                <p class="mb-0 text-secondary small">
                    Status Bayar: 
                    <span class="badge {{ in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']) ? 'bg-success-subtle text-success border border-success' : ($booking->status === 'cancelled' ? 'bg-danger-subtle text-danger border border-danger' : 'bg-warning-subtle text-warning-emphasis border border-warning') }} ms-1 fw-semibold">
                        {{ strtoupper($booking->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- 3. TABEL ITEM SEWA & RINCIAN HARGA -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-invoice align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Deskripsi Kendaraan</th>
                        <th class="text-center">Durasi / Periode</th>
                        <th class="text-end">Harga / Hari</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotalMotor =$booking->total_price;
                        $deliveryFee = ($booking->pickup_method === 'delivery') ? ($booking->delivery_fee ?? 0) : 0;
                        $grandTotal = $subtotalMotor +$deliveryFee;
                        $dailyPrice = $booking->motorcycle->price_per_day ?? ($booking->total_days > 0 ? $subtotalMotor / $booking->total_days : 0);
                    @endphp
                    <tr>
                        <td class="text-center fw-semibold">1</td>
                        <td>
                            <strong class="d-block text-dark">{{ $booking->motorcycle->name ?? 'Motor' }}</strong>
                            <small class="text-muted">
                                Plat: {{ $booking->motorcycle->plate_number ?? $booking->motorcycle->license_plate ?? '-' }} 
                                ({{ $booking->motorcycle->model ?? $booking->motorcycle->type ?? 'Matic' }})
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold text-dark">{{ $booking->total_days }} Hari</span>
                            <small class="d-block text-muted" style="font-size: 0.75rem;">
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td class="text-end">Rp {{ number_format($dailyPrice, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($subtotalMotor, 0, ',', '.') }}</td>
                    </tr>
                    @if ($deliveryFee > 0)
                    <tr>
                        <td class="text-center fw-semibold">2</td>
                        <td>
                            <strong class="d-block text-dark">Biaya Pengantaran Motor</strong>
                            <small class="text-muted">Layanan pengantaran lokasi penyewa</small>
                        </td>
                        <td class="text-center">1x</td>
                        <td class="text-end">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold text-dark">Grand Total</td>
                        <td class="text-end fw-bold text-primary fs-5">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 4. AKSI PEMBAYARAN & HUBUNGI ADMIN (Layar Monitor Only) -->
        <div class="mb-4 no-print">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-credit-card-2-front text-primary"></i> Metode Pembayaran
            </h6>

            <div class="d-flex justify-content-between align-items-center mb-3 text-secondary small">
                <div>Channel Pembayaran:</div>
                <div>
                    @if ($booking->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-1.5 rounded-pill fw-semibold">
                            Midtrans / Automatic Payment
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill fw-semibold">
                            {{ $booking->payment_method ?? 'Transfer Bank' }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- AKSES TOMBOL STATUS PENDING -->
            @if ($booking->status === 'pending')
                @php
                    $createdAt = \Illuminate\Support\Carbon::parse($booking->created_at);
                    $expiresAt =$createdAt->copy()->addMinutes(5);
                    $remainingSeconds = max(0, now()->diffInSeconds($expiresAt, false));
                @endphp

                <!-- Box Countdown Timer -->
                <div class="p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center" style="background-color: #FFF9E6; border: 1px dashed #FFE599;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-clock-history fs-3 text-warning"></i>
                        <div>
                            <span class="extra-small text-muted d-block" style="font-size: 0.75rem;">Sisa waktu pembayaran</span>
                            <div class="fw-bold text-dark fs-5" id="countdown-timer">00 : 00 : 00</div>
                        </div>
                    </div>
                    <small class="text-muted text-end extra-small d-none d-md-block" style="max-width: 200px; font-size: 0.75rem;">
                        Selesaikan pembayaran sebelum waktu habis agar pesanan Anda tetap diproses.
                    </small>
                </div>

                <div class="d-flex flex-column gap-2">
                    <button type="button" id="pay-now-btn" class="btn btn-warning text-white fw-bold py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        Bayar Sekarang <i class="bi bi-arrow-right"></i>
                    </button>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20pesanan%20%23{{ $booking->booking_code ?? 'RM' . sprintf('\%05d', $booking->id) }}"
                       target="_blank"
                       class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i> Bantuan Pembayaran
                    </a>
                </div>

            <!-- AKSES TOMBOL STATUS CONFIRMED -->
            @elseif (in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']))
                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20bertanya%20terkait%20pesanan%20dengan%20kode%20booking%20%23{{ $booking->booking_code ?? 'RM' . sprintf('\%05d', $booking->id) }}"
                   target="_blank"
                   class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-whatsapp"></i> Hubungi Admin
                </a>

            <!-- AKSES TOMBOL STATUS CANCELLED -->
            @elseif ($booking->status === 'cancelled')
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('motorcycles.index') }}" class="btn btn-danger fw-bold py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-clockwise"></i> Booking Kembali
                    </a>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20pesanan%20saya%20dengan%20kode%20booking%20%23{{ $booking->booking_code ?? 'RM' . sprintf('\%05d', $booking->id) }}%20telah%20dibatalkan.%20Saya%20butuh%20bantuan%20untuk%20melakukan%20pemesanan%20ulang."
                       target="_blank"
                       class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i> Hubungi Admin
                    </a>
                </div>
            @endif
        </div>

        <!-- 5. CATATAN SYARAT & KETENTUAN DAN TANDA TANGAN -->
        <div class="row pt-4 border-top">
            <div class="col-7">
                <h6 class="fw-bold text-dark small text-uppercase mb-2">Syarat & Ketentuan:</h6>
                <ul class="text-secondary small ps-3 mb-0" style="font-size: 0.78rem; line-height: 1.5;">
                    <li>Wajib membawa KTP & SIM C asli saat serah terima unit.</li>
                    <li>Keterlambatan pengembalian tanpa konfirmasi dikenakan denda sesuai aturan.</li>
                    <li>Kerusakan atau kehilangan helm/jas hujan sepenuhnya tanggung jawab penyewa.</li>
                </ul>
            </div>
            <div class="col-5 text-center mt-3 mt-md-0">
                <p class="text-secondary small mb-5">Petugas Operational,</p>
                <div class="border-bottom mx-auto" style="width: 140px;"></div>
                <p class="fw-semibold text-dark small mt-1 mb-0">Staf Pengelola</p>
            </div>
        </div>

    </div>

</div>

@if ($booking->status === 'pending')
<!-- INTEGRASI MIDTRANS SNAP SCRIPT -->
@php
    $isProduction = filter_var(config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);
    $snapUrl =$isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script>
    // 1. Logic Hitung Mundur
    let remainingSeconds = {{ $remainingSeconds }};

    function updateTimer() {
        if (remainingSeconds <= 0) {
            document.getElementById('countdown-timer').innerText = "00 : 00 : 00";
            window.location.reload();
            return;
        }

        let hours = Math.floor(remainingSeconds / 3600);
        let minutes = Math.floor((remainingSeconds % 3600) / 60);
        let seconds = remainingSeconds % 60;

        let hStr = String(hours).padStart(2, '0');
        let mStr = String(minutes).padStart(2, '0');
        let sStr = String(seconds).padStart(2, '0');

        document.getElementById('countdown-timer').innerText = `${hStr} : ${mStr} : ${sStr}`;
        remainingSeconds--;
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    // 2. Logic Trigger Pop-Up Midtrans
    let currentSnapToken = "{{ $booking->snap_token ?? '' }}";

    document.getElementById('pay-now-btn')?.addEventListener('click', async function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Menyiapkan Pembayaran...';

        try {
            if (!currentSnapToken) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PATCH');
                formData.append('pickup_method', '{{ $booking->pickup_method ?? "pool" }}');
                formData.append('pickup_address', '{{ $booking->pickup_address ?? "" }}');
                formData.append('delivery_fee', '{{ $booking->delivery_fee ?? 0 }}');

                const response = await fetch("{{ route('bookings.update-pickup', $booking) }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.success && result.snap_token) {
                    currentSnapToken = result.snap_token;
                } else {
                    throw new Error(result.message || 'Gagal mengambil token pembayaran.');
                }
            }

            snap.pay(currentSnapToken, {
                onSuccess: function(result) {
                    window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '') + "&transaction_status=" + (result.transaction_status || 'settlement');
                },
                onPending: function(result) {
                    window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '');
                },
                onError: function(result) {
                    window.location.href = "{{ route('bookings.status', $booking) }}";
                },
                onClose: function() {
                    btn.disabled = false;
                    btn.innerHTML = 'Bayar Sekarang <i class="bi bi-arrow-right"></i>';
                }
            });

        } catch (error) {
            alert(error.message || 'Terjadi kesalahan sistem.');
            btn.disabled = false;
            btn.innerHTML = 'Bayar Sekarang <i class="bi bi-arrow-right"></i>';
        }
    });
</script>
@endif
@endsection