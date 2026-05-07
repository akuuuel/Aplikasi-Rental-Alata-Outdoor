<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT t.*, p.nama, p.no_telp, p.alamat FROM transaksi t 
          JOIN pelanggan p ON t.id_pelanggan = p.id 
          WHERE t.id = $id";
$transaksi = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$transaksi) {
    header("Location: index.php");
    exit;
}

$details = mysqli_query($conn, "SELECT d.*, b.nama_barang, b.harga_sewa FROM detail_transaksi d 
                                JOIN barang b ON d.id_barang = b.id 
                                WHERE d.id_transaksi = $id");

include '../includes/header.php';
?>

<div class="mb-4">
    <a href="index.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Transaksi #TRX-<?= str_pad($transaksi['id'], 4, '0', STR_PAD_LEFT) ?></h4>
            <span class="badge bg-<?= ($transaksi['status_transaksi'] == 'disewa') ? 'warning' : 'success' ?> fs-6">
                <?= strtoupper($transaksi['status_transaksi']) ?>
            </span>
        </div>
        <div class="text-end">
            <a href="cetak_struk.php?id=<?= $transaksi['id'] ?>" target="_blank" class="btn btn-dark mb-2"><i class="bi bi-printer"></i> Cetak Struk</a>
            <p class="mb-0 text-muted small">Waktu Transaksi: <?= date('d M Y H:i:s', strtotime($transaksi['tgl_sewa'])) ?></p>
            <p class="mb-0 text-muted small">Batas Kembali: <?= date('d M Y', strtotime($transaksi['tgl_kembali'])) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="fw-bold text-muted border-bottom pb-2">Informasi Pelanggan</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="150">Nama</td>
                    <td>: <?= $transaksi['nama'] ?></td>
                </tr>
                <tr>
                    <td>No. Telp</td>
                    <td>: <?= $transaksi['no_telp'] ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: <?= $transaksi['alamat'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h6 class="fw-bold text-muted mb-3">Item yang Disewa</h6>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga/Hari</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($details)): ?>
                <tr>
                    <td><?= $d['nama_barang'] ?></td>
                    <td>Rp <?= number_format($d['harga_sewa'], 0, ',', '.') ?></td>
                    <td><?= $d['qty'] ?></td>
                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">Total Bayar:</th>
                    <th>Rp <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
