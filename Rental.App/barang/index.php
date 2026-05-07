<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = "";
if (!empty($search)) {
    $where = "WHERE nama_barang LIKE '%$search%' OR kategori LIKE '%$search%'";
}

$query_count = "SELECT COUNT(*) as total FROM barang $where";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $query_count))['total'];
$total_page = ceil($total_data / $limit);

$query = "SELECT * FROM barang $where LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 px-2 px-md-0">
    <div>
        <h4 class="fw-800 mb-1">Layanan Barang</h4>
        <p class="text-muted small mb-0">Klik barang untuk detail & aksi.</p>
    </div>
    <a href="tambah.php" class="btn btn-gojek px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> <span class="d-none d-md-inline">Barang</span>
    </a>
</div>

<div class="px-2 px-md-0 mb-4">
    <div class="input-group bg-white rounded-pill px-3 py-1 shadow-sm border">
        <span class="input-group-text border-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-0 bg-transparent small" placeholder="Cari nama barang..." value="<?= $search ?>" onchange="window.location.href='?search='+this.value">
    </div>
</div>

<?= flash('success') ?>

<!-- Desktop View -->
<div class="card-modern d-none d-md-block overflow-hidden">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0">
            <thead>
                <tr class="text-muted small border-bottom">
                    <th class="pb-3 ps-4">BARANG</th>
                    <th class="pb-3">KATEGORI</th>
                    <th class="pb-3 text-center">STOK</th>
                    <th class="pb-3">HARGA</th>
                    <th class="pb-3 text-center">STATUS</th>
                    <th class="pb-3 text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="border-bottom hover-row" onclick="showItemDetail(<?= htmlspecialchars(json_encode($row)) ?>)" style="cursor: pointer;">
                    <td class="py-3 ps-4">
                        <div class="d-flex align-items-center">
                            <?php if ($row['foto']): ?>
                                <img src="<?= base_url('assets/img/' . $row['foto']) ?>" class="rounded-3 me-3" width="44" height="44" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="fw-800 text-dark small"><?= $row['nama_barang'] ?></div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark rounded-pill px-2 py-1 small"><?= $row['kategori'] ?></span></td>
                    <td class="text-center fw-800"><?= $row['stok'] ?></td>
                    <td class="fw-800 text-success">Rp <?= number_format($row['harga_sewa'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <span class="badge-gojek <?= ($row['status'] == 'tersedia') ? 'bg-gojek-soft' : 'bg-danger bg-opacity-10 text-danger' ?> small">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </td>
                    <td class="text-end pe-4" onclick="event.stopPropagation();">
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-light btn-sm rounded-circle me-1"><i class="bi bi-pencil-fill text-primary"></i></a>
                    </td>
                </tr>
                <?php endwhile; mysqli_data_seek($result, 0); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile View (Card List) -->
<div class="d-md-none px-2">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="card-modern mb-3 p-0 overflow-hidden shadow-sm border-0" onclick="showItemDetail(<?= htmlspecialchars(json_encode($row)) ?>)" style="cursor: pointer;">
        <div class="d-flex">
            <div style="width: 100px; height: 100px;">
                <?php if ($row['foto']): ?>
                    <img src="<?= base_url('assets/img/' . $row['foto']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                        <i class="bi bi-image fs-2"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="p-3 flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="fw-800 mb-0 text-dark"><?= $row['nama_barang'] ?></h6>
                    <span class="badge-gojek <?= ($row['status'] == 'tersedia') ? 'bg-gojek-soft' : 'bg-danger bg-opacity-10 text-danger' ?>" style="font-size: 8px;">
                        <?= strtoupper($row['status']) ?>
                    </span>
                </div>
                <div class="mt-1 text-muted small" style="font-size: 11px;"><?= $row['kategori'] ?></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="fw-800 text-success">Rp <?= number_format($row['harga_sewa'], 0, ',', '.') ?></div>
                    <div class="small text-muted fw-bold">Stok: <?= $row['stok'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Modal Detail Barang -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0" style="border-radius: 24px;">
            <div class="modal-body p-0">
                <div id="m-img-container" style="height: 250px; position: relative;">
                    <img id="m-img" src="" class="w-100 h-100" style="object-fit: cover; border-radius: 24px 24px 0 0;">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                </div>
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span id="m-kategori" class="badge bg-light text-dark mb-2"></span>
                            <h4 id="m-nama" class="fw-800 text-dark mb-0"></h4>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block fw-bold">HARGA SEWA</small>
                            <span id="m-harga" class="fw-800 fs-5 text-success"></span>
                        </div>
                    </div>
                    <div class="row g-3 bg-light rounded-4 p-3 mb-4">
                        <div class="col-6">
                            <small class="text-muted d-block fw-bold">STOK</small>
                            <span id="m-stok" class="fw-800 fs-5"></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block fw-bold">STATUS</small>
                            <span id="m-status" class="badge-gojek"></span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a id="btn-edit" href="#" class="btn btn-gojek py-3 shadow-sm">
                            <i class="bi bi-pencil-square me-2"></i> Edit Informasi Barang
                        </a>
                        <a id="btn-delete" href="#" class="btn btn-light py-3 fw-bold rounded-pill text-danger" onclick="return confirm('Hapus barang ini?')">
                            <i class="bi bi-trash-fill me-2"></i> Hapus Barang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showItemDetail(data) {
    document.getElementById('m-nama').innerText = data.nama_barang;
    document.getElementById('m-kategori').innerText = data.kategori;
    document.getElementById('m-stok').innerText = data.stok + ' Unit';
    document.getElementById('m-harga').innerText = 'Rp ' + parseInt(data.harga_sewa).toLocaleString('id-ID');
    document.getElementById('m-status').innerText = data.status.toUpperCase();
    document.getElementById('m-status').className = 'badge-gojek ' + (data.status === 'tersedia' ? 'bg-gojek-soft' : 'bg-danger bg-opacity-10 text-danger');
    
    const img = data.foto ? '<?= base_url("assets/img/") ?>' + data.foto : 'https://placehold.co/400x400?text=No+Image';
    document.getElementById('m-img').src = img;
    
    document.getElementById('btn-edit').href = 'edit.php?id=' + data.id;
    document.getElementById('btn-delete').href = 'hapus.php?id=' + data.id;
    
    var myModal = new bootstrap.Modal(document.getElementById('itemModal'));
    myModal.show();
}
</script>

<style>
    .hover-row:hover { background-color: #f9f9f9 !important; }
</style>

<?php include '../includes/footer.php'; ?>
