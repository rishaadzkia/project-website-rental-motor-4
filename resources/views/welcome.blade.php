@extends('layouts.app')

@section('title', 'Sewa Motor Mudah & Cepat')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            
            <!-- Alert Notifikasi Berhasil -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 text-start shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Alert Notifikasi Gagal / Akses Ditolak -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 text-start shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <h1 class="fw-bold text-dark mb-3 display-6">
                Sewa Motor Mudah & Cepat untuk Liburanmu
            </h1>
            <p class="text-muted mb-4 leading-relaxed">
                Pesan motor favoritmu tanpa ribet. Silakan masuk ke akunmu atau daftar terlebih dahulu untuk memulai pemesanan.
            </p>

            <div class="d-flex justify-content-center gap-3">
                @auth
                    <!-- Tombol untuk user yang sudah login -->
                    <a href="#pilih-kendaraan" class="btn btn-primary btn-lg px-4 rounded-3 shadow-sm fs-6">
                        <i class="bi bi-scooter me-1"></i> Pilih Kendaraan
                    </a>
                @else
                    <!-- Tombol untuk guest / belum login -->
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 rounded-3 shadow-sm fs-6">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-3 fs-6">
                        Masuk Akun 
                    </a>
                @endauth 
            </div>
        </div>
    </div>
</div>
@endsection