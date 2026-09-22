<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Lapangan #{{ $booking->booking_code ?? 'RENT-' . $booking->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #222; }
        .surat-box { max-width: 800px; margin: auto; padding: 25px; border: 1px solid #ccc; background: #fff; }
        .checklist-box { border: 1px solid #000; padding: 8px; font-weight: bold; }
        @media print {
            .no-print { display: none !important; }
            .surat-box { border: none; padding: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print container text-center my-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">🖨️ Cetak Surat Jalan</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    <div class="surat-box rounded">
        <!-- Header Dokumen -->
        <div class="row align-items-center mb-3 pb-2 border-bottom border-2 border-dark">
            <div class="col-7">
                <h4 class="fw-bold mb-0 text-uppercase">SURAT JALAN & SERAH TERIMA</h4>
                <small class="text-muted">Kode Booking: <strong>#{{ ltrim($booking->booking_code ?? 'RENT-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT), '#') }}</strong></small>
            </div>
            <div class="col-5 text-end">
                <h6 class="fw-bold mb-0">GARAGE RENTAL MOTOR</h6>
                <small>Layanan Serah Terima Lapangan</small>
            </div>
        </div>

        <!-- Detail Layanan & Driver -->
        <div class="row mb-3">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="35%" class="text-muted">Staf Bertugas</td><td>: <strong>{{ $booking->driver_name ?? 'Staf Kurir' }}</strong> {{ $booking->driver_phone ? '('.$booking->driver_phone.')' : '' }}</td></tr>
                    <tr><td class="text-muted">Metode Layanan</td><td>: <strong>{{ ($booking->pickup_method ?? 'pool') === 'delivery' ? '🛵 Delivery' : '🏢 Ambil di Pool' }}</strong></td></tr>
                    <tr><td class="text-muted">Alamat Antar</td><td>: {{ $booking->pickup_address ?? 'Pool Garasi Utama' }}</td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="40%" class="text-muted">Nama Customer</td><td>: <strong>{{ optional($booking->user)->name ?? '-' }}</strong></td></tr>
                    <tr><td class="text-muted">No. KTP / SIM</td><td>: {{ $booking->nik ?? optional($booking->user)->nik ?? '-' }} / {{ $booking->sim_number ?? optional($booking->user)->sim_number ?? '-' }}</td></tr>
                    <tr><td class="text-muted">No. WhatsApp</td><td>: {{ optional($booking->user)->no_hp ?? '-' }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Detail Unit Motor & Jadwal -->
        <div class="card mb-3 border-dark">
            <div class="card-header bg-dark text-white fw-bold py-1">
                🛵 DATA KENDARAAN & WAKTU SEWA
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-6">
                        <strong>Nama Motor:</strong> {{ optional($booking->motorcycle)->name ?? '-' }}<br>
                        <strong>Plat Nomor:</strong> {{ optional($booking->motorcycle)->plate_number ?? '-' }}<br>
                        <strong>Warna / Tipe:</strong> {{ optional($booking->motorcycle)->color ?? '-' }} / {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                    </div>
                    <div class="col-6">
                        <strong>Rencana Pickup:</strong> {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('d M Y, H:i') }} WIB<br>
                        <strong>Rencana Return:</strong> {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('d M Y, H:i') }} WIB<br>
                        <strong>Jaminan Fisik Ditahan:</strong> {{ $booking->physical_deposit ?? 'E-KTP Asli / Paspor' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Checklist Fisik Lapangan (Isian Tangan / Verifikasi Staf) -->
        <div class="card mb-3 border-dark">
            <div class="card-header bg-light fw-bold py-1 border-bottom border-dark">
                📋 CHECKLIST KONDISI FISIK FLEET (DIISI OLEH STAF KHUSUS LAPANGAN)
            </div>
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-4">
                        <div class="checklist-box">Bensin Awal: [ &nbsp; &nbsp; &nbsp; &nbsp; ] Bar</div>
                    </div>
                    <div class="col-4">
                        <div class="checklist-box">KM Awal: [ &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ] KM</div>
                    </div>
                    <div class="col-4">
                        <div class="checklist-box">Helm (2 Pcs): [ &nbsp; ] Ya [ &nbsp; ] Tidak</div>
                    </div>
                    <div class="col-4">
                        <div class="checklist-box">Jas Hujan: [ &nbsp; ] Ya [ &nbsp; ] Tidak</div>
                    </div>
                    <div class="col-4">
                        <div class="checklist-box">STNK Asli: [ &nbsp; ] Ada [ &nbsp; ] Tidak</div>
                    </div>
                    <div class="col-4">
                        <div class="checklist-box">Kondisi Body: [ &nbsp; ] Mulus [ &nbsp; ] Lecet</div>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted fw-bold">Catatan Fisik Bawaan / Fisik Motor:</small>
                    <div class="border-bottom border-dotted py-2">Catatan: {{ $booking->initial_condition ?? '........................................................................................................................' }}</div>
                </div>
            </div>
        </div>

        <!-- Lembar Tanda Tangan Serah Terima -->
        <div class="row text-center mt-4 pt-2">
            <div class="col-4">
                <small>Diserahkan Oleh (Staf/Driver),</small>
                <div style="height: 60px;"></div>
                <strong>( {{ $booking->driver_name ?? '....................................' }} )</strong>
            </div>
            <div class="col-4">
                <small>Verifikasi Jaminan Fisik,</small>
                <div style="height: 60px;"></div>
                <strong>( [✓] Jaminan Diterima )</strong>
            </div>
            <div class="col-4">
                <small>Penyewa / Customer,</small>
                <div style="height: 60px;"></div>
                <strong>( {{ optional($booking->user)->name ?? '....................................' }} )</strong>
            </div>
        </div>
    </div>

</body>
</html>