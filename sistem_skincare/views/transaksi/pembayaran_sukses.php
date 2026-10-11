<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_transaksi = $_GET['id'];
$query = "SELECT t.*, u.nama_lengkap FROM transaksi t 
          JOIN users u ON t.id_user = u.id_user WHERE t.id_transaksi = '$id_transaksi'";
$trx = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

if (!$trx) { header("Location: ../dashboard.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Pembayaran Sukses - GlowSkin</title>
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
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            background: white;
            text-align: center;
            padding: 35px 25px;
        }
        .check-circle {
            width: 90px; height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #66bb6a, #43a047);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
            animation: pop 0.6s ease-out;
        }
        @keyframes pop {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-title { color: #43a047; font-weight: bold; font-size: 1.3rem; margin-bottom: 8px; }
        .success-sub { color: #888; font-size: 0.85rem; margin-bottom: 15px; }
        .success-info { text-align: left; font-size: 0.82rem; margin: 15px 0; }
        .success-info p { margin: 4px 0; }
        .progress { height: 5px; border-radius: 10px; margin-top: 15px; }
        
        /* HP */
        @media (max-width: 768px) {
            body { padding: 15px; }
            .success-card { padding: 28px 20px; max-width: 100%; }
            .check-circle { width: 80px; height: 80px; font-size: 2.5rem; }
            .success-title { font-size: 1.15rem; }
            .success-sub { font-size: 0.78rem; }
            .success-info { font-size: 0.78rem; }
        }
    </style>
</head>
<body>

<div class="success-card">
    <div class="check-circle">✓</div>
    <div class="success-title">Pembayaran Berhasil!</div>
    <div class="success-sub">Terima kasih, <b><?= htmlspecialchars($trx['nama_pelanggan']); ?></b></div>

    <div class="success-info">
        <p><b>Kode:</b> <?= $trx['kode_transaksi']; ?></p>
        <p><b>Total:</b> <b style="color: #d81b60;"><?= rupiah($trx['total_bayar']); ?></b></p>
        <p><b>Metode:</b> <?= $trx['metode_bayar']; ?></p>
        <p><b>Sales:</b> <?= htmlspecialchars($trx['nama_lengkap']); ?></p>
        <p><b>Tanggal:</b> <?= date('d/m/Y H:i', strtotime($trx['tanggal'])); ?></p>
    </div>

    <p class="text-muted" style="font-size: 0.75rem;">Mengarahkan ke struk...</p>
    <div class="progress">
        <div class="progress-bar bg-success" id="pb" style="width: 0%; transition: width 2.5s linear;"></div>
    </div>
</div>

<script>
setTimeout(function() { document.getElementById('pb').style.width = '100%'; }, 100);
setTimeout(function() {
    window.location.href = 'struk.php?id=<?= $id_transaksi; ?>';
}, 3000);
</script>
</body>
</html>