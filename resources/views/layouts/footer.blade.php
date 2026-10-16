<footer class="bg-white pt-5 pb-4 border-top">
    <div class="container">
        <div class="row gy-4 pb-4">
            <div class="col-lg-5 col-md-12">
                <a href="{{ route('home') }}" class="navbar-brand fw-bold text-primary fs-3 d-block mb-3">
                    ED.RENT
                </a>
                <p class="text-secondary small mb-0 lh-base" style="max-width: 320px;">
                    Sewa motor dengan mudah dan nyaman untuk menemani perjalanan serta aktivitasmu.
                </p>
            </div>

            <div class="col-lg-7 col-md-12">
                <div class="row gy-4">
                    <div class="col-6 col-sm-4">
                        <h6 class="fw-bold text-dark mb-3">Jelajahi</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                            <li><a href="{{ route('home') }}" class="text-secondary text-decoration-none small">Beranda</a></li>
                            @auth
                                @if (auth()->user()->role === 'admin')
                                    <li><a href="{{ route('admin.motorcycles.index') }}" class="text-secondary text-decoration-none small">Kelola motor</a></li>
                                @else
                                    <li><a href="{{ route('motorcycles.index') }}" class="text-secondary text-decoration-none small">Pilih motor</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('register') }}" class="text-secondary text-decoration-none small">Daftar akun</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="col-6 col-sm-4">
                        <h6 class="fw-bold text-dark mb-3">Akun</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                            @auth
                                @if (auth()->user()->role === 'admin')
                                    <li><a href="{{ route('admin.bookings.index') }}" class="text-secondary text-decoration-none small">Pesanan masuk</a></li>
                                @else
                                    <li><a href="{{ route('bookings.index') }}" class="text-secondary text-decoration-none small">Pesanan saya</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('login') }}" class="text-secondary text-decoration-none small">Masuk</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="col-12 col-sm-4">
                        <h6 class="fw-bold text-dark mb-3">Butuh bantuan?</h6>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="text-secondary text-decoration-none small">
                            <i class="bi bi-whatsapp text-success me-1"></i> Hubungi kami
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="text-secondary opacity-25 my-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-2">
            <p class="text-secondary small mb-0">&copy; {{ date('Y') }} ED.RENT. Teman perjalananmu.</p>
            <span class="text-secondary small">Berkendara aman, nikmati perjalanan.</span>
        </div>
    </div>
</footer>