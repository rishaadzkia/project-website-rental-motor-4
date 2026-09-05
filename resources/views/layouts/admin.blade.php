<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name', 'ED.RENT') }}</title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

@include('layouts.navigation')
<div class="d-flex min-vh-100">
    <!-- Sidebar Left -->
    <aside class="bg-white border-end p-3 d-flex flex-column justify-content-between" style="width: 260px;">
        <div>
            <div class="text-uppercase text-muted fw-bold extra-small mb-4 tracking-wider" style="font-size: 0.75rem;">
                MAIN MENU
            </div>

            <div class="nav nav-pills flex-column gap-2">
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>

                <!-- Menu Daftar Pesanan -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.orders.*') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-bag me-2"></i> Daftar Pesanan
                </a>

                <!-- Menu Kendaraan -->
                <a href="{{ route('admin.motors.index') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.motors.*') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-scooter me-2"></i> Kendaraan
                </a>
            </div>
        </div>

        <!-- Logout Bottom -->
        <div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-link text-secondary text-decoration-none p-0 small">
                    <i class="bi bi-box-arrow-left me-2"></i> Log Out
                </button>
            </form>
        </div>

       
    </aside>

    

    <!-- Main Content Right -->
    <main class="flex-grow-1 p-4 overflow-auto">
        @yield('content')
    </main>
</div>
 @include('layouts.footer')



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>