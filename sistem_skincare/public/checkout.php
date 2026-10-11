<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_barang = $_GET['id'];
    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang' AND stok > 0");
    $b = mysqli_fetch_assoc($q);
    if (!$b) { header("Location: katalog.php"); exit; }
    $keranjang = [$id_barang => 1];
} else {
    $keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
    if (empty($keranjang)) { header("Location: katalog.php"); exit; }
}

$items = [];
$total = 0;
foreach ($keranjang as $id_barang => $jumlah) {
    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id_barang'");
    $b = mysqli_fetch_assoc($q);
    if ($b) {
        $subtotal = $b['harga'] * $jumlah;
        $total += $subtotal;
        $items[] = [
            'id_barang' => $b['id_barang'],
            'nama_barang' => $b['nama_barang'],
            'kategori' => $b['kategori'],
            'harga' => $b['harga'],
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Checkout - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            width: 100%;
            overflow-x: hidden;
            background: #fff5f8;
            font-family: 'Segoe UI', sans-serif;
        }
        body { padding: 20px 15px; }
        
        .checkout-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            width: 100%;
            max-width: 620px;
            margin: auto;
            background: white;
            padding: 20px;
        }
        .checkout-card h5 {
            color: #d81b60;
            font-weight: bold;
            text-align: center;
            margin-bottom: 18px;
            font-size: 1.05rem;
        }
        .section-title {
            color: #d81b60;
            font-weight: 600;
            font-size: 0.78rem;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1.5px solid #fce4ec;
        }
        .form-label { 
            font-size: 0.72rem; 
            font-weight: 500; 
            color: #333; 
            margin-bottom: 3px;
        }
        .form-control {
            font-size: 0.78rem;
            padding: 7px 11px;
            border-radius: 7px;
            border: 1.5px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #d81b60;
            box-shadow: 0 0 0 0.15rem rgba(216,27,96,0.15);
        }
        .mb-3 { margin-bottom: 10px !important; }
        
        .produk-list-item {
            padding: 7px 0;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
        }
        .produk-list-item:last-child { border-bottom: none; }
        .produk-list-item .subtotal {
            color: #d81b60;
            font-weight: bold;
        }
        
        /* ========================================== */
        /* TOTAL BOX - BACKGROUND SOLID, TEKS PUTIH TEGAS */
        /* ========================================== */
        .total-box {
            background: #ad1457;
            color: white;
            padding: 14px 18px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            box-shadow: 0 4px 12px rgba(173,20,87,0.3);
        }
        .total-box .total-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .total-box .total-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }
        
        .metode-option { display: flex; gap: 5px; flex-wrap: wrap; }
        .metode-option input[type="radio"] { display: none; }
        .metode-option label {
            flex: 1;
            min-width: 80px;
            padding: 7px 9px;
            border: 1.5px solid #eee;
            border-radius: 7px;
            text-align: center;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .metode-option label:hover { border-color: #f8bbd0; background: #fff5f8; }
        .metode-option input[type="radio"]:checked + label {
            border-color: #d81b60;
            background: #fce4ec;
            color: #d81b60;
            font-weight: 600;
        }
        
        .btn-bayar {
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white;
            border: none;
            border-radius: 9px;
            padding: 10px;
            font-weight: 600;
            font-size: 0.82rem;
            width: 100%;
            margin-top: 10px;
        }
        .btn-bayar:hover { background: linear-gradient(135deg, #d81b60, #ad1457); color: white; }
        .btn-batal {
            background: #f5f5f5;
            color: #666;
            border-radius: 9px;
            padding: 9px;
            font-weight: 500;
            font-size: 0.78rem;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 7px;
        }
        .btn-batal:hover { background: #e0e0e0; color: #333; }
        
        /* ========================================== */
        /* RESPONSIVE HP */
        /* ========================================== */
        @media (max-width: 768px) {
            body { padding: 15px 12px; }
            .checkout-card { padding: 16px 14px; max-width: 100%; border-radius: 10px; }
            .checkout-card h5 { font-size: 0.95rem; margin-bottom: 14px; }
            .section-title { font-size: 0.72rem; }
            .form-label { font-size: 0.68rem; }
            .form-control { font-size: 0.75rem; padding: 6px 10px; }
            .mb-3 { margin-bottom: 8px !important; }
            
            .row > .col-md-7,
            .row > .col-md-5 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            
            .produk-list-item { font-size: 0.7rem; padding: 5px 0; }
            
            /* Total box di HP - tetap jelas */
            .total-box { padding: 12px 15px; }
            .total-box .total-label { font-size: 0.68rem; }
            .total-box .total-value { font-size: 1.15rem; }
            
            .metode-option label { font-size: 0.65rem; padding: 6px 7px; min-width: 70px; }
            .btn-bayar { font-size: 0.78rem; padding: 9px; }
            .btn-batal { font-size: 0.72rem; padding: 8px; }
        }
    </style>
</head>
<body>

<div class="checkout-card">
    <h5>📋 Checkout Pesanan</h5>

    <form action="proses_checkout.php" method="POST">
        <?php foreach ($items as $item): ?>
            <input type="hidden" name="id_barang[]" value="<?= $item['id_barang']; ?>">
            <input type="hidden" name="jumlah[]" value="<?= $item['jumlah']; ?>">
        <?php endforeach; ?>

        <div class="row">
            <!-- KIRI: Form Data Pelanggan -->
            <div class="col-md-7">
                <div class="section-title">📝 Data Pelanggan</div>
                
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Rianty" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. HP / WhatsApp *</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Pengiriman *</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Tulis alamat lengkap..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Contoh: kirim hari Sabtu">
                </div>

                <div class="section-title mt-2">💳 Metode Pembayaran</div>
                <div class="metode-option">
                    <input type="radio" name="metode_bayar" id="qris" value="QRIS" checked>
                    <label for="qris">📷 QRIS</label>
                    <input type="radio" name="metode_bayar" id="transfer" value="Transfer Bank">
                    <label for="transfer">🏦 Transfer</label>
                    <input type="radio" name="metode_bayar" id="ewallet" value="E-Wallet">
                    <label for="ewallet">📱 E-Wallet</label>
                </div>
            </div>

            <!-- KANAN: Ringkasan Pesanan -->
            <div class="col-md-5">
                <div class="section-title">🛍️ Ringkasan Pesanan</div>
                
                <div style="background: #fff5f8; padding: 10px; border-radius: 8px;">
                    <?php foreach ($items as $item): ?>
                    <div class="produk-list-item">
                        <div>
                            <b><?= htmlspecialchars($item['nama_barang']); ?></b><br>
                            <small class="text-muted"><?= $item['jumlah']; ?> x <?= rupiah($item['harga']); ?></small>
                        </div>
                        <div class="subtotal"><?= rupiah($item['subtotal']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- TOTAL BOX - BACKGROUND SOLID PINK GELAP -->
                <div class="total-box">
                    <div>
                        <div class="total-label">Total Bayar</div>
                        <div class="total-value"><?= rupiah($total); ?></div>
                    </div>
                    <div style="font-size: 1.8rem; opacity: 0.5;">💰</div>
                </div>

                <?php if (!isset($_GET['id'])): ?>
                    <a href="keranjang.php" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 7px; font-size: 0.72rem; padding: 7px;">✏️ Ubah Keranjang</a>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn-bayar">💳 Bayar Sekarang</button>
        <a href="katalog.php" class="btn-batal">⬅️ Kembali ke Katalog</a>
    </form>
</div>

</body>
</html>