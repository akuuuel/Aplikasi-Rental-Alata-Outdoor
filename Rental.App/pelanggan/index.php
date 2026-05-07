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
    $where = "WHERE nama LIKE '%$search%' OR ktp LIKE '%$search%'";
}

$query_count = "SELECT COUNT(*) as total FROM pelanggan $where";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $query_count))['total'];
$total_page = ceil($total_data / $limit);

$query = "SELECT * FROM pelanggan $where LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 px-2 px-md-0">
    <div>
        <h4 class="fw-800 mb-1">Daftar Pelanggan</h4>
        <p class="text-muted small mb-0">Klik profil untuk detail & aksi.</p>
    </div>
    <a href="tambah.php" class="btn btn-gojek px-4 shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> <span class="d-none d-md-inline">Baru</span>
    </a>
</div>

<div class="px-2 px-md-0 mb-4">
    <div class="input-group bg-white rounded-pill px-3 py-1 shadow-sm border">
        <span class="input-group-text border-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-0 bg-transparent small" placeholder="Cari nama atau KTP..." value="<?= $search ?>" onchange="window.location.href='?search='+this.value">
    </div>
</div>

<?= flash('success') ?>

<!-- Desktop View -->
<div class="card-modern d-none d-md-block overflow-hidden">
    <div class="table-responsive">
        <table class="table table-borderless align-middle mb-0">
            <thead>
                <tr class="text-muted small border-bottom">
                    <th class="pb-3 ps-4">PELANGGAN</th>
                    <th class="pb-3">KONTAK</th>
                    <th class="pb-3">KTP</th>
                    <th class="pb-3 text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="border-bottom hover-row" onclick="showCustDetail(<?= htmlspecialchars(json_encode($row)) ?>)" style="cursor: pointer;">
                    <td class="py-3 ps-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-gojek-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill text-success"></i>
                            </div>
                            <div class="fw-800 text-dark small"><?= $row['nama'] ?></div>
                        </div>
                    </td>
                    <td class="py-3 text-muted small"><?= $row['no_telp'] ?></td>
                    <td class="py-3"><span class="badge bg-light text-dark rounded-pill px-2 py-1 small"><?= $row['ktp'] ?></span></td>
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
    <div class="card-modern mb-3 p-3 shadow-sm border-0" onclick="showCustDetail(<?= htmlspecialchars(json_encode($row)) ?>)" style="cursor: pointer;">
        <div class="d-flex align-items-center mb-2">
            <div class="bg-gojek-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                <i class="bi bi-person-fill text-success fs-5"></i>
            </div>
            <div>
                <h6 class="fw-800 mb-0 text-dark"><?= $row['nama'] ?></h6>
                <div class="text-muted" style="font-size: 11px;"><?= $row['no_telp'] ?></div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<!-- Modal Detail Pelanggan -->
<div class="modal fade" id="custModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0" style="border-radius: 24px;">
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-gojek-soft rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-person-vcard text-success fs-1"></i>
                    </div>
                    <h4 id="m-nama" class="fw-800 text-dark mb-1"></h4>
                    <p class="text-muted small mb-0">Informasi Profil Pelanggan</p>
                </div>

                <div class="bg-light rounded-4 p-4 mb-4">
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold mb-1">NOMOR TELEPON</small>
                        <span id="m-telp" class="fw-800 fs-5 text-dark"></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block fw-bold mb-1">NOMOR KTP</small>
                        <span id="m-ktp" class="fw-800 fs-5 text-dark"></span>
                    </div>
                    <div class="">
                        <small class="text-muted d-block fw-bold mb-1">ALAMAT LENGKAP</small>
                        <p id="m-alamat" class="text-muted mb-0 fw-600"></p>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a id="btn-edit" href="#" class="btn btn-gojek py-3 shadow-sm">
                        <i class="bi bi-pencil-square me-2"></i> Edit Data Pelanggan
                    </a>
                    <a id="btn-delete" href="#" class="btn btn-light py-3 fw-bold rounded-pill text-danger" onclick="return confirm('Hapus pelanggan ini?')">
                        <i class="bi bi-trash-fill me-2"></i> Hapus Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showCustDetail(data) {
    document.getElementById('m-nama').innerText = data.nama;
    document.getElementById('m-telp').innerText = data.no_telp;
    document.getElementById('m-ktp').innerText = data.ktp;
    document.getElementById('m-alamat').innerText = data.alamat;
    
    document.getElementById('btn-edit').href = 'edit.php?id=' + data.id;
    document.getElementById('btn-delete').href = 'hapus.php?id=' + data.id;
    
    var myModal = new bootstrap.Modal(document.getElementById('custModal'));
    myModal.show();
}
</script>

<?php include '../includes/footer.php'; ?>
