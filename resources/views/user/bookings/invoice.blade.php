@extends('layouts.app')

@section('title', 'Detail Pesanan #' . ($booking->booking_code ?? 'RM' . sprintf('%05d', $booking->id)))

@section('content')
<style>
    :root {
        --primary: #1e3a8a;
        --primary-dark: #1e40af;
        --success: #166534;
        --success-bg: #ecfdf5;
        --success-border: #6ee7b7;
        --warning: #c2410c;
        --warning-bg: #fff7ed;
        --warning-border: #fdba74;
        --danger: #b91c1c;
        --danger-bg: #fef2f2;
        --danger-border: #fca5a5;
        --text-main: #0f172a;
        --text-secondary: #334155;
        --text-muted: #64748b;
        --border: #e2e8f0;
    }

    .invoice-page {
        max-width: 860px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* ========== STATUS BANNER ========== */
    .status-banner {
        border-radius: 14px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        flex-wrap: wrap; /* Biar rapi di HP */
    }
    .status-banner.confirmed {
        background: var(--success-bg);
        border: 1.5px solid var(--success-border);
    }
    .status-banner.pending {
        background: var(--warning-bg);
        border: 1.5px solid var(--warning-border);
    }
    .status-banner.cancelled {
        background: var(--danger-bg);
        border: 1.5px solid var(--danger-border);
    }

    .status-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .status-banner.confirmed .status-icon { color: #059669; }
    .status-banner.pending .status-icon { color: #c2410c; }
    .status-banner.cancelled .status-icon { color: #dc2626; }

    .status-content {
        flex: 1;
        min-width: 140px;
    }
    .status-content h6 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a !important;
        margin: 0 0 2px 0;
    }
    .status-content p {
        font-size: 0.825rem;
        color: #475569 !important;
        margin: 0;
        line-height: 1.35;
    }

    /* Badge Lunas / Terkonfirmasi (Perbaikan presisi di HP) */
    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        max-width: 100%;
    }
    .status-banner.confirmed .status-badge {
        background: #059669;
        color: #ffffff;
    }
    .status-banner.pending .status-badge {
        background: #ea580c;
        color: #ffffff;
    }
    .status-banner.cancelled .status-badge {
        background: #dc2626;
        color: #ffffff;
    }

    /* ========== ACTION CARD ========== */
    .action-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .action-card .card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a !important;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .action-card .card-title i {
        color: #1e40af;
        font-size: 1.15rem;
    }

    /* Countdown Box */
    .countdown-box {
        background: #fff7ed;
        border: 1.5px dashed #fb923c;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
    }
    .countdown-box .timer-label {
        font-size: 0.75rem;
        color: #9a3412;
        margin-bottom: 2px;
    }
    .countdown-box #countdown-timer {
        font-size: 1.35rem;
        font-weight: 800;
        color: #c2410c;
        letter-spacing: 1px;
    }
    .countdown-box .timer-note {
        font-size: 0.78rem;
        color: #9a3412;
        opacity: 0.9;
        max-width: 220px;
        text-align: right;
    }

    /* ========== INVOICE CARD ========== */
    /* Mengizinkan scroll horizontal di layar HP tanpa merusak layout */
    .invoice-card {
        background: #ffffff;
        border-radius: 16px;
        overflow-x: auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid var(--border);
    }

    .invoice-wrapper-inner {
        min-width: 600px; /* Memastikan lebar minimal agar tidak rapat & bisa di-scroll di HP */
    }

    .invoice-header {
        background: var(--primary);
        color: #ffffff;
        padding: 28px 32px;
    }
    .invoice-header h3 {
        font-size: 1.45rem;
        font-weight: 700;
        margin: 0 0 6px 0;
        letter-spacing: 0.3px;
    }
    .invoice-header .company-info {
        font-size: 0.8rem;
        opacity: 0.92;
        line-height: 1.55;
    }
    .invoice-header .invoice-meta {
        text-align: right;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .invoice-body {
        padding: 32px;
        color: var(--text-main);
    }

    .section-label {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .info-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .info-line {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .table-invoice {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 28px;
    }
    .table-invoice thead th {
        background: #f1f5f9;
        color: var(--text-main);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 13px 14px;
        border-bottom: 1px solid var(--border);
    }
    .table-invoice tbody td {
        padding: 14px;
        font-size: 0.9rem;
        color: var(--text-main);
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-invoice tbody tr:last-child td {
        border-bottom: none;
    }
    .table-invoice .item-name {
        font-weight: 600;
        color: var(--text-main);
    }
    .table-invoice .item-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    .table-invoice tfoot td {
        background: #f8fafc;
        padding: 16px 14px;
        font-weight: 700;
        border-top: 2px solid var(--border);
        color: var(--text-main);
    }
    .table-invoice .grand-total {
        color: var(--primary);
        font-size: 1.15rem;
        font-weight: 800;
    }

    /* Footer Invoice Tetap Bersisian (Syarat di kiri, Tanda tangan di kanan) */
    .invoice-footer {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between;
        gap: 24px;
        margin-top: 8px;
    }
    .terms-list {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.65;
        padding-left: 18px;
        margin: 0;
    }
    .signature-area {
        text-align: center;
        min-width: 180px;
        flex-shrink: 0;
    }
    .signature-area .label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 48px;
    }
    .signature-line {
        width: 160px;
        border-bottom: 1.5px solid #94a3b8;
        margin: 0 auto 8px;
    }
    .signature-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Tombol Kembali */
    .btn-back {
        background: #1e293b !important;
        color: #e2e8f0 !important;
        border: 1px solid #475569 !important;
        transition: all 0.2s ease;
    }
    .btn-back:hover {
        background: #334155 !important;
        color: #ffffff !important;
        border-color: #64748b !important;
    }

    @media print {
        nav, footer, .no-print { display: none !important; }
        body, .invoice-page {
            background: #fff !important;
            color: #000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .invoice-card {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }
        .invoice-wrapper-inner {
            min-width: 100% !important;
        }
        .invoice-header {
            background: #1e3a8a !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color: #fff !important;
        }
        .invoice-body { padding: 20px 0 !important; }
        @page { size: A4; margin: 1.4cm; }
    }
</style>

<div class="container py-4 invoice-page">

    {{-- ===================== BAGIAN ATAS (NO-PRINT) ===================== --}}
    <div class="no-print">

        {{-- Navigasi --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('bookings.index') }}"
               class="btn btn-back btn-sm rounded-3 fw-medium d-inline-flex align-items-center gap-1 px-3">
                <i class="bi bi-chevron-left"></i> Kembali
            </a>
            <button onclick="window.print()"
                    class="btn btn-primary btn-sm rounded-3 fw-medium d-inline-flex align-items-center gap-1 px-3 shadow-sm">
                <i class="bi bi-printer-fill"></i> Cetak Invoice
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Status Banner --}}
        @if ($booking->status === 'pending')
            <div class="status-banner pending">
                <div class="d-flex align-items-center gap-3">
                    <div class="status-icon">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div class="status-content">
                        <h6>Pending</h6>
                        <p>Silakan selesaikan pembayaran agar motor tidak dialihkan ke penyewa lain!</p>
                    </div>
                </div>
                <span class="status-badge">Menunggu Pembayaran</span>
            </div>

        @elseif (in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']))
            <div class="status-banner confirmed">
                <div class="d-flex align-items-center gap-2">
                    <div class="status-icon">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div class="status-content">
                        <h6>Confirmed</h6>
                        <p>Pembayaran berhasil. Pesanan sedang disiapkan.</p>
                    </div>
                </div>
                <span class="status-badge">Lunas / Terkonfirmasi</span>
            </div>

        @elseif ($booking->status === 'cancelled')
            <div class="status-banner cancelled">
                <div class="d-flex align-items-center gap-3">
                    <div class="status-icon">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                    <div class="status-content">
                        <h6>Cancelled</h6>
                        <p>Pesanan ini telah dibatalkan.</p>
                    </div>
                </div>
                <span class="status-badge">Dibatalkan</span>
            </div>
        @endif

        {{-- Action Card --}}
        <div class="action-card">
            <div class="card-title">
                <i class="bi bi-credit-card-2-front"></i>
                Aksi Pembayaran & Bantuan
            </div>

            @if ($booking->status === 'pending')
                @php
                    $createdAt = \Illuminate\Support\Carbon::parse($booking->created_at);
                    $expiresAt = $createdAt->copy()->addMinutes(5);
                    $remainingSeconds = max(0, (int) now()->diffInSeconds($expiresAt, false));
                @endphp

                <div class="countdown-box d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-clock-history fs-3" style="color: #c2410c;"></i>
                        <div>
                            <div class="timer-label">Sisa waktu pembayaran</div>
                            <div id="countdown-timer">00 : 00 : 00</div>
                        </div>
                    </div>
                    <div class="timer-note d-none d-md-block">
                        Selesaikan pembayaran sebelum waktu habis agar pesanan tetap diproses.
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <button type="button" id="pay-now-btn"
                            class="btn fw-bold py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2"
                            style="background: #f59e0b; color: #ffffff; border: none;">
                        Bayar Sekarang <i class="bi bi-arrow-right"></i>
                    </button>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20pesanan%20%23{{ $booking->booking_code ?? 'RM' . sprintf('%05d', $booking->id) }}"
                       target="_blank"
                       class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i> Bantuan Pembayaran
                    </a>
                </div>

            @elseif (in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']))
                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20bertanya%20terkait%20pesanan%20dengan%20kode%20booking%20%23{{ $booking->booking_code ?? 'RM' . sprintf('%05d', $booking->id) }}"
                   target="_blank"
                   class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-whatsapp"></i> Hubungi Admin
                </a>

            @elseif ($booking->status === 'cancelled')
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('motorcycles.index') }}"
                       class="btn btn-danger fw-bold py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-clockwise"></i> Booking Kembali
                    </a>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20pesanan%20saya%20dengan%20kode%20booking%20%23{{ $booking->booking_code ?? 'RM' . sprintf('%05d', $booking->id) }}%20telah%20dibatalkan.%20Saya%20butuh%20bantuan%20untuk%20melakukan%20pemesanan%20ulang."
                       target="_blank"
                       class="btn btn-outline-success fw-medium py-2.5 rounded-3 w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i> Hubungi Admin
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ===================== INVOICE ===================== --}}
    <div class="invoice-card">
        <div class="invoice-wrapper-inner">
            <div class="invoice-header d-flex justify-content-between align-items-start">
                <div>
                    <h3>RENTAL MOTOR</h3>
                    <div class="company-info">
                        Jl. Pemuda No. 123, Jakarta Timur<br>
                        WhatsApp: +62 812-3456-7890 · Email: info@rentalmotor.com
                    </div>
                </div>
                <div class="invoice-meta">
                    <div style="font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px;">INVOICE</div>
                    No. Referensi: <strong>#{{ $booking->booking_code ?? 'RM' . sprintf('%05d', $booking->id) }}</strong><br>
                    Tanggal: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y') }}
                </div>
            </div>

            <div class="invoice-body">
                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <div class="section-label">Penyewa</div>
                        <div class="info-name">{{ $booking->user->name ?? '-' }}</div>
                        <div class="info-line">
                            <i class="bi bi-whatsapp text-success"></i>
                            {{ $booking->user->no_hp ?? '-' }}
                        </div>
                        <div class="info-line">
                            <i class="bi bi-card-heading"></i>
                            KTP: {{ $booking->user->no_ktp ?? '-' }}
                        </div>
                        <div class="info-line">
                            <i class="bi bi-credit-card-2-front"></i>
                            SIM: {{ $booking->user->no_sim ?? '-' }}
                        </div>
                    </div>

                    <div class="col-6 text-end">
                        <div class="section-label">Detail Pengambilan</div>
                        <div class="mb-2">
                            <span class="text-secondary small">Metode:</span>
                            <span class="badge bg-dark text-white ms-1">
                                {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                            </span>
                        </div>
                        @if (($booking->pickup_method ?? '') === 'delivery')
                            <div class="text-secondary small mb-2">Alamat: {{ $booking->pickup_address ?? '-' }}</div>
                        @endif
                        <div>
                            <span class="text-secondary small">Status Bayar:</span>
                            <span class="badge {{ in_array($booking->status, ['confirmed', 'paid', 'success', 'ongoing', 'completed']) ? 'bg-success' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }} ms-1">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <table class="table-invoice">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 48px;">No</th>
                            <th>Deskripsi Kendaraan</th>
                            <th class="text-center">Durasi / Periode</th>
                            <th class="text-end">Harga / Hari</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subtotalMotor = $booking->total_price;
                            $deliveryFee  = ($booking->pickup_method === 'delivery') ? ($booking->delivery_fee ?? 0) : 0;
                            $grandTotal   = $subtotalMotor + $deliveryFee;
                            $dailyPrice   = $booking->motorcycle->price_per_day
                                            ?? ($booking->total_days > 0 ? $subtotalMotor / $booking->total_days : 0);
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold">1</td>
                            <td>
                                <div class="item-name">{{ $booking->motorcycle->name ?? 'Motor' }}</div>
                                <div class="item-meta">
                                    Plat: {{ $booking->motorcycle->plate_number ?? $booking->motorcycle->license_plate ?? '-' }}
                                    ({{ $booking->motorcycle->model ?? $booking->motorcycle->type ?? 'Matic' }})
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-semibold">{{ $booking->total_days }} Hari</div>
                                <div class="item-meta">
                                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}
                                    –
                                    {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-end">Rp {{ number_format($dailyPrice, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($subtotalMotor, 0, ',', '.') }}</td>
                        </tr>

                        @if ($deliveryFee > 0)
                        <tr>
                            <td class="text-center fw-semibold">2</td>
                            <td>
                                <div class="item-name">Biaya Pengantaran Motor</div>
                                <div class="item-meta">Layanan antar ke lokasi penyewa</div>
                            </td>
                            <td class="text-center">1×</td>
                            <td class="text-end">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Grand Total</td>
                            <td class="text-end grand-total">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div class="invoice-footer">
                    <div style="flex: 1;">
                        <div class="section-label">Syarat & Ketentuan</div>
                        <ul class="terms-list">
                            <li>Penyewa wajib membawa KTP & SIM C asli saat serah terima kendaraan.</li>
                            <li>Keterlambatan pengembalian tanpa konfirmasi dikenakan denda sesuai ketentuan.</li>
                            <li>Segala bentuk kerusakan / kehilangan helm & jas hujan menjadi tanggung jawab penyewa.</li>
                        </ul>
                    </div>
                    <div class="signature-area">
                        <div class="label">Petugas Admin / Operational,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Staf Pengelola</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($booking->status === 'pending')
@php
    $isProduction = filter_var(config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);
    $snapUrl = $isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script>
    let remainingSeconds = Math.floor({{ $remainingSeconds }});

    function updateTimer() {
        if (remainingSeconds <= 0) {
            document.getElementById('countdown-timer').innerText = "00 : 00 : 00";
            window.location.reload();
            return;
        }

        const hours   = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = Math.floor(remainingSeconds % 60);

        document.getElementById('countdown-timer').innerText =
            `${String(hours).padStart(2, '0')} : ${String(minutes).padStart(2, '0')} : ${String(seconds).padStart(2, '0')}`;

        remainingSeconds--;
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    let currentSnapToken = "{{ $booking->snap_token ?? '' }}";

    document.getElementById('pay-now-btn')?.addEventListener('click', async function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyiapkan Pembayaran...';

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
                onSuccess: function (result) {
                    window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '') + "&transaction_status=" + (result.transaction_status || 'settlement');
                },
                onPending: function (result) {
                    window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '');
                },
                onError: function () {
                    window.location.href = "{{ route('bookings.status', $booking) }}";
                },
                onClose: function () {
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