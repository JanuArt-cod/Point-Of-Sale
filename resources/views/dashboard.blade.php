@extends('layouts.app')

@section('content')
    <!-- BANNER SAMBUTAN & TOMBOL AKSES POS -->
    <div class="welcome-banner px-4 py-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
                <span class="badge bg-primary bg-opacity-25 text-dark px-3 py-1 rounded-pill fw-bold mb-1" style="font-size: 0.7rem;">
                    <i class="bi bi-shop me-1"></i> Sistem Kasir Pintar (POS)
                </span>
                <h5 class="fw-bold text-dark mb-1">Selamat Datang, {{ Auth::user()->name ?? 'Kasir' }}! 👋</h5>
                <p class="text-secondary mb-0" style="font-size: 0.8rem;">
                        Siap melayani transaksi pelanggan hari ini ? Buka layar kasir utama di samping.
                </p>
                </div>
                <div>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary bg-opacity-25 fw-bold px-4 py-2 rounded shadow-lg" style="font-size: 2rem;">
                        <i class="bi bi-calculator"></i>
                    </a>
                </div>
    </div>

    <!-- Baris Kartu Statistik (mb-3) -->
    <div class="row mb-3 flex-shrink-0">
        <!-- Card 1 -->
        <div class="col-md-3">
            <div class="card glass-animated-card p-3 fade-in-up delay-1 border-0 h-100">
                <div class="card-body position-relative overflow-hidden p-2 d-flex flex-column justify-content-between">
                    <i class="bi bi-cart-check position-absolute opacity-25" style="font-size: 4rem; right: -5px; bottom: -5px; color: #0d6efd;"></i>
                    <div>
                        <span class="text-dark text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Sales Today</span>
                        <h3 class="fw-bold text-dark counter mb-0" data-target="{{ $sales_today }}">0</h3>
                    </div>
                    <h5 class="fw-bold mb-0 text-primary mt-2">Rp <span class="counter" data-target="{{ $revenue_today }}">0</span></h5>
                </div>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="col-md-3">
            <div class="card glass-animated-card p-3 fade-in-up delay-2 border-0 h-100">
                <div class="card-body position-relative overflow-hidden p-2 d-flex flex-column justify-content-between">
                    <i class="bi bi-box-seam position-absolute opacity-25" style="font-size: 4rem; right: -5px; bottom: -5px; color: #198754;"></i>
                    <span class="text-dark text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Total Produk</span>
                    <h2 class="fw-bold mb-0 text-success counter mt-auto" data-target="{{ $total_products }}">0</h2>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-md-3">
            <div class="card glass-animated-card p-3 fade-in-up delay-3 border-0 h-100">
                <div class="card-body position-relative overflow-hidden p-2 d-flex flex-column justify-content-between">
                    <i class="bi bi-people position-absolute opacity-25" style="font-size: 4rem; right: -5px; bottom: -5px; color: #ffc107;"></i>
                    <span class="text-dark text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Total Pelanggan</span>
                    <h2 class="fw-bold mb-0 text-warning counter mt-auto" data-target="{{ $total_customers }}">0</h2>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <!-- Card 4: Total Profit -->
        <div class="col-md-3">
            <div class="card glass-animated-card p-3 fade-in-up delay-4 border-0 h-100">
                <div class="card-body position-relative overflow-hidden p-2 d-flex flex-column justify-content-between">
                    <i class="bi bi-wallet2 position-absolute opacity-25" style="font-size: 4rem; right: -5px; bottom: -5px; color: #dc3545;"></i>
                    <span class="text-dark text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem;">Total Profit</span>
                    <h5 class="fw-bold mb-0 text-danger mt-auto">Rp {{ number_format($total_profit ?? 0, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Konten Bawah (Chart + Limit Stock) -->
    <!-- flex-grow-1 memastikan area ini mengisi sisa ruang bawah layar tanpa menambah scroll -->
    <div class="row flex-grow-1">
        <!-- CHART LINE ART MINIMALIS (TERHUBUNG KE DATABASE) -->
<div class="col-md-8 h-100 pb-2">
    <div class="card glass-animated-card p-3 fade-in-up delay-3 h-100 border-0 d-flex flex-column">
        <div class="card-header bg-transparent border-0 flex-shrink-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up text-primary me-2"></i>PRODUCTS BEST SELLING (TREND)</h6>
            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size: 0.7rem;">Real Time</span>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center flex-grow-1 position-relative" style="min-height: 200px;">
            @if(isset($chartLabels) && count($chartLabels) > 0)
                <canvas id="bestSellingLineChart" style="max-height: 180px; width: 100%;"></canvas>
            @else
                <div class="text-center text-muted">
                    <i class="bi bi-bar-chart fs-1 opacity-25 d-block mb-1"></i>
                    <small style="font-size: 0.8rem;">Belum ada data penjualan untuk grafik.</small>
                </div>
            @endif
        </div>
    </div>
</div>

        <!-- LIMIT STOCK DI KANAN -->
        <div class="col-md-4 h-100 pb-2">
            <div class="card glass-animated-card p-3 fade-in-up delay-4 h-100 border-0 d-flex flex-column">
                <div class="card-header bg-transparent border-0 flex-shrink-0">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>LIMIT STOCK</h6>
                </div>
                <!-- overflow-auto diletakkan di dalam card-body agar jika darurat scroll hanya terjadi di dalam kotak ini -->
                <div class="card-body overflow-auto flex-grow-1">
                    @forelse($limit_stocks as $item)
                        <div class="d-flex align-items-center border-bottom border-light pb-2 mb-2">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-box fs-5 text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $item->name }}</h6>
                                <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;">Sisa: {{ $item->stock }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-circle fs-2 text-success mb-2"></i>
                            <p style="font-size: 0.85rem;">Stok produk aman.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Script Animasi Counter -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                counter.innerText = '0';
                const updateCounter = () => {
                    const target = +counter.getAttribute('data-target');
                    const c = +counter.innerText.replace(/\D/g, ''); 
                    const increment = target / 50; 
                    if(c < target) {
                        counter.innerText = Math.ceil(c + increment).toLocaleString('id-ID');
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.innerText = target.toLocaleString('id-ID');
                    }
                };
                updateCounter();
            });
        });
    </script>
   <!-- PASTIKAN CDN CHART.JS TERPASANG -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(isset($chartLabels) && count($chartLabels) > 0)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('bestSellingLineChart').getContext('2d');
        
        // Membuat efek gradasi transparan di bawah garis (Area Chart Transparan)
        const gradient = ctx.createLinearGradient(0, 0, 0, 180);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.35)'); // Biru transparan di atas
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');  // Pudar ke bawah

        new Chart(ctx, {
            type: 'line', // Grafik Garis (Line Art)
            data: {
                labels: {!! json_encode($chartLabels) !!}, // <-- Typo "ilan" sudah dihapus di sini
                datasets: [{
                    label: 'Terjual',
                    data: {!! json_encode($chartData) !!}, // Jumlah Qty
                    borderColor: '#0d6efd',         // Warna garis utama
                    borderWidth: 3,                 // Ketebalan garis (Line art style)
                    backgroundColor: gradient,      // Efek transparan di bawah garis
                    fill: true,                     // Mengaktifkan area isi transparan
                    tension: 0.4,                   // Membuat garis melengkung halus (smooth curve)
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Sembunyikan legenda karena label sudah jelas di sumbu X
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false // Hilangkan garis kisi vertikal agar bersih
                        },
                        ticks: {
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)' // Garis kisi horizontal tipis transparan
                        },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection