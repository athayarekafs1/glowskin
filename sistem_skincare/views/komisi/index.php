<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();

$id_user = $_SESSION['id_user'];
$role    = $_SESSION['role'];

$filter_status = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$filter_sales  = isset($_GET['sales']) ? $_GET['sales'] : 'semua';

$where = [];
if ($role == 'sales') { $where[] = "k.id_user = '$id_user'"; }
if ($filter_status == 'pending') { $where[] = "k.status = 'Pending'"; }
elseif ($filter_status == 'dibayar') { $where[] = "k.status = 'Dibayar'"; }
if ($role == 'admin' && $filter_sales != 'semua') { $where[] = "k.id_user = '$filter_sales'"; }
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT k.*, u.nama_lengkap, t.kode_transaksi 
          FROM komisi k 
          JOIN users u ON k.id_user = u.id_user 
          JOIN transaksi t ON k.id_transaksi = t.id_transaksi 
          $where_sql
          ORDER BY k.id_komisi DESC";
$result = mysqli_query($koneksi, $query);

$total_komisi = 0;
$total_pending = 0;
$total_dibayar = 0;
$data_komisi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total_komisi += $row['jumlah_komisi'];
    if ($row['status'] == 'Pending') $total_pending += $row['jumlah_komisi'];
    else $total_dibayar += $row['jumlah_komisi'];
    $data_komisi[] = $row;
}

