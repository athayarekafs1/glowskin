<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];

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
            'gambar' => $b['gambar'],
            'stok' => $b['stok'],
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
    <title>Keranjang Belanja - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }
        body { 
            background: #fff5f8; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            min-height: 100vh;
        }
        
        .keranjang-card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.08); 
            width: 100%;
            max-width: 420px;
            background: white; 
            padding: 20px;
        }
        
        .header-keranjang { text-align: center; margin-bottom: 15px; }
        .header-keranjang h5 { color: #d81b60; font-weight: bold; margin-bottom: 2px; font-size: 1.1rem; }
        .header-keranjang small { color: #888; font-size: 0.7rem; }
        
        .item-row { 
            display: flex; 
            align-items: center; 
            padding: 8px 0; 
            border-bottom: 1px solid #f0f0f0; 
            gap: 10px; 
        }
        .item-row:last-of-type { border-bottom: none; }
        
        .item-img { 
            width: 45px; 
            height: 45px; 
            object-fit: contain; 
            background: #fce4ec; 
            border-radius: 8px; 
            padding: 3px;
            flex-shrink: 0;
        }
        
        .item-info { flex: 1; min-width: 0; }
        .item-info h6 { margin: 0; font-weight: 600; color: #333; font-size: 0.78rem; }
        .item-info small { color: #999; font-size: 0.65rem; }
        .item-info .harga { color: #d81b60; font-weight: bold; font-size: 0.78rem; margin-top: 2px; }
        
        .qty-input { 
            width: 45px; 
            text-align: center; 
            border-radius: 6px; 
            border: 1px solid #ddd; 
            padding: 3px 2px;
            font-size: 0.75rem;
        }
        
        .item-subtotal { 
            font-weight: bold; 
            color: #d81b60; 
            font-size: 0.78rem;
            min-width: 70px; 
            text-align: right; 
        }
        
        .btn-hapus { 
            color: #dc3545; 
            text-decoration: none; 
            font-size: 0.9rem;
            padding: 2px 4px;
            flex-shrink: 0;
        }
        .btn-hapus:hover { color: #a00; }
        
        .total-box { 
            background: linear-gradient(135deg, #ec407a, #d81b60); 
            color: white; 
            padding: 10px 15px; 
            border-radius: 10px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 12px;
        }
        .total-box small { opacity: 0.9; font-size: 0.65rem; }
        .total-box h5 { margin: 0; font-weight: bold; font-size: 1.05rem; }
        
        .btn-checkout { 
            background: linear-gradient(135deg, #ec407a, #d81b60); 
            color: white; 
            border: none; 
            border-radius: 10px; 
            padding: 10px; 
            font-weight: 600; 
            font-size: 0.82rem; 
            width: 100%; 
            margin-top: 10px;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        .btn-checkout:hover { background: linear-gradient(135deg, #d81b60, #ad1457); color: white; }
        
        .btn-lanjut { 
            background: #f5f5f5; 
            color: #666; 
            border: none; 
            border-radius: 10px; 
            padding: 9px; 
            font-weight: 500; 
            font-size: 0.78rem; 
            width: 100%; 
            margin-top: 6px;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .btn-lanjut:hover { background: #e0e0e0; color: #333; }
        
        .empty-cart { text-align: center; padding: 35px 20px; }
        .empty-cart .icon { font-size: 3rem; margin-bottom: 10px; }
        .empty-cart h5 { font-size: 0.95rem; }
        .empty-cart p { font-size: 0.78rem; }
        
        /* ========================================== */
        /* RESPONSIVE HP */
        /* ========================================== */
        @media (max-width: 768px) {
            body { 
                padding: 15px 12px;
                align-items: flex-start;   /* Di HP, card dari atas */
            }
            .keranjang-card { 
                padding: 16px 14px; 
                max-width: 100%; 
                border-radius: 12px;
            }
            .header-keranjang h5 { font-size: 1rem; }
            .header-keranjang small { font-size: 0.65rem; }
            
            .item-row { padding: 7px 0; gap: 8px; }
            .item-img { width: 40px; height: 40px; }
            .item-info h6 { font-size: 0.72rem; }
            .item-info small { font-size: 0.6rem; }
            .item-info .harga { font-size: 0.72rem; }
            
            .qty-input { width: 40px; font-size: 0.7rem; }
            .item-subtotal { font-size: 0.72rem; min-width: 65px; }
            .btn-hapus { font-size: 0.85rem; }
            
            .total-box { padding: 9px 12px; }
            .total-box small { font-size: 0.6rem; }
            .total-box h5 { font-size: 0.95rem; }
            
            .btn-checkout { font-size: 0.78rem; padding: 9px; }
            .btn-lanjut { font-size: 0.72rem; padding: 8px; }
        }
    </style>
</head>
<body>

<div class="keranjang-card">
    <div class="header-keranjang">
        <h5>🛒 Keranjang Belanja</h5>
        <small>Periksa pesananmu sebelum checkout</small>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <div class="icon">🛍️</div>
            <h5>Keranjang masih kosong</h5>
            <p class="text-muted">Yuk, pilih produk skincare favoritmu dulu!</p>
            <a href="katalog.php" class="btn btn-danger mt-2 px-4 btn-sm">⬅️ Kembali ke Katalog</a>
        </div>
    <?php else: ?>
        
        <?php foreach ($items as $item): ?>
        <div class="item-row">
            <?php if ($item['gambar'] && file_exists('../public/uploads/' . $item['gambar'])): ?>
                <img src="uploads/<?= $item['gambar']; ?>" class="item-img">
            <?php else: ?>
                <div class="item-img d-flex align-items-center justify-content-center" style="font-size: 1.2rem;">🧴</div>
            <?php endif; ?>
            
            <div class="item-info">
                <h6><?= htmlspecialchars($item['nama_barang']); ?></h6>
                <small><?= htmlspecialchars($item['kategori']); ?></small>
                <div class="harga"><?= rupiah($item['harga']); ?></div>
            </div>

            <form action="update_keranjang.php" method="POST" style="margin: 0;">
                <input type="hidden" name="id_barang" value="<?= $item['id_barang']; ?>">
                <input type="number" name="jumlah" class="qty-input" min="1" max="<?= $item['stok']; ?>" value="<?= $item['jumlah']; ?>" onchange="this.form.submit()">
            </form>

            <div class="item-subtotal"><?= rupiah($item['subtotal']); ?></div>

            <a href="hapus_keranjang.php?id=<?= $item['id_barang']; ?>" class="btn-hapus" onclick="return confirm('Hapus produk ini?')">🗑️</a>
        </div>
        <?php endforeach; ?>

        <div class="total-box">
            <div>
                <small>TOTAL BAYAR</small>
                <h5><?= rupiah($total); ?></h5>
            </div>
            <div style="font-size: 1.5rem; opacity: 0.5;">💰</div>
        </div>

        <div class="mt-3">
            <a href="checkout.php" class="btn-checkout">💳 Lanjut ke Checkout</a>
            <a href="katalog.php" class="btn-lanjut">⬅️ Lanjut Belanja</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>