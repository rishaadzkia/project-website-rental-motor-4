<nav class="navbar navbar-expand-lg sticky-top py-2.5" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 8px rgba(0,0,0,0.04);">
    <div class="container d-flex align-items-center justify-content-between">

        {{-- Brand Logo --}}
        <a class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2" href="{{ route('home') }}"
           style="font-size: 1.35rem; color: #1e40af; letter-spacing: -0.02em;">
            ED.RENT
        </a>

        {{-- Area Kanan (Profil + WhatsApp + Toggler Hamburger) --}}
        <div class="d-flex align-items-center gap-2 order-lg-last">

            {{-- Tombol WhatsApp --}}
            <a href="https://wa.me/6281234567890" target="_blank"
               class="btn btn-wa d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-whatsapp"></i>
                <span>Butuh bantuan?</span>
            </a>

            @auth
                {{-- Dropdown Akun (Selalu Tampil) --}}
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent dropdown-toggle hide-arrow d-flex align-items-center gap-2"
                            type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1e40af&color=fff&rounded=true' }}"
                             alt="{{ Auth::user()->name }}"
                             class="rounded-circle shadow-sm border border-2 border-white"
                             style="width: 38px; height: 38px; object-fit: cover;">
                        <i class="bi bi-chevron-down text-muted d-none d-md-inline" style="font-size: 0.7rem;"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2"
                        aria-labelledby="userMenu" style="min-width: 210px;">
                        <li class="px-3 py-2 mb-1">
                            <div class="fw-semibold text-dark small text-truncate" style="max-width: 180px;">{{ Auth::user()->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem; max-width: 180px;">{{ Auth::user()->email ?? '' }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <button type="button" class="dropdown-item rounded-2 py-2 px-3 small fw-medium d-flex align-items-center gap-2 w-100 border-0 bg-transparent text-start"
                                    data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="bi bi-person"></i> Profil saya
                            </button>
                        </li>

                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item rounded-2 py-2 px-3 small fw-medium d-flex align-items-center gap-2"
                                   href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                        @endif

                        <li>
                            <a class="dropdown-item rounded-2 py-2 px-3 small fw-medium d-flex align-items-center gap-2"
                               href="{{ Auth::user()->role === 'admin' ? route('admin.bookings.index') : route('bookings.index') }}">
                                <i class="bi bi-bag"></i> Daftar Pesanan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 px-3 small fw-medium d-flex align-items-center gap-2"
                               href="{{ Auth::user()->role === 'admin' ? route('admin.motorcycles.index') : route('motorcycles.index') }}">
                                <i class="bi bi-scooter"></i> Kendaraan
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <a class="dropdown-item rounded-2 py-2 px-3 small fw-medium d-flex align-items-center gap-2 text-danger"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 px-md-4 rounded-3 fw-semibold shadow-sm">
                    Masuk
                </a>
            @endauth

            {{-- Tombol Hamburger --}}
            <button class="btn border-0 shadow-none p-1 ms-1 d-lg-none" type="button" id="btnNavToggler">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>
        </div>

        {{-- Container Menu Mobile & Desktop --}}
        <div class="mobile-nav-wrapper" id="mobileNavMenu">
            <ul class="navbar-nav align-items-lg-center gap-1 m-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        <i class="bi bi-house-door me-2"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a class="nav-link nav-link-custom {{ request()->routeIs('admin.motorcycles.*') ? 'active' : '' }}"
                               href="{{ route('admin.motorcycles.index') }}">
                                <i class="bi bi-scooter me-2"></i>Pilih Kendaraan
                            </a>
                        @else
                            <a class="nav-link nav-link-custom {{ request()->routeIs('motorcycles.*') ? 'active' : '' }}"
                               href="{{ route('motorcycles.index') }}">
                                <i class="bi bi-scooter me-2"></i>Pilih Kendaraan
                            </a>
                        @endif
                    @else
                        <a class="nav-link nav-link-custom {{ request()->routeIs('motorcycles.*') ? 'active' : '' }}"
                           href="{{ route('motorcycles.index') }}">
                            <i class="bi bi-scooter me-2"></i>Pilih Kendaraan
                        </a>
                    @endauth
                </li>
                <li class="nav-item">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a class="nav-link nav-link-custom {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                               href="{{ route('admin.bookings.index') }}">
                                <i class="bi bi-receipt me-2"></i>Daftar Pesanan
                            </a>
                        @else
                            <a class="nav-link nav-link-custom {{ request()->routeIs('bookings.*') ? 'active' : '' }}"
                               href="{{ route('bookings.index') }}">
                                <i class="bi bi-receipt me-2"></i>Daftar Pesanan
                            </a>
                        @endif
                    @else
                        <a class="nav-link nav-link-custom" href="{{ route('login') }}">
                            <i class="bi bi-receipt me-2"></i>Daftar Pesanan
                        </a>
                    @endauth
                </li>

                {{-- Tombol Bantuan WA khusus HP --}}
                <li class="nav-item d-md-none mt-2 pt-2 border-top border-secondary">
                    <a href="https://wa.me/6281234567890" target="_blank"
                       class="btn btn-wa w-100 justify-content-center py-2">
                        <i class="bi bi-whatsapp"></i>
                        <span>Butuh bantuan?</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>

@auth
{{-- Modal Profil --}}
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-5" id="profileModalLabel">Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img id="avatarPreview"
                             src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1e40af&color=fff&rounded=true' }}"
                             alt="{{ Auth::user()->name }}"
                             class="rounded-circle shadow-sm"
                             style="width: 64px; height: 64px; object-fit: cover;">
                        <label for="avatarInput" class="text-primary small fw-medium d-flex align-items-center gap-1" style="cursor: pointer;">
                            <i class="bi bi-pencil"></i> Edit gambar
                        </label>
                        <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2" value="{{ old('name', Auth::user()->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">No WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control rounded-3 py-2" value="{{ old('no_hp', Auth::user()->no_hp) }}" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">No. KTP (16 Digit)</label>
                        <input type="text" name="no_ktp" class="form-control rounded-3 py-2" value="{{ old('no_ktp', Auth::user()->no_ktp) }}" placeholder="Contoh: 3201234567890001" maxlength="16" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium text-dark">No. SIM C (12 Digit)</label>
                        <input type="text" name="no_sim" class="form-control rounded-3 py-2" value="{{ old('no_sim', Auth::user()->no_sim) }}" placeholder="Contoh: 123456789012" maxlength="12" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Sukses --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow p-3 text-center">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 pb-4 px-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 60px; height: 60px;">
                    <i class="bi bi-check-circle-fill fs-2 text-primary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2 fs-5">Berhasil!</h5>
                <p class="text-muted small mb-4">Data berhasil diperbarui.</p>
                <button type="button" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold small" data-bs-dismiss="modal">
                    Oke
                </button>
            </div>
        </div>
    </div>
</div>
@endauth

<style>
    .hide-arrow::after {
        display: none !important;
    }

    /* Style Navlink Dasar */
    .nav-link-custom {
        color: #475569 !important;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem;
        transition: all 0.18s ease;
        display: flex;
        align-items: center;
    }
    .nav-link-custom:hover {
        color: #1e40af !important;
        background: #eff6ff !important;
    }

    /* GLOBAL ACTIVE STATE (Berlaku untuk Desktop & Mobile) */
    .nav-link-custom.active {
        background: #eff6ff !important;
        color: #1e40af !important;
        font-weight: 600 !important;
    }
    .nav-link-custom.active i {
        color: #1e40af !important;
    }

    /* Media Query Hamburger Mobile & iPad */
    @media (max-width: 991.98px) {
        .mobile-nav-wrapper {
            display: none;
            width: 100%;
            background: #1e293b !important;
            padding: 0.75rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-top: 0.75rem;
        }
        .mobile-nav-wrapper.active {
            display: block !important;
        }

        /* Teks Putih untuk Menu Non-Aktif di Hamburger */
        .mobile-nav-wrapper .nav-link-custom:not(.active) {
            color: #ffffff !important;
            padding: 0.75rem 1rem !important;
        }
        .mobile-nav-wrapper .nav-link-custom:not(.active) i {
            color: #94a3b8 !important;
        }
        .mobile-nav-wrapper .nav-link-custom:not(.active):hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
    }

    @media (min-width: 992px) {
        .mobile-nav-wrapper {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        .nav-link-custom i {
            display: none !important;
        }
    }

    /* Tombol WhatsApp */
    .btn-wa {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.4rem 0.9rem;
        transition: all 0.18s ease;
    }
    .btn-wa:hover {
        background: #dcfce7;
        color: #14532d;
        border-color: #86efac;
    }
    .btn-wa i {
        font-size: 1.05rem;
        color: #22c55e !important;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: #1e40af;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById('btnNavToggler');
        const menuWrapper = document.getElementById('mobileNavMenu');

        if (toggleBtn && menuWrapper) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                menuWrapper.classList.toggle('active');
            });
        }
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('avatarPreview').src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

@if(session('profile_updated'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('successModal')).show();
    });
</script>
@endif