<?php
require_once 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

// Get statistics
$count_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$count_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan"))['total'];
$count_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status_transaksi = 'disewa'"))['total'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM transaksi WHERE status_transaksi = 'kembali'"))['total'] ?? 0;

include 'includes/header.php';
?>

<!-- Gojek Style Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-800 mb-1">Halo, <?= explode(' ', $_SESSION['user']['nama_lengkap'])[0] ?>!</h4>
            <p class="text-muted small mb-0">Cek performa rental Anda hari ini.</p>
        </div>
        <div class="bg-gojek-soft p-2 rounded-circle">
            <i class="bi bi-patch-check-fill text-success fs-4"></i>
        </div>
    </div>
</div>

<!-- Stats Carousel (Gojek Style Cards) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card-modern h-100 p-3">
            <div class="bg-gojek-soft rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-box-seam text-success"></i>
            </div>
            <h3 class="fw-800 mb-0"><?= $count_barang ?></h3>
            <p class="text-muted small mb-0 fw-bold">Total Barang</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-modern h-100 p-3">
            <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-person text-danger"></i>
            </div>
            <h3 class="fw-800 mb-0"><?= $count_pelanggan ?></h3>
            <p class="text-muted small mb-0 fw-bold">Pelanggan</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-modern h-100 p-3">
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-cart text-primary"></i>
            </div>
            <h3 class="fw-800 mb-0"><?= $count_transaksi ?></h3>
            <p class="text-muted small mb-0 fw-bold">Sewa Aktif</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card-modern h-100 p-3">
            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 40px; height: 40px;">
                <i class="bi bi-wallet2 text-warning"></i>
            </div>
            <h3 class="fw-800 mb-0" style="font-size: 1.2rem;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
            </h3>
            <p class="text-muted small mb-0 fw-bold">Pendapatan</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card-modern">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-800 mb-0">Pesanan Terbaru</h6>
                <a href="<?= base_url('transaksi/index.php') ?>"
                    class="text-success text-decoration-none small fw-800">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                    <thead>
                        <tr class="text-muted small border-bottom">
                            <th class="pb-3">PELANGGAN</th>
                            <th class="pb-3">TANGGAL</th>
                            <th class="pb-3 text-end">TOTAL</th>
                            <th class="pb-3 text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT t.*, p.nama FROM transaksi t JOIN pelanggan p ON t.id_pelanggan = p.id ORDER BY t.id DESC LIMIT 5";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)):
                            ?>
                            <tr class="border-bottom">
                                <td class="py-3 fw-800 text-dark"><?= $row['nama'] ?></td>
                                <td class="py-3 text-muted"><?= date('d M Y', strtotime($row['tgl_sewa'])) ?></td>
                                <td class="py-3 text-end fw-800 text-dark">Rp
                                    <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                <td class="py-3 text-center">
                                    <span
                                        class="badge-gojek <?= ($row['status_transaksi'] == 'disewa') ? 'bg-warning bg-opacity-10 text-warning' : 'bg-gojek-soft' ?>">
                                        <?= strtoupper($row['status_transaksi']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-modern">
            <h6 class="fw-800 mb-4">Layanan Terlaris</h6>
            <?php
            $pop_query = "SELECT b.nama_barang, b.kategori, SUM(d.qty) as total_qty 
                          FROM detail_transaksi d 
                          JOIN barang b ON d.id_barang = b.id 
                          GROUP BY b.id 
                          ORDER BY total_qty DESC LIMIT 5";
            $pop_result = mysqli_query($conn, $pop_query);
            while ($pop = mysqli_fetch_assoc($pop_result)):
                ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i class="bi bi-lightning-fill text-success"></i>
                        </div>
                        <div>
                            <div class="fw-800 small text-dark"><?= $pop['nama_barang'] ?></div>
                            <small class="text-muted"><?= $pop['kategori'] ?></small>
                        </div>
                    </div>
                    <span
                        class="badge bg-light text-dark border-0 rounded-pill px-3 py-2 small fw-800"><?= $pop['total_qty'] ?>x</span>
                </div>
            <?php endwhile; ?>
            <a href="<?= base_url('transaksi/tambah.php') ?>" class="btn btn-gojek w-100 mt-3 shadow-sm">
                Mulai Transaksi Baru
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>