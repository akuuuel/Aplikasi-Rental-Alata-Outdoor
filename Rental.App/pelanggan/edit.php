<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM pelanggan WHERE id = $id";
$pelanggan = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$pelanggan) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $ktp = mysqli_real_escape_string($conn, $_POST['ktp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $update = "UPDATE pelanggan SET nama = '$nama', no_telp = '$no_telp', ktp = '$ktp', alamat = '$alamat' WHERE id = $id";
    
    if (mysqli_query($conn, $update)) {
        flash('success', 'Data pelanggan berhasil diperbarui!');
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal memperbarui pelanggan: " . mysqli_error($conn);
    }
}

include '../includes/header.php';
?>

<div class="mb-4">
    <a href="index.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card p-4 mx-auto" style="max-width: 600px;">
    <h4 class="fw-bold mb-4">Edit Pelanggan</h4>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= $pelanggan['nama'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" value="<?= $pelanggan['no_telp'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">No. KTP</label>
            <input type="text" name="ktp" class="form-control" value="<?= $pelanggan['ktp'] ?>" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required><?= $pelanggan['alamat'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Perbarui Pelanggan</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
