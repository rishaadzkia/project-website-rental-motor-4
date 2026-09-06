@extends('layouts.app')

@section('title', 'Edit Motor')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Motor</h1>
        <a href="{{ route('admin.motorcycles.index') }}" class="btn btn-outline-secondary">
            &larr; Kembali
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
            <form method="POST" action="{{ route('admin.motorcycles.update', $motorcycle) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Motor</label>
                        <input type="text" name="name" value="{{ old('name', $motorcycle->name) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $motorcycle->brand) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" value="{{ old('model', $motorcycle->model) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nomor Polisi</label>
                        <input type="text" name="plate_number" value="{{ old('plate_number', $motorcycle->plate_number) }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $motorcycle->year) }}" class="form-control" min="2000" max="{{ date('Y') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Harga Per Hari</label>
                        <input type="number" name="price_per_day" value="{{ old('price_per_day', $motorcycle->price_per_day) }}" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="available" @selected(old('status', $motorcycle->status) === 'available')>Available</option>
                            <option value="rented" @selected(old('status', $motorcycle->status) === 'rented')>Rented</option>
                            <option value="maintenance" @selected(old('status', $motorcycle->status) === 'maintenance')>Maintenance</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $motorcycle->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Foto Motor</label>

                        @if($motorcycle->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $motorcycle->image) }}" width="150" class="rounded border" alt="Foto saat ini">
                                <div class="form-text">Foto saat ini</div>
                            </div>
                        @endif

                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Motor</button>
                    <a href="{{ route('admin.motorcycles.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection