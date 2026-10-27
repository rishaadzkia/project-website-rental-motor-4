@extends('layouts.admin')

@section('title', 'Detail Booking')

@section('content')
<div class="container-fluid booking-detail-page py-3 py-md-4">

    {{-- ==================== HEADER ==================== --}}
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.bookings.index') }}"
               class="btn btn-light border d-flex align-items-center justify-content-center rounded-circle shadow-sm flex-shrink-0"
               style="width: 40px; height: 40px;"
               title="Kembali">
                <i class="fas fa-arrow-left text-secondary"></i>
            </a>
            <div>
                <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4">
                    #{{ ltrim($booking->booking_code ?? 'RENT-' . date('Ym') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT), '#') }}
                </h4>
                <small class="text-muted">
                    Dibuat {{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d M Y, H.i') : '-' }} WIB
                </small>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto">
            @php
                $statusClasses = [
                    'pending'   => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning',
                    'confirmed' => 'bg-info bg-opacity-10 text-info border border-info',
                    'ongoing'   => 'bg-primary bg-opacity-10 text-primary border border-primary',
                    'completed' => 'bg-success bg-opacity-10 text-success border border-success',
                    'cancelled' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary',
                ];

                $statusLabels = [
                    'pending'   => 'Menunggu pembayaran',
                    'confirmed' => 'Siap ambil / diantar',
                    'ongoing'   => 'Sedang disewa',
                    'completed' => 'Selesai',
                    'cancelled' => 'Batal',
                ];
            @endphp

            <span class="badge rounded-pill px-3 py-2 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark border' }}">
                {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
            </span>

            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-print me-1"></i> Cetak dokumen
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                    <li>
                        <a class="dropdown-item small py-2" href="{{ route('admin.bookings.print-invoice', $booking->id) }}" target="_blank">
                            <i class="fas fa-file-invoice me-2 text-muted"></i> Cetak Nota / Invoice
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item small py-2" href="{{ route('admin.bookings.print-surat-jalan', $booking->id) }}" target="_blank">
                            <i class="fas fa-file-alt me-2 text-muted"></i> Cetak Surat Jalan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 g-lg-4">
        {{-- ==================== KOLOM KIRI ==================== --}}
        <div class="col-lg-8">

            {{-- 1. Presisi Waktu & Serah Terima --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-clock text-primary me-2"></i>Presisi waktu & serah terima
                        <small class="text-muted fw-normal d-none d-sm-inline ms-1">Jadwal rencana versus realisasi di lapangan</small>
                    </h6>

                    @php
                        $startDateOnly = $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') : null;
                        $endDateOnly   = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') : null;
                        $pickupTimeVal = $booking->pickup_time ?? $booking->start_time ?? '00:00:00';
                        $returnTimeVal = $booking->return_time ?? $booking->end_time ?? '00:00:00';
                        $startString   = $startDateOnly ? ($startDateOnly . ' ' . $pickupTimeVal) : null;
                        $endString     = $endDateOnly ? ($endDateOnly . ' ' . $returnTimeVal) : null;
                        $isDelivery    = ($booking->pickup_method ?? 'pool') === 'delivery';
                    @endphp

                    <div class="row g-3 mb-3 mb-md-4">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="text-muted small mb-1">{{ $isDelivery ? 'Delivery rencana' : 'Pickup rencana' }}</div>
                                <div class="fw-semibold text-dark">
                                    @if($startString)
                                        {{ \Carbon\Carbon::parse($startString)->translatedFormat('l, d F Y') }}
                                        <br>
                                        <span class="fs-5">{{ \Carbon\Carbon::parse($startString)->format('H.i') }} WIB</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="p-3 bg-light rounded-3 h-100 position-relative">
                                <div class="text-muted small mb-1">Pengembalian rencana</div>
                                <div class="fw-semibold text-dark">
                                    @if($endString)
                                        {{ \Carbon\Carbon::parse($endString)->translatedFormat('l, d F Y') }}
                                        <br>
                                        <span class="fs-5">{{ \Carbon\Carbon::parse($endString)->format('H.i') }} WIB</span>
                                    @else
                                        -
                                    @endif
                                </div>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill small">
                                    {{ $booking->total_days ?? 1 }} hari · {{ ($booking->total_days ?? 1) * 24 }} jam
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actual Pickup / Return --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <span class="text-muted small">{{ $isDelivery ? 'Actual penyerahan' : 'Actual pickup' }}</span>
                                <div class="d-flex flex-column flex-sm-row flex-wrap align-items-stretch align-items-sm-center gap-2">
                                    <span class="bg-white border rounded-3 px-3 py-2 small text-muted flex-grow-1">
                                        {{ $booking->actual_pickup ? \Carbon\Carbon::parse($booking->actual_pickup)->translatedFormat('d M Y, H.i') . ' WIB' : 'Belum diserahkan' }}
                                    </span>
                                    @if(!$booking->actual_pickup)
                                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action_type" value="record_pickup">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold w-100 w-sm-auto">
                                                {{ $isDelivery ? 'Catat penyerahan' : 'Catat pickup' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <span class="text-muted small">Actual return</span>
                                <div class="d-flex flex-column flex-sm-row flex-wrap align-items-stretch align-items-sm-center gap-2">
                                    <span class="bg-white border rounded-3 px-3 py-2 small text-muted flex-grow-1">
                                        {{ $booking->actual_return ? \Carbon\Carbon::parse($booking->actual_return)->translatedFormat('d M Y, H.i') . ' WIB' : 'Belum dikembalikan' }}
                                    </span>
                                    @if(!$booking->actual_return && $booking->actual_pickup)
                                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action_type" value="record_return">
                                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-3 px-3 py-2 fw-semibold w-100 w-sm-auto">
                                                Catat pengembalian
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Identitas & Verifikasi --}}
<div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fas fa-id-card text-primary me-2"></i>Identitas & verifikasi fisik
                </h6>
                <span class="badge rounded-pill px-2 py-1 small {{ ($booking->is_verified ?? false) ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning' }}">
                    {{ ($booking->is_verified ?? false) ? 'Terverifikasi' : 'Belum verifikasi' }}
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
                   class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold">
                    <i class="fab fa-whatsapp me-1"></i> Chat WhatsApp
                </a>
            @endif
        </div>

        <p class="text-muted small mb-3">Cocokkan dokumen fisik customer dengan data sistem</p>

        {{-- Data identitas - stack rapi di mobile --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="text-muted small">Nama customer</div>
                <div class="fw-semibold text-dark text-break">{{ optional($booking->user)->name ?? '-' }}</div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="text-muted small">No. KTP</div>
                <div class="fw-semibold text-dark text-break">{{ $booking->no_ktp ?? optional($booking->user)->no_ktp ?? '-' }}</div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="text-muted small">No. SIM C</div>
                <div class="fw-semibold text-dark text-break">{{ $booking->no_sim ?? optional($booking->user)->no_sim ?? '-' }}</div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="text-muted small">No. WhatsApp</div>
                <div class="fw-semibold text-dark text-break">{{ optional($booking->user)->no_hp ?? '-' }}</div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between align-items-start align-items-sm-center gap-3 pt-3 border-top">
            <div class="w-100 w-sm-auto">
                <div class="text-muted small mb-1">Jaminan di lapangan</div>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-medium text-wrap text-start" style="white-space: normal; max-width: 100%;">
                    <i class="fas fa-shield-alt me-1 text-warning-emphasis"></i>
                    {{ $booking->physical_deposit ?? 'E-KTP asli / Paspor / Motor pribadi' }}
                </span>
            </div>

            <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST" class="w-100 w-sm-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="action_type" value="toggle_verification">
                @if($booking->is_verified ?? false)
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 py-2 fw-semibold w-100">
                        <i class="fas fa-times me-1"></i> Batalkan verifikasi
                    </button>
                @else
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold w-100">
                        <i class="fas fa-check me-1"></i> Tandai terverifikasi fisik
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

            {{-- 3. Data Kendaraan & Driver --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-motorcycle text-primary me-2"></i>Data kendaraan & pengantar
                    </h6>

                    <div class="text-muted small mb-3">
                        {{ $isDelivery ? 'Delivery – Diantar jemput ke lokasi' : 'Pool pickup – Ambil langsung di tempat' }}
                    </div>

                    <div class="d-flex flex-column flex-sm-row align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                        <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}"
                             alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}"
                             class="rounded-3 object-fit-cover bg-white flex-shrink-0"
                             style="width: 110px; height: 80px; object-fit: contain;">
                        <div class="text-center text-sm-start">
                            <div class="text-muted small text-uppercase">{{ optional($booking->motorcycle)->brand ?? '' }}</div>
                            <h6 class="fw-bold text-dark mb-1">{{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}</h6>
                            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-1">
                                <span class="badge bg-dark text-white rounded-pill px-2">
                                    {{ optional($booking->motorcycle)->plate_number ?? 'Belum diatur' }}
                                </span>
                                <span class="badge bg-secondary rounded-pill px-2">
                                    {{ optional($booking->motorcycle)->type ?? 'Sport' }} · {{ optional($booking->motorcycle)->color ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($isDelivery)
                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action_type" value="update_driver">

                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-sm-5">
                                    <label class="form-label text-muted small mb-1">Nama kurir / pengantar</label>
                                    <input type="text" name="driver_name" class="form-control form-control-sm rounded-3"
                                           value="{{ $booking->driver_name ?? '' }}" placeholder="Contoh: Budi (Kurir)">
                                </div>
                                <div class="col-12 col-sm-5">
                                    <label class="form-label text-muted small mb-1">No. HP kurir</label>
                                    <input type="text" name="driver_phone" class="form-control form-control-sm rounded-3"
                                           value="{{ $booking->driver_phone ?? '' }}" placeholder="08123456789">
                                </div>
                                <div class="col-12 col-sm-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-3 py-2 fw-semibold">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    @if($isDelivery && $booking->pickup_address)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <div class="text-muted small mb-1">Alamat pengantaran / penjemputan</div>
                            <div class="fw-medium text-dark">{{ $booking->pickup_address }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4. Catatan Internal --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-1">
                        <i class="fas fa-sticky-note text-primary me-2"></i>Catatan internal admin
                    </h6>
                    <p class="text-muted small mb-3">Privat — hanya staf internal. Tidak tampil ke customer.</p>

                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action_type" value="update_notes">
                        <textarea name="admin_notes" rows="3"
                                  class="form-control border-0 bg-light rounded-3 p-3 small mb-3"
                                  placeholder="Tulis catatan internal (misal: customer minta helm XL, jaminan KTP disimpan di laci A)...">{{ $booking->admin_notes ?? '' }}</textarea>
                        <div class="text-end">
                            <button type="submit" class="btn btn-outline-primary btn-sm rounded-3 px-3 py-2 fw-semibold">
                                <i class="fas fa-save me-1"></i> Simpan catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== KOLOM KANAN ==================== --}}
        <div class="col-lg-4">

            {{-- Rincian Keuangan --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-wallet text-primary me-2"></i>Rincian keuangan
                    </h6>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Sewa motor ({{ $booking->total_days ?? 1 }} hari)</span>
                        <span class="fw-medium text-nowrap">Rp {{ number_format($booking->rental_fee ?? ($booking->total_price ?? 0), 0, ',', '.') }}</span>
                    </div>

                    @if($isDelivery)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Ongkir / Delivery</span>
                            <span class="fw-medium text-nowrap">Rp {{ number_format($booking->delivery_fee ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if(($booking->addons_fee ?? 0) > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Add-ons</span>
                            <span class="fw-medium text-nowrap">Rp {{ number_format($booking->addons_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if(($booking->penalty_fee ?? 0) > 0)
                        <div class="d-flex justify-content-between mb-2 small text-danger">
                            <span>Denda keterlambatan</span>
                            <span class="fw-medium text-nowrap">Rp {{ number_format($booking->penalty_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <hr class="my-3 opacity-25">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total nominal</span>
                        <span class="fw-bold text-primary fs-5 text-nowrap">
                            Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Metode bayar</span>
                            <span class="fw-medium text-uppercase">{{ $booking->payment_method ?? 'Payment gateway' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Status bayar</span>
                            <span class="badge rounded-pill px-2 py-1 {{ ($booking->paid_at ?? null) ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ($booking->paid_at ?? null) ? 'Lunas' : 'Menunggu' }}
                            </span>
                        </div>
                    </div>

                    @if(!($booking->paid_at ?? null))
                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action_type" value="mark_paid">
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                <i class="fas fa-check-circle me-1"></i> Tandai lunas
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Eksekusi Status --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-bolt text-primary me-2"></i>Eksekusi status
                    </h6>

                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action_type" value="update_status">

                        <div class="mb-3">
                            <label class="form-label text-muted small">Ubah status pesanan</label>
                            <select name="status" class="form-select form-select-sm rounded-3 border-0 bg-light py-2 fw-medium">
                                @foreach ([
                                    'pending'   => 'Menunggu pembayaran',
                                    'confirmed' => 'Siap ambil / diantar',
                                    'ongoing'   => 'Disewa (aktif)',
                                    'completed' => 'Selesai (kembali)',
                                    'cancelled' => 'Batal'
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(($booking->status ?? '') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                            Update status pesanan
                        </button>
                    </form>

                    <p class="text-muted small mt-3 mb-0">
                        Status mengikuti alur operasional: bayar → pickup → pengembalian. Perubahan status akan mempengaruhi ketersediaan unit di lapangan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection