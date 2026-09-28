<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; margin: 0; padding: 20px; background: #f0f4f8; color: #000; display: flex; flex-direction: column; align-items: center; }
        .receipt-container { width: 100%; max-width: 320px; background: #fff; padding: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-radius: 5px; margin-bottom: 20px; }
        .action-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-family: sans-serif; font-size: 14px; transition: 0.2s; }
        .btn-print { background: #0d6efd; color: white; }
        .btn-print:hover { background: #0b5ed7; }
        .btn-download { background: #198754; color: white; }
        .btn-download:hover { background: #157347; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .dashed-line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; vertical-align: top; }
        .store-logo { max-width: 120px; max-height: 80px; margin-bottom: 10px; }
        @media print { body { background: #fff; padding: 0; display: block; } .receipt-container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; border-radius: 0; } .action-buttons { display: none !important; } }
    </style>
</head>
<body>
    
    <div class="action-buttons">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Printer</button>
        <button class="btn btn-download" onclick="downloadJPG()">🖼️ Download JPG</button>
    </div>

    <div class="receipt-container" id="receipt-area">
        <div class="text-center">
            @if(isset($setting) && $setting->store_logo)
                <img src="{{ asset('uploads/logo/' . $setting->store_logo) }}" class="store-logo" alt="Logo Toko">
            @endif
            <h2 style="margin: 0; font-family: sans-serif; letter-spacing: 1px;">
                {{ $setting->store_name ?? 'POS APP' }}
            </h2>
            <p style="margin: 5px 0; font-size: 11px;">
                {{ $setting->store_address ?? 'Alamat Toko' }}<br>
                Telp: {{ $setting->store_phone ?? '-' }}
            </p>
        </div>
        
        <div class="dashed-line"></div>
        
        <table style="font-size: 11px;">
            <tr><td>Tgl</td><td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Inv</td><td>: {{ $transaction->invoice_number }}</td></tr>
            <tr><td>Kasir</td><td>: {{ $transaction->user->name ?? 'Admin' }}</td></tr>
            <tr><td>Plg</td><td>: {{ $transaction->customer->name ?? 'Umum' }}</td></tr>
            <tr><td>Via</td><td>: {{ strtoupper($transaction->payment_method) }}</td></tr>
        </table>

        <div class="dashed-line"></div>

        <table>
            @foreach($transaction->details as $item)
            <tr><td colspan="3" class="fw-bold">{{ $item->product->name ?? 'Produk Dihapus' }}</td></tr>
            <tr>
                <td style="width: 20%;">{{ $item->quantity }}x</td>
                <td style="width: 40%;">{{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="width: 40%; text-align: right;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount > 0)
            <tr>
                <td>Diskon (Loyalty)</td>
                <td class="text-right fw-bold" style="color: red;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="fw-bold" style="font-size: 14px;">Total</td>
                <td class="text-right fw-bold" style="font-size: 14px;">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div class="dashed-line"></div>
        
        <table>
            <tr>
                <td>Tunai/Bayar</td>
                <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="dashed-line"></div>

        <div class="text-center" style="font-size: 11px; margin-top: 15px;">
            <p>Terima Kasih Atas Kunjungan Anda<br>Barang yang dibeli tidak dapat ditukar.</p>
        </div>
    </div>

    <script>
        function downloadJPG() {
            const receipt = document.getElementById('receipt-area');
            html2canvas(receipt, { scale: 2, backgroundColor: "#ffffff" }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Struk_{{ $transaction->invoice_number }}.jpg';
                link.href = canvas.toDataURL('image/jpeg', 1.0);
                link.click();
            });
        }
    </script>
</body>
</html>