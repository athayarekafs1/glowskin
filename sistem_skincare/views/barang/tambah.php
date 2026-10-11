<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
            padding: 20px 22px;
            background: white;
            max-width: 550px;
            margin: auto;
        }
        .page-card h4 {
            color: #d81b60;
            font-weight: bold;
            margin-bottom: 18px;
            font-size: 1.1rem;
            text-align: center;
        }
        .form-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 4px;
        }
        .form-control {
            font-size: 0.82rem;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #d81b60;
            box-shadow: 0 0 0 0.15rem rgba(216,27,96,0.12);
        }
        .mb-3 { margin-bottom: 12px !important; }
        .btn { font-size: 0.82rem; padding: 9px 12px; border-radius: 8px; font-weight: 600; }
        .text-muted { font-size: 0.72rem; }
        
        /* RESPONSIVE HP */
        @media (max-width: 768px) {
            .page-card {
                padding: 16px 16px !important;
                border-radius: 10px;
                max-width: 100%;
            }
            .page-card h4 { font-size: 1rem; margin-bottom: 14px; }
            .form-label { font-size: 0.75rem; }
            .form-control { font-size: 0.8rem; padding: 7px 10px; }
            .mb-3 { margin-bottom: 10px !important; }
            .btn { font-size: 0.78rem; padding: 8px 10px; }
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h4>➕ Tambah Produk Skincare</h4>
        <form action="../../controllers/BarangController.php?aksi=tambah" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" class="form-control" placeholder="Serum, Moisturizer, dll" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Produk</label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>
            </div>
            <div class="d-flex gap-2 mt-3">
                <a href="index.php" class="btn btn-secondary w-50">Batal</a>
                <button type="submit" class="btn btn-danger w-50">Simpan</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>