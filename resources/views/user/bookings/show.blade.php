@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Detail Booking</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Kembali ke Riwayat
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">

            {{-- Header Motor --}}
            <div class="mb-4">
                <h2 class="h4 fw-bold mb-1">{{ $booking->motorcycle->name }}</h2>
                <p class="text-muted mb-0">
                    {{ $booking->motorcycle->brand }}
                    @if($booking->motorcycle->model)
                        · {{ $booking->motorcycle->model }}
                    @endif
                </p>
            </div>

            <hr class="my-4">

            {{-- Info Motor --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2" style="min-width: 120px;">Nomor Polisi</span>
                        <strong>{{ $booking->motorcycle->plate_number }}</strong>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Detail Booking --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2" style="min-width: 140px;">Tanggal Mulai</span>
                        <strong>{{ $booking->start_date->format('d M Y') }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2" style="min-width: 140px;">Tanggal Selesai</span>
                        <strong>{{ $booking->end_date->format('d M Y') }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2" style="min-width: 140px;">Total Hari</span>
                        <strong>{{ $booking->total_days }} hari</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2" style="min-width: 140px;">Total Harga</span>
                        <strong class="text-primary">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Status --}}
            <div class="d-flex align-items-center mb-4">
                <span class="text-muted me-2" style="min-width: 140px;">Status</span>
                @php
                    $statusClass = match($booking->status) {
                        'pending'   => 'bg-warning text-dark',
                        'confirmed' => 'bg-success',
                        'cancelled' => 'bg-danger',
                        'completed' => 'bg-primary',
                        default     => 'bg-secondary',
                    };
                @endphp
                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>

            {{-- Tombol Aksi --}}
            @if ($booking->status === 'pending')
                <form
                    action="{{ route('bookings.cancel', $booking) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin membatalkan booking?')"
                    class="d-inline"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-outline-danger">
                        Batalkan Booking
                    </button>
                </form>
            @endif

        </div>
    </div>

</div>
@endsection