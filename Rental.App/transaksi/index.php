<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = "";
if (!empty($search)) {
    $where = "WHERE p.nama LIKE '%$search%' OR t.status_transaksi LIKE '%$search%'";
}

$query_count = "SELECT COUNT(*) as total FROM transaksi t JOIN pelanggan p ON t.id_pelanggan = p.id $where";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $query_count))['total'];
$total_page = ceil($total_data / $limit);

$query = "SELECT t.*, p.nama, p.no_telp, p.alamat FROM transaksi t 
          JOIN pelanggan p ON t.id_pelanggan = p.id 
          $where 
          ORDER BY t.id DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 px-2 px-md-0">
    <div>
        <h4 class="fw-800 mb-1">Riwayat Pesanan</h4>
        <p class="text-muted small mb-0">Ketuk untuk rincian biaya yang transparan.</p>
    </div>
    <a href="tambah.php" class="btn btn-gojek px-4 shadow-sm fw-800">
        <i class="bi bi-plus-lg"></i> <span class="d-none d-md-inline">Baru</span>
    </a>
</div>

<div class="px-2 px-md-0 mb-4">
    <div class="input-group bg-white rounded-pill px-3 py-1 shadow-sm border">
        <span class="input-group-text border-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-0 bg-transparent small" placeholder="Cari pelanggan..."
            value="<?= $search ?>" onchange="window.location.href='?search='+this.value">
    </div>
</div>

<?= flash('success') ?>

<!-- Table Desktop -->
<div class="card-modern d-none d-md-block overflow-hidden">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0">
            <thead>
                <tr class="text-muted small border-bottom">
                    <th class="pb-3 ps-4">ID TRX</th>
                    <th class="pb-3">PELANGGAN</th>
                    <th class="pb-3">PERIODE</th>
                    <th class="pb-3 text-end">TOTAL</th>
                    <th class="pb-3 text-center">STATUS</th>
                    <th class="pb-3 text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="border-bottom hover-row" onclick="showDetail(<?= htmlspecialchars(json_encode($row)) ?>)"
                        style="cursor: pointer;">
                        <td class="py-3 ps-4 fw-800 text-success">#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="py-3 fw-800 text-dark"><?= $row['nama'] ?></td>
                        <td class="py-3 small text-muted"><?= date('d/m', strtotime($row['tgl_sewa'])) ?> -
                            <?= date('d/m/y', strtotime($row['tgl_kembali'])) ?></td>
                        <td class="py-3 text-end fw-800">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                        <td class="py-3 text-center">
                            <span
                                class="badge-gojek <?= ($row['status_transaksi'] == 'disewa') ? 'bg-warning bg-opacity-10 text-warning' : 'bg-gojek-soft' ?>">
                                <?= strtoupper($row['status_transaksi']) ?>
                            </span>
                        </td>
                        <td class="py-3 text-end pe-4" onclick="event.stopPropagation();">
                            <a href="cetak_struk.php?id=<?= $row['id'] ?>" target="_blank"
                                class="btn btn-light btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;"><i
                                    class="bi bi-printer-fill"></i></a>
                        </td>
                    </tr>
                <?php endwhile;
                mysqli_data_seek($result, 0); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="d-md-none px-2">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card-modern mb-3 p-3 border-0 shadow-sm"
            onclick="showDetail(<?= htmlspecialchars(json_encode($row)) ?>)" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="text-success fw-800 small">#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    <h6 class="fw-800 mb-0 text-dark mt-1"><?= $row['nama'] ?></h6>
                </div>
                <span
                    class="badge-gojek <?= ($row['status_transaksi'] == 'disewa') ? 'bg-warning bg-opacity-10 text-warning' : 'bg-gojek-soft' ?> x-small">
                    <?= strtoupper($row['status_transaksi']) ?>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    <i class="bi bi-clock-history me-1"></i> <?= date('d/m', strtotime($row['tgl_sewa'])) ?> -
                    <?= date('d/m', strtotime($row['tgl_kembali'])) ?>
                </div>
                <div class="fw-800 text-dark">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Modal Detail Transaksi (Kalkulasi Transparan) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0" style="border-radius: 28px;">
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-800 mb-0">Rincian Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="bg-light rounded-4 p-3 mb-4">
                    <div class="row g-2">
                        <div class="col-12">
                            <small class="text-muted d-block fw-bold" style="font-size: 10px;">PENYEWA</small>
                            <span id="m-nama" class="fw-800 text-dark"></span>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted d-block fw-bold" style="font-size: 10px;">ID TRX</small>
                            <span id="m-id" class="fw-800 text-success small"></span>
                        </div>
                        <div class="col-6 mt-2 text-end">
                            <small class="text-muted d-block fw-bold" style="font-size: 10px;">DURASI</small>
                            <span id="m-durasi" class="fw-800 text-dark small"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-800 small text-muted mb-2 border-bottom pb-2">RINCIAN ALAT (PER HARI)</h6>
                    <div id="m-items-list" class="mb-3"></div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span class="text-muted small fw-600">Total Alat / Hari</span>
                        <span id="m-total-hari" class="fw-800 small"></span>
                    </div>
                </div>

                <div class="bg-gojek-soft p-3 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-600">Kalkulasi Akhir</span>
                        <span id="m-calc-label" class="text-muted small"></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-800 text-dark">Total Bayar</span>
                        <span id="m-total" class="fw-800 text-success fs-4"></span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a id="btn-kembali" href="#" class="btn btn-gojek py-3 shadow-sm fw-800 d-none">
                        <i class="bi bi-arrow-return-left me-2"></i> KEMBALIKAN BARANG
                    </a>
                    <a id="btn-print" href="#" target="_blank" class="btn btn-light py-3 border fw-800">
                        <i class="bi bi-printer-fill me-2"></i> CETAK STRUK
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(data) {
        document.getElementById('m-id').innerText = '#TX-' + data.id.toString().padStart(4, '0');
        document.getElementById('m-nama').innerText = data.nama;
        document.getElementById('m-total').innerText = 'Rp ' + parseInt(data.total_bayar).toLocaleString('id-ID');

        // Hitung Durasi
        const t1 = new Date(data.tgl_sewa);
        const t2 = new Date(data.tgl_kembali);
        let diff = Math.ceil((t2 - t1) / (1000 * 60 * 60 * 24));
        if (diff <= 0) diff = 1;
        document.getElementById('m-durasi').innerText = diff + ' Hari';

        // Tombol Pengembalian
        const btnKembali = document.getElementById('btn-kembali');
        if (data.status_transaksi == 'disewa') {
            btnKembali.classList.remove('d-none');
            btnKembali.href = '../pengembalian/tambah.php?id_transaksi=' + data.id;
        } else {
            btnKembali.classList.add('d-none');
        }

        document.getElementById('btn-print').href = 'cetak_struk.php?id=' + data.id;

        // Ambil Barang via AJAX
        const itemsList = document.getElementById('m-items-list');
        itemsList.innerHTML = '<div class="text-center py-2 text-muted small">Memuat...</div>';

        fetch('get_detail.php?id=' + data.id)
            .then(response => response.json())
            .then(items => {
                itemsList.innerHTML = '';
                let totalHarian = 0;
                items.forEach(item => {
                    // Gunakan harga_at_rental yang merupakan harga satuan per hari
                    const subPerItemHarian = item.harga_at_rental * item.qty;
                    totalHarian += subPerItemHarian;

                    const div = document.createElement('div');
                    div.className = 'd-flex justify-content-between mb-2';
                    div.innerHTML = `
                    <div>
                        <div class="small fw-600 text-dark">${item.nama_barang}</div>
                        <div class="text-muted" style="font-size: 10px;">${item.qty} x Rp ${item.harga_at_rental.toLocaleString('id-ID')}</div>
                    </div>
                    <span class="small fw-800 text-dark">Rp ${subPerItemHarian.toLocaleString('id-ID')}</span>
                `;
                    itemsList.appendChild(div);
                });
                document.getElementById('m-total-hari').innerText = 'Rp ' + totalHarian.toLocaleString('id-ID');
                document.getElementById('m-calc-label').innerText = `Rp ${totalHarian.toLocaleString('id-ID')} x ${diff} Hari`;
            });

        var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
        myModal.show();
    }
</script>

<style>
    .hover-row:hover {
        background-color: #f9f9f9 !important;
    }

    .x-small {
        font-size: 9px !important;
    }
</style>

<?php include '../includes/footer.php'; ?>