<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$query = "SELECT t.*, p.nama, p.no_telp FROM transaksi t 
          JOIN pelanggan p ON t.id_pelanggan = p.id 
          WHERE t.status_transaksi = 'disewa' 
          ORDER BY t.tgl_kembali ASC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Antrian Pengembalian</h4>
</div>

<?= flash('success') ?>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Tgl Kembali (Batas)</th>
                    <th>Status Keterlambatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
                    $tgl_kembali = new DateTime($row['tgl_kembali']);
                    $tgl_sekarang = new DateTime(date('Y-m-d'));
                    $diff = $tgl_sekarang->diff($tgl_kembali);
                    $is_late = ($tgl_sekarang > $tgl_kembali);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>#TRX-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <div class="fw-semibold"><?= $row['nama'] ?></div>
                            <small class="text-muted"><?= $row['no_telp'] ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></td>
                        <td>
                            <?php if ($is_late): ?>
                                <span class="badge bg-danger">Terlambat <?= $diff->days ?> Hari</span>
                            <?php else: ?>
                                <span class="badge bg-success">Belum Terlambat</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="proses.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Proses Kembali</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada barang yang sedang disewa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>