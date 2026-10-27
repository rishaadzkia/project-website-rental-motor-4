@extends('layouts.app')

@section('title', $motorcycle->name . ' - ED.RENT')

@section('content')
<div class="container py-5">
    <div class="row g-4 align-items-stretch">
        
        <!-- Kartu Kiri: Foto & Spesifikasi Motor -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <!-- Foto Motor -->
                <div class="text-center my-auto py-3" style="min-height: 260px;">
                    @if($motorcycle->image)
                        <img src="{{ asset('storage/' . $motorcycle->image) }}" 
                             alt="{{ $motorcycle->name }}" 
                             class="img-fluid object-fit-contain" 
                             style="max-height: 280px; width: 100%;">
                    @else
                        <div class="bg-light rounded-3 d-flex flex-column align-items-center justify-content-center text-muted py-5" style="height: 260px;">
                            <i class="bi bi-image fs-1 mb-2"></i>
                            <span>Tidak ada foto</span>
                        </div>
                    @endif
                </div>

                <!-- Spesifikasi Teknis Ringkas -->
                <div class="pt-3 border-top mt-auto">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Brand</span>
                        <span class="fw-semibold text-dark small">{{ $motorcycle->brand }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tipe</span>
                        <span class="fw-semibold text-dark small">{{ $motorcycle->model }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Kapasitas Tangki</span>
                        <span class="fw-semibold text-dark small">{{ $motorcycle->tank_capacity ? $motorcycle->tank_capacity . ' L' : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">No. Plat</span>
                        <span class="fw-semibold text-dark small">{{ $motorcycle->plate_number }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Kanan: Deskripsi, Perlengkapan, & Harga -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-white d-flex flex-column justify-content-between">
                <div>
                    <!-- Nama Motor -->
                    <h2 class="fw-bold text-dark mb-2">{{ $motorcycle->name }}</h2>

                    <!-- Status Ketersediaan -->
                    <div class="mb-4">
                        @if($motorcycle->status === 'available')
                            <span class="small fw-semibold text-success d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Tersedia
                            </span>
                        @elseif($motorcycle->status === 'rented')
                            <span class="small fw-semibold text-warning d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Sedang Disewa
                            </span>
                        @else
                            <span class="small fw-semibold text-secondary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Maintenance
                            </span>
                        @endif
                    </div>

                    <!-- Deskripsi Motor -->
                    <p class="text-secondary small lh-lg mb-4">
                        {{ $motorcycle->description ?? 'Motor bebek yang terkenal sangat irit bahan bakar dan tangguh. ' . $motorcycle->name . ' adalah pilihan ideal untuk Anda yang mencari kendaraan praktis, lincah, dan handal untuk mobilitas sehari-hari. Mesin yang responsif dan mudah dikendalikan, sangat cocok untuk menjelajahi jalanan padat perkotaan atau perjalanan jarak dekat dengan efisiensi maksimal.' }}
                    </p>

                    <hr class="text-border opacity-25 my-4">

                    <!-- Perlengkapan Included -->
                    <div class="mb-4">
                        <h6 class="text-primary small fw-medium mb-3">Perlengkapan:</h6>
                        <div class="row g-2 text-secondary small">
                            <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                                <i class="bi bi-dot fs-5 text-secondary"></i> Bensin 1 liter
                            </div>
                            <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                                <i class="bi bi-dot fs-5 text-secondary"></i> Jas hujan 1 buah
                            </div>
                            
                            <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                                <i class="bi bi-dot fs-5 text-secondary"></i> Helm SNI 1 buah
                            </div>
                            <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                                <i class="bi bi-dot fs-5 text-secondary"></i> STNK
                            </div>
                            
                        </div>
                    </div>
                </div>

                <!-- Footer Kartu: Harga & Tombol Aksi -->
                <div class="d-flex justify-content-between align-items-end pt-3 mt-auto">
                    <div>
                        <span class="fs-3 fw-bold text-dark">
                            Rp{{ number_format($motorcycle->price_per_day, 0, ',', '.') }}
                        </span>
                        <span class="text-muted small">/ hari</span>
                    </div> 

                    <div>
                        {{-- HANYA TAMPILKAN TOMBOL BOOKING UNTUK PELANGGAN / GUEST (BUKAN ADMIN) --}}
                        @unless (Auth::check() && (Auth::user()->is_admin || (Auth::user()->role ?? '') === 'admin'))
                            @if ($motorcycle->status === 'available')
                                <a href="{{ route('bookings.create', $motorcycle) }}" class="btn btn-primary px-4 py-2 rounded-3 fw-medium">
                                    Booking
                                </a> 
                            @else
                                <button class="btn btn-secondary px-4 py-2 rounded-3 fw-medium" disabled>
                                    Tidak Tersedia
                                </button>
                            @endif 
                        @endunless
                    </div>
                </div> 
            
            </div>
        </div>

    </div>
</div>
@endsection