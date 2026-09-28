<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Apps</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
</head>
<style>
    /* Styling Navigasi Glassmorphism */
    .nav-pills .nav-link {
        color: #333;
        font-weight: 600;
        border-radius: 0; /* Hilangkan sudut bulat agar mirip tab garis */
        transition: all 0.3s ease;
        background: transparent !important;
        position: relative;
        padding-bottom: 8px;
    }
    
    .nav-pills .nav-link:hover {
        color: #0d6efd;
    }

    /* Ketika menu AKTIF: Tampilkan garis bawah (underline) warna biru */
    .nav-pills .nav-link.active {
        background: transparent !important;
        color: #0d6efd !important;
        font-weight: bold;
        box-shadow: none !important;
    }

    /* Efek garis bawah biru menggunakan pseudo-element */
    .nav-pills .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 10px;
        right: 10px;
        height: 3px;
        background-color: #0d6efd;
        border-radius: 3px;
    }
</style>
<!-- Tambahkan class ios-bg dari tema login sebelumnya -->
<!-- Kunci tinggi tepat 100% layar (vh-100) dan matikan scroll (overflow-hidden) -->
<body class="ios-bg">

    <!-- HEADER ATAS -->
    <!-- Tambahkan position-relative dan z-index: 1050; di sini -->
    <div class="glass-header py-2 flex-shrink-0 position-relative" style="z-index: 1050;">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-shop fs-3 text-primary me-2"></i>
                <h4 class="mb-0 fw-bold text-dark">CASHIER APPS</h4>
            </div>
            
            <!-- Kumpulan Ikon dan Profil -->
            <div class="d-flex align-items-center">
                <i class="bi bi-moon fs-5 me-4 text-muted" style="cursor: pointer;"></i>
                
                <!-- Dropdown Profil -->
                <div class="dropdown">
                    <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" class="rounded-circle me-2" width="40">
                        <div class="d-flex flex-column text-start">
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3 glass-dropdown">
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVBAR UTAMA -->
    <div class="container flex-shrink-0 position-relative" style="z-index: 1040;">
        <nav class="main-navbar glass-nav px-3 mb-3">
            <ul class="nav nav-pills align-items-center gap-2">
            <!-- Menu Home / Dashboard (Selalu bisa diakses) -->
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door me-1"></i> HOME
                </a>
            </li>

            <!-- Menu Kategori -->
            @if(Auth::user()->hasPermission('categories'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                <i class="bi bi-tags me-1"></i> KATEGORI
                </a>
            </li>
            @endif

            <!-- Menu Product -->
            @if(Auth::user()->hasPermission('products'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <i class="bi bi-box-seam me-1"></i> PRODUCT
                </a>
            </li>
            @endif

            <!-- Menu Customers -->
            @if(Auth::user()->hasPermission('customers'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <i class="bi bi-people me-1"></i> CUSTOMERS
                </a>
            </li>
            @endif

            <!-- Menu Transactions -->
            @if(Auth::user()->hasPermission('transactions'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                <i class="bi bi-cart-check me-1"></i> TRANSACTIONS
                </a>
            </li>
            @endif

            <!-- Menu Report -->
            @if(Auth::user()->hasPermission('reports'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> REPORT
                </a>
            </li>
            @endif

            <!-- Menu Users -->
            @if(Auth::user()->hasPermission('users'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="bi bi-person-badge me-1"></i> USERS
                </a>
            </li>
            @endif

            <!-- Menu Settings -->
            @if(Auth::user()->hasPermission('settings'))
            <li class="nav-item">
                <a class="nav-link px-3 {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <i class="bi bi-gear me-1"></i> SETTINGS
                </a>
            </li>
            @endif
        </ul>
        </nav>
    </div>

    <!-- KONTEN UTAMA (flex-grow-1 agar mengisi sisa ruang bawah) -->
    <div class="container flex-grow-1 d-flex flex-column pb-3">
        @yield('content')
    </div>
    <!-- 1. Script JQuery (Opsional tapi sering membantu untuk plugin lain) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 2. Script Bootstrap 5 Bundle (Wajib untuk Dropdown!) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. Script SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>