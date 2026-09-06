@extends('layouts.app')

@section('title', 'Kelola Motor')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Kelola Motor</h1>
        <a href="{{ route('admin.motorcycles.create') }}" class="btn btn-primary">
            + Tambah Motor
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Motor</th>
                            <th>Plat</th>
                            <th>Tahun</th>
                            <th>Harga/Hari</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($motorcycles as $motorcycle)
                            <tr>
                                <td>
                                    @if($motorcycle->image)
                                        <img src="{{ asset('storage/' . $motorcycle->image) }}"
                                             width="80" class="rounded" alt="{{ $motorcycle->name }}">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $motorcycle->brand }}</strong><br>
                                    {{ $motorcycle->name }}
                                </td>
                                <td>{{ $motorcycle->plate_number }}</td>
                                <td>{{ $motorcycle->year }}</td>
                                <td>Rp {{ number_format($motorcycle->price_per_day, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge 
                                        @if($motorcycle->status === 'available') bg-success
                                        @elseif($motorcycle->status === 'rented') bg-warning
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($motorcycle->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.motorcycles.show', $motorcycle) }}" class="btn btn-sm btn-outline-info">Detail</a>
                                    <a href="{{ route('admin.motorcycles.edit', $motorcycle) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.motorcycles.destroy', $motorcycle) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus motor ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada data motor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $motorcycles->links() }}
    </div>
</div>
@endsection