@extends('layouts.app')

@section('title', 'Detail Motor')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Motor</h1>
        <div>
            <a href="{{ route('admin.motorcycles.edit', $motorcycle) }}" class="btn btn-primary">
                Edit
            </a>
            <a href="{{ route('admin.motorcycles.index') }}" class="btn btn-outline-secondary">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                {{-- Foto --}}
                <div class="col-md-5 mb-4 mb-md-0">
                    @if($motorcycle->image)
                        <img src="{{ asset('storage/' . $motorcycle->image) }}" 
                             class="img-fluid rounded border" 
                             alt="{{ $motorcycle->name }}">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 300px;">
                            <span class="text-muted">Tidak ada foto</span>
                        </div>
                    @endif
                </div>

                {{-- Detail --}}
                <div class="col-md-7">
                    <h2 class="h4 mb-3">
                        {{ $motorcycle->brand }} {{ $motorcycle->name }}
                    </h2>

                    <table class="table table-borderless">
                        <tr>
                            <th width="160">Brand</th>
                            <td>{{ $motorcycle->brand }}</td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <td>{{ $motorcycle->model }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Polisi</th>
                            <td>{{ $motorcycle->plate_number }}</td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td>{{ $motorcycle->year }}</td>
                        </tr>
                        <tr>
                            <th>Harga per Hari</th>
                            <td>
                                <strong>Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge 
                                    @if($motorcycle->status === 'available') bg-success
                                    @elseif($motorcycle->status === 'rented') bg-warning text-dark
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($motorcycle->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $motorcycle->description ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection