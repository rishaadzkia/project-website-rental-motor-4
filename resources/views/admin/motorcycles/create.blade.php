@extends('layouts.app')

@section('title', 'Tambah Motor')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Motor</h1>
        <a href="{{ route('admin.motorcycles.index') }}" class="btn btn-outline-secondary">
            ← Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.motorcycles.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Motor</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" value="{{ old('model') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nomor Polisi</label>
                        <input type="text" name="plate_number" value="{{ old('plate_number') }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="year" value="{{ old('year') }}" class="form-control" min="2000" max="{{ date('Y') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Harga Per Hari</label>
                        <input type="number" name="price_per_day" value="{{ old('price_per_day') }}" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="available" @selected(old('status') === 'available')>Available</option>
                            <option value="rented" @selected(old('status') === 'rented')>Rented</option>
                            <option value="maintenance" @selected(old('status') === 'maintenance')>Maintenance</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Foto Motor</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 2MB.</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Simpan Motor
                    </button>
                    <a href="{{ route('admin.motorcycles.index') }}" class="btn btn-light">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection