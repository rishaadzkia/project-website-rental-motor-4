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
                        {{-- Form jika masih pending --}}
                        <form id="pickup-form" action="{{ route('bookings.update-pickup', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
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

                            <!-- Input Alamat & Pilih Wilayah (Tampil jika memilih 'Diantar ke Lokasi') -->
                            <div class="mb-3 {{ ($booking->pickup_method ?? '') === 'delivery' ? '' : 'd-none' }}" id="address-container">
                                <div class="mb-3">
                                    <label class="form-label small text-muted fw-bold">Pilih Wilayah Pengantaran (Khusus Jakarta)</label>
                                    <select name="delivery_fee" id="delivery_fee" class="form-select rounded-3" onchange="updateTotalPrice()">
                                        <option value="20000" {{ ($booking->delivery_fee ?? 20000) == 20000 ? 'selected' : '' }}>Jakarta Timur (Rp 20.000)</option>
                                        <option value="30000" {{ ($booking->delivery_fee ?? 0) == 30000 ? 'selected' : '' }}>Jakarta Pusat (Rp 30.000)</option>
                                        <option value="30000" {{ ($booking->delivery_fee ?? 0) == 30000 ? 'selected' : '' }}>Jakarta Selatan (Rp 30.000)</option>
                                        <option value="40000" {{ ($booking->delivery_fee ?? 0) == 40000 ? 'selected' : '' }}>Jakarta Utara (Rp 40.000)</option>
                                        <option value="40000" {{ ($booking->delivery_fee ?? 0) == 40000 ? 'selected' : '' }}>Jakarta Barat (Rp 40.000)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label small text-muted fw-bold">Detail Alamat / Nama Hotel / Patokan</label>
                                    <textarea name="pickup_address" id="pickup_address" 
                                              class="form-control rounded-3 @error('pickup_address') is-invalid @enderror" 
                                              rows="2" 
                                              placeholder="Contoh: Jl. Pemuda No. 10, Rawamangun (Lobby Hotel X)">{{ old('pickup_address', $booking->pickup_address ?? '') }}</textarea>
                                    
                                    <!-- Pesan error validasi dari JavaScript / Backend -->
                                    <div id="address-error" class="invalid-feedback d-none">
                                        Alamat pengantaran wajib diisi jika Anda memilih metode 'Diantar ke Lokasi'.
                                    </div>
                                    @error('pickup_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </form>

                    @else
                        {{-- Tampilan Read-Only jika sudah dibayar/selesai --}}
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
                        @if ($booking->status === 'pending')
                            <a href="#" class="text-primary text-decoration-none small fw-semibold">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        @endif
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
                        // Menentukan harga per hari dari objek motor atau kalkulasi
                        $dailyPrice = $booking->motorcycle->price_per_day ?? ($booking->total_days > 0 ? $booking->total_price / $booking->total_days : 0);
                        $subtotalMotor = $dailyPrice * $booking->total_days;
                    @endphp

                    <!-- 1. Harga Motor per Hari x Hari -->
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

                    <!-- 2. Biaya Pengantaran / Delivery Fee -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Biaya Pengantaran</span>
                        <span class="fw-medium text-dark small" id="display-delivery-fee-text">
                            Gratis
                        </span>
                    </div>

                    <!-- 3. Diskon -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Diskon</span>
                        <span class="text-muted small">-</span>
                    </div>

                    <hr class="text-muted opacity-25 my-2">

                    <!-- Total Harga -->
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="fw-bold text-dark fs-6">Total Harga</span>
                        <strong class="text-primary fs-5" id="display-grand-total">
                            Rp {{ number_format($subtotalMotor, 0, ',', '.') }}
                        </strong>
                    </div>

                    <!-- Tombol Aksi / Bayar -->
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
    const basePrice = {{ $subtotalMotor }}; // Subtotal sewa motor murni

    function toggleAddressInput() {
        const deliveryRadio = document.getElementById('pickup_delivery');
        const addressContainer = document.getElementById('address-container');
        
        if (deliveryRadio && addressContainer) {
            if (deliveryRadio.checked) {
                addressContainer.classList.remove('d-none');
            } else {
                addressContainer.classList.add('d-none');
                
                // Reset validasi error jika pindah ke opsi 'Pool'
                const addressInput = document.getElementById('pickup_address');
                const errorElement = document.getElementById('address-error');
                if (addressInput) addressInput.classList.remove('is-invalid');
                if (errorElement) {
                    errorElement.classList.add('d-none');
                    errorElement.classList.remove('d-block');
                }
            }
        }

        // Perbarui total harga saat metode berubah
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

        // Format angka ke Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        };

        // Update UI Teks Ongkir
        const textOngkir = isDelivery ? formatRupiah(deliveryFee) : 'Gratis';
        const displayFeeEl = document.getElementById('display-delivery-fee-text');
        if (displayFeeEl) {
            displayFeeEl.innerText = textOngkir;
            if (isDelivery) {
                displayFeeEl.classList.add('text-primary', 'fw-bold');
            } else {
                displayFeeEl.classList.remove('text-primary', 'fw-bold');
            }
        }

        // Update UI Grand Total
        const displayTotalEl = document.getElementById('display-grand-total');
        if (displayTotalEl) {
            displayTotalEl.innerText = formatRupiah(grandTotal);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Hitung awal total harga saat pertama kali halaman dimuat
        updateTotalPrice();

        const payButton = document.getElementById('pay-button');
        const addressInput = document.getElementById('pickup_address');
        const errorElement = document.getElementById('address-error');

        // Hilangkan error secara otomatis saat user mulai mengetik di textarea alamat
        if (addressInput) {
            addressInput.addEventListener('input', function () {
                if (this.value.trim() !== "") {
                    this.classList.remove('is-invalid');
                    if (errorElement) {
                        errorElement.classList.add('d-none');
                        errorElement.classList.remove('d-block');
                    }
                }
            });
        }

        if (payButton) {
            payButton.addEventListener('click', async function (e) {
                e.preventDefault();

                const deliveryRadio = document.getElementById('pickup_delivery');
                const isDelivery = deliveryRadio ? deliveryRadio.checked : false;
                const deliveryFeeElement = document.getElementById('delivery_fee');

                // --- 1. VALIDASI ALAMAT PENGANTARAN ---
                if (isDelivery) {
                    const addressValue = addressInput ? addressInput.value.trim() : '';

                    if (addressValue === "") {
                        if (addressInput) {
                            addressInput.classList.add('is-invalid');
                            addressInput.focus();
                        }
                        if (errorElement) {
                            errorElement.classList.remove('d-none');
                            errorElement.classList.add('d-block');
                        }
                        return; // Hentikan proses jika alamat kosong
                    }
                }

                // Ubah tampilan tombol saat memproses simpan
                const originalText = payButton.innerHTML;
                payButton.disabled = true;
                payButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status"></span> Menyimpan data...`;

                // --- 2. SIMPAN PILIHAN PENGAMBILAN KE SERVER SEBELUM BAYAR ---
                try {
                    const response = await fetch("{{ route('bookings.update-pickup', $booking) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            _method: "PATCH",
                            pickup_method: isDelivery ? 'delivery' : 'pool',
                            pickup_address: isDelivery ? addressInput.value.trim() : null,
                            delivery_fee: isDelivery ? (deliveryFeeElement ? deliveryFeeElement.value : 0) : 0
                        })
                    });

                    if (!response.ok) {
                        throw new Error("Gagal menyimpan data lokasi pengambilan.");
                    }
                } catch (err) {
                    alert("Terjadi kesalahan saat menyimpan pilihan pengambilan: " + err.message);
                    payButton.disabled = false;
                    payButton.innerHTML = originalText;
                    return;
                }

                // --- 3. JALANKAN MIDTRANS SNAP ---
                const snapToken = @json($snapToken ?? null);

                if (!snapToken || snapToken === "") {
                    alert("Gagal Membuka Pembayaran!\n\nSnap Token tidak ditemukan di database.");
                    payButton.disabled = false;
                    payButton.innerHTML = originalText;
                    return;
                }

                if (typeof window.snap === 'undefined') {
                    alert("Gagal Memuat Midtrans SDK!\n\nScript snap.js gagal diunduh. Cek koneksi internet.");
                    payButton.disabled = false;
                    payButton.innerHTML = originalText;
                    return;
                }

                payButton.disabled = false;
                payButton.innerHTML = originalText;

                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=settlement";
                    },
                    onPending: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=pending";
                    },
                    onError: function (result) {
                        window.location.href = "{{ route('bookings.status', $booking) }}?transaction_status=deny";
                    },
                    onClose: function () {
                        window.location.href = "{{ route('bookings.status', $booking) }}";
                    }
                });
            });
        }
    });
</script>

@endsection