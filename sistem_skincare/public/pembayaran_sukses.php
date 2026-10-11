<?php
require_once '../config/koneksi.php';
require_once '../config/helper.php';

$id_pesanan = $_GET['id'];

mysqli_query($koneksi, "UPDATE pesanan SET status = 'Dibayar' WHERE id_pesanan = '$id_pesanan'");

$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) { header("Location: katalog.php"); exit; }

$detail = mysqli_query($koneksi, "SELECT pd.*, b.nama_barang FROM pesanan_detail pd 
                                   JOIN barang b ON pd.id_barang = b.id_barang 
                                   WHERE pd.id_pesanan = '$id_pesanan'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Pembayaran Berhasil - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            width: 100%; min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
        }
        .success-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 340px;      /* Diperkecil dari 360 */
            background: white;
            text-align: center;
            padding: 22px 18px;
        }
        .check-circle {
            width: 65px; height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #66bb6a, #43a047);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 12px;
            animation: pop 0.6s ease-out;
        }
        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-title { color: #43a047; font-weight: bold; font-size: 1rem; margin-bottom: 5px; }
        .success-sub { color: #888; font-size: 0.72rem; margin-bottom: 10px; }
        
        .detail-box {
            background: #fce4ec;
            padding: 9px 11px;
            border-radius: 7px;
            margin: 10px 0;
            text-align: left;
            font-size: 0.68rem;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            border-bottom: 1px dashed #f8bbd0;
        }
        .detail-row:last-child { border-bottom: none; }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 11px;
            background: #fce4ec;
            border-radius: 7px;
            margin-top: 6px;
        }
        .total-row small { font-size: 0.62rem; color: #888; }
        .total-row h5 { margin: 0; color: #d81b60; font-weight: bold; font-size: 0.9rem; }
        
        .info-bottom { text-align: left; font-size: 0.68rem; margin: 10px 0; }
        .info-bottom p { margin: 2px 0; }
        
        .btn-kembali {
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px;
            font-weight: 600;
            font-size: 0.78rem;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 8px;
        }
        .btn-kembali:hover { background: linear-gradient(135deg, #d81b60, #ad1457); color: white; }
        
        @media (max-width: 768px) {
            body { padding: 15px; }
            .success-card { padding: 20px 16px; max-width: 100%; }
            .check-circle { width: 60px; height: 60px; font-size: 2rem; }
            .success-title { font-size: 0.95rem; }
            .success-sub { font-size: 0.68rem; }
            .detail-box { font-size: 0.65rem; padding: 8px 9px; }
            .total-row h5 { font-size: 0.85rem; }
            .info-bottom { font-size: 0.65rem; }
            .btn-kembali { font-size: 0.75rem; padding: 8px; }
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="check-circle">✓</div>
    <div class="success-title">Pembayaran Berhasil!</div>
    <div class="success-sub">Terima kasih, <b><?= htmlspecialchars($pesanan['nama_pemesan']); ?></b></div>

    <div class="detail-box">
        <b style="color: #d81b60;">🛍️ Produk:</b>
        <?php while ($d = mysqli_fetch_assoc($detail)): ?>
        <div class="detail-row">
            <span><?= htmlspecialchars($d['nama_barang']); ?> (<?= $d['jumlah']; ?>x)</span>
            <b><?= rupiah($d['subtotal']); ?></b>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="total-row">
        <small>TOTAL BAYAR</small>
        <h5><?= rupiah($pesanan['total_bayar']); ?></h5>
    </div>

    <div class="info-bottom">
        <p><b>Metode:</b> <?= $pesanan['metode_bayar']; ?></p>
        <p><b>No. HP:</b> <?= htmlspecialchars($pesanan['no_hp']); ?></p>
        <p><b>Alamat:</b> <?= htmlspecialchars($pesanan['alamat']); ?></p>
    </div>

    <p class="text-muted" style="font-size: 0.62rem; margin: 8px 0 0;">Sales kami akan segera menghubungi Anda.</p>

    <a href="katalog.php" class="btn-kembali">🏠 Kembali ke Katalog</a>
</div>

</body>
</html>