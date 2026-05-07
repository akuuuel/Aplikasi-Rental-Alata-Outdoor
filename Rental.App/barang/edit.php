<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $harga = (float)$_POST['harga_sewa'];
    $status = $_POST['status'];

    // Handle File Upload
    $foto = $data['foto'];
    if ($_FILES['foto']['name']) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_new = time() . "." . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/" . $foto_new)) {
            // Hapus foto lama jika ada
            if ($data['foto'] && file_exists("../assets/img/" . $data['foto'])) {
                unlink("../assets/img/" . $data['foto']);
            }
            $foto = $foto_new;
        }
    }

    $update = "UPDATE barang SET 
               nama_barang = '$nama', 
               kategori = '$kategori', 
               stok = $stok, 
               harga_sewa = $harga, 
               foto = '$foto', 
               status = '$status' 
               WHERE id = $id";
    
    if (mysqli_query($conn, $update)) {
        flash('success', 'Barang berhasil diperbarui!');
        header("Location: index.php");
        exit;
    }
}

include '../includes/header.php';
?>

<div class="mb-4 d-flex align-items-center">
    <a href="index.php" class="btn btn-light rounded-circle me-3 shadow-sm" style="width:40px; height:40px;"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-800 mb-0">Edit Informasi Barang</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-modern p-4 p-md-5">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">NAMA BARANG</label>
                    <input type="text" name="nama_barang" class="form-control-gojek w-100" value="<?= $data['nama_barang'] ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-muted small fw-800">KATEGORI</label>
                        <select name="kategori" class="form-select form-control-gojek" required>
                            <?php 
                            $cats = ['Tenda', 'Tas/Carrier', 'Sepatu', 'Alat Masak', 'Lampu/Headlamp', 'Lainnya'];
                            foreach($cats as $cat):
                            ?>
                                <option value="<?= $cat ?>" <?= ($data['kategori'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-muted small fw-800">STOK SAAT INI</label>
                        <input type="number" name="stok" class="form-control-gojek w-100" value="<?= $data['stok'] ?>" min="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">HARGA SEWA PER HARI</label>
                    <div class="input-group bg-light rounded-4 overflow-hidden border-0">
                        <span class="input-group-text border-0 bg-transparent fw-800 text-success">Rp</span>
                        <input type="number" name="harga_sewa" class="form-control border-0 bg-transparent py-3 fw-800" value="<?= (int)$data['harga_sewa'] ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800 d-block">STATUS KETERSEDIAAN</label>
                    <div class="d-flex gap-4 mt-2">
                        <div class="form-check custom-check">
                            <input class="form-check-input" type="radio" name="status" id="tersedia" value="tersedia" <?= ($data['status'] == 'tersedia') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-600" for="tersedia">Tersedia</label>
                        </div>
                        <div class="form-check custom-check">
                            <input class="form-check-input" type="radio" name="status" id="kosong" value="kosong" <?= ($data['status'] == 'kosong') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-600" for="kosong">Kosong / Maintenance</label>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label text-muted small fw-800">FOTO BARANG</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php if ($data['foto']): ?>
                            <img src="<?= base_url('assets/img/' . $data['foto']) ?>" class="rounded-4 shadow-sm" width="80" height="80" style="object-fit: cover;">
                            <span class="text-muted small fw-600">Foto saat ini</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light position-relative">
                        <input type="file" name="foto" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/*">
                        <i class="bi bi-cloud-arrow-up text-success fs-2"></i>
                        <p class="mb-0 mt-2 small fw-600 text-muted">Klik untuk ganti foto baru</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-gojek w-100 py-3 shadow-lg fs-5">
                    Perbarui Data Barang
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control-gojek {
        background: #F6F6F6 !important;
        border: 2px solid #F6F6F6 !important;
        border-radius: 14px !important;
        padding: 12px 18px !important;
        font-weight: 600 !important;
    }
    .form-control-gojek:focus {
        background: white !important;
        border-color: #00AA13 !important;
        box-shadow: none !important;
    }
    .custom-check .form-check-input:checked {
        background-color: #00AA13;
        border-color: #00AA13;
    }
    .border-dashed { border-style: dashed !important; }
</style>

<?php include '../includes/footer.php'; ?>
