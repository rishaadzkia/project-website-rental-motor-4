@extends('layouts.app')

@section('title', 'Daftar Motor')

@section('content')
<div class="container py-4">

    <h1 class="h3 mb-4">Daftar Motor Tersedia</h1>

    <form method="GET" action="{{ route('motorcycles.index') }}" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="form-control" placeholder="Cari motor...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>

    <div class="row g-4">
        @forelse ($motorcycles as $motorcycle)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    @if ($motorcycle->image)
                        <img src="{{ asset('storage/' . $motorcycle->image) }}" 
                             class="card-img-top" style="height: 180px; object-fit: cover;" 
                             alt="{{ $motorcycle->name }}">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 180px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title mb-1">{{ $motorcycle->brand }} {{ $motorcycle->name }}</h5>
                        <p class="text-muted small mb-2">{{ $motorcycle->model }}</p>
                        <p class="fw-bold text-primary mb-3">
                            Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }} / hari
                        </p>
                        <a href="{{ route('motorcycles.show', $motorcycle) }}" class="btn btn-outline-primary btn-sm w-100">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Motor tidak ditemukan.
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $motorcycles->links() }}
    </div>
</div>
@endsection