<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $ktp = mysqli_real_escape_string($conn, $_POST['ktp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $query = "INSERT INTO pelanggan (nama, no_telp, ktp, alamat) VALUES ('$nama', '$no_telp', '$ktp', '$alamat')";
    
    if (mysqli_query($conn, $query)) {
        flash('success', 'Pelanggan berhasil ditambahkan!');
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menambahkan pelanggan: " . mysqli_error($conn);
    }
}

include '../includes/header.php';
?>

<div class="mb-4">
    <a href="index.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card p-4 mx-auto" style="max-width: 600px;">
    <h4 class="fw-bold mb-4">Tambah Pelanggan Baru</h4>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">No. KTP</label>
            <input type="text" name="ktp" class="form-control" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Simpan Pelanggan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