$sales_list = [];
if ($role == 'admin') {
    $q_sales = mysqli_query($koneksi, "SELECT id_user, nama_lengkap FROM users WHERE role = 'sales' ORDER BY nama_lengkap ASC");
    while ($s = mysqli_fetch_assoc($q_sales)) { $sales_list[] = $s; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Komisi - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 22px;
            background: white;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .page-header h4 {
            margin: 0;
            color: #d81b60;
            font-weight: bold;
            font-size: 1.15rem;
        }
        .total-card {
            color: white;
            padding: 14px 18px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .total-card h6 { margin: 0; opacity: 0.9; font-size: 0.72rem; }
        .total-card h3 { margin: 3px 0 0; font-weight: bold; font-size: 1.15rem; }
        .total-card .icon { font-size: 1.7rem; opacity: 0.4; }
        
        .filter-bar {
            background: #fafafa;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 15px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-bar label { font-size: 0.75rem; font-weight: 600; color: #666; margin: 0; }
        .filter-bar select { font-size: 0.78rem; padding: 5px 10px; border-radius: 8px; border: 1.5px solid #e0e0e0; background: white; }
        .filter-bar select:focus { border-color: #d81b60; outline: none; }
        .btn-filter { font-size: 0.72rem; padding: 4px 10px; border-radius: 8px; font-weight: 600; }
        
        /* ========================================== */
        /* TABEL - KOLOM RAPAT & RATA KONSISTEN */
        /* ========================================== */
        .table-komisi {
            font-size: 0.8rem;
            margin: 0;
            width: 100%;
            table-layout: fixed;    /* biar kolom rapat & proporsional */
        }
        .table-komisi thead th {
            background: #fce4ec;
            color: #880e4f;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            padding: 9px 10px;
            border: none;
            white-space: nowrap;
            text-align: left;
            vertical-align: middle;
        }
        .table-komisi tbody td {
            padding: 9px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f5f5f5;
            font-size: 0.78rem;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .table-komisi tbody tr:hover { background: #fff9fb; }
        
        /* Kolom Jumlah Komisi → rata KANAN */
        .table-komisi th.col-komisi,
        .table-komisi td.col-komisi {
            text-align: right !important;
            padding-right: 15px;
        }
        
        /* Kolom Aksi → rata TENGAH */
        .table-komisi th.col-aksi,
        .table-komisi td.col-aksi {
            text-align: center !important;
        }
        
        /* Kolom Status → rata KIRI (default) */
        .table-komisi th.col-status,
        .table-komisi td.col-status {
            text-align: left !important;
        }
        
        .badge-status {
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            .page-card { padding: 15px !important; }
            .page-header h4 { font-size: 1rem; }
            .total-card { padding: 10px 12px; margin-bottom: 8px; }
            .total-card h6 { font-size: 0.62rem; }
            .total-card h3 { font-size: 0.95rem; }
            .total-card .icon { font-size: 1.3rem; }
            .filter-bar { padding: 8px 10px; gap: 6px; }
            .filter-bar label { font-size: 0.7rem; }
            .filter-bar select { font-size: 0.72rem; padding: 4px 8px; }
            .btn-filter { font-size: 0.66rem; padding: 3px 8px; }
            
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                display: block !important;
                width: 100%;
                margin: 0 -15px;
                padding: 0 15px;
            }
            .table-komisi {
                min-width: 650px !important;
                display: table !important;
                width: auto !important;
            }
            .table-komisi thead th { font-size: 0.6rem; padding: 6px 6px; }
            .table-komisi tbody td { padding: 6px 6px; font-size: 0.68rem; }
            .badge-status { font-size: 0.58rem; padding: 3px 6px; }
            .btn-sm { font-size: 0.65rem; padding: 3px 6px; }
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <div class="page-header">
            <h4>💰 Riwayat Komisi <?= $role == 'sales' ? 'Saya' : 'Semua Sales'; ?></h4>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-success py-2 small"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <!-- FILTER -->
        <div class="filter-bar">
            <label>Status:</label>
            <div class="btn-group">
                <a href="?filter=semua&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'semua' ? 'btn-danger' : 'btn-outline-danger'; ?> btn-filter">Semua</a>
                <a href="?filter=pending&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?> btn-filter">⏳ Pending</a>
                <a href="?filter=dibayar&sales=<?= $filter_sales; ?>" 
                   class="btn <?= $filter_status == 'dibayar' ? 'btn-success' : 'btn-outline-success'; ?> btn-filter">✅ Dibayar</a>
            </div>

            <?php if ($role == 'admin'): ?>
            <label class="ms-1">Sales:</label>
            <form method="GET" style="display: inline-flex; gap: 5px; margin: 0;">
                <input type="hidden" name="filter" value="<?= $filter_status; ?>">
                <select name="sales" onchange="this.form.submit()">
                    <option value="semua" <?= $filter_sales == 'semua' ? 'selected' : ''; ?>>Semua Sales</option>
                    <?php foreach ($sales_list as $s): ?>
                        <option value="<?= $s['id_user']; ?>" <?= $filter_sales == $s['id_user'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($s['nama_lengkap']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>

        <!-- STATISTIK -->
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <div class="total-card" style="background: linear-gradient(135deg, #ec407a, #d81b60);">
                    <div>
                        <h6>Total Komisi</h6>
                        <h3><?= rupiah($total_komisi); ?></h3>
                    </div>
                    <div class="icon">💎</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="total-card" style="background: linear-gradient(135deg, #ffb300, #ff8f00);">
                    <div>
                        <h6>Pending</h6>
                        <h3><?= rupiah($total_pending); ?></h3>
                    </div>
                    <div class="icon">⏳</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="total-card" style="background: linear-gradient(135deg, #66bb6a, #43a047);">
                    <div>
                        <h6>Dibayar</h6>
                        <h3><?= rupiah($total_dibayar); ?></h3>
                    </div>
                    <div class="icon">✅</div>
                </div>
            </div>
        </div>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-komisi align-middle">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th style="width: 150px;">Kode Transaksi</th>
                        <?php if ($role == 'admin'): ?>
                        <th style="width: 110px;">Sales</th>
                        <?php endif; ?>
                        <th class="col-komisi" style="width: 115px;">Jumlah Komisi</th>
                        <th class="col-status" style="width: 95px;">Status</th>
                        <th style="width: 95px;">Tanggal</th>
                        <?php if ($role == 'admin'): ?>
                        <th class="col-aksi" style="width: 85px;">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data_komisi as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['kode_transaksi']; ?></td>
                        <?php if ($role == 'admin'): ?>
                            <td><?= $row['nama_lengkap']; ?></td>
                        <?php endif; ?>
                        <td class="col-komisi"><b><?= rupiah($row['jumlah_komisi']); ?></b></td>
                        <td class="col-status">
                            <?php if ($row['status'] == 'Pending'): ?>
                                <span class="badge-status" style="background: #ffe0b2; color: #e65100;">⏳ Pending</span>
                            <?php else: ?>
                                <span class="badge-status" style="background: #c8e6c9; color: #2e7d32;">✅ Dibayar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_komisi'])); ?></td>
                        <?php if ($role == 'admin'): ?>
                        <td class="col-aksi">
                            <?php if ($row['status'] == 'Pending'): ?>
                                <a href="../../controllers/KomisiController.php?aksi=bayar&id=<?= $row['id_komisi']; ?>" 
                                   class="btn btn-success btn-sm"
                                   style="font-size: 0.65rem; padding: 3px 8px;"
                                   onclick="return confirm('Tandai komisi ini sudah dibayar?')">
                                   💵 Bayar
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data_komisi)): ?>
                    <tr>
                        <td colspan="<?= $role == 'admin' ? 7 : 5; ?>" class="text-center text-muted py-4">
                            Belum ada komisi.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>