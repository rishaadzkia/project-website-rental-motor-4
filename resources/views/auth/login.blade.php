@extends('layouts.auth')

@section('title', 'Masuk - ED.RENT')

@section('content')
<div class="container-fluid p-0 min-vh-100 d-flex align-items-center bg-light">
    <div class="row g-0 w-100 min-vh-100">
        
        <!-- Kolom Kiri: Banner Hero Gambar -->
        <div class="col-lg-6 d-none d-lg-block position-relative bg-dark">
            <img src="{{ asset('images/hero-rental.jpg') }}" alt="ED.RENT Banner" class="w-100 h-100 object-fit-cover opacity-75">
            
            <div class="position-absolute top-0 start-0 p-5 text-white d-flex flex-column justify-content-between h-100" style="z-index: 2;">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="text-white text-decoration-none fw-bold fs-4 tracking-wider">
                    ED.RENT
                </a>

                <!-- Title Overlay -->
                <div class="mb-5">
                    <h1 class="fw-bold display-5 mb-0 lh-sm">
                        Rental Motor<br>
                        Cepat & Aman,<br>
                        Mulai <span class="text-white">Rp75.000/</span><br>
                        hari
                    </h1>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Login -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-sm-5 bg-white">
            <div class="w-100" style="max-width: 420px;">
                
                <!-- Header / Title -->
                <div class="mb-4">
                    <h2 class="fw-bold text-dark h3 mb-2">Masuk</h2>
                    <p class="text-muted small">
                        Siap keliling Jogja lagi? Masuk untuk melanjutkan booking motor Anda.
                    </p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Input No. WhatsApp -->
                    <div class="mb-3">
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="No. WhatsApp" 
                            class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 px-3 py-2-5 @error('no_hp') is-invalid @enderror">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div class="mb-3 position-relative">
                        <input type="password" name="password" id="password" placeholder="Password" 
                            class="form-control form-control-lg fs-6 rounded-3 bg-light border-0 px-3 py-2-5 pe-5 @error('password') is-invalid @enderror">
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 text-muted role-button" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Options: Ingatkan Saya & Lupa Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check m-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Ingatkan saya
                            </label>
                        </div>
                        <a href="#" class="small text-primary text-decoration-none fw-medium">Lupa password?</a>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fs-6 fw-semibold py-2-5">
                        Masuk
                    </button>
                </form>

                <!-- Divider Cara Lain -->
                <div class="position-relative text-center my-4">
                    <hr class="text-muted opacity-25 m-0">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 extra-small text-muted fw-semibold" style="font-size: 0.75rem;">
                        CARA LAIN
                    </span>
                </div>

                <!-- Tombol Google -->
                <a href="#" class="btn btn-outline-light text-dark w-100 rounded-3 border py-2-5 d-flex align-items-center justify-content-center gap-2 mb-4">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span class="small fw-semibold">Masuk dengan Google</span>
                </a>

                <!-- Footer Link -->
                <p class="text-center small text-muted mb-0">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Buat akun</a>
                </p>

            </div>
        </div>
    </div>
</div>

<!-- JS untuk Toggle Password & Clear Invalid Class -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        }

        // Auto hide error class when user typing
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                this.classList.remove('is-invalid');
                const parent = this.parentElement;
                const feedback = parent.querySelector('.invalid-feedback');
                if (feedback) feedback.style.display = 'none';
            });
        });
    });
</script>
@endsection