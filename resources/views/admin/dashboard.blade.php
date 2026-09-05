@extends('layouts.admin')

@section('content')
<h3 class="fw-bold text-dark mb-4">Dashboard</h3>

<div class="row g-4">
    <!-- Card Top Orderan & Chart -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <h5 class="fw-bold mb-3">5 Top Orderan Rental</h5>
            <!-- Tempat Chart / Ringkasan -->
        </div>
    </div>
    <!-- Card Pesanan Terakhir -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <h5 class="fw-bold mb-3">Pesanan Terakhir</h5>
        </div>
    </div>
</div>
@endsection