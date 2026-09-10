@extends('layouts.app')

@section('title', 'Booking Motor')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Booking Motor</h1>
        <a href="{{ route('motorcycles.show', $motorcycle) }}" class="btn btn-outline-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">

            {{-- Info Motor --}}
            <div class="mb-4">
                <h2 class="h4 fw-bold mb-1">{{ $motorcycle->name }}</h2>
                <p class="text-muted mb-2">
                    {{ $motorcycle->brand }}
                    @if($motorcycle->model)
                        · {{ $motorcycle->model }}
                    @endif
                </p>
                <p class="mb-0">
                    <span class="text-muted">Harga:</span>
                    <strong class="text-primary">
                        Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}
                    </strong>
                    <span class="text-muted">/ hari</span>
                </p>
            </div>

            <hr class="my-4">

            {{-- Form Booking --}}
            <form action="{{ route('bookings.store', $motorcycle) }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label fw-semibold">
                            Tanggal Mulai
                        </label>
                        <input
                            type="date"
                            class="form-control @error('start_date') is-invalid @enderror"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                            min="{{ date('Y-m-d') }}"
                            required
                        >
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label fw-semibold">
                            Tanggal Selesai
                        </label>
                        <input
                            type="date"
                            class="form-control @error('end_date') is-invalid @enderror"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date') }}"
                            min="{{ date('Y-m-d') }}"
                            required
                        >
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        Buat Booking
                    </button>
                    <a href="{{ route('motorcycles.show', $motorcycle) }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection