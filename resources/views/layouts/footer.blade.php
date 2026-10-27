<footer class="pt-5 pb-4" style="background: #0f172a; border-top: 1px solid #1e293b;">
    <div class="container">
        <div class="row gy-4 pb-4">

            {{-- Brand & Deskripsi --}}
            <div class="col-lg-5 col-md-12">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                   
                    <span class="fw-bold fs-4" style="color: #f1f5f9; letter-spacing: -0.02em;">ED.RENT</span>
                </a>
                <p class="mb-0 lh-base" style="color: #94a3b8; font-size: 0.9rem; max-width: 320px;">
                    Sewa motor dengan mudah dan nyaman untuk menemani perjalanan serta aktivitasmu.
                </p>
            </div>

            {{-- Links --}}
            <div class="col-lg-7 col-md-12">
                <div class="row gy-4">

                    <div class="col-6 col-sm-4">
                        <h6 class="fw-bold mb-3" style="color: #e2e8f0; font-size: 0.9rem;">Jelajahi</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                            <li>
                                <a href="{{ route('home') }}" class="footer-link">Beranda</a>
                            </li>
                            @auth
                                @if (auth()->user()->role === 'admin')
                                    <li><a href="{{ route('admin.motorcycles.index') }}" class="footer-link">Kelola motor</a></li>
                                @else
                                    <li><a href="{{ route('motorcycles.index') }}" class="footer-link">Pilih motor</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('register') }}" class="footer-link">Daftar akun</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="col-6 col-sm-4">
                        <h6 class="fw-bold mb-3" style="color: #e2e8f0; font-size: 0.9rem;">Akun</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                            @auth
                                @if (auth()->user()->role === 'admin')
                                    <li><a href="{{ route('admin.bookings.index') }}" class="footer-link">Pesanan masuk</a></li>
                                @else
                                    <li><a href="{{ route('bookings.index') }}" class="footer-link">Pesanan saya</a></li>
                                @endif
                            @else
                                <li><a href="{{ route('login') }}" class="footer-link">Masuk</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="col-12 col-sm-4">
                        <h6 class="fw-bold mb-3" style="color: #e2e8f0; font-size: 0.9rem;">Butuh bantuan?</h6>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer"
                           class="footer-link d-inline-flex align-items-center gap-2">
                            <i class="bi bi-whatsapp" style="color: #22c55e; font-size: 1.1rem;"></i>
                            Hubungi kami
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <hr style="border-color: #334155; opacity: 1; margin: 1.25rem 0;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-1">
            <p class="mb-0" style="color: #64748b; font-size: 0.85rem;">
                &copy; {{ date('Y') }} ED.RENT. Teman perjalananmu.
            </p>
            <span style="color: #64748b; font-size: 0.85rem;">
                Berkendara aman, nikmati perjalanan.
            </span>
        </div>
    </div>
</footer>

<style>
    .footer-link {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.15s ease;
    }
    .footer-link:hover {
        color: #60a5fa;
    }
</style>