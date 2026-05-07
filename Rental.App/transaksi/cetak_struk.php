<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT t.*, p.nama, p.no_telp, p.alamat 
          FROM transaksi t 
          JOIN pelanggan p ON t.id_pelanggan = p.id 
          WHERE t.id = $id";
$result = mysqli_query($conn, $query);
$trx = mysqli_fetch_assoc($result);

if (!$trx) {
    die("Transaksi tidak ditemukan!");
}

// Durasi Hari
$start = new DateTime($trx['tgl_sewa']);
$end = new DateTime($trx['tgl_kembali']);
$diff = $start->diff($end)->days;
if ($diff <= 0) $diff = 1;

// Detail Transaksi - Hanya menggunakan kolom yang ada
$detail_query = "SELECT dt.qty, dt.subtotal, b.nama_barang, b.harga_sewa
                 FROM detail_transaksi dt
                 JOIN barang b ON dt.id_barang = b.id 
                 WHERE dt.id_transaksi = $id";
$detail = mysqli_query($conn, $detail_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></title>
    <style>
        @page { size: auto; margin: 0; }
        body { font-family: 'Courier New', Courier, monospace; background: #f0f0f0; margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .struk-kertas { background: white; width: 300px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dashed-line { border-top: 1px dashed #000; margin: 10px 0; }
        .table { width: 100%; font-size: 0.8rem; border-collapse: collapse; }
        
        /* Tombol Navigasi */
        .nav-buttons { margin-bottom: 20px; display: flex; gap: 10px; }
        .btn-nav { border: none; padding: 10px 20px; border-radius: 50px; cursor: pointer; font-family: sans-serif; font-weight: bold; text-decoration: none; font-size: 13px; }
        .btn-print { background: #333; color: white; }
        .btn-back { background: #00AA13; color: white; }
        
        @media print { 
            .nav-buttons { display: none; } 
            body { background: white; padding: 0; } 
            .struk-kertas { box-shadow: none; width: 100%; } 
        }
    </style>
</head>
<body>
    <div class="nav-buttons">
        <a href="index.php" class="btn-nav btn-back">KEMBALI KE PESANAN</a>
        <button class="btn-nav btn-print" onclick="window.print()">CETAK ULANG</button>
    </div>
    <div class="struk-kertas">
        <div class="header text-center">
            <h2 style="margin:0;">OUTDOORRENT</h2>
            <p style="font-size:0.7rem;">Malang, Jawa Timur</p>
        </div>
        <div class="dashed-line"></div>
        <table class="table">
            <tr><td>ID TRX</td><td class="text-right">#<?= str_pad($id, 5, '0', STR_PAD_LEFT) ?></td></tr>
            <tr><td>CUST</td><td class="text-right"><?= strtoupper($trx['nama']) ?></td></tr>
            <tr><td>TGL</td><td class="text-right"><?= date('d/m/y', strtotime($trx['tgl_sewa'])) ?></td></tr>
        </table>
        <div class="dashed-line"></div>
        <div style="font-size:0.8rem;">DURASI SEWA: <?= $diff ?> HARI</div>
        <div class="dashed-line"></div>
        <table class="table">
            <?php 
            $total_alat_only = 0;
            while ($d = mysqli_fetch_assoc($detail)): 
                $harga_satuan = $d['harga_sewa'];
                $total_alat_only += ($harga_satuan * $d['qty']);
            ?>
            <tr><td colspan="2"><?= $d['nama_barang'] ?></td></tr>
            <tr>
                <td><?= $d['qty'] ?> x <?= number_format($harga_satuan, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($harga_satuan * $d['qty'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <div class="dashed-line"></div>
        <table class="table">
            <tr><td>Subtotal Alat</td><td class="text-right">Rp <?= number_format($total_alat_only, 0, ',', '.') ?></td></tr>
            <tr><td>Durasi Sewa</td><td class="text-right">x <?= $diff ?> Hari</td></tr>
            <tr style="font-weight:bold;">
                <td style="font-size:1rem; padding-top:10px;">TOTAL BAYAR</td>
                <td class="text-right" style="font-size:1rem; padding-top:10px;">Rp <?= number_format($trx['total_bayar'], 0, ',', '.') ?></td>
            </tr>
        </table>
        <div class="dashed-line"></div>
        <div class="footer text-center" style="font-size: 0.7rem;">
            <p>Terima kasih. Harap kembalikan tepat waktu.</p>
            <p>*** LUNAS ***</p>
        </div>
    </div>
    <script>window.onload = function() { setTimeout(function() { window.print(); }, 500); }</script>
</body>
</html>
