@extends('layouts.app')

@section('content')
<style>
    /* CSS GLASSMORPHISM TETAP SAMA */
    body { background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); background-attachment: fixed; }
    .glass-card { background: rgba(255, 255, 255, 0.65) !important; backdrop-filter: blur(16px) !important; border: 1px solid rgba(255, 255, 255, 0.5) !important; border-radius: 20px; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important; }
    .product-scroll-area { height: calc(100vh - 300px); overflow-y: auto; overflow-x: hidden; padding-right: 8px; }
    .cart-scroll-area { height: calc(100vh - 350px); overflow-y: auto; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
    .search-box { border-radius: 15px; }
    .category-scroll { display: flex; overflow-x: auto; gap: 12px; padding-bottom: 10px; }
    .category-chip { background: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.5); border-radius: 20px; padding: 10px 20px; font-weight: 600; color: #444; text-decoration: none; display: flex; align-items: center; gap: 8px; white-space: nowrap; transition: all 0.3s; }
    .category-chip:hover, .category-chip.active { background: #fff; color: #0d6efd; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .product-card { transition: transform 0.2s; position: relative; overflow: hidden; cursor: pointer; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important; }
    .price-tag { position: absolute; top: 15px; right: -8px; background: rgba(40, 167, 69, 0.9); backdrop-filter: blur(4px); color: white; padding: 6px 15px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10; }
    .price-tag::after { content: ''; position: absolute; bottom: -7px; right: 0; border-width: 7px 8px 0 0; border-style: solid; border-color: #1e7e34 transparent transparent transparent; }

    /* CSS KHUSUS VOUCHER */
    .voucher-link { color: #0d6efd; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; }
    .voucher-link:hover { text-decoration: underline; }
    #voucher-box { display: none; margin-bottom: 15px; }
</style>

<!-- CSRF Token untuk AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row g-4">
    <!-- KOLOM KIRI (PRODUK) -->
    <div class="col-lg-8">
        <div class="glass-card p-3 mb-4">
            <div class="search-box p-1 mb-3 d-flex align-items-center bg-white bg-opacity-50 rounded-pill">
                <i class="bi bi-search text-muted ms-3 me-2"></i>
                <form action="{{ route('transactions.create') }}" method="GET" class="w-100">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none fw-bold" placeholder="Cari nama atau scan barcode..." value="{{ request('search') }}" autofocus>
                </form>
            </div>

            <div class="category-scroll custom-scrollbar">
                <a href="{{ route('transactions.create') }}" class="category-chip {{ !request('category_id') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill text-primary"></i> All
                </a>
                @if(isset($categories))
                    <?php foreach($categories as$category): ?>
                        <a href="{{ route('transactions.create', ['category_id' => $category->id]) }}" class="category-chip {{ request('category_id') == $category->id ? 'active' : '' }}">
                            <i class="bi bi-tag-fill text-danger"></i> {{ $category->name }}
                        </a>
                    <?php endforeach; ?>
                @endif
            </div>
        </div>

        <div class="product-scroll-area custom-scrollbar pb-4">
            <div class="row g-4">
                @if(isset($products) && count($products) > 0)
                    <?php foreach($products as$product): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="glass-card product-card h-100 d-flex flex-column p-3">
                                <div class="price-tag">RP {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                                <div class="text-center pt-4 pb-3" style="height: 160px;">
                                    @if($product->image)
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" style="height: 100%; max-width: 100%; object-fit: contain;">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-white bg-opacity-50 rounded-4"><i class="bi bi-box fs-1 opacity-25"></i></div>
                                    @endif
                                </div>
                                <div class="mt-auto text-center">
                                    <h6 class="fw-bold text-dark mb-3 text-truncate">{{ $product->name }}</h6>
                                    <button class="btn btn-primary w-100 rounded-pill fw-bold" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{$product->selling_price }}, '{{ $product->image }}', {{$product->stock }})">
                                        <i class="bi bi-cart-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                @else
                    <div class="col-12 text-center py-5">
                        <div class="glass-card p-5 d-inline-block">
                            <h5 class="text-muted fw-bold mb-0">Produk tidak ditemukan.</h5>
                        </div>
                    </div>
                @endif
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN (KERANJANG & VOUCHER) -->
    <div class="col-lg-4">
        <div class="glass-card sticky-top" style="top: 20px; display: flex; flex-direction: column; height: calc(100vh - 120px);">
            <div class="p-4 border-bottom border-light">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-2 text-primary"></i>ORDER ITEMS</h6>
            </div>
            
            <div class="p-3 flex-grow-1 cart-scroll-area custom-scrollbar" id="cart-items-container">
                <div class="text-center text-muted mt-5" id="empty-cart-message">
                    Belum ada pesanan
                </div>
            </div>
            
            <div class="p-4 bg-white bg-opacity-50" style="border-radius: 0 0 20px 20px;">
                
                <!-- KOLOM INPUT KODE VOUCHER ELEGAN -->
                <div class="text-end mb-2">
                    <a class="voucher-link" onclick="toggleVoucher()"><i class="bi bi-tag-fill me-1"></i> Punya Kode Promo?</a>
                </div>
                <div id="voucher-box">
                    <div class="input-group input-group-sm mb-2 shadow-sm rounded-pill overflow-hidden">
                        <input type="text" id="input_voucher_code" class="form-control border-0 fw-bold px-3" placeholder="Masukan Kode" style="text-transform: uppercase;">
                        <button class="btn btn-dark px-3 fw-bold border-0" type="button" onclick="applyVoucher()">Gunakan</button>
                    </div>
                    <small id="voucher-msg" class="d-block text-end fw-bold mb-2"></small>
                </div>
                <!-- AKHIR KOLOM VOUCHER -->

                <div class="d-flex justify-content-between mb-1" style="font-size: 0.9rem;">
                    <span class="text-muted" id="total-items-label">Subtotal (0)</span>
                    <span class="fw-bold text-dark" id="subtotal-label">Rp 0</span>
                </div>
                
                <!-- Baris Diskon Voucher -->
                <div class="d-flex justify-content-between mb-3 border-bottom border-light pb-2" style="font-size: 0.9rem;" id="discount-row">
                    <span class="text-muted"><i class="bi bi-tags text-danger me-1"></i> Diskon</span>
                    <span class="fw-bold text-danger" id="discount-label">- Rp 0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-dark fs-5">TOTAL</span>
                    <h4 class="fw-bold mb-0 text-primary" id="total-price-label">Rp 0</h4>
                </div>
                
                <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius: 15px;" disabled id="btn-process" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    PROSES PEMBAYARAN
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light pb-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 text-primary me-2"></i>Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('transactions.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="cart_data" id="cart_data_input">
                    <input type="hidden" name="grand_total" id="grand_total_input">
                    <!-- HIDDEN INPUT UNTUK KIRIM VOUCHER KE CONTROLLER -->
                    <input type="hidden" name="voucher_code" id="hidden_voucher_code">

                    <div class="text-center mb-4 p-4 bg-white bg-opacity-75 rounded-4 shadow-sm border border-light">
                        <span class="text-muted fw-bold d-block mb-1">TOTAL TAGIHAN</span>
                        <h1 class="fw-bold text-success mb-0" id="modal-total-label">Rp 0</h1>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">No. HP (Opsional)</label>
                            <input type="text" name="customer_phone" class="form-control bg-white bg-opacity-75" style="border-radius: 10px;" placeholder="0812...">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Nama (Opsional)</label>
                            <input type="text" name="customer_name" class="form-control bg-white bg-opacity-75" style="border-radius: 10px;" placeholder="Nama">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select fw-bold bg-white bg-opacity-75" style="border-radius: 10px;" required>
                            <option value="cash">Tunai (Cash)</option>
                            <option value="qris">QRIS / E-Wallet</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nominal Uang (Rp)</label>
                        <input type="number" name="paid_amount" id="input_paid_amount" class="form-control form-control-lg text-end fw-bold bg-white bg-opacity-75" style="border-radius: 15px;" placeholder="0" required min="0">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <span class="text-muted fw-bold fs-5">Kembalian</span>
                        <h3 class="fw-bold text-danger mb-0" id="modal-change-label">Rp 0</h3>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm fs-5" style="border-radius: 15px;" id="btn-submit-payment" disabled>
                        SELESAIKAN TRANSAKSI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let cart = [];
    let currentSubtotal = 0;
    let currentGrandTotal = 0;
    
    // Variabel Voucher
    let activeVoucherType = null;
    let activeVoucherValue = 0;

    function formatRupiah(angka) { return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

    // Efek Expand (Buka/Tutup) Input Voucher
    function toggleVoucher() {
        const box = document.getElementById('voucher-box');
        box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
    }

    // Fungsi AJAX untuk Cek Voucher
    function applyVoucher() {
        let code = document.getElementById('input_voucher_code').value.trim();
        let msgBox = document.getElementById('voucher-msg');

        if(code === '') {
            msgBox.innerHTML = '<span class="text-danger">Masukkan kode dulu!</span>';
            activeVoucherType = null;
            activeVoucherValue = 0;
            renderCart();
            return;
        }

        msgBox.innerHTML = '<span class="text-muted">Mengecek kode...</span>';

        // Panggil endpoint /transactions/check-voucher menggunakan Fetch API
        fetch("{{ route('transactions.check_voucher') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Voucher Valid! Simpan nilainya dan render ulang keranjang.
                msgBox.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> ${data.message}</span>`;
                activeVoucherType = data.data.type;
                activeVoucherValue = data.data.value;
                document.getElementById('hidden_voucher_code').value = code; // Siap dilempar ke controller
                renderCart();
            } else {
                // Voucher Gagal
                msgBox.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill"></i> ${data.message}</span>`;
                activeVoucherType = null;
                activeVoucherValue = 0;
                document.getElementById('hidden_voucher_code').value = '';
                renderCart();
            }
        })
        .catch(error => {
            msgBox.innerHTML = '<span class="text-danger">Koneksi Error.</span>';
        });
    }

    function addToCart(id, name, price, image, maxStock) {
        let item = cart.find(i => i.id === id);
        if (item) {
            if (item.qty < maxStock) item.qty++;
            else alert('Stok produk tidak mencukupi!');
        } else {
            cart.push({ id: id, name: name, price: price, image: image, qty: 1, maxStock: maxStock });
        }
        renderCart();
    }

    function updateQty(id, change) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += change;
            if (item.qty > item.maxStock) { item.qty = item.maxStock; alert('Stok maksimal!'); }
            if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const emptyMsg = document.getElementById('empty-cart-message');
        const btnProcess = document.getElementById('btn-process');
        
        container.innerHTML = '';
        let totalItems = 0; currentSubtotal = 0;

        if (cart.length === 0) {
            container.appendChild(emptyMsg);
            emptyMsg.style.display = 'block';
            btnProcess.disabled = true;
            document.getElementById('total-items-label').innerText = `Subtotal (0)`;
            document.getElementById('subtotal-label').innerText = 'Rp 0';
            document.getElementById('discount-label').innerText = '- Rp 0';
            document.getElementById('total-price-label').innerText = 'Rp 0';
            return;
        } 
        
        btnProcess.disabled = false;
        cart.forEach((item) => {
            totalItems += item.qty; currentSubtotal += (item.price * item.qty);
            let imagePath = item.image ? `/uploads/products/${item.image}` : 'https://via.placeholder.com/50';

            container.innerHTML += `
                <div class="glass-card p-2 mb-3 d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 p-1 shadow-sm" style="width: 55px; height: 55px;">
                        <img src="${imagePath}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold text-dark text-truncate" style="font-size: 0.9rem; max-width: 130px;">${item.name}</h6>
                        <div class="text-success fw-bold" style="font-size: 0.85rem;">${formatRupiah(item.price)}</div>
                    </div>
                    <div class="d-flex align-items-center bg-white bg-opacity-75 rounded-pill px-2 py-1 shadow-sm border border-light">
                        <button type="button" class="btn btn-sm py-0 px-1 text-danger fw-bold border-0" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="mx-2 fw-bold" style="font-size: 0.85rem;">${item.qty}</span>
                        <button type="button" class="btn btn-sm py-0 px-1 text-primary fw-bold border-0" onclick="updateQty(${item.id}, 1)">+</button>
                    </div>
                </div>
            `;
        });

        // HITUNG DISKON VOUCHER
        let discountAmount = 0;
        if(activeVoucherType === 'percent') {
            discountAmount = currentSubtotal * (activeVoucherValue / 100);
        } else if (activeVoucherType === 'nominal') {
            discountAmount = parseFloat(activeVoucherValue);
        }

        currentGrandTotal = currentSubtotal - discountAmount;
        if (currentGrandTotal < 0) currentGrandTotal = 0; // Cegah minus

        // TAMPILKAN KE LAYAR
        document.getElementById('total-items-label').innerText = `Subtotal (${totalItems})`;
        document.getElementById('subtotal-label').innerText = formatRupiah(currentSubtotal);
        document.getElementById('discount-label').innerText = '- ' + formatRupiah(discountAmount);
        document.getElementById('total-price-label').innerText = formatRupiah(currentGrandTotal);
    }

    document.getElementById('btn-process').addEventListener('click', function() {
        document.getElementById('cart_data_input').value = JSON.stringify(cart);
        document.getElementById('grand_total_input').value = currentGrandTotal;
        document.getElementById('modal-total-label').innerText = formatRupiah(currentGrandTotal);
        document.getElementById('input_paid_amount').value = '';
        document.getElementById('modal-change-label').innerText = 'Rp 0';
        document.getElementById('btn-submit-payment').disabled = true;
        setTimeout(() => document.getElementById('input_paid_amount').focus(), 500);
    });

    document.getElementById('input_paid_amount').addEventListener('input', function() {
        let paid = parseFloat(this.value) || 0;
        let change = paid - currentGrandTotal;
        let labelChange = document.getElementById('modal-change-label');
        let btnSubmit = document.getElementById('btn-submit-payment');

        if (change >= 0 && cart.length > 0) {
            labelChange.innerText = formatRupiah(change);
            labelChange.classList.replace('text-danger', 'text-success');
            btnSubmit.disabled = false;
        } else {
            labelChange.innerText = 'Uang Kurang!';
            labelChange.classList.replace('text-success', 'text-danger');
            btnSubmit.disabled = true;
        }
    });

    @if(session('success_transaction'))
        Swal.fire({
            title: 'Transaksi Sukses!',
            html: `<h3 class="text-success fw-bold">{{ session('success_transaction') }}</h3>
                   {!! session('reward') !!}`,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-printer"></i> Cetak Struk',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.open("{{ url('/transactions') }}/{{ session('transaction_id') }}/receipt", "Cetak Struk", "width=350,height=600");
            }
        });
    @endif
    @if(session('error'))
        Swal.fire('Gagal!', "{{ session('error') }}", 'error');
    @endif
</script>
@endsection