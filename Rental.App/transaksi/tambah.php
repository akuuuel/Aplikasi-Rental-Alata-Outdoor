<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
$barang_list = mysqli_query($conn, "SELECT * FROM barang WHERE status = 'tersedia' AND stok > 0");

include '../includes/header.php';
?>

<div class="mb-4 d-flex align-items-center px-3 px-md-0">
    <a href="index.php" class="btn btn-light rounded-circle me-3 shadow-sm" style="width:40px; height:40px;"><i
            class="bi bi-arrow-left"></i></a>
    <h4 class="fw-800 mb-0 text-dark">Buat Pesanan</h4>
</div>

<div class="row g-4 px-2 px-md-0 pb-5">
    <!-- Step 1: Pilih Alat -->
    <div class="col-lg-5">
        <div class="card-modern p-4 shadow-sm border-0 h-100">
            <h6 class="fw-800 text-dark mb-4"><span class="bg-gojek-soft rounded-circle px-2 me-2 text-success">1</span>
                Pilih Alat</h6>
            <div class="mb-3">
                <label class="form-label text-muted small fw-800">CARI ALAT</label>
                <select id="sel_id_barang" class="form-select form-control-gojek py-3" required>
                    <option value="">-- Pilih Alat --</option>
                    <?php while ($b = mysqli_fetch_assoc($barang_list)): ?>
                        <option value="<?= $b['id'] ?>" data-nama="<?= $b['nama_barang'] ?>"
                            data-harga="<?= $b['harga_sewa'] ?>" data-stok="<?= $b['stok'] ?>">
                            <?= $b['nama_barang'] ?> (Rp <?= number_format($b['harga_sewa'], 0, ',', '.') ?>/hari) - Stok:
                            <?= $b['stok'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-800">JUMLAH (QTY)</label>
                <input type="number" id="sel_qty" class="form-control-gojek w-100 py-3" value="1" min="1">
            </div>
            <button type="button" onclick="addToCart()" class="btn btn-gojek w-100 py-3 shadow-sm fw-800">
                Tambah ke Keranjang
            </button>
        </div>
    </div>

    <!-- Step 2 & 3 -->
    <div class="col-lg-7">
        <form action="simpan.php" method="POST" id="formSewa">
            <!-- Step 2: Detail -->
            <div class="card-modern p-4 shadow-sm border-0 mb-4">
                <h6 class="fw-800 text-dark mb-4"><span
                        class="bg-gojek-soft rounded-circle px-2 me-2 text-success">2</span> Detail & Waktu</h6>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-800">NAMA PENYEWA</label>
                    <select name="id_pelanggan" class="form-select form-control-gojek py-3" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php mysqli_data_seek($pelanggan, 0);
                        while ($p = mysqli_fetch_assoc($pelanggan)): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nama'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small fw-800">TANGGAL PINJAM</label>
                        <div class="date-input-container">
                            <i class="bi bi-calendar2-check text-success fs-5"></i>
                            <input type="date" id="tgl_sewa" name="tgl_sewa" class="form-control-gojek py-3"
                                value="<?= date('Y-m-d') ?>" required onchange="calculateTotal()">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted small fw-800">TANGGAL KEMBALI</label>
                        <div class="date-input-container">
                            <i class="bi bi-calendar2-x text-danger fs-5"></i>
                            <input type="date" id="tgl_kembali" name="tgl_kembali" class="form-control-gojek py-3"
                                value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="calculateTotal()">
                        </div>
                    </div>
                </div>

                <div class="mt-4 bg-light rounded-pill px-3 py-2 d-inline-block">
                    <span class="small fw-800 text-muted">Estimasi Durasi: <span id="durasiText"
                            class="text-dark">1</span> Hari</span>
                </div>
            </div>

            <!-- Step 3: Keranjang (Isi Otomatis oleh JS) -->
            <div class="card-modern p-4 shadow-sm border-0">
                <h6 class="fw-800 text-dark mb-3"><span
                        class="bg-gojek-soft rounded-circle px-2 me-2 text-success">3</span> Keranjang</h6>

                <div id="keranjang_kosong" class="text-center py-4 text-muted small fw-600">Keranjang kosong</div>

                <div id="daftar_keranjang" class="mb-4">
                    <!-- Barang akan muncul di sini secara realtime -->
                </div>

                <div class="p-3 bg-gojek-soft rounded-4 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block fw-800" style="font-size: 9px;">TOTAL AKHIR</small>
                        <h4 class="fw-800 text-success mb-0" id="totalBayarText">Rp 0</h4>
                    </div>
                    <button type="submit" id="btn_gas" class="btn btn-gojek px-4 py-3 shadow-sm fw-800" disabled>
                        GAS SEWA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let cart = [];

    // Mencegah Klik Ganda
    function handleFormSubmit(form) {
        const btn = document.getElementById('btn_gas');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMPROSES...';
        return true;
    }

    function addToCart() {
        const sel = document.getElementById('sel_id_barang');
        const opt = sel.options[sel.selectedIndex];
        if (!opt.value) return;

        const id = opt.value;
        const nama = opt.dataset.nama;
        const harga = parseInt(opt.dataset.harga);
        const stok = parseInt(opt.dataset.stok);
        const qty = parseInt(document.getElementById('sel_qty').value);

        // SweetAlert untuk Stok
        if (qty > stok) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Kurang!',
                text: 'Maaf, sisa stok ' + nama + ' hanya ' + stok + '.',
                confirmButtonColor: '#00AA13'
            });
            return;
        }

        const exist = cart.find(i => i.id === id);
        if (exist) {
            if (exist.qty + qty > stok) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Stok!',
                    text: 'Total di keranjang melebihi stok gudang.',
                    confirmButtonColor: '#00AA13'
                });
                return;
            }
            exist.qty += qty;
            exist.subtotal = exist.qty * harga;
        } else {
            cart.push({ id, nama, harga, qty, subtotal: harga * qty });
        }

        // Notifikasi Sukses
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: nama + ' masuk ke keranjang.',
            timer: 1000,
            showConfirmButton: false
        });

        renderCart();
    }

    function remove(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('daftar_keranjang');
        const emptyMsg = document.getElementById('keranjang_kosong');
        const btnGas = document.getElementById('btn_gas');

        if (cart.length === 0) {
            emptyMsg.style.display = 'block';
            container.innerHTML = '';
            btnGas.disabled = true;
        } else {
            emptyMsg.style.display = 'none';
            btnGas.disabled = false;
            container.innerHTML = '';

            cart.forEach(item => {
                container.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <div class="fw-800 text-dark">${item.nama}</div>
                            <div class="text-muted small">Rp ${item.harga.toLocaleString('id-ID')} x ${item.qty}</div>
                            <input type="hidden" name="cart_items[${item.id}][qty]" value="${item.qty}">
                            <input type="hidden" name="cart_items[${item.id}][subtotal]" value="${item.subtotal}">
                        </div>
                        <div class="text-end">
                            <div class="fw-800 text-dark">Rp ${item.subtotal.toLocaleString('id-ID')}</div>
                            <a href="javascript:void(0)" onclick="remove('${item.id}')" class="text-danger x-small fw-800 text-decoration-none">HAPUS</a>
                        </div>
                    </div>
                `;
            });
        }
        calculateTotal();
    }

    function calculateTotal() {
        const t1 = new Date(document.getElementById('tgl_sewa').value);
        const t2 = new Date(document.getElementById('tgl_kembali').value);

        let diff = Math.ceil((t2 - t1) / (1000 * 60 * 60 * 24));
        if (diff <= 0) diff = 1;

        let totalHarian = 0;
        cart.forEach(i => totalHarian += i.subtotal);

        const totalAkhir = totalHarian * diff;

        document.getElementById('durasiText').innerText = diff;
        document.getElementById('totalBayarText').innerText = 'Rp ' + totalAkhir.toLocaleString('id-ID');
    }
</script>

<style>
    .x-small {
        font-size: 10px;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: auto;
        height: auto;
        color: transparent;
        background: transparent;
    }
</style>

<?php include '../includes/footer.php'; ?>