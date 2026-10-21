@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Ringkasan aktivitas rental motor Anda.</p>
    </div>
    <span class="text-muted small">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Orderan</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalBookings, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Pendapatan Bulan Ini</div>
                    <div class="fs-5 fw-bold">Rp {{ number_format($currentMonthRevenue, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Menunggu Konfirmasi</div>
                    <div class="fs-4 fw-bold">{{ number_format($pendingBookings, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Grafik Pendapatan</h5>
                        <p class="text-muted small mb-0">Pendapatan transaksi dibayar · 6 bulan terakhir</p>
                    </div>
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Lunas</span>
                </div>
                <div style="height: 280px;">
                    <canvas id="revenueChart" aria-label="Grafik pendapatan enam bulan terakhir" role="img"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="mb-3">
                    <h5 class="fw-bold mb-1">Top Orderan</h5>
                    <p class="text-muted small mb-0">Motor paling sering dipesan</p>
                </div>

                @forelse ($topMotorcycles as $index => $motorcycle)
                    <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $motorcycle->name }}</div>
                            <div class="text-muted small">{{ $motorcycle->brand }}</div>
                        </div>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">
                            {{ number_format($motorcycle->orders_count, 0, ',', '.') }} order
                        </span>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-scooter fs-2 d-block mb-2"></i>
                        Belum ada orderan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">Transaksi History</h5>
                <p class="text-muted small mb-0">Orderan terbaru yang masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary btn-sm rounded-3">
                Lihat semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th class="fw-semibold text-nowrap">Kode Order</th>
                        <th class="fw-semibold">Pelanggan</th>
                        <th class="fw-semibold">Motor</th>
                        <th class="fw-semibold text-nowrap">Tanggal</th>
                        <th class="fw-semibold">Total</th>
                        <th class="fw-semibold">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentBookings as $booking)
                        @php
                            $statusStyles = [
                                'pending' => 'bg-warning bg-opacity-10 text-warning-emphasis',
                                'confirmed' => 'bg-success bg-opacity-10 text-success',
                                'ongoing' => 'bg-primary bg-opacity-10 text-primary',
                                'completed' => 'bg-info bg-opacity-10 text-info-emphasis',
                                'cancelled' => 'bg-danger bg-opacity-10 text-danger',
                            ];
                            $statusLabels = [
                                'pending' => 'Menunggu',
                                'confirmed' => 'Dikonfirmasi',
                                'ongoing' => 'Berlangsung',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <tr>
                            <td class="fw-semibold text-nowrap">{{ $booking->booking_code }}</td>
                            <td>{{ $booking->user?->name ?? '-' }}</td>
                            <td>{{ $booking->motorcycle?->name ?? '-' }}</td>
                            <td class="text-nowrap">{{ $booking->created_at?->format('d M Y') ?? '-' }}</td>
                            <td class="fw-semibold text-nowrap">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $statusStyles[$booking->status] ?? 'bg-secondary bg-opacity-10 text-secondary' }}">
                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-light btn-sm rounded-3" aria-label="Lihat detail {{ $booking->booking_code }}">
                                    <i class="bi bi-arrow-up-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada transaksi untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const revenueCanvas = document.getElementById('revenueChart');
    const revenueData = @json($revenueChart);

    new Chart(revenueCanvas, {
        type: 'line',
        data: {
            labels: revenueData.map((month) => month.label),
            datasets: [{
                label: 'Pendapatan',
                data: revenueData.map((month) => month.revenue),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.12)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#0d6efd',
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => ` Rp ${new Intl.NumberFormat('id-ID').format(context.raw)}`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f3f5' },
                    ticks: {
                        callback: (value) => `Rp ${new Intl.NumberFormat('id-ID', {
                            notation: 'compact',
                            maximumFractionDigits: 1,
                        }).format(value)}`,
                    },
                },
                x: { grid: { display: false } },
            },
        },
    });
</script>
@endpush