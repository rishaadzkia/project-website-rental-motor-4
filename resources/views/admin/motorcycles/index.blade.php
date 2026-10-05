@extends('layouts.admin')

@section('title', 'Kelola Motor')

@section('content')
<div class="container py-4">

    <!-- Header & Tombol Trigger Modal Tambah -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold">Kelola Motor</h1>
        <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-medium" data-bs-toggle="modal" data-bs-target="#createMotorModal">
            + Tambah Motor
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Filter & Pencarian -->
    <form method="GET" action="{{ route('admin.motorcycles.index') }}" class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
        <div class="row g-3 align-items-end">
            <!-- Filter Status -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Status</label>
                <select name="status" class="form-select bg-light border-0 py-2 rounded-3" onchange="this.form.submit()">
                    <option value="semua" @selected(request('status') == 'semua')>Semua Status</option>
                    <option value="available" @selected(request('status') == 'available')>Tersedia</option>
                    <option value="rented" @selected(request('status') == 'rented')>Disewa</option>
                    <option value="maintenance" @selected(request('status') == 'maintenance')>Perawatan</option>
                </select>
            </div>

            <!-- Filter Tipe Kendaraan -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Tipe kendaraan</label>
                <select name="model" class="form-select bg-light border-0 py-2 rounded-3" onchange="this.form.submit()">
                    <option value="semua" @selected(request('model') == 'semua')>Semua Tipe</option>
                    <option value="Matic (Skutik)" @selected(request('model') == 'Matic (Skutik)')>Matic (Skutik)</option>
                    <option value="Sport" @selected(request('model') == 'Sport')>Sport</option>
                    <option value="Cub (Bebek)" @selected(request('model') == 'Cub (Bebek)')>Cub (Bebek)</option>
                    <option value="EV (Kendaraan Listrik)" @selected(request('model') == 'EV (Kendaraan Listrik)')>EV (Kendaraan Listrik)</option>
                    <option value="Big Bike (Moge)" @selected(request('model') == 'Big Bike (Moge)')>Big Bike (Moge)</option>
                </select>
            </div>

            <!-- Filter Tanggal & Jam Sewa -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Tanggal & jam sewa</label>
                <input type="datetime-local" name="rental_time" value="{{ request('rental_time') }}" class="form-control bg-light border-0 py-2 rounded-3" onchange="this.form.submit()">
            </div>

            <!-- Input Cari Kendaraan -->
            <div class="col-md-3">
                <label class="form-label text-muted small fw-medium mb-1">Cari kendaraan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 pe-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0 py-2 ps-2 rounded-end-3" placeholder="Nama motor" onblur="this.form.submit()">
                </div>
            </div>
        </div>
    </form>

    <!-- Grid Card Kendaraan -->
    <div class="row g-4 mb-4">
        @forelse($motorcycles as $motor)
            @php 
                $currentStatus = $motor->computed_status;
                $isRented = $currentStatus === 'rented'; 
            @endphp
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-3 position-relative {{ $isRented ? 'opacity-75' : '' }}">
                    
                    <!-- Header Card: Nama & Tipe -->
                    <div class="mb-2">
                        <h5 class="fw-bold mb-0 {{ $isRented ? 'text-secondary' : 'text-dark' }}">{{ $motor->name }}</h5>
                        <span class="text-muted small">{{ $motor->model }}</span>
                    </div>

                    <!-- Gambar Motor -->
                    <div class="text-center my-3" style="height: 160px;">
                        @if($motor->image)
                            <img src="{{ asset('storage/' . $motor->image) }}" class="w-100 h-100 object-fit-contain" alt="{{ $motor->name }}">
                        @else
                            <div class="bg-light w-100 h-100 rounded-3 d-flex align-items-center justify-content-center text-muted">
                                Tidak ada gambar
                            </div>
                        @endif
                    </div>

                    <!-- Plat & Badge Status -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small d-flex align-items-center gap-1">
                            <i class="bi bi-card-text"></i> {{ $motor->plate_number }}
                        </span>
                        <span class="small fw-semibold 
                            @if($currentStatus === 'available') text-success
                            @elseif($currentStatus === 'rented') text-primary
                            @else text-secondary
                            @endif">
                            • @if($currentStatus === 'available')
                                Tersedia
                              @elseif($currentStatus === 'rented')
                                Disewa
                              @else
                                Perawatan
                              @endif
                        </span>
                    </div>

                    <!-- Harga & Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <div>
                            <span class="fw-bold fs-5 {{ $isRented ? 'text-secondary' : 'text-dark' }}">
                                Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}
                            </span>
                            <span class="text-muted small">/hari</span>
                        </div>

                        <div class="btn-group gap-1">
                            <!-- Tombol Detail -->
                            <button type="button" class="btn btn-sm btn-primary rounded-2 px-3 py-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#showMotorModal{{ $motor->id }}">
                                Detail
                            </button>
                            <!-- Tombol Icon Edit -->
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2" data-bs-toggle="modal" data-bs-target="#editMotorModal{{ $motor->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <!-- Tombol Icon Hapus -->
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2" data-bs-toggle="modal" data-bs-target="#deleteMotorModal{{ $motor->id }}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ================= MODAL DETAIL MOTOR ================= -->
            <div class="modal fade" id="showMotorModal{{ $motor->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 p-2 shadow">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark">Detail Kendaraan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small text-dark mb-1">Gambar</label>
                                    <div class="border rounded-3 text-center bg-light position-relative d-flex align-items-center justify-content-center overflow-hidden" style="height: 180px;">
                                        @if($motor->image)
                                            <img src="{{ asset('storage/' . $motor->image) }}" class="w-100 h-100 object-fit-contain" alt="{{ $motor->name }}">
                                        @else
                                            <span class="text-muted small">Tidak ada gambar</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold small text-dark mb-1">Deskripsi</label>
                                    <textarea class="form-control rounded-3 p-3 bg-light" rows="7" readonly>{{ $motor->description ?? '-' }}</textarea>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Nama kendaraan</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="{{ $motor->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Tipe</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="{{ $motor->model }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Brand</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="{{ $motor->brand }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">No. Plat</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="{{ $motor->plate_number }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Harga</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="Rp{{ number_format($motor->price_per_day, 0, ',', '.') }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-dark mb-1">Kapasitas Tangki</label>
                                    <input type="text" class="form-control rounded-3 py-2 bg-light" value="{{ $motor->tank_capacity ? $motor->tank_capacity . ' L' : '-' }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= MODAL EDIT MOTOR ================= -->
            <div class="modal fade" id="editMotorModal{{ $motor->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 p-2 shadow">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-dark">Edit Kendaraan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.motorcycles.update', $motor) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold small text-dark mb-1">
                                            Gambar <span class="text-muted fw-normal opacity-75">(Klik untuk mengubah)</span>
                                        </label>
                                        <div class="border rounded-3 text-center bg-light position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden" 
                                             style="height: 180px; cursor: pointer; border-style: dashed !important;" 
                                             onclick="document.getElementById('editImageInput{{ $motor->id }}').click()">
                                            
                                            <img id="editImagePreview{{ $motor->id }}" 
                                                 src="{{ $motor->image ? asset('storage/' . $motor->image) : '' }}" 
                                                 class="{{ $motor->image ? '' : 'd-none' }} w-100 h-100 object-fit-cover position-absolute top-0 start-0" 
                                                 alt="Preview Image" />
                                            
                                            <i class="bi bi-image fs-1 text-secondary opacity-50 {{ $motor->image ? 'd-none' : '' }}" id="editUploadIcon{{ $motor->id }}"></i>
                                            <input type="file" name="image" id="editImageInput{{ $motor->id }}" class="d-none" accept=".jpg,.jpeg,.png,.webp" onchange="previewEditImage(event, {{ $motor->id }})">
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold small text-dark mb-1">Deskripsi</label>
                                        <textarea name="description" class="form-control rounded-3 p-3 bg-white" rows="7" placeholder="Deskripsi kendaraan...">{{ old('description', $motor->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Nama kendaraan</label>
                                        <input type="text" name="name" value="{{ old('name', $motor->name) }}" class="form-control rounded-3 py-2" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Tipe / Model</label>
                                        <select name="model" class="form-select rounded-3 py-2" required>
                                            <option value="Matic (Skutik)" @selected(old('model', $motor->model) === 'Matic (Skutik)')>Matic (Skutik)</option>
                                            <option value="Sport" @selected(old('model', $motor->model) === 'Sport')>Sport</option>
                                            <option value="Cub (Bebek)" @selected(old('model', $motor->model) === 'Cub (Bebek)')>Cub (Bebek)</option>
                                            <option value="EV (Kendaraan Listrik)" @selected(old('model', $motor->model) === 'EV (Kendaraan Listrik)')>EV (Kendaraan Listrik)</option>
                                            <option value="Big Bike (Moge)" @selected(old('model', $motor->model) === 'Big Bike (Moge)')>Big Bike (Moge)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Brand</label>
                                        <input type="text" name="brand" value="{{ old('brand', $motor->brand) }}" class="form-control rounded-3 py-2" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">No. Plat</label>
                                        <input type="text" name="plate_number" value="{{ old('plate_number', $motor->plate_number) }}" class="form-control rounded-3 py-2" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Harga (per hari)</label>
                                        <input type="number" name="price_per_day" value="{{ old('price_per_day', $motor->price_per_day) }}" class="form-control rounded-3 py-2" min="0" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Kapasitas Tangki (Liter)</label>
                                        <input type="number" step="0.1" name="tank_capacity" value="{{ old('tank_capacity', $motor->tank_capacity) }}" class="form-control rounded-3 py-2" min="0">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Tahun</label>
                                        <input type="number" name="year" value="{{ old('year', $motor->year) }}" class="form-control rounded-3 py-2" min="2000" max="{{ date('Y') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-dark mb-1">Status Kendaraan</label>
                                        <select name="status" class="form-select rounded-3 py-2" required>
                                            <option value="available" @selected(old('status', $motor->status) === 'available')>Tersedia (Otomatis Cek Sewa)</option>
                                            <option value="maintenance" @selected(old('status', $motor->status) === 'maintenance')>Perawatan (Maintenance)</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 rounded-3 py-2.5 fw-semibold fs-6 mt-2">
                                    Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
            <div class="modal fade" id="deleteMotorModal{{ $motor->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 p-3 shadow">
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-exclamation-triangle text-warning display-3 mb-3"></i>
                            <h4 class="fw-bold text-dark mb-2">Konfirmasi Hapus</h4>
                            <p class="text-muted mb-4">Apakah Anda yakin ingin menghapus motor <strong>{{ $motor->name }}</strong> ({{ $motor->plate_number }})?</p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-light px-4 rounded-3 fw-medium" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('admin.motorcycles.destroy', $motor) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-medium">Ya, Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <p class="text-muted mb-0">Kendaraan tidak ditemukan.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $motorcycles->withQueryString()->links() }}
    </div>

</div>

<!-- ================= MODAL TAMBAH MOTOR ================= -->
<div class="modal fade" id="createMotorModal" tabindex="-1" aria-labelledby="createMotorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 p-2 shadow">
            
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="createMotorModalLabel">Tambah Kendaraan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.motorcycles.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Upload Gambar & Deskripsi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-dark mb-1">
                                Gambar <span class="text-muted fw-normal opacity-75">(Klik untuk mengunggah)</span>
                            </label>
                            <div class="border rounded-3 text-center bg-light position-relative d-flex flex-column align-items-center justify-content-center overflow-hidden" 
                                 style="height: 180px; cursor: pointer; border-style: dashed !important;" 
                                 onclick="document.getElementById('imageInput').click()">
                                <i class="bi bi-image fs-1 text-secondary opacity-50" id="uploadIcon"></i>
                                <img id="imagePreview" class="d-none w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="Preview Image" />
                                <input type="file" name="image" id="imageInput" class="d-none" accept=".jpg,.jpeg,.png,.webp" onchange="previewImage(event)">
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-dark mb-1">Deskripsi</label>
                            <textarea name="description" class="form-control rounded-3 p-3 bg-white" rows="7" placeholder="Deskripsi kendaraan...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Nama kendaraan</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-3 py-2" placeholder="Vega Force" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Tipe / Model</label>
                            <select name="model" class="form-select rounded-3 py-2" required>
                                <option value="" disabled @selected(!old('model'))>Pilih Tipe</option>
                                <option value="Matic (Skutik)" @selected(old('model') === 'Matic (Skutik)')>Matic (Skutik)</option>
                                <option value="Sport" @selected(old('model') === 'Sport')>Sport</option>
                                <option value="Cub (Bebek)" @selected(old('model') === 'Cub (Bebek)')>Cub (Bebek)</option>
                                <option value="EV (Kendaraan Listrik)" @selected(old('model') === 'EV (Kendaraan Listrik)')>EV (Kendaraan Listrik)</option>
                                <option value="Big Bike (Moge)" @selected(old('model') === 'Big Bike (Moge)')>Big Bike (Moge)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}" class="form-control rounded-3 py-2" placeholder="Yamaha" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">No. Plat</label>
                            <input type="text" name="plate_number" value="{{ old('plate_number') }}" class="form-control rounded-3 py-2" placeholder="AB 34567 GH" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Harga (per hari)</label>
                            <input type="number" name="price_per_day" value="{{ old('price_per_day') }}" class="form-control rounded-3 py-2" placeholder="75000" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Kapasitas Tangki (Liter)</label>
                            <input type="number" step="0.1" name="tank_capacity" value="{{ old('tank_capacity') }}" class="form-control rounded-3 py-2" placeholder="4.0" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Tahun</label>
                            <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="form-control rounded-3 py-2" min="2000" max="{{ date('Y') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark mb-1">Status Kendaraan</label>
                            <select name="status" class="form-select rounded-3 py-2" required>
                                <option value="available" @selected(old('status', 'available') === 'available')>Tersedia (Otomatis Cek Sewa)</option>
                                <option value="maintenance" @selected(old('status') === 'maintenance')>Perawatan (Maintenance)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2.5 fw-semibold fs-6 mt-2">
                        Simpan
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Scripts JavaScript Preview Image & Auto Open Modal saat Validation Error -->
<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const icon = document.getElementById('uploadIcon');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                icon.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEditImage(event, id) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('editImagePreview' + id);
                const icon = document.getElementById('editUploadIcon' + id);
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (icon) icon.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            var myModal = new bootstrap.Modal(document.getElementById('createMotorModal'));
            myModal.show();
        });
    @endif
</script>
@endsection