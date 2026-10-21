@extends('layouts.admin')

@section('title', 'Manage Booking')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-dark mb-4">Manage Booking</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4" role="alert">
            <p class="fw-semibold mb-1">Pesanan belum berhasil diperbarui:</p>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- List Card Pesanan -->
    <div class="d-flex flex-column gap-3">
        @forelse($bookings as $booking)
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="row align-items-center g-3">
                    
                    <!-- Gambar Motor -->
                    <div class="col-md-2 text-center">
                        <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                             alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}" 
                             class="img-fluid rounded-3 object-fit-cover" 
                             style="max-height: 80px;">
                    </div>

                    <!-- Detail Kendaraan & Customer -->
                    <div class="col-md-3">
                        <span class="text-muted extra-small d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">
                            {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                        </span>
                        <h6 class="fw-bold text-dark mb-1">{{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}</h6>
                        <small class="text-muted d-block">User: <strong>{{ optional($booking->user)->name ?? '-' }}</strong></small>
                        <small class="text-primary d-block mt-1" style="font-size: 0.75rem;">
                            🛵 {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                        </small>
                    </div>

                    <!-- Kode Booking & Tanggal -->
                    <div class="col-md-2">
                        <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Kode Booking</span>
                        <strong class="text-dark d-block mb-1">#ED-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">
                            {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M Y') : '-' }} 
                            - 
                            {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}
                        </small>
                    </div>

                    <!-- Status Badge -->
                    <div class="col-md-2 text-md-center">
                        <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Status</span>
                        
                        @php
                            $statusClasses = [
                                'pending' => 'bg-secondary bg-opacity-10 text-secondary',
                                'confirmed' => 'bg-success bg-opacity-10 text-success',
                                'ongoing' => 'bg-warning bg-opacity-10 text-warning-emphasis',
                                'completed' => 'bg-info bg-opacity-10 text-info',
                                'cancelled' => 'bg-danger bg-opacity-10 text-danger',
                            ];
                            
                            $statusLabels = [
                                'pending' => 'Pending',
                                'confirmed' => 'Lunas / Dikonfirmasi',
                                'ongoing' => 'Sedang Dibawa',
                                'completed' => 'Sudah Kembali',
                                'cancelled' => 'Batal',
                            ];
                        @endphp

                        <span class="badge rounded-pill px-3 py-2 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}">
                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </span>
                    </div>

                    <!-- Harga Total -->
                    <div class="col-md-2">
                        <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Total Harga</span>
                        <strong class="text-primary fs-6 d-block">
                            Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                        </strong>
                        <small class="text-muted d-block extra-small" style="font-size: 0.7rem;">
                            ({{ $booking->total_days ?? 1 }} Hari{{ ($booking->pickup_method ?? '') === 'delivery' && ($booking->delivery_fee ?? 0) > 0 ? ' + Ongkir' : '' }})
                        </small>
                    </div>

                    <!-- Tombol Aksi: Detail & Update -->
                    <div class="col-md-1 text-md-end d-flex flex-column gap-1">
                        @if(Route::has('admin.bookings.show'))
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-secondary btn-sm rounded-3">
                                Detail
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-primary btn-sm rounded-3 fw-semibold" 
                                data-bs-toggle="modal" 
                                data-bs-target="#updateStatusModal{{ $booking->id }}">
                            Update
                        </button>
                    </div>

                </div>
            </div>

            <!-- Modal Update Status -->
            <div class="modal fade" id="updateStatusModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 border-0 p-3">
                        
                        <!-- Header Modal -->
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark">Update Pemesanan #{{ $booking->id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hidden input untuk penanganan Controller -->
                            <input type="hidden" name="action_type" value="update_status">

                            <div class="modal-body">
                                <div class="row g-4 align-items-center mb-4">
                                    
                                    <!-- Kolom Kiri: Motor -->
                                    <div class="col-md-5 border-end-md">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h5 class="fw-bold text-dark mb-0">{{ optional($booking->motorcycle)->name ?? 'Motor' }}</h5>
                                        </div>
                                        <span class="text-muted small d-block mb-2">
                                            {{ optional($booking->motorcycle)->type ?? 'Matic' }}
                                        </span>
                                        <div class="text-center py-2">
                                            <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                                                 alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}" 
                                                 class="img-fluid object-fit-contain" 
                                                 style="max-height: 140px;">
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan: Detail Pemesan & Rincian Biaya -->
                                    <div class="col-md-7 ps-md-4">
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

                                            <div class="row pt-1 border-top mt-1">
                                                <div class="col-5 text-muted fw-bold">Total Pembayaran</div>
                                                <div class="col-7 fw-bold text-primary">: Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <hr class="text-muted opacity-25 my-4">

                                <!-- Baris Bawah: Tanggal Sewa & Pilihan Status -->
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-4">
                                        <span class="text-muted small d-block mb-1">Tanggal Sewa</span>
                                        <strong class="d-block text-dark small">
                                            {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->translatedFormat('D, d M Y') : '-' }}
                                        </strong>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="text-muted small d-block mb-1">Selesai Sewa</span>
                                        <strong class="d-block text-dark small">
                                            {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->translatedFormat('D, d M Y') : '-' }}
                                        </strong>
                                    </div>

                                    <div class="col-md-4">
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

                            <!-- Footer / Tombol Simpan -->
                            <div class="modal-footer border-0 pt-3">
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

    <!-- Pagination Links -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $bookings->links() }}
    </div>
</div>
@endsection