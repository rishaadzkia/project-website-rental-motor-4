@extends('layouts.app')

@section('title', 'ED.RENT - Rental Motor Cepat & Aman')

@section('content')
<style>
    :root {
        --primary: #1e40af;
        --primary-light: #3b82f6;
        --card-bg: #ffffff;
        --text-main: #0f172a;
        --text-secondary: #475569;
        --text-muted: #64748b;
        --border: #e2e8f0;
    }

    .landing-page {
        max-width: 1140px;
        margin: 0 auto;
    }

    /* ========== HERO ========== */
    .hero-banner {
        position: relative;
        border-radius: 1.25rem;
        overflow: hidden;
        min-height: 380px;
        background: url('{{ asset("images/hero-rental.jpg") }}') center/cover no-repeat;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0.55) 55%, rgba(15, 23, 42, 0.15) 100%);
        display: flex;
        align-items: center;
        padding: 3rem 2.75rem;
    }
    .hero-overlay h1 {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 800;
        line-height: 1.25;
        color: #ffffff;
        margin-bottom: 1rem;
    }
    .hero-overlay p {
        font-size: 1.05rem;
        color: #e2e8f0;
        line-height: 1.65;
        max-width: 520px;
        margin: 0;
    } 

    /* ========== SECTION TITLE ========== */
    .section-title {
        color: #f1f5f9 !important;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .section-subtitle {
        color: #94a3b8 !important;
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    /* ========== STEP CARDS ========== */
    .step-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.35rem 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
    }
    .step-icon {
        width: 44px;
        height: 44px;
        border-radius: 0.75rem;
        background: #eff6ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
    }
    .step-card h6 {
        color: var(--text-main) !important;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .step-card p {
        color: var(--text-secondary) !important;
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.5;
    }

    /* ========== MOTOR CARDS ========== */
    .motor-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.22s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .motor-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }
    .motor-name {
        color: var(--text-main) !important;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.15rem;
    }
    .motor-type {
        color: var(--text-muted) !important;
        font-size: 0.78rem;
    }
    .motor-img-wrapper {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 0.75rem;
        margin: 0.85rem 0 1rem;
    }
    .motor-img-wrapper img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .motor-meta {
        font-size: 0.78rem;
        color: var(--text-secondary) !important;
    }
    .motor-price {
        color: var(--text-main) !important;
        font-weight: 700;
        font-size: 1.05rem;
    }
    .motor-price span {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-muted) !important;
    }
    .badge-available {
        background: #dcfce7;
        color: #166534;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }
    .badge-rented {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
    }
    .motor-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* ========== TERMS ========== */
    .terms-box {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 1rem;
        padding: 1.75rem 2rem; 
    }
    .terms-box ol {
        padding-left: 1.25rem;
        margin: 0;
    }
    .terms-box li {
        color: #cbd5e1;
        font-size: 0.9rem;
        line-height: 1.65;
        margin-bottom: 0.9rem;
    }
    .terms-box li:last-child {
        margin-bottom: 0;
    }
    .terms-box strong {
        color: #f1f5f9;
    }

    .link-all {
        color: #60a5fa !important;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.15s;
    }
    .link-all:hover {
        color: #93c5fd !important;
    }
</style>

<div class="container py-4 landing-page">

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 1. HERO --}}
    <section class="hero-banner mb-5">
        <div class="hero-overlay">
            <div>
                <h1>
                    Rental Motor Cepat & Aman,<br>
                    Mulai Rp75.000/hari
                </h1>
                <p>
                    Pilihan terlengkap, harga transparan, dan layanan 24 jam.
                    Siap menemani setiap petualangan Anda.
                </p>
            </div>
        </div>
    </section>

    {{-- 2. 3 LANGKAH --}}
    <section class="mb-5">
        <div class="mb-4">
            <h3 class="section-title">Mulai Petualangan Anda dalam 3 Langkah Mudah</h3>
            <p class="section-subtitle">Tidak perlu repot. Proses penyewaan motor tercepat, paling aman, dan transparan.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="step-card d-flex align-items-start gap-3">
                    <div class="step-icon">
                        <i class="bi bi-credit-card-2-front"></i>
                    </div>
                    <div>
                        <h6>Pilih & Booking</h6>
                        <p>Pilih motor impian Anda dan tentukan tanggal rental.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card d-flex align-items-start gap-3">
                    <div class="step-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h6>Konfirmasi & Pembayaran</h6>
                        <p>Selesaikan pembayaran dan dapatkan konfirmasi instan via email/WhatsApp.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card d-flex align-items-start gap-3">
                    <div class="step-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <h6>Ambil / Diantar</h6>
                        <p>Ambil motor di lokasi kami, atau konfirmasi melalui WhatsApp untuk diantar ke lokasi Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. MOTOR TERPOPULER --}}
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <h3 class="section-title">Pilihan Motor Terpopuler</h3>
                <p class="section-subtitle">Pilih kendaraan yang paling sesuai dengan gaya perjalanan dan budget Anda. Semua motor terawat, siap jalan!</p>
            </div>
            <a href="{{ route('motorcycles.index') }}" class="link-all">
                Lihat semua →
            </a>
        </div>

        <div class="row g-3">
            @forelse($motorcycles ?? [] as $motor)
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="motor-card">
                        <div class="flex-grow-1">
                            <div class="motor-name text-truncate">{{ $motor->name }}</div>
                            <div class="motor-type">{{ $motor->type ?? $motor->model ?? 'Matic' }}</div>

                            <div class="motor-img-wrapper">
                                <img src="{{ $motor->image ? asset('storage/' . $motor->image) : asset('images/default-motor.png') }}"
                                     alt="{{ $motor->name }}"
                                     loading="lazy">
                            </div>

                            <div class="d-flex justify-content-between align-items-center motor-meta mb-3">
                                <span>
                                    <i class="bi bi-card-heading me-1"></i>
                                    {{ $motor->plate_number ?? $motor->license_plate ?? '-' }}
                                </span>
                                @if(($motor->status ?? 'available') === 'available')
                                    <span class="badge-available">Tersedia</span>
                                @else
                                    <span class="badge-rented">Disewa</span>
                                @endif
                            </div>
                        </div>

                        {{-- Footer: Harga Kiri | Tombol Kanan Disesuaikan Per Role --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div class="motor-price">
                                Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}
                                <span>/ hari</span>
                            </div>
                            <div class="motor-actions">
                                {{-- Tombol Detail (Mata) -- Tampil untuk Semua Role --}}
                                <a href="{{ route('motorcycles.show', $motor) }}"
                                   class="btn btn-outline-primary btn-sm rounded-3 px-2.5 py-1.5 fw-medium"
                                   title="Lihat Detail">
                                    <i class="bi bi-eye"></i> 
                                </a>

                                {{-- Tombol Booking -- HANYA Tampil jika BUKAN Admin --}}
                                @unless (Auth::check() && (Auth::user()->is_admin || (Auth::user()->role ?? '') === 'admin'))
                                    <a href="{{ route('bookings.create', $motor) }}"
                                       class="btn btn-primary btn-sm px-3 rounded-3 fw-semibold">
                                        Booking
                                    </a>
                                @endunless
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary text-center rounded-4 py-4 mb-0">
                        Belum ada data motor yang tersedia saat ini.
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- 4. SYARAT & KETENTUAN --}}
    <section class="mb-5">
        <div class="mb-4">
            <h3 class="section-title">Syarat & Ketentuan Rental Motor</h3>
            <p class="section-subtitle">Transparansi adalah kunci. Pastikan Anda memenuhi beberapa persyaratan dasar sebelum melakukan pemesanan.</p>
        </div>

        <div class="terms-box">
            <ol>
                <li>
                    Lakukan pemesanan minimal <strong>1×24 jam</strong> sebelum waktu penggunaan. Setelah pesanan dikonfirmasi, penyewa wajib melakukan konfirmasi pengambilan motor melalui nomor WhatsApp atau datang ke lokasi ED.RENT.
                </li>
                <li>
                    Membawa <strong>2 kartu identitas</strong> (SIM A, KK, KTP, ID Kerja atau ID Pelajar).
                </li>
                <li>
                    Menyertakan akun sosial media dan <strong>nomor WhatsApp</strong> yang aktif.
                </li>
                <li>
                    Pada saat serah terima kendaraan, pihak penyedia rental akan melakukan <strong>pengecekan keaslian data</strong> dan kondisi kendaraan.
                </li>
                <li>
                    Pihak penyedia rental <strong>berhak membatalkan</strong> apabila data tidak sesuai dan syarat sewa tidak dapat dilengkapi.
                </li>
                <li>
                    Apabila pembayaran telah selesai dan kendaraan yang dipesan tidak tersedia, penyewa <strong>berhak memilih kendaraan lain</strong> yang tersedia tanpa dikenakan biaya tambahan.
                </li>
            </ol>
        </div>
    </section>

</div>
@endsection