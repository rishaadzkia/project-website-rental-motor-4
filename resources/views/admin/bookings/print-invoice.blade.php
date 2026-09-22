<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->booking_code ?? 'RENT-' . $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; background: #fff; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #1a252f; }
        .table-custom th { background-color: #f8f9fa; }
        @media print {
            .no-print { display: none !important; }
            .invoice-box { border: none; padding: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print container text-center my-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    <div class="invoice-box shadow-sm rounded">
        <!-- Header -->
        <div class="row align-items-center mb-4 pb-3 border-bottom">
            <div class="col-6">
                <div class="invoice-title">INVOICE SEWA MOTOR</div>
                <div class="text-muted small">No: #{{ ltrim($booking->booking_code ?? 'RENT-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT), '#') }}</div>
            </div>
            <div class="col-6 text-end">
                <h5 class="fw-bold mb-0 text-primary">GARAGE RENTAL MOTOR</h5>
                <small class="text-muted d-block">Layanan Sewa & Delivery Motor</small>
                <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y') }}</small>
            </div>
        </div>

        <!-- Info Penyewa & Transaksi -->
        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-bold text-secondary mb-2">DITERBITKAN UNTUK:</h6>
                <strong>{{ optional($booking->user)->name ?? 'Customer' }}</strong><br>
                No. HP: {{ optional($booking->user)->no_hp ?? '-' }}<br>
                Email: {{ optional($booking->user)->email ?? '-' }}<br>
                Metode Layanan: <strong>{{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Delivery (Diantar)' : 'Ambil di Pool' }}</strong>
            </div>
            <div class="col-6 text-end">
                <h6 class="fw-bold text-secondary mb-2">STATUS PEMBAYARAN:</h6>
                <span class="badge {{ ($booking->payment_status ?? 'unpaid') === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} fs-6 mb-2">
                    {{ strtoupper($booking->payment_status ?? 'UNPAID') }}
                </span><br>
                Metode Bayar: <strong>{{ strtoupper($booking->payment_method ?? 'Payment Gateway') }}</strong>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <table class="table table-bordered table-custom mb-4 align-middle">
            <thead>
                <tr>
                    <th>Deskripsi Item / Unit</th>
                    <th class="text-center">Durasi / Qty</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ optional($booking->motorcycle)->name ?? 'Unit Kendaraan' }}</strong><br>
                        <small class="text-muted">Plat: {{ optional($booking->motorcycle)->plate_number ?? '-' }} | {{ optional($booking->motorcycle)->type ?? 'Motor' }}</small><br>
                        <small class="text-muted">Sewa: {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d M Y, H:i') }} - {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d M Y, H:i') }}</small>
                    </td>
                    <td class="text-center">{{ $booking->total_days ?? 1 }} Hari</td>
                    <td class="text-end">Rp {{ number_format(($booking->rental_fee ?? $booking->total_price) / max($booking->total_days, 1), 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($booking->rental_fee ?? $booking->total_price ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if(($booking->pickup_method ?? '') === 'delivery' && ($booking->delivery_fee ?? 0) > 0)
                    <tr>
                        <td colspan="3">Biaya Antar / Jemput (Delivery Fee)</td>
                        <td class="text-end">Rp {{ number_format($booking->delivery_fee, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(($booking->addons_fee ?? 0) > 0)
                    <tr>
                        <td colspan="3">Biaya Fasilitas Tambahan (Add-ons)</td>
                        <td class="text-end">Rp {{ number_format($booking->addons_fee, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if(($booking->penalty_fee ?? 0) > 0)
                    <tr class="text-danger">
                        <td colspan="3">Denda Keterlambatan / Overdue</td>
                        <td class="text-end">Rp {{ number_format($booking->penalty_fee, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end fw-bold fs-6">TOTAL BAYAR:</th>
                    <th class="text-end fw-bold fs-6 text-primary">Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Footer / Catatan -->
        <div class="row pt-3 border-top">
            <div class="col-8">
                <small class="text-muted d-block"><strong>Catatan:</strong></small>
                <small class="text-muted">1. Bukti pembayaran ini sah dan diterbitkan secara elektronik oleh sistem.</small><br>
                <small class="text-muted">2. Harap simpan invoice ini sebagai bukti transaksi resmi.</small>
            </div>
            <div class="col-4 text-center">
                <small class="text-muted">Admin Operasional,</small>
                <div style="height: 50px;"></div>
                <strong>( Garage Rental )</strong>
            </div>
        </div>
    </div>

</body>
</html>