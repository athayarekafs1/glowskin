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
    <title>Pembayaran - GlowSkin</title>
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
        .pay-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 360px;
            background: white;
            padding: 18px 16px;
            text-align: center;
        }
        .pay-icon { font-size: 32px; margin-bottom: 3px; }
        .pay-title { color: #d81b60; font-weight: bold; margin-bottom: 3px; font-size: 1rem; }
        .pay-code { color: #888; font-size: 0.7rem; margin-bottom: 10px; }
        
        .total-show {
            background: #fce4ec;
            padding: 9px;
            border-radius: 8px;
            margin: 9px 0;
        }
        .total-show small { font-size: 0.62rem; color: #888; }
        .total-show h4 { color: #d81b60; font-weight: bold; margin: 2px 0 0; font-size: 1.1rem; }
        
        .qr-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 9px 0;
            padding: 9px;
            background: #fff5f8;
            border-radius: 8px;
        }
        .qr-wrapper p { margin: 0 0 5px 0; font-size: 0.65rem; color: #888; }
        .qr-img {
            width: 140px; height: 140px;
            border-radius: 6px;
            border: 2px solid #f8bbd0;
            background: white;
            padding: 3px;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }
        
        .timer-circle {
            width: 50px; height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ec407a, #d81b60);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; font-weight: bold;
            margin: 9px auto 4px;
            box-shadow: 0 4px 12px rgba(216,27,96,0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .timer-text { font-size: 0.62rem; color: #888; margin: 0; }
        .progress { height: 4px; border-radius: 10px; margin-top: 6px; }
        
        .info-box {
            background: #fff5f8;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            text-align: left;
            margin: 6px 0;
        }
        
        @media (max-width: 768px) {
            body { padding: 15px; }
            .pay-card { padding: 16px 14px; max-width: 100%; }
            .pay-icon { font-size: 28px; }
            .pay-title { font-size: 0.92rem; }
            .qr-img { width: 130px; height: 130px; }
            .timer-circle { width: 45px; height: 45px; font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<div class="pay-card">
    <div class="pay-icon">💳</div>
    <div class="pay-title">Menunggu Pembayaran</div>
    <div class="pay-code">Kode: <b><?= $trx['kode_transaksi']; ?></b></div>

    <div class="total-show">
        <small>Total yang harus dibayar</small>
        <h4><?= rupiah($trx['total_bayar']); ?></h4>
    </div>

    <?php if ($trx['metode_bayar'] == 'QRIS'): ?>
        <div class="qr-wrapper">
            <p>📷 Scan QRIS untuk membayar</p>
            <img src="/sistem_skincare/public/uploads/qris.png" alt="QRIS" class="qr-img">
            <p style="margin-top: 6px; margin-bottom: 0; font-size: 0.62rem;">a.n. GlowSkin Skincare</p>
        </div>
    <?php elseif ($trx['metode_bayar'] == 'Transfer Bank'): ?>
        <div class="info-box">
            <b style="color: #d81b60;">🏦 Transfer ke:</b><br>
            Bank BCA - 1234567890<br>
            a.n. GlowSkin Skincare
        </div>
    <?php else: ?>
        <div class="info-box">
            <b style="color: #d81b60;">📱 E-Wallet:</b><br>
            OVO / Dana / Gopay<br>
            0812-3456-7890
        </div>
    <?php endif; ?>

    <div class="timer-circle" id="timer">8</div>
    <p class="timer-text">Diverifikasi otomatis</p>
    <div class="progress">
        <div class="progress-bar bg-danger" id="progressBar" style="width: 0%; transition: width 1s linear;"></div>
    </div>
</div>

<script>
let detik = 8;
const timerEl = document.getElementById('timer');
const progressEl = document.getElementById('progressBar');

const interval = setInterval(function() {
    detik--;
    timerEl.innerText = detik;
    progressEl.style.width = ((8 - detik) / 8 * 100) + '%';
    if (detik <= 0) {
        clearInterval(interval);
        window.location.href = 'pembayaran_sukses.php?id=<?= $id_transaksi; ?>';
    }
}, 1000);
</script>
</body>
</html>