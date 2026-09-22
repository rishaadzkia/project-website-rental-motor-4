@extends('layouts.admin')

@section('content')
<h3 class="fw-bold text-dark mb-4">Daftar Pesanan</h3>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- List Card Pesanan -->
<div class="d-flex flex-column gap-3">
    @forelse($bookings as $booking)
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="row align-items-center g-3">
                
                <!-- 1. Foto & Nama Motor + Plat Nomor -->
                <div class="col-md-3 d-flex align-items-center gap-3">
                    <img src="{{ optional($booking->motorcycle)->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                         alt="{{ optional($booking->motorcycle)->name ?? 'Motor' }}" 
                         class="img-fluid rounded-3 object-fit-cover bg-light p-1" 
                         style="width: 70px; height: 70px;">
                    <div>
                        <span class="text-muted extra-small d-block text-uppercase fw-semibold" style="font-size: 0.7rem;">
                            {{ optional($booking->motorcycle)->type ?? 'Motor' }}
                        </span>
                        <h6 class="fw-bold text-dark mb-1">{{ optional($booking->motorcycle)->name ?? 'Nama Motor' }}</h6>
                        <!-- Plat Nomor Ditampilkan -->
                        <span class="badge bg-dark text-white fw-semibold" style="font-size: 0.75rem;">
                            {{ optional($booking->motorcycle)->plate_number ?? 'No Plat' }}
                        </span>
                    </div>
                </div>

                <!-- 2. Nama Customer & Quick Chat WA -->
                <div class="col-md-2">
                    <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Customer</span>
                    <strong class="text-dark d-block mb-1">{{ optional($booking->user)->name ?? '-' }}</strong>
                    
                    @if(optional($booking->user)->no_hp)
                        @php
                            $phone = preg_replace('/[^0-9]/', '', $booking->user->no_hp);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $phone }}" target="_blank" class="text-success text-decoration-none extra-small fw-semibold" style="font-size: 0.75rem;">
                            💬 {{ $booking->user->no_hp }}
                        </a>
                    @else
                        <small class="text-muted" style="font-size: 0.75rem;">-</small>
                    @endif
                </div>

                <!-- 3. Kode Booking, Tanggal Sewa, & Metode -->
                <div class="col-md-2">
                    <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Kode & Tanggal</span>
                    <strong class="text-dark d-block style-code" style="font-size: 0.85rem;">
                        {{ $booking->booking_code ?? 'RENT-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                    </strong>
                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                        {{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('d M') : '-' }} - 
                        {{ $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}
                    </small>
                    <!-- Badge Metode Ambil -->
                    <span class="badge {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} mt-1" style="font-size: 0.7rem;">
                        {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? '🛵 Delivery' : '🏢 Pool Pickup' }}
                    </span>
                </div>

                <!-- 4. Status Pesanan -->
                <div class="col-md-2 text-md-center">
                    <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Status</span>
                    
                    @php
                        $statusClasses = [
                            'pending' => 'bg-secondary bg-opacity-10 text-secondary',
                            'confirmed' => 'bg-info bg-opacity-10 text-info',
                            'ongoing' => 'bg-warning bg-opacity-10 text-dark',
                            'completed' => 'bg-success bg-opacity-10 text-success',
                            'overdue' => 'bg-danger text-white',
                            'cancelled' => 'bg-danger bg-opacity-10 text-danger',
                        ];
                        
                        $statusLabels = [
                            'pending' => 'Menunggu Bayar',
                            'confirmed' => 'Siap Ambil',
                            'ongoing' => 'Disewa',
                            'completed' => 'Selesai',
                            'overdue' => 'Terlambat',
                            'cancelled' => 'Batal',
                        ];
                    @endphp

                    <span class="badge rounded-pill px-3 py-2 fw-medium {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}" style="font-size: 0.75rem;">
                        {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                    </span>
                </div>

                <!-- 5. Total Biaya Ringkasan -->
                <div class="col-md-2">
                    <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Total Biaya</span>
                    <strong class="text-primary fs-6 d-block">
                        Rp {{ number_format($booking->grand_total ?? $booking->total_price ?? 0, 0, ',', '.') }}
                    </strong>
                </div>

                <!-- 6. Aksi Cepat -->
                <div class="col-md-1 text-md-end d-flex flex-column gap-1">
                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm rounded-3 py-1 fw-semibold w-100" style="font-size: 0.75rem;">
                        Detail
                    </a>
                    <button type="button" class="btn btn-primary btn-sm rounded-3 py-1 fw-semibold w-100" style="font-size: 0.75rem;"
                            data-bs-toggle="modal" 
                            data-bs-target="#quickUpdateModal{{ $booking->id }}">
                        Update
                    </button>
                </div>

            </div>
        </div>

        <!-- Modal Update Status Ringkas (Tanpa Informasi Berlebihan) -->
        <div class="modal fade" id="quickUpdateModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 p-3">
                    
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold text-dark">Update Status</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body py-3">
                            <p class="small text-muted mb-2">
                                Booking: <strong>#{{ $booking->booking_code ?? $booking->id }}</strong>
                            </p>
                            <label for="statusSelect{{ $booking->id }}" class="form-label small text-muted">Status Pesanan Baru:</label>
                            <select name="status" id="statusSelect{{ $booking->id }}" class="form-select border-0 bg-light rounded-3 py-2 fw-semibold text-secondary small">
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Siap Ambil / Diantar</option>
                                <option value="ongoing" {{ $booking->status == 'ongoing' ? 'selected' : '' }}>Disewa (Aktif)</option>
                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="overdue" {{ $booking->status == 'overdue' ? 'selected' : '' }}>Terlambat (Overdue)</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>

                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 btn-sm fw-semibold">
                                Simpan Status
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
@endsection