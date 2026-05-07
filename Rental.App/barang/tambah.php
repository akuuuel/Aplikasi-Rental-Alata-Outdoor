<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok = (int)$_POST['stok'];
    $harga = (float)$_POST['harga_sewa'];
    $status = $_POST['status'];

    // Handle File Upload
    $foto = "";
    if ($_FILES['foto']['name']) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/" . $foto);
    }

    $query = "INSERT INTO barang (nama_barang, kategori, stok, harga_sewa, foto, status) 
              VALUES ('$nama', '$kategori', $stok, $harga, '$foto', '$status')";
    
    if (mysqli_query($conn, $query)) {
        flash('success', 'Barang berhasil ditambahkan!');
        header("Location: index.php");
        exit;
    }
}

include '../includes/header.php';
?>

<div class="mb-4 d-flex align-items-center">
    <a href="index.php" class="btn btn-light rounded-circle me-3 shadow-sm" style="width:40px; height:40px;"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-800 mb-0">Tambah Barang Baru</h4>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-modern p-4 p-md-5">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">NAMA BARANG</label>
                    <input type="text" name="nama_barang" class="form-control-gojek w-100" placeholder="Contoh: Tenda Dome 4P" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-muted small fw-800">KATEGORI</label>
                        <select name="kategori" class="form-select form-control-gojek" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Tenda">Tenda</option>
                            <option value="Tas/Carrier">Tas/Carrier</option>
                            <option value="Sepatu">Sepatu</option>
                            <option value="Alat Masak">Alat Masak</option>
                            <option value="Lampu/Headlamp">Lampu/Headlamp</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label text-muted small fw-800">STOK AWAL</label>
                        <input type="number" name="stok" class="form-control-gojek w-100" value="1" min="1" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">HARGA SEWA PER HARI</label>
                    <div class="input-group bg-light rounded-4 overflow-hidden border-0">
                        <span class="input-group-text border-0 bg-transparent fw-800 text-success">Rp</span>
                        <input type="number" name="harga_sewa" class="form-control border-0 bg-transparent py-3 fw-800" placeholder="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800 d-block">STATUS KETERSEDIAAN</label>
                    <div class="d-flex gap-4 mt-2">
                        <div class="form-check custom-check">
                            <input class="form-check-input" type="radio" name="status" id="tersedia" value="tersedia" checked>
                            <label class="form-check-label fw-600" for="tersedia">Tersedia</label>
                        </div>
                        <div class="form-check custom-check">
                            <input class="form-check-input" type="radio" name="status" id="kosong" value="kosong">
                            <label class="form-check-label fw-600" for="kosong">Kosong / Maintenance</label>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label text-muted small fw-800">FOTO BARANG</label>
                    <div class="p-4 border-2 border-dashed rounded-4 text-center bg-light position-relative">
                        <input type="file" name="foto" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/*">
                        <i class="bi bi-cloud-arrow-up text-success fs-1"></i>
                        <p class="mb-0 mt-2 small fw-600 text-muted">Klik atau drag gambar ke sini untuk upload</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-gojek w-100 py-3 shadow-lg fs-5">
                    Simpan ke Inventaris
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
