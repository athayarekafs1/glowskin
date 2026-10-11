<?php
require_once '../../config/koneksi.php';
require_once '../../config/helper.php';
cek_login();
cek_role('admin');

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang = '$id'");
$barang = mysqli_fetch_assoc($query);

if (!$barang) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - GlowSkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .page-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
            padding: 15px 18px;
            background: white;
            max-width: 450px;
            margin: auto;
        }
        .page-card h4 {
            color: #d81b60;
            font-weight: bold;
            margin-bottom: 14px;
            font-size: 0.95rem;
            text-align: center;
        }
        .form-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 3px;
        }
        .form-control {
            font-size: 0.78rem;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1.5px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #d81b60;
            box-shadow: 0 0 0 0.12rem rgba(216,27,96,0.12);
        }
        .mb-3 { margin-bottom: 10px !important; }
        .btn { font-size: 0.75rem; padding: 7px 10px; border-radius: 6px; font-weight: 600; }
        .text-muted { font-size: 0.65rem; }
        .preview-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 6px;
            border: 2px solid #fce4ec;
        }
        
        /* RESPONSIVE HP */
        @media (max-width: 768px) {
            .page-card {
                padding: 14px 14px !important;
                border-radius: 8px;
                max-width: 100%;
            }
            .page-card h4 { font-size: 0.92rem; margin-bottom: 12px; }
            .form-label { font-size: 0.68rem; }
            .form-control { font-size: 0.76rem; padding: 6px 9px; }
            .mb-3 { margin-bottom: 8px !important; }
            .btn { font-size: 0.72rem; padding: 7px 8px; }
            .preview-img { width: 55px; height: 55px; }
        }
    </style>
</head>
<body>

<?php include '../partials/sidebar.php'; ?>

<div class="main-content">
    <div class="page-card">
        <h4>✏️ Edit Produk Skincare</h4>
        <form action="../../controllers/BarangController.php?aksi=edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_barang" value="<?= $barang['id_barang']; ?>">
            <input type="hidden" name="gambar_lama" value="<?= $barang['gambar']; ?>">

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($barang['kategori']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="<?= $barang['harga']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $barang['stok']; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($barang['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Produk</label><br>
                <?php if ($barang['gambar'] && file_exists('../../public/uploads/' . $barang['gambar'])): ?>
                    <img src="/sistem_skincare/public/uploads/<?= $barang['gambar']; ?>" class="preview-img">
                <?php else: ?>
                    <p class="text-muted small">Belum ada foto.</p>
                <?php endif; ?>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <small class="text-muted">Kosongkan jika tidak ingin ganti foto.</small>
            </div>
            <div class="d-flex gap-2 mt-2">
                <a href="index.php" class="btn btn-secondary w-50">Batal</a>
                <button type="submit" class="btn btn-danger w-50">Update</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>