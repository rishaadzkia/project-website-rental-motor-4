@extends('layouts.admin')

@section('title', 'Manage Booking')

@section('content')
<div class="container py-3 py-md-4">
    <h1 class="fw-bold text-dark mb-3 mb-md-4 fs-3">Manage Booking</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 mb-md-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-3 mb-md-4" role="alert">
            <p class="fw-semibold mb-1">Pesanan belum berhasil diperbarui:</p>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse($bookings as $booking)
            @php
                $statusClasses = [
                    'pending'   => 'bg-secondary bg-opacity-10 text-secondary',
                    'confirmed' => 'bg-success bg-opacity-10 text-success',
                    'ongoing'   => 'bg-warning bg-opacity-10 text-warning-emphasis',
                    'completed' => 'bg-info bg-opacity-10 text-info',
                    'cancelled' => 'bg-danger bg-opacity-10 text-danger',
                ];
                $statusLabels = [
                    'pending'   => 'Pending',
                    'confirmed' => 'Lunas',
                    'ongoing'   => 'Sedang Dibawa',
                    'completed' => 'Sudah Kembali',
                    'cancelled' => 'Batal',
                ];
            @endphp

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-3">

                    {{-- ==================== TABLET & DESKTOP ==================== --}}
                    <div class="d-none d-md-flex align-items-center gap-3">
                        <div style="width: 90px;" class="flex-shrink-0 text-center">
                            <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}"
                                 alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}"
                                 class="rounded-3"
                                 style="width: 80px; height: 65px; object-fit: cover;">
                        </div>

                        <div style="min-width: 180px; max-width: 220px;" class="flex-grow-1">
                            <span class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.68rem;">
                                {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                            </span>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                {{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}
                            </h6>
                            <small class="text-muted d-block" style="font-size: 0.78rem;">
                                User: <strong>{{ optional($booking->user)->name ?? '-' }}</strong>
                            </small>
                            <small class="text-primary d-block mt-1" style="font-size: 0.72rem;">
                                <i class="bi bi-geo-alt"></i>
                                {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                            </small>
                        </div>

                        <div style="min-width: 130px;">
                            <span class="text-muted d-block" style="font-size: 0.68rem;">Kode Booking</span>
                            <strong class="text-dark d-block" style="font-size: 0.9rem;">
                                #ED-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </strong>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M Y') : '-' }}
                                –
                                {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}
                            </small>
                        </div>

                        <div style="min-width: 110px;" class="text-center">
                            <span class="text-muted d-block mb-1" style="font-size: 0.68rem;">Status</span>
                            <span class="badge rounded-pill px-2 py-1 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}" style="font-size: 0.75rem;">
                                {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div style="min-width: 120px;">
                            <span class="text-muted d-block" style="font-size: 0.68rem;">Total Harga</span>
                            <strong class="text-primary d-block text-nowrap" style="font-size: 0.95rem;">
                                Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                            </strong>
                            <small class="text-muted d-block" style="font-size: 0.68rem;">
                                ({{ $booking->total_days ?? 1 }} Hari{{ ($booking->pickup_method ?? '') === 'delivery' && ($booking->delivery_fee ?? 0) > 0 ? ' + Ongkir' : '' }})
                            </small>
                        </div>

                        <div class="ms-auto d-flex gap-2 flex-shrink-0">
                            @if(Route::has('admin.bookings.show'))
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                    Detail
                                </a>
                            @endif
                            <button type="button"
                                    class="btn btn-primary btn-sm rounded-3 fw-semibold px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateStatusModal{{ $booking->id }}">
                                Update
                            </button>
                        </div>
                    </div>

                    {{-- ==================== MOBILE ==================== --}}
                    <div class="d-md-none">

                        {{-- Gambar Full --}}
                        <div class="text-center mb-3">
                            <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}"
                                 alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}"
                                 class="rounded-3 w-100"
                                 style="max-height: 170px; object-fit: contain; background: rgba(255,255,255,0.03);">
                        </div>

                        {{-- Nama Motor --}}
                        <div class="mb-3">
                            <span class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.7rem;">
                                {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                            </span>
                            <h6 class="fw-bold text-dark mb-1">
                                {{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}
                            </h6>
                            <small class="text-muted d-block">
                                User: <strong>{{ optional($booking->user)->name ?? '-' }}</strong>
                            </small>
                            <small class="text-primary d-block mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-geo-alt"></i>
                                {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                            </small>
                        </div>

                        {{-- Kode + Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.68rem;">Kode Booking</span>
                                <strong class="text-dark d-block">#ED-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 0.68rem;">Tanggal</span>
                                <small class="text-dark d-block" style="font-size: 0.8rem;">
                                    {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M') : '-' }}
                                    –
                                    {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}
                                </small>
                            </div>
                        </div>

                        {{-- Status & Harga --}}
                        {{-- Di layar sangat kecil (Flip) → harga turun ke bawah --}}
                        {{-- Di HP biasa (≥576px) → tetap sejajar kiri-kanan --}}
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <span class="text-muted d-block mb-1" style="font-size: 0.68rem;">Status</span>
                                <span class="badge rounded-pill px-2 py-1 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}" style="font-size: 0.72rem;">
                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </div>

                            <div class="text-sm-end">
                                <span class="text-muted d-block" style="font-size: 0.68rem;">Total Harga</span>
                                <strong class="text-primary d-block text-nowrap" style="font-size: 1rem;">
                                    Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                                </strong>
                                <small class="text-muted d-block" style="font-size: 0.65rem;">
                                    ({{ $booking->total_days ?? 1 }} Hari{{ ($booking->pickup_method ?? '') === 'delivery' && ($booking->delivery_fee ?? 0) > 0 ? ' + Ongkir' : '' }})
                                </small>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex gap-2">
                            @if(Route::has('admin.bookings.show'))
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="btn btn-outline-secondary btn-sm rounded-3 flex-fill py-2">
                                    Detail
                                </a>
                            @endif
                            <button type="button"
                                    class="btn btn-primary btn-sm rounded-3 fw-semibold flex-fill py-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateStatusModal{{ $booking->id }}">
                                Update
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Modal --}}
            <div class="modal fade" id="updateStatusModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                    <div class="modal-content rounded-4 border-0">
                        <div class="modal-header border-0 pb-0 px-3 px-md-4 pt-3">
                            <h5 class="modal-title fw-bold text-dark">Update Pemesanan #{{ $booking->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action_type" value="update_status">

                            <div class="modal-body px-3 px-md-4">
                                <div class="row g-4 mb-4">
                                    <div class="col-12 col-md-5 border-md-end">
                                        <h5 class="fw-bold text-dark mb-1">{{ optional($booking->motorcycle)->name ?? 'Motor' }}</h5>
                                        <span class="text-muted small d-block mb-3">
                                            {{ optional($booking->motorcycle)->type ?? 'Matic' }}
                                        </span>
                                        <div class="text-center">
                                            <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}"
                                                 alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}"
                                                 class="img-fluid object-fit-contain rounded-3"
                                                 style="max-height: 140px;">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-7">
                                        <h6 class="fw-bold text-dark mb-3">Detail Pemesan & Pengantaran</h6>
                                        <div class="d-flex flex-column gap-2 small">
                                            <div class="row">
                                                <div class="col-5 text-muted">Nama User</div>
                                                <div class="col-7 fw-bold text-dark">: {{ optional($booking->user)->name ?? '-' }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5 text-muted">No. WhatsApp</div>
                                                <div class="col-7 fw-bold text-dark">: {{ optional($booking->user)->no_hp ?? '-' }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5 text-muted">Kode Booking</div>
                                                <div class="col-7 fw-bold text-dark">: #ED-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5 text-muted">Metode Ambil</div>
                                                <div class="col-7 fw-bold text-dark">: {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}</div>
                                            </div>

                                            @if(($booking->pickup_method ?? '') === 'delivery')
                                                <div class="row">
                                                    <div class="col-5 text-muted">Alamat Antar</div>
                                                    <div class="col-7 fw-bold text-dark">: {{ $booking->pickup_address ?? '-' }}</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-5 text-muted">Biaya Ongkir</div>
                                                    <div class="col-7 fw-bold text-dark">: Rp {{ number_format($booking->delivery_fee ?? 0, 0, ',', '.') }}</div>
                                                </div>
                                            @endif

                                            <div class="row pt-2 border-top mt-1">
                                                <div class="col-5 text-muted fw-bold">Total Pembayaran</div>
                                                <div class="col-7 fw-bold text-primary">: Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="text-muted opacity-25 my-3">

                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <span class="text-muted small d-block mb-1">Tanggal Sewa</span>
                                        <strong class="d-block text-dark small">
                                            {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->translatedFormat('D, d M Y') : '-' }}
                                        </strong>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <span class="text-muted small d-block mb-1">Selesai Sewa</span>
                                        <strong class="d-block text-dark small">
                                            {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->translatedFormat('D, d M Y') : '-' }}
                                        </strong>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <span class="text-muted small d-block mb-1">Status Transaksi</span>
                                        <select name="status" class="form-select border-0 bg-light rounded-pill px-3 py-2 fw-semibold text-secondary small">
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed (Lunas)</option>
                                            <option value="ongoing" {{ $booking->status == 'ongoing' ? 'selected' : '' }}>Ongoing (Sedang Dibawa)</option>
                                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed (Sudah Kembali)</option>
                                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 px-3 px-md-4 pb-3 pt-2">
                                <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <p class="text-muted mb-0">Belum ada pesanan yang masuk.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>
</div>
@endsection