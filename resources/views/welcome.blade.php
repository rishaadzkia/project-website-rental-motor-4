@extends('layouts.app')

@section('title', 'Sewa Motor Mudah & Cepat')

@section('content')
<div class="home-page">
    <div class="container py-4 py-lg-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="home-hero row align-items-center g-4 g-lg-5 py-3 py-lg-4">
            <div class="col-lg-5">
                <span class="home-eyebrow"><i class="bi bi-compass-fill"></i> PERJALANANMU, CARA KAMU</span>
                <h1 class="home-title mt-4 mb-3">
                    Jelajahi lebih jauh,<br>
                    <span>mulai dari sini.</span>
                </h1>
                <p class="home-intro mb-4">
                    Temukan motor yang pas untuk perjalananmu. Proses mudah, pilihan nyaman, dan siap menemani setiap tujuan.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('motorcycles.index') }}" class="btn home-button-primary">
                            Pilih motor <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn home-button-primary">
                            Mulai sewa <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn home-button-secondary">Masuk akun</a>
                    @endauth
                </div>

                <div class="home-trust mt-4">
                    <span class="home-trust-icon"><i class="bi bi-shield-check"></i></span>
                    <span><strong>Praktis dan terpercaya</strong><small>Pesan dengan nyaman, berkendara dengan tenang.</small></span>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="home-visual">
                    <img src="{{ asset('images/hero-rental.jpg') }}" alt="Pengendara menikmati perjalanan dengan motor">
                    <div class="home-visual-shade"></div>
                    <div class="home-photo-label">
                        <span class="home-photo-icon"><i class="bi bi-geo-alt-fill"></i></span>
                        <span><strong>Mulai petualanganmu</strong><small>Jalan terbuka, pengalaman baru.</small></span>
                    </div>
                    <div class="home-photo-mark"><i class="bi bi-scooter"></i> ED.RENT</div>
                </div>
            </div>
        </section>

        <section class="home-benefits row g-3 g-lg-4 mt-4 mt-lg-5" aria-label="Keunggulan layanan">
            <div class="col-md-4">
                <article class="home-benefit-card h-100">
                    <span class="home-benefit-icon"><i class="bi bi-lightning-charge"></i></span>
                    <h2>Pemesanan praktis</h2>
                    <p>Pilih motor dan ajukan pesanan dengan langkah yang sederhana.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="home-benefit-card h-100">
                    <span class="home-benefit-icon"><i class="bi bi-wallet2"></i></span>
                    <h2>Harga transparan</h2>
                    <p>Lihat harga sewa sebelum memesan, tanpa kejutan di akhir.</p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="home-benefit-card h-100">
                    <span class="home-benefit-icon"><i class="bi bi-signpost-split"></i></span>
                    <h2>Siap ke mana saja</h2>
                    <p>Temukan kendaraan yang sesuai untuk aktivitas dan tujuanmu.</p>
                </article>
            </div>
        </section>

        <section class="home-steps mt-5 mt-lg-6">
            <div class="row align-items-end g-3 mb-4">
                <div class="col-md-8">
                    <span class="home-section-kicker">MUDAH DARI AWAL</span>
                    <h2 class="home-section-title mt-2 mb-0">Tiga langkah menuju perjalananmu</h2>
                </div>
                <div class="col-md-4 text-md-end">
                    @auth
                        <a href="{{ route('motorcycles.index') }}" class="home-text-link">Lihat pilihan motor <i class="bi bi-arrow-right"></i></a>
                    @else
                        <a href="{{ route('register') }}" class="home-text-link">Buat akun sekarang <i class="bi bi-arrow-right"></i></a>
                    @endauth
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="home-step-card">
                        <span class="home-step-number">01</span>
                        <div><h3>Pilih motor</h3><p>Temukan motor yang cocok untuk kebutuhanmu.</p></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="home-step-card">
                        <span class="home-step-number">02</span>
                        <div><h3>Ajukan pesanan</h3><p>Lengkapi detail perjalanan dan kirim permintaan sewa.</p></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="home-step-card">
                        <span class="home-step-number">03</span>
                        <div><h3>Siap berangkat</h3><p>Periksa status pesananmu dan bersiap untuk perjalanan.</p></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
