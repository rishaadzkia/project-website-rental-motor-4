@extends('layouts.admin')

@section('title', 'Detail Booking')

@section('content')
<div class="container py-4">
    <!-- Header Page & Akses Cepat Cetak -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                ←
            </a>
            <div>
                <h3 class="fw-bold text-dark mb-0">
                    #{{ ltrim($booking->booking_code ?? 'RENT-' . date('Ym') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT), '#') }}
                </h3>
                <small class="text-muted">Dibuat pada: {{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d M Y, H:i') : '-' }} WIB</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Indikator Overdue -->
            @php
                $endDateOnly = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') : null;
                $returnTimeVal = $booking->return_time ?? $booking->end_time ?? '00:00:00';
                $endDateParsed = $endDateOnly ? \Carbon\Carbon::parse($endDateOnly . ' ' . $returnTimeVal) : null;
                
                $isOverdue = $booking->status === 'ongoing' && $endDateParsed && \Carbon\Carbon::now()->gt($endDateParsed);
            @endphp
            @if($isOverdue)
                <span class="badge bg-danger text-white px-3 py-2 fs-6 rounded-pill">
                    ⚠️ Terlambat Pengembalian
                </span>
            @endif

            <!-- Badge Status Utama -->
            @php
                $statusClasses = [
                    'pending'   => 'bg-warning bg-opacity-10 text-warning border border-warning',
                    'confirmed' => 'bg-info bg-opacity-10 text-info border border-info',
                    'ongoing'   => 'bg-primary bg-opacity-10 text-primary border border-primary',
                    'completed' => 'bg-success bg-opacity-10 text-success border border-success',
                    'cancelled' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary',
                ];

                $statusLabels = [
                    'pending'   => 'Menunggu Bayar',
                    'confirmed' => 'Siap Ambil / Diantar',
                    'ongoing'   => 'Sedang Disewa',
                    'completed' => 'Selesai',
                    'cancelled' => 'Batal',
                ];
            @endphp
            <span class="badge rounded-pill px-3 py-2 fs-6 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}">
                {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
            </span>

            <!-- Cetak Dokumen -->
            <div class="dropdown">
                <button class="btn btn-outline-dark btn-sm rounded-3 dropdown-toggle px-3 py-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                    🖨️ Cetak Dokumen
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li><a class="dropdown-item small" href="{{ route('admin.bookings.print-invoice', $booking->id) }}" target="_blank">Cetak Nota / Invoice</a></li>
                    <li><a class="dropdown-item small" href="{{ route('admin.bookings.print-surat-jalan', $booking->id) }}" target="_blank">Cetak Surat Jalan Lapangan</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Operasional -->
        <div class="col-lg-8">
            
            <!-- 1. Presisi Waktu & Serah Terima Lapangan -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">⏱️ Presisi Waktu & Serah Terima Lapangan</h5>
                
                @php
                    $startDateOnly = $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') : null;
                    $endDateOnly = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') : null;

                    $pickupTimeVal = $booking->pickup_time ?? $booking->start_time ?? '00:00:00';
                    $returnTimeVal = $booking->return_time ?? $booking->end_time ?? '00:00:00';

                    $startString = $startDateOnly ? ($startDateOnly . ' ' . $pickupTimeVal) : null;
                    $endString = $endDateOnly ? ($endDateOnly . ' ' . $returnTimeVal) : null;
                @endphp

                <div class="row g-3 small mb-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block mb-1">Jadwal Pickup (Rencana)</span>
                            <strong class="text-dark fs-6 d-block">
                                @if($startString)
                                    {{ \Carbon\Carbon::parse($startString)->translatedFormat('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted d-block mb-1">Jadwal Pengembalian (Rencana)</span>
                            <strong class="text-dark fs-6 d-block">
                                @if($endString)
                                    {{ \Carbon\Carbon::parse($endString)->translatedFormat('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </strong>
                            <small class="text-primary fw-semibold">Durasi: {{ $booking->total_days ?? 1 }} Hari ({{ ($booking->total_days ?? 1) * 24 }} Jam)</small>
                        </div>
                    </div>
                </div>

                <!-- Record Realisasi -->
                <div class="row g-3 small border-top pt-3 align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted d-block mb-1">Actual Pickup (Realisasi Serah Terima):</span>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-dark bg-light px-2 py-1 rounded">
                                {{ $booking->actual_pickup ? \Carbon\Carbon::parse($booking->actual_pickup)->translatedFormat('d M Y, H:i') . ' WIB' : 'Belum diserahkan' }}
                            </strong>
                            @if(!$booking->actual_pickup)
                                <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action_type" value="record_pickup">
                                    <button type="submit" class="btn btn-sm btn-primary py-1 px-2 fw-semibold" style="font-size: 0.75rem;">
                                        📲 Catat Pickup Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted d-block mb-1">Actual Return (Realisasi Pengembalian):</span>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-dark bg-light px-2 py-1 rounded">
                                {{ $booking->actual_return ? \Carbon\Carbon::parse($booking->actual_return)->translatedFormat('d M Y, H:i') . ' WIB' : 'Belum dikembalikan' }}
                            </strong>
                            @if(!$booking->actual_return && $booking->actual_pickup)
                                <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action_type" value="record_return">
                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2 fw-semibold" style="font-size: 0.75rem;">
                                        🔄 Catat Return Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Identitas & Verifikasi Berkas Customer -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0">👤 Identitas & Verifikasi Fisik Lapangan</h5>
                        <span class="badge {{ ($booking->is_verified ?? false) ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                            {{ ($booking->is_verified ?? false) ? '✓ Terverifikasi Fisik' : '⚠️ Belum Diverifikasi' }}
                        </span>
                    </div>

                    @if(optional($booking->user)->no_hp)
                        @php
                            $phone = preg_replace('/[^0-9]/', '', $booking->user->no_hp);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $phone }}?text=Halo%20{{ urlencode($booking->user->name) }},%20mengenai%20pesanan%20sewa%20motor%20{{ urlencode($booking->booking_code ?? '') }}" 
                           target="_blank" 
                           class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                            💬 Chat WhatsApp
                        </a>
                    @endif
                </div>

                <div class="row g-3 small mb-3">
                    <div class="col-md-3">
                        <span class="text-muted d-block">Nama Customer</span>
                        <strong class="text-dark fs-6">{{ optional($booking->user)->name ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block">No. KTP</span>
                        <strong class="text-dark fs-6">{{ $booking->no_ktp ?? optional($booking->user)->no_ktp ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block">No. SIM C</span>
                        <strong class="text-dark fs-6">{{ $booking->no_sim ?? optional($booking->user)->no_sim ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block">No. WhatsApp</span>
                        <strong class="text-dark fs-6">{{ optional($booking->user)->no_hp ?? '-' }}</strong>
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-3">

                <!-- Action Verifikasi Fisik & Jaminan -->
                <div class="row g-3 small align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted d-block mb-1">Jaminan Fisik Ditahan di Lapangan:</span>
                        <strong class="text-dark bg-warning bg-opacity-10 px-2 py-1 rounded border border-warning d-inline-block">
                            📌 {{ $booking->physical_deposit ?? 'E-KTP Asli / Paspor / Motor Pribadi' }}
                        </strong>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action_type" value="toggle_verification">
                            @if($booking->is_verified ?? false)
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                    Batalkan Status Verifikasi Fisik
                                </button>
                            @else
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
                                    ✓ Tandai Identitas Terverifikasi Fisik
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- 3. Data Kendaraan & Driver Lapangan -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">🛵 Data Kendaraan & Penugasan Staf</h5>
                
                <div class="d-flex align-items-center gap-4 mb-3">
                    <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                         alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}" 
                         class="img-fluid rounded-3 object-fit-contain bg-light p-2" 
                         style="max-height: 90px; width: 110px;">
                    <div class="small">
                        <span class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.75rem;">
                            {{ optional($booking->motorcycle)->brand ?? '' }} {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                        </span>
                        <h5 class="fw-bold text-dark mb-1">{{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}</h5>
                        <span class="badge bg-dark text-white fw-semibold me-1">
                            Plat Nomor: {{ optional($booking->motorcycle)->plate_number ?? 'Belum Diatur' }}
                        </span>
                        <span class="badge bg-secondary fw-semibold">
                            Warna: {{ optional($booking->motorcycle)->color ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Form Penugasan Driver -->
                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action_type" value="update_driver">
                        
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="text-muted fw-bold mb-1">Nama Driver / Staf Bertugas:</label>
                                <input type="text" name="driver_name" class="form-control form-control-sm" value="{{ $booking->driver_name ?? '' }}" placeholder="Misal: Budi (Kurir)">
                            </div>
                            <div class="col-md-5">
                                <label class="text-muted fw-bold mb-1">No. HP Driver:</label>
                                <input type="text" name="driver_phone" class="form-control form-control-sm" value="{{ $booking->driver_phone ?? '' }}" placeholder="08123456789">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-sm w-100 fw-semibold">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="row g-3 small border-top pt-3">
                    <div class="col-md-6">
                        <span class="text-muted d-block">Metode Layanan:</span>
                        <strong class="text-dark fs-6">
                            {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? '🛵 Delivery (Diantar-Jemput)' : '🏢 Pool Pickup (Ambil Sendiri)' }}
                        </strong>
                    </div>

                    @if(($booking->pickup_method ?? '') === 'delivery')
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted d-block mb-1">Alamat Antar / Jemput:</span>
                                <strong class="text-dark">{{ $booking->pickup_address ?? '-' }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 4. Catatan Internal Admin -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-2">📝 Catatan Internal Admin (Privat)</h5>
                <p class="text-muted small mb-3">Catatan ini khusus untuk koordinasi internal staf/admin dan tidak akan terlihat oleh customer.</p>

                <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action_type" value="update_notes">
                    <textarea name="admin_notes" rows="3" class="form-control border-0 bg-light rounded-3 p-3 small mb-2" placeholder="Tulis catatan internal (misal: Customer minta helm XL, Jaminan KTP disimpan di Laci A)...">{{ $booking->admin_notes ?? '' }}</textarea>
                    <button type="submit" class="btn btn-secondary btn-sm rounded-3 py-1 px-3 fw-semibold float-end">
                        Simpan Catatan
                    </button>
                </form>
            </div>

        </div>

        <!-- Kolom Kanan: Keuangan & Eksekusi Status -->
        <div class="col-lg-4">
            
            <!-- Rincian Keuangan Lengkap -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3">💳 Rincian Keuangan</h5>
                <div class="d-flex flex-column gap-2 small">
                    
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Sewa Motor ({{ $booking->total_days ?? 1 }} Hari)</span>
                        <strong class="text-dark">Rp {{ number_format($booking->rental_fee ?? ($booking->total_price ?? 0), 0, ',', '.') }}</strong>
                    </div>

                    @if(($booking->pickup_method ?? '') === 'delivery')
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ongkir / Delivery Fee</span>
                            <strong class="text-dark">Rp {{ number_format($booking->delivery_fee ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    @if(($booking->addons_fee ?? 0) > 0)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Biaya Extra Add-ons</span>
                            <strong class="text-dark">Rp {{ number_format($booking->addons_fee, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    @if(($booking->penalty_fee ?? 0) > 0 || $isOverdue)
                        <div class="d-flex justify-content-between text-danger">
                            <span>Denda Keterlambatan</span>
                            <strong>Rp {{ number_format($booking->penalty_fee ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    <hr class="text-muted opacity-25 my-2">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark fs-6">Total Nominal</span>
                        <strong class="text-primary fs-5">
                            Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-1">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Metode Bayar:</span>
                            <strong class="text-dark text-uppercase">
                                {{ strtoupper($booking->payment_method ?? 'Payment Gateway') }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status Bayar:</span>
                            <span class="badge {{ ($booking->paid_at ?? null) ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ($booking->paid_at ?? null) ? 'PAID' : 'UNPAID' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status Utama -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-3">⚡ Eksekusi Status</h5>
                
                <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action_type" value="update_status">

                    <div class="mb-3">
                        <label for="status" class="form-label text-muted small fw-semibold">Ubah Status Pesanan</label>
                        <select name="status" id="status" class="form-select border-0 bg-light rounded-3 py-2 fw-semibold text-secondary small">
                            @foreach ([
                                'pending'   => 'Menunggu Bayar',
                                'confirmed' => 'Siap Ambil / Diantar',
                                'ongoing'   => 'Disewa (Aktif)',
                                'completed' => 'Selesai (Kembali)',
                                'cancelled' => 'Batal'
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected(($booking->status ?? '') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        Update Status Pesanan
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection