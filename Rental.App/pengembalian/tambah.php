<?php
require_once '../config/koneksi.php';

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

// Pastikan ID Transaksi ada
if (!isset($_GET['id_transaksi'])) {
    die("Error: ID Transaksi tidak ditemukan di URL.");
}

$id_transaksi = (int) $_GET['id_transaksi'];

// Ambil data transaksi yang statusnya masih 'disewa'
$query = "SELECT t.*, p.nama, p.no_telp 
          FROM transaksi t 
          JOIN pelanggan p ON t.id_pelanggan = p.id 
          WHERE t.id = $id_transaksi";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error Database: " . mysqli_error($conn));
}

$trx = mysqli_fetch_assoc($result);

// Jika transaksi tidak ditemukan atau sudah kembali
if (!$trx) {
    die("Error: Data transaksi tidak ditemukan.");
}

if ($trx['status_transaksi'] == 'kembali') {
    flash('success', 'Transaksi ini sudah dikembalikan sebelumnya.');
    header("Location: ../transaksi/index.php");
    exit;
}

// Hitung Denda Otomatis
$tgl_kembali = new DateTime($trx['tgl_kembali']);
$tgl_sekarang = new DateTime(date('Y-m-d'));
$denda = 0;
$hari_telat = 0;

if ($tgl_sekarang > $tgl_kembali) {
    $hari_telat = $tgl_sekarang->diff($tgl_kembali)->days;
    $denda = $hari_telat * 20000; // Denda 20rb per hari
}

include '../includes/header.php';
?>

<div class="mb-4 d-flex align-items-center px-2 px-md-0">
    <a href="../transaksi/index.php" class="btn btn-light rounded-circle me-3 shadow-sm"
        style="width:40px; height:40px;"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-800 mb-0">Konfirmasi Pengembalian</h4>
</div>

<div class="row g-4 px-2 px-md-0 pb-5">
    <div class="col-lg-5">
        <div class="card-modern p-4 border-0 shadow-sm mb-4">
            <h6 class="fw-800 text-dark mb-4">Informasi Penyewa</h6>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-gojek-soft p-3 rounded-circle me-3">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-800 mb-0"><?= htmlspecialchars($trx['nama']) ?></h5>
                    <p class="text-muted small mb-0"><?= $trx['no_telp'] ?></p>
                </div>
            </div>

            <div class="p-3 bg-light rounded-4">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted fw-bold" style="font-size: 10px;">ID TRANSAKSI</small>
                    <small class="fw-800 text-dark"
                        style="font-size: 10px;">#TRX-<?= str_pad($id_transaksi, 4, '0', STR_PAD_LEFT) ?></small>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted fw-bold" style="font-size: 10px;">BATAS KEMBALI</small>
                    <small class="fw-800 text-dark"
                        style="font-size: 10px;"><?= date('d M Y', strtotime($trx['tgl_kembali'])) ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <form action="proses.php" method="POST">
            <input type="hidden" name="id_transaksi" value="<?= $id_transaksi ?>">

            <div class="card-modern p-4 border-0 shadow-sm">
                <h6 class="fw-800 text-dark mb-4">Proses Kembali</h6>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">TANGGAL KEMBALI (HARI INI)</label>
                    <input type="date" name="tgl_dikembalikan" class="form-control-gojek py-3"
                        value="<?= date('Y-m-d') ?>" readonly>
                </div>

                <div class="p-4 rounded-4 mb-4 <?= ($denda > 0) ? 'bg-danger bg-opacity-10' : 'bg-gojek-soft' ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-800 small <?= ($denda > 0) ? 'text-danger' : 'text-success' ?>">
                            <?= ($denda > 0) ? "TERLAMBAT $hari_telat HARI" : "TEPAT WAKTU" ?>
                        </span>
                        <span class="fw-800 fs-5 <?= ($denda > 0) ? 'text-danger' : 'text-success' ?>">
                            Rp <?= number_format($denda, 0, ',', '.') ?>
                        </span>
                    </div>
                    <small class="text-muted d-block" style="font-size: 11px;">
                        <?= ($denda > 0) ? "*Denda Rp 20.000 / hari keterlambatan." : "Hebat! Pengembalian tepat waktu." ?>
                    </small>
                    <input type="hidden" name="denda" value="<?= $denda ?>">
                </div>

                <div class="alert alert-warning border-0 rounded-4 p-3 d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <small class="fw-600">Pastikan semua alat sudah dicek kelengkapannya sebelum konfirmasi.</small>
                </div>

                <button type="submit" class="btn btn-gojek w-100 py-3 shadow-sm fw-800">
                    KONFIRMASI PENGEMBALIAN
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>