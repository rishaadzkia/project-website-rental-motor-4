@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold text-dark mb-4">Detail Pemesanan</h3>

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Pemesan & Pilihan Lokasi Pengambilan -->
        <div class="col-lg-7">
            <div class="d-flex flex-column gap-4">
                
                <!-- Card 1: Data Pemesan -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-4">Pemesan</h5>

                    <div class="d-flex flex-column gap-3 fs-6">
                        <div class="row">
                            <div class="col-4 text-muted">Nama</div>
                            <div class="col-8 fw-semibold text-dark">: {{ $booking->user->name ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted">No. Whatsapp</div>
                            <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_hp ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted">No. KTP</div>
                            <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_ktp ?? '-' }}</div>
                        </div>
                        <div class="row">
                            <div class="col-4 text-muted">No. SIM</div>
                            <div class="col-8 fw-semibold text-dark">: {{ $booking->user->no_sim ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2">
                        <small class="text-muted" style="font-size: 0.8rem;">
                            Kamu bisa mengubah data di atas lewat profilmu di menu Akun.
                        </small>
                    </div>
                </div>

                <!-- Card 2: Metode & Lokasi Pengambilan Motor -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3">Metode Pengambilan Motor</h5>

                    @if ($booking->status === 'pending')
                        <form id="pickup-form">
                            @csrf
                            
                            <div class="mb-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="pickup_method" id="pickup_pool" value="pool" 
                                           {{ ($booking->pickup_method ?? 'pool') === 'pool' ? 'checked' : '' }} onchange="toggleAddressInput()">
                                    <label class="form-check-label fw-semibold text-dark" for="pickup_pool">
                                        Ambil Sendiri di Pool / Garasi
                                    </label>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Garasi Utama: Jakarta Timur (Gratis Ongkir)
                                    </small>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pickup_method" id="pickup_delivery" value="delivery" 
                                           {{ ($booking->pickup_method ?? '') === 'delivery' ? 'checked' : '' }} onchange="toggleAddressInput()">
                                    <label class="form-check-label fw-semibold text-dark" for="pickup_delivery">
                                        Diantar ke Lokasi (Hotel / Stasiun / Tempat Tinggal)
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3 {{ ($booking->pickup_method ?? '') === 'delivery' ? '' : 'd-none' }}" id="address-container">
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">Pilih Wilayah Pengantaran (Khusus Jakarta)</label>
                                    <select name="delivery_fee" id="delivery_fee" class="form-select rounded-3" onchange="updateTotalPrice()">
                                        <option value="20000" {{ (string)($booking->delivery_fee ?? 20000) === '20000' ? 'selected' : '' }}>Jakarta Timur (Rp 20.000)</option>
                                        <option value="30000" {{ (string)($booking->delivery_fee ?? 0) === '30000' ? 'selected' : '' }}>Jakarta Pusat / Selatan (Rp 30.000)</option>
                                        <option value="40000" {{ (string)($booking->delivery_fee ?? 0) === '40000' ? 'selected' : '' }}>Jakarta Utara / Barat (Rp 40.000)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label small text-muted fw-bold">Detail Alamat / Nama Hotel / Patokan</label>
                                    <textarea name="pickup_address" id="pickup_address" 
                                              class="form-control rounded-3" 
                                              rows="2" 
                                              placeholder="Contoh: Jl. Pemuda No. 10, Rawamangun (Lobby Hotel X)">{{ old('pickup_address', $booking->pickup_address ?? '') }}</textarea>
                                </div>
                            </div>
                        </form>

                    @else
                        <div class="fs-6">
                            <p class="mb-1 text-muted">Metode:</p>
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">
                                {{ ($booking->pickup_method ?? 'pool') === 'delivery' ? 'Diantar ke Lokasi' : 'Ambil Sendiri di Pool' }}
                            </span>

                            @if (($booking->pickup_method ?? '') === 'delivery')
                                <div class="mt-3">
                                    <p class="mb-1 text-muted">Alamat Pengantaran:</p>
                                    <p class="fw-semibold text-dark mb-0">{{ $booking->pickup_address ?? '-' }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Kolom Kanan: Card Durasi & Ringkasan Transaksi -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4">

                <!-- Card 1: Durasi & Kendaraan -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">
                            Durasi sewa : <strong>{{ $booking->total_days }} hari</strong>
                        </span>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Tanggal sewa</span>
                            <strong class="d-block text-dark small">
                                {{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('D, d M Y') }}
                            </strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted extra-small d-block mb-1" style="font-size: 0.75rem;">Selesai sewa</span>
                            <strong class="d-block text-dark small">
                                {{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('D, d M Y') }}
                            </strong>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 my-2">

                    <!-- Info Kendaraan -->
                    <div class="d-flex align-items-center gap-3 pt-2">
                        <img src="{{ $booking->motorcycle && $booking->motorcycle->image ? asset('storage/' . $booking->motorcycle->image) : asset('images/default-motor.png') }}" 
                             alt="{{ $booking->motorcycle->name ?? 'Motor' }}" 
                             class="img-fluid rounded-3 object-fit-cover" 
                             style="width: 80px; height: 60px;">
                        
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $booking->motorcycle->name ?? 'Nama Motor' }}</h6>
                            <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">
                                {{ $booking->motorcycle->type ?? 'Matic' }}
                            </span>
                            <span class="badge bg-light text-muted border border-secondary border-opacity-25 mt-1" style="font-size: 0.7rem;">
                                {{ $booking->motorcycle->plate_number ?? 'B 1234 CD' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Ringkasan Transaksi -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-dark mb-3">Ringkasan Transaksi</h6>

                    @php
                        $subtotalMotor = $booking->total_price;
                        $deliveryFee = $booking->pickup_method === 'delivery' ? ($booking->delivery_fee ?? 0) : 0;
                        $grandTotal = $subtotalMotor + $deliveryFee;
                        $dailyPrice = $booking->motorcycle->price_per_day ?? ($booking->total_days > 0 ? $subtotalMotor / $booking->total_days : 0);
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">
                            Sewa Motor 
                            <small class="d-block text-muted" style="font-size: 0.75rem;">
                                (Rp {{ number_format($dailyPrice, 0, ',', '.') }} × {{ $booking->total_days }} hari)
                            </small>
                        </span>
                        <span class="fw-medium text-dark small">
                            Rp {{ number_format($subtotalMotor, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Biaya Pengantaran</span>
                        <span class="fw-medium text-dark small" id="display-delivery-fee-text">
                            {{ $deliveryFee > 0 ? 'Rp ' . number_format($deliveryFee, 0, ',', '.') : 'Gratis' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Diskon</span>
                        <span class="text-muted small">-</span>
                    </div>

                    <hr class="text-muted opacity-25 my-2">

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="fw-bold text-dark fs-6">Total Harga</span>
                        <strong class="text-primary fs-5" id="display-grand-total">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="mt-4">
                        @if ($booking->status === 'pending')
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="mb-2" onsubmit="return confirm('Yakin ingin membatalkan pesanan?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-3 py-2 fw-semibold btn-sm">
                                    Batalkan Pesanan
                                </button>
                            </form>

                            <button type="button" id="pay-button" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                                Bayar Sekarang
                            </button>
                        @elseif (in_array($booking->status, ['confirmed', 'paid', 'success']))
                            <a href="{{ route('bookings.status', $booking) }}" class="btn btn-success w-100 rounded-3 py-2 fw-semibold text-decoration-none d-block text-center">
                                <i class="bi bi-check-circle-fill me-1"></i> Lihat Status Pembayaran
                            </a>
                        @elseif ($booking->status === 'cancelled')
                            <div class="alert alert-danger text-center fw-bold rounded-3 mb-0 py-2">
                                Pesanan Dibatalkan
                            </div>
                        @else
                            <button class="btn bg-light text-muted w-100 rounded-3 py-2 fw-semibold" disabled>
                                Status: {{ ucfirst($booking->status) }}
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- MIDTRANS SCRIPT INTEGRATION -->
@php
    $isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
    $snapUrl = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN)
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script type="text/javascript">
    const basePrice = {{ $subtotalMotor }};
    let currentSnapToken = "{{ $snapToken ?? '' }}";

    function toggleAddressInput() {
        const deliveryRadio = document.getElementById('pickup_delivery');
        const addressContainer = document.getElementById('address-container');
        
        if (deliveryRadio && addressContainer) {
            if (deliveryRadio.checked) {
                addressContainer.classList.remove('d-none');
            } else {
                addressContainer.classList.add('d-none');
            }
        }
        updateTotalPrice();
    }

    function updateTotalPrice() {
        const deliveryRadio = document.getElementById('pickup_delivery');
        const isDelivery = deliveryRadio ? deliveryRadio.checked : false;
        
        let deliveryFee = 0;
        const deliveryFeeElement = document.getElementById('delivery_fee');

        if (isDelivery && deliveryFeeElement) {
            deliveryFee = parseInt(deliveryFeeElement.value) || 0;
        }

        const grandTotal = basePrice + deliveryFee;

        const formatRupiah = (number) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        };

        const displayFeeEl = document.getElementById('display-delivery-fee-text');
        if (displayFeeEl) {
            displayFeeEl.innerText = isDelivery ? formatRupiah(deliveryFee) : 'Gratis';
        }

        const displayTotalEl = document.getElementById('display-grand-total');
        if (displayTotalEl) {
            displayTotalEl.innerText = formatRupiah(grandTotal);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleAddressInput();

        const payButton = document.getElementById('pay-button');
        
        if (payButton) {
            payButton.addEventListener('click', async function () {
                const isDelivery = document.getElementById('pickup_delivery')?.checked;
                const pickupAddress = document.getElementById('pickup_address')?.value;
                const deliveryFee = document.getElementById('delivery_fee')?.value;

                if (isDelivery && (!pickupAddress || pickupAddress.trim().length < 10)) {
                    alert('Silakan isi detail alamat pengantaran minimal 10 karakter.');
                    document.getElementById('pickup_address')?.focus();
                    return;
                }

                payButton.disabled = true;
                payButton.innerText = 'Menyiapkan Pembayaran...';

                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PATCH');
                    formData.append('pickup_method', isDelivery ? 'delivery' : 'pool');
                    formData.append('pickup_address', isDelivery ? pickupAddress : '');
                    formData.append('delivery_fee', isDelivery ? deliveryFee : 0);

                    const updateResponse = await fetch("{{ route('bookings.update-pickup', $booking) }}", {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await updateResponse.json();

                    if (!updateResponse.ok || !result.success) {
                        throw new Error(result.message || 'Gagal memperbarui metode pengantaran.');
                    }

                    if (result.snap_token) {
                        currentSnapToken = result.snap_token;
                    }

                    if (currentSnapToken) {
                        snap.pay(currentSnapToken, {
                            onSuccess: function(result) { 
                                window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '') + "&transaction_status=" + (result.transaction_status || 'settlement'); 
                            },
                            onPending: function(result) { 
                                // Redirect ke halaman status saat pending
                                window.location.href = "{{ route('bookings.status', $booking) }}?order_id=" + (result.order_id || '');
                            },
                            onError: function(result) { 
                                alert("Pembayaran gagal atau dibatalkan."); 
                                payButton.disabled = false;
                                payButton.innerText = 'Bayar Sekarang';
                            },
                            onClose: function() { 
                                // DIUBAH: Saat user close pop-up, langsung alihkan ke halaman status/countdown!
                                window.location.href = "{{ route('bookings.status', $booking) }}";
                            }
                        });
                    } else {
                        alert('Token gagal dimuat. Silakan muat ulang halaman.');
                        window.location.reload();
                    }

                } catch (error) {
                    alert(error.message || 'Terjadi kesalahan sistem.');
                    payButton.disabled = false;
                    payButton.innerText = 'Bayar Sekarang';
                }
            });
        }
    });
</script>
@endsection