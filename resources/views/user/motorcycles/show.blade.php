@extends('layouts.app') {{-- sesuaikan dengan layout kamu --}}

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="row g-0">
                    <!-- Gambar Motor -->
                    <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-4">
                        @if($motorcycle->image)
                            <img src="{{ asset('storage/' . $motorcycle->image) }}" 
                                 alt="{{ $motorcycle->name }}" 
                                 class="img-fluid rounded-3"
                                 style="max-height: 420px; object-fit: contain;">
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-image" style="font-size: 4rem;"></i>
                                <p class="mt-2">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detail Motor -->
                    <div class="col-md-6">
                        <div class="card-body p-4 p-lg-5">
                            <!-- Badge Status -->
                            <div class="mb-3">
                                @if($motorcycle->status === 'available')
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> Available
                                    </span>
                                @elseif($motorcycle->status === 'rented')
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-clock me-1"></i> Sedang Disewa
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <i class="bi bi-tools me-1"></i> Maintenance
                                    </span>
                                @endif
                            </div>

                            <!-- Nama Motor -->
                            <h2 class="fw-bold mb-1">{{ $motorcycle->name }}</h2>
                            <p class="text-muted mb-4">{{ $motorcycle->brand }} • {{ $motorcycle->model }}</p>

                            <!-- Info Grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <small class="text-muted d-block">Tahun</small>
                                        <span class="fw-semibold">{{ $motorcycle->year }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3 h-100">
                                        <small class="text-muted d-block">Plat Nomor</small>
                                        <span class="fw-semibold">{{ $motorcycle->plate_number }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                        <small class="text-muted d-block">Harga Sewa</small>
                                        <span class="fs-4 fw-bold text-primary">
                                            Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}
                                        </span>
                                        <span class="text-muted">/ hari</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            @if($motorcycle->description)
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-2">Deskripsi</h6>
                                    <p class="text-muted mb-0">{{ $motorcycle->description }}</p>
                                </div>
                            @endif

                            <!-- Tombol Aksi -->
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('motorcycles.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>

                            @if ($motorcycle->status === 'available')
                                <a href="{{ route('bookings.create', $motorcycle) }}" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-1"></i> Booking Motor
                                </a>
                            @else
                                <p>Motor sedang tidak tersedia.</p>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection