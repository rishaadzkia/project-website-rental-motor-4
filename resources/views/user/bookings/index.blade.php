@extends('layouts.app')

@section('title', 'Riwayat Booking')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Riwayat Booking</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($bookings->count())

        <div class="row g-4">
            @foreach ($bookings as $booking)
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 bg-white">
                        <div class="card-body p-4">

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">

                                {{-- Info Utama --}}
                                <div class="flex-grow-1">
                                    <h2 class="h5 fw-bold mb-1 text-dark">
                                        {{ $booking->motorcycle->name }}
                                    </h2>

                                    <p class="text-muted mb-2 small">
                                        {{ $booking->motorcycle->brand }}
                                        @if($booking->motorcycle->model)
                                            · {{ $booking->motorcycle->model }}
                                        @endif
                                        · Plat: {{ $booking->motorcycle->license_plate ?? $booking->motorcycle->plate_number ?? '-' }}
                                    </p>

                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-calendar3"></i>
                                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}
                                            →
                                            {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                                        </span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-clock"></i>
                                            {{ $booking->total_days }} Hari
                                        </span>
                                        @if(isset($booking->pickup_method))
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt"></i>
                                                {{ $booking->pickup_method === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil di Pool' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Harga + Status + Tombol --}}
                                <div class="text-md-end">
                                    @php
                                        $statusClass = match($booking->status) {
                                            'pending'   => 'bg-warning-subtle text-warning-emphasis',
                                            'confirmed' => 'bg-success-subtle text-success',
                                            'ongoing'   => 'bg-info-subtle text-info-emphasis',
                                            'completed' => 'bg-primary-subtle text-primary',
                                            'cancelled' => 'bg-danger-subtle text-danger',
                                            default     => 'bg-secondary-subtle text-secondary',
                                        };

                                        $totalAmount = $booking->grand_total ?? $booking->total_price ?? 0;
                                    @endphp

                                    <div class="fw-bold text-primary mb-1 fs-5">
                                        Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                    </div>

                                    @if(($booking->pickup_method ?? '') === 'delivery' && ($booking->delivery_fee ?? 0) > 0)
                                        <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">
                                            (Sewa + Ongkir Rp {{ number_format($booking->delivery_fee, 0, ',', '.') }})
                                        </small>
                                    @endif

                                    <div class="mb-3">
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-1.5 fw-semibold">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>

                                    <a href="{{ route('bookings.show', $booking) }}"
                                       class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-medium">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
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
        <div class="card shadow-sm border-0 rounded-4 bg-white">
            <div class="card-body text-center py-5">
                <div class="mb-3 text-muted">
                    <i class="bi bi-scooter" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-semibold mb-2 text-dark">Belum ada booking</h5>
                <p class="text-muted mb-4 small">
                    Kamu belum pernah melakukan booking motor.
                </p>
                <a href="{{ route('motorcycles.index') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-medium">
                    <i class="bi bi-search me-1"></i> Cari Motor
                </a>
            </div>
        </div>
    @endif

</div>
@endsection