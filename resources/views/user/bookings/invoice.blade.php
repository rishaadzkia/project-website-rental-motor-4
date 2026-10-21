<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $booking->booking_code ?? '#' . $booking->id }} - {{ $booking->user->name ?? 'Pelanggan' }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .invoice-card {
            background: #fff;
            max-width: 800px;
            margin: 30px auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table-invoice th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }

        /* CSS KHUSUS TAMPILAN CETAK / PRINT */
        @media print {
            body {
                background-color: #fff !important;
            }

            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Aksi (Hanya tampil di layar monitor) -->
    <div class="container no-print mt-4 mb-2 text-center">
        @if ($booking->status === 'pending')
            <a href="{{ route('bookings.status', $booking) }}" class="btn btn-success px-4 py-2 me-2 rounded-3 shadow-sm">
                Lanjutkan Pembayaran
            </a>
        @endif
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 me-2 rounded-3 shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Invoice
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 py-2 rounded-3">
            <i class="bi bi-x-circle me-1"></i> Tutup
        </button>
    </div>

    <!-- Konten Utama Invoice -->
    <div class="invoice-card">
        <!-- Header Invoice -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-1">RENTAL MOTOR</h3>
                <p class="text-muted small mb-0">Jl. Pemuda No. 123, Jakarta Timur</p>
                <p class="text-muted small mb-0">WhatsApp: +62 812-3456-7890 | Email: info@rentalmotor.com</p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-uppercase text-dark mb-1">INVOICE</h4>
                <p class="mb-1 text-muted small">No. Referensi: <strong class="text-dark">{{ $booking->booking_code ?? '#' . sprintf('%05d', $booking->id) }}</strong></p>
                <p class="mb-0 text-muted small">Tanggal: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Informasi Transaksi & Pelanggan -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-dark text-uppercase small text-muted mb-2">Penyewa:</h6>
                <h5 class="fw-bold text-dark mb-1">{{ $booking->user->name ?? '-' }}</h5>
                <p class="mb-1 text-muted small"><i class="bi bi-whatsapp me-1"></i>{{ $booking->user->no_hp ?? '-' }}</p>
                <p class="mb-1 text-muted small"><i class="bi bi-card-heading me-1"></i>KTP: {{ $booking->user->no_ktp ?? '-' }}</p>
                <p class="mb-0 text-muted small"><i class="bi bi-credit-card-2-front me-1"></i>SIM: {{ $booking->user->no_sim ?? '-' }}</p>
            </div>

            <div class="col-6 text-end">
                <h6 class="fw-bold text-dark text-uppercase small text-muted mb-2">Detail Pengambilan:</h6>
                <p class="mb-1 fw-semibold text-dark">
                    Metode: 
                    <span class="badge bg-light text-dark border ms-1">
                        {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                    </span>
                </p>
                @if (($booking->pickup_method ?? '') === 'delivery')
                    <p class="mb-1 text-muted small">Alamat: {{ $booking->pickup_address ?? '-' }}</p>
                @endif
                <p class="mb-0 text-muted small">
                    Status Bayar: 
                    <span class="badge {{ in_array($booking->status, ['confirmed', 'paid', 'success']) ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ strtoupper($booking->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Tabel Detail Sewa -->
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
                        $subtotalMotor = $booking->total_price;
                        $deliveryFee = ($booking->pickup_method === 'delivery') ? ($booking->delivery_fee ?? 0) : 0;
                        $grandTotal = $subtotalMotor + $deliveryFee;
                        $dailyPrice = $booking->motorcycle->price_per_day ?? ($booking->total_days > 0 ? $subtotalMotor / $booking->total_days : 0);
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <strong class="d-block text-dark">{{ $booking->motorcycle->name ?? 'Motor' }}</strong>
                            <small class="text-muted">Plat: {{ $booking->motorcycle->plate_number ?? '-' }} ({{ $booking->motorcycle->type ?? 'Matic' }})</small>
                        </td>
                        <td class="text-center">
                            {{ $booking->total_days }} Hari
                            <small class="d-block text-muted" style="font-size: 0.75rem;">
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td class="text-end">Rp {{ number_format($dailyPrice, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($subtotalMotor, 0, ',', '.') }}</td>
                    </tr>
                    @if ($deliveryFee > 0)
                    <tr>
                        <td class="text-center">2</td>
                        <td>
                            <strong class="d-block text-dark">Biaya Pengantaran Motor</strong>
                            <small class="text-muted">Layanan antar ke lokasi penyewa</small>
                        </td>
                        <td class="text-center">1x</td>
                        <td class="text-end">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Grand Total</td>
                        <td class="text-end fw-bold text-primary fs-6">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Catatan & Tanda Tangan -->
        <div class="row pt-3">
            <div class="col-7">
                <h6 class="fw-bold text-dark small text-uppercase mb-2">Syarat & Ketentuan Tambahan:</h6>
                <ul class="text-muted small ps-3 mb-0" style="font-size: 0.8rem;">
                    <li>Penyewa wajib membawa KTP & SIM C asli saat serah terima kendaraan.</li>
                    <li>Keterlambatan pengembalian tanpa konfirmasi dikenakan denda sesuai ketentuan.</li>
                    <li>Segala bentuk kerusakan / kehilangan helm & jas hujan menjadi tanggung jawab penyewa.</li>
                </ul>
            </div>
            <div class="col-5 text-center mt-3 mt-md-0">
                <p class="text-muted small mb-5">Petugas Admin / Operational,</p>
                <div class="border-bottom mx-auto" style="width: 150px;"></div>
                <p class="fw-semibold text-dark small mt-1">Staf Pengelola</p>
            </div>
        </div>
    </div>

</body>
</html>