<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name', 'ED.RENT') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Icons (Bootstrap & Font Awesome) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <!-- Top Navigation -->
    @include('layouts.navigation')

    <!-- Main Layout Wrapper -->
    <div class="d-flex min-vh-100">
        
        <!-- Sidebar Left -->
        <aside class="bg-white border-end p-3 shadow-sm" style="width: 260px; shrink: 0;">
            <!-- Header Sidebar -->
            <div class="text-uppercase text-muted fw-bold mb-3 tracking-wider" style="font-size: 0.75rem;">
                MAIN MENU
            </div>

            <!-- Navigation Links -->
            <div class="nav nav-pills flex-column gap-1">
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>

                <!-- Menu Daftar Pesanan -->
                <a href="{{ route('admin.bookings.index') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.bookings.*') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-bag me-2"></i> Daftar Pesanan
                </a>

                <!-- Menu Kendaraan -->
                <a href="{{ route('admin.motorcycles.index') }}" 
                   class="nav-link text-start rounded-3 py-2 px-3 {{ request()->routeIs('admin.motorcycles.*') ? 'active bg-primary fw-semibold' : 'text-secondary' }}">
                    <i class="bi bi-scooter me-2"></i> Kendaraan
                </a>
            </div>

            <!-- Separator Line -->
            <hr class="text-muted my-3 opacity-25">

            <!-- Logout Section (Menempel Rapi di Bawah Menu) -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 text-start rounded-3 py-2 px-3 border-0 small d-flex align-items-center">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out
                </button>
            </form>
        </aside>

        <!-- Main Content Right -->
        <main class="flex-grow-1 overflow-auto d-flex flex-column">
            <div class="p-4 flex-grow-1">
                @yield('content') 
            </div>
            
            <div class="mt-auto border-top bg-white">
                @include('layouts.footer')
            </div>
        </main>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>