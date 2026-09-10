@extends('layouts.app')

@section('title', 'Riwayat Booking')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Riwayat Booking</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($bookings->count())

        <div class="row g-4">
            @foreach ($bookings as $booking)
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">

                                {{-- Info Utama --}}
                                <div class="flex-grow-1">
                                    <h2 class="h5 fw-bold mb-1">
                                        {{ $booking->motorcycle->name }}
                                    </h2>

                                    <p class="text-muted mb-2 small">
                                        {{ $booking->motorcycle->brand }}
                                        @if($booking->motorcycle->model)
                                            · {{ $booking->motorcycle->model }}
                                        @endif
                                        · {{ $booking->motorcycle->plate_number }}
                                    </p>

                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        <span>
                                            📅 {{ $booking->start_date->format('d M Y') }}
                                            →
                                            {{ $booking->end_date->format('d M Y') }}
                                        </span>
                                        <span>
                                            ⏱ {{ $booking->total_days }} hari
                                        </span>
                                    </div>
                                </div>

                                {{-- Harga + Status + Tombol --}}
                                <div class="text-md-end">
                                    <div class="fw-bold text-primary mb-2">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </div>

                                    @php
                                        $statusClass = match($booking->status) {
                                            'pending'   => 'bg-warning text-dark',
                                            'confirmed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            'completed' => 'bg-primary',
                                            default     => 'bg-secondary',
                                        };
                                    @endphp

                                    <div class="mb-3">
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>

                                    <a href="{{ route('bookings.show', $booking) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $bookings->links() }}
        </div>

    @else
        {{-- Empty State --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center py-5">
                <div class="mb-3" style="font-size: 3rem;">🛵</div>
                <h5 class="fw-semibold mb-2">Belum ada booking</h5>
                <p class="text-muted mb-4">
                    Kamu belum pernah melakukan booking motor.
                </p>
                <a href="{{ route('motorcycles.index') }}" class="btn btn-primary">
                    Cari Motor
                </a>
            </div>
        </div>
    @endif

</div>
@endsection