<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
    <div class="container d-flex justify-content-between align-items-center">
        
        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold text-primary fs-4 tracking-tight m-0" href="{{ route('home') }}">
            ED.RENT
        </a>

        <!-- Menu Navigasi Tengah -->
        <ul class="navbar-nav d-flex flex-row gap-4 m-0">
            <li class="nav-item">
                <a class="nav-link text-dark fw-medium small p-0" href="{{ route('home') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-muted small p-0" href="#">Pilih Kendaraan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-muted small p-0" href="#">Daftar Pesanan</a>
            </li>
        </ul>

        <!-- Area Kanan (Bantuan & Auth) -->
        <div class="d-flex align-items-center gap-3">
            <!-- Tombol WhatsApp Butuh Bantuan -->
            <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-light text-dark border shadow-sm rounded-2 btn-sm px-3 d-flex align-items-center gap-2 small">
                <i class="bi bi-whatsapp text-success"></i>
                <span class="fw-medium">Butuh bantuan?</span>
            </a>

           @auth
    <!-- Dropdown Akun jika Login -->
    <div class="dropdown">
        <button class="btn btn-primary btn-sm px-3 rounded-2 fw-semibold dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" aria-labelledby="userMenu">
            
            <!-- Hanya tampil jika role Admin -->
            @if(Auth::user()->role === 'admin')
                <li>
                    <a class="dropdown-item small text-primary fw-semibold" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-shield-lock me-2"></i> Panel Admin
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif

            <!-- Logout -->
            <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item small text-danger fw-semibold">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                    </button>
                </form>
            </li>
        </ul>
    </div>
@else
    <!-- Tombol Masuk jika Belum Login -->
    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4 rounded-2 fw-medium">
        Masuk
    </a>
@endauth
        </div>

    </div>
</nav>