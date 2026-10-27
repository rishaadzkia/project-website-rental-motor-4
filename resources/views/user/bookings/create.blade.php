
@extends('layouts.app')

@section('title', 'Sewa ' . ($motorcycle->name ?? 'Motor'))

@section('content')
<div class="container py-4">

    <!-- Alert Notifikasi Error -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('motorcycles.show', $motorcycle) }}" class="btn btn-sm btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Motor
        </a>
    </div>

    <div class="row g-4">
        <!-- Info Motor (Kiri) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-white" style="top: 20px;">
                <img src="{{ $motorcycle->image ? asset('storage/' . $motorcycle->image) : asset('images/default-motor.png') }}"
                     alt="{{ $motorcycle->name }}"
                     class="img-fluid rounded-3 object-fit-contain mb-3"
                     style="max-height: 250px; width: 100%;">

                <h4 class="fw-bold text-dark mb-1">{{ $motorcycle->name }}</h4>
                <p class="text-muted small mb-2">{{ $motorcycle->brand }} - {{ $motorcycle->year }}</p>

                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-card-text me-1"></i> {{ $motorcycle->plate_number }}
                    </span>
                    <span class="badge bg-success-subtle text-success fw-semibold">Tersedia</span>
                </div>

                <hr class="text-muted opacity-25">

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Harga Sewa / Hari:</span>
                    <span class="fw-bold fs-5 text-primary">
                        Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Booking (Kanan) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-4">Form Pengajuan Sewa</h5>

                <form action="{{ route('bookings.store', $motorcycle) }}" method="POST" id="booking-form">
                    @csrf
                    <input type="hidden" name="motorcycle_id" value="{{ $motorcycle->id }}">

                    <!-- Tanggal & Jam Mulai -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="start_date" class="form-label small fw-bold text-muted mb-1">Tanggal Mulai Sewa</label>
                            <input type="date"
                                   name="start_date"
                                   id="start_date"
                                   class="form-control rounded-3 bg-light border-0 py-2 @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   onchange="calculateTotal()">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="start_time" class="form-label small fw-bold text-muted mb-1">Jam Ambil Motor</label>
                            <input type="time"
                                   name="start_time"
                                   id="start_time"
                                   class="form-control rounded-3 bg-light border-0 py-2 @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', '08:00') }}"
                                   required
                                   onchange="calculateTotal()">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tanggal & Jam Selesai -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label for="end_date" class="form-label small fw-bold text-muted mb-1">Tanggal Selesai Sewa</label>
                            <input type="date"
                                   name="end_date"
                                   id="end_date"
                                   class="form-control rounded-3 bg-light border-0 py-2 @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   onchange="calculateTotal()">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="end_time" class="form-label small fw-bold text-muted mb-1">Jam Pengembalian</label>
                            <input type="time"
                                   name="end_time"
                                   id="end_time"
                                   class="form-control rounded-3 bg-light border-0 py-2 @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', '08:00') }}"
                                   required
                                   onchange="calculateTotal()">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Estimasi Biaya -->
                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Durasi Sewa:</span>
                            <strong id="display-days" class="text-dark small">1 Hari</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Estimasi Total Biaya Motor:</span>
                            <strong id="display-total" class="text-primary fs-5">
                                Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}
                            </strong>
                        </div>

                        <small class="text-muted extra-small d-block mt-2" style="font-size: 0.75rem;">
                            * Biaya pengantaran (jika ada) dapat dipilih pada halaman selanjutnya saat checkout.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                        Lanjutkan ke Checkout & Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const pricePerDay = {{ $motorcycle->price_per_day }};

    function calculateTotal() {
        const startDateVal = document.getElementById('start_date')?.value;
        const startTimeVal = document.getElementById('start_time')?.value || '00:00';
        const endDateVal = document.getElementById('end_date')?.value;
        const endTimeVal = document.getElementById('end_time')?.value || '00:00';

        if (startDateVal && endDateVal) {
            const start = new Date(`${startDateVal}T${startTimeVal}`);
            const end = new Date(`${endDateVal}T${endTimeVal}`);

            const diffMs = end - start;

            if (diffMs > 0) {
                const diffHours = diffMs / (1000 * 60 * 60);
                let diffDays = Math.ceil(diffHours / 24);

                if (diffDays < 1) diffDays = 1;

                const total = diffDays * pricePerDay;

                const daysEl = document.getElementById('display-days');
                const totalEl = document.getElementById('display-total');

                if (daysEl) daysEl.innerText = diffDays + ' Hari';
                if (totalEl) {
                    totalEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                }
            } else {
                const daysEl = document.getElementById('display-days');
                const totalEl = document.getElementById('display-total');

                if (daysEl) daysEl.innerText = '0 Hari (Waktu tidak valid)';
                if (totalEl) totalEl.innerText = 'Rp 0';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection
```