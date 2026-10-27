@extends('layouts.app')

@section('title', 'Pilih Kendaraan - ED.RENT')

@section('content')
<div class="container py-4">

    <!-- Header / Title Section -->
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark mb-1">Daftar Kendaraan</h1>
        <p class="text-secondary small mb-0">
            Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!
        </p>
    </div>

    <!-- Form Filter & Pencarian -->
    <form method="GET" action="{{ route('motorcycles.index') }}" class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
        <div class="row g-3 align-items-end">
            
            <!-- Filter Tipe Kendaraan -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Tipe kendaraan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 pe-1"><i class="bi bi-grid text-muted"></i></span>
                    <select name="model" class="form-select bg-light border-0 py-2 rounded-end-3" onchange="this.form.submit()">
                        <option value="semua" @selected(request('model') == 'semua' || !request('model'))>Semua Tipe</option>
                        <option value="Matic (Skutik)" @selected(request('model') == 'Matic (Skutik)')>Matic (Skutik)</option>
                        <option value="Sport" @selected(request('model') == 'Sport')>Sport</option>
                        <option value="Cub (Bebek)" @selected(request('model') == 'Cub (Bebek)')>Cub (Bebek)</option>
                        <option value="EV (Kendaraan Listrik)" @selected(request('model') == 'EV (Kendaraan Listrik)')>EV (Kendaraan Listrik)</option>
                        <option value="Big Bike (Moge)" @selected(request('model') == 'Big Bike (Moge)')>Big Bike (Moge)</option>
                    </select>
                </div>
            </div>

            <!-- Filter Tanggal & Jam Sewa -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Tanggal & jam sewa</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 pe-1"><i class="bi bi-calendar-event text-muted"></i></span>
                    <input type="datetime-local" name="rental_time" value="{{ request('rental_time') }}" class="form-control bg-light border-0 py-2 rounded-end-3" onchange="this.form.submit()">
                </div>
            </div>

            <!-- Cari Kendaraan -->
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Cari kendaraan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 pe-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0 py-2 ps-2 rounded-end-3" placeholder="Nama motor" onblur="this.form.submit()">
                </div>
            </div>

            <!-- Tombol Aksi (Reset & Submit) -->
            <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                @if(request()->hasAny(['model', 'rental_time', 'search']) && (request('model') !== 'semua' || request('rental_time') || request('search')))
                    <a href="{{ route('motorcycles.index') }}" class="btn btn-outline-secondary py-2 w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                @endif
                <button type="submit" class="btn btn-primary py-2 w-100 rounded-3 d-flex align-items-center justify-content-center gap-1">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>

        </div>
    </form>

    <!-- Grid Card Kendaraan -->
    <div class="row g-3 g-md-4 mb-4">
        @forelse($motorcycles as $motor)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-column justify-content-between">
                    
                    <div>
                        <!-- Header Card: Nama & Tipe -->
                        <div class="mb-2">
                            <h5 class="fw-bold mb-0 text-dark fs-6 text-truncate" title="{{ $motor->name }}">{{ $motor->name }}</h5>
                            <span class="text-muted small text-truncate d-block">{{ $motor->model }}</span>
                        </div>

                        <!-- Gambar Motor -->
                        <div class="text-center my-3 d-flex align-items-center justify-content-center" style="height: 140px;">
                            @if($motor->image)
                                <img src="{{ asset('storage/' . $motor->image) }}" class="mw-100 mh-100 object-fit-contain" alt="{{ $motor->name }}">
                            @else
                                <div class="bg-light w-100 h-100 rounded-3 d-flex align-items-center justify-content-center text-muted small">
                                    Tidak ada gambar
                                </div>
                            @endif
                        </div>

                        <!-- Plat & Badge Status -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                <i class="bi bi-card-text"></i> {{ $motor->plate_number }}
                            </span>
                            <span class="small fw-semibold text-success" style="font-size: 0.78rem;">
                                • Tersedia
                            </span>
                        </div>
                    </div>

                    <!-- Harga & Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto gap-2">
                        <!-- Komponen Harga -->
                        <div class="me-auto text-nowrap">
                            <span class="fw-bold text-dark fs-6">
                                Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}
                            </span>
                            <span class="text-muted small" style="font-size: 0.75rem;">/hari</span>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex gap-1 align-items-center flex-shrink-0">
                            <!-- Tombol Detail (Ikon Mata) -->
                            <a href="{{ route('motorcycles.show', $motor) }}" 
                               class="btn btn-outline-primary btn-sm rounded-3 px-2 py-1.5 fw-medium d-inline-flex align-items-center justify-content-center"
                               title="Lihat Detail Kendaraan">
                                <i class="bi bi-eye fs-6"></i>
                            </a>

                            <!-- Tombol Booking -->
                            <a href="{{ route('bookings.create', $motor) }}" 
                               class="btn btn-primary btn-sm rounded-3 px-2.5 py-1.5 fw-medium d-inline-flex align-items-center justify-content-center text-center">
                                Booking
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">Kendaraan tidak ditemukan atau sedang tidak tersedia.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $motorcycles->withQueryString()->links() }}
    </div>

</div>
@endsection