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
    @auth
        @if(auth()->user()->role === 'admin') {{-- Sesuaikan dengan nama kolom role di database kamu (misal: is_admin / role) --}}
            <a class="nav-link text-secondary small p-0" href="{{ route('admin.motorcycles.index') }}">Pilih Kendaraan</a>
        @else
            <a class="nav-link text-secondary small p-0" href="{{ route('motorcycles.index') }}">Pilih Kendaraan</a>
        @endif
    @else
        <a class="nav-link text-secondary small p-0" href="{{ route('motorcycles.index') }}">Pilih Kendaraan</a>
    @endauth
</li>
            <li class="nav-item">
    @auth
        @if(auth()->user()->role === 'admin') {{-- Sesuaikan kolom role (misal: is_admin == 1 atau role === 'admin') --}}
            <a class="nav-link text-secondary small p-0" href="{{ route('admin.bookings.index') }}">Daftar Pesanan</a>
        @else
            <a class="nav-link text-secondary small p-0" href="{{ route('bookings.index') }}">Daftar Pesanan</a>
        @endif
    @else
        <a class="nav-link text-secondary small p-0" href="{{ route('login') }}">Daftar Pesanan</a>
    @endauth
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
                    <button class="btn p-0 border-0 bg-transparent dropdown-toggle hide-arrow d-flex align-items-center" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Avatar User -->
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0D6EFD&color=fff&rounded=true' }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle shadow-sm" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2" aria-labelledby="userMenu" style="min-width: 200px;">
                        <!-- Profil Saya -->
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="bi bi-person fs-6"></i> Profil saya
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right fs-6"></i> Keluar
                            </a>
                        </li>

                        <!-- Hanya tampil jika role Admin -->
                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-gear fs-6"></i> Dashboard
                                </a>
                            </li>
                        @endif

                        <!-- Menu Tambahan -->
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2"
                               href="{{ Auth::user()->role === 'admin' ? route('admin.bookings.index') : route('bookings.index') }}">
                                <i class="bi bi-bag fs-6"></i> Daftar Pesanan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2"
                               href="{{ Auth::user()->role === 'admin' ? route('admin.motorcycles.index') : route('motorcycles.index') }}">
                                <i class="bi bi-scooter fs-6"></i> Kendaraan
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-1"></li>

                        <!-- Keluar -->
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a class="dropdown-item rounded-3 py-2 px-3 small text-secondary fw-medium d-flex align-items-center gap-2 cursor-pointer" 
                               href="#" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-arrow-return-left fs-6"></i> Keluar
                            </a>
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

@auth
<!-- Modal Form Profil Saya -->
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

                    <!-- Area Foto Avatar & Edit Gambar -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img id="avatarPreview" 
                             src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0D6EFD&color=fff&rounded=true' }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle shadow-sm" 
                             style="width: 64px; height: 64px; object-fit: cover;">
                        
                        <label for="avatarInput" class="text-primary text-decoration-none small fw-medium d-flex align-items-center gap-1 cursor-pointer" style="cursor: pointer;">
                            <i class="bi bi-pencil"></i> Edit gambar
                        </label>
                        
                        <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <!-- Input Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2" value="{{ old('name', Auth::user()->name) }}" required>
                    </div>

                    <!-- Input No WhatsApp -->
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">No WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control rounded-3 py-2" value="{{ old('no_hp', Auth::user()->no_hp) }}" placeholder="Contoh: 081234567890">
                    </div>

                    <!-- Input No KTP -->
                    <div class="mb-3">
                        <label class="form-label small fw-medium text-dark">No. KTP</label>
                        <input type="text" name="no_ktp" class="form-control rounded-3 py-2" value="{{ old('no_ktp', Auth::user()->no_ktp) }}" placeholder="Contoh: 3201234567890001">
                    </div>

                    <!-- Input No SIM -->
                    <div class="mb-4">
                        <label class="form-label small fw-medium text-dark">No. SIM</label>
                        <input type="text" name="no_sim" class="form-control rounded-3 py-2" value="{{ old('no_sim', Auth::user()->no_sim) }}" placeholder="Contoh: 123456789012">
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pop-up Sukses -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow p-3 text-center">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 pb-4 px-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-check-circle-fill fs-2 text-primary"></i>
                </div>
                
                <h5 class="fw-bold text-dark mb-2 fs-5">Berhasil!</h5>
                <p class="text-muted small mb-4">Data profil berhasil diperbarui.</p>

                <button type="button" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold small" data-bs-dismiss="modal">
                    Oke
                </button>
            </div>
        </div>
    </div>
</div>
@endauth

<style>
    /* Menghilangkan panah kecil bawaan dropdown tombol */
    .hide-arrow::after {
        display: none !important;
    }
</style>

<script>
    // Preview gambar sebelum diunggah
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

{{-- Cek khusus session profile_updated --}}
@if(session('profile_updated'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif