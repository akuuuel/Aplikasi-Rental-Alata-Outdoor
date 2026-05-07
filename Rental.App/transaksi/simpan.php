<?php
require_once '../config/koneksi.php';

// Aktifkan laporan error database secara detail
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_pelanggan = isset($_POST['id_pelanggan']) ? (int)$_POST['id_pelanggan'] : 0;
        $tgl_sewa = $_POST['tgl_sewa'];
        $tgl_kembali = $_POST['tgl_kembali'];
        $cart_items = isset($_POST['cart_items']) ? $_POST['cart_items'] : [];

        if ($id_pelanggan == 0) {
            throw new Exception("Anda belum memilih pelanggan.");
        }

        if (empty($cart_items)) {
            throw new Exception("Keranjang belanja kosong. Pastikan Anda sudah mengklik tombol TAMBAH.");
        }

        // Hitung Durasi Hari
        $start = new DateTime($tgl_sewa);
        $end = new DateTime($tgl_kembali);
        $days = $start->diff($end)->days;
        if ($days <= 0) $days = 1;

        // Mulai Transaksi
        mysqli_begin_transaction($conn);

        // 1. Hitung Total
        $total_bayar_akhir = 0;
        foreach ($cart_items as $item) {
            $total_bayar_akhir += ((float)$item['subtotal'] * $days);
        }

        // 2. Simpan Header Transaksi
        $query_trx = "INSERT INTO transaksi (id_pelanggan, tgl_sewa, tgl_kembali, total_bayar, status_transaksi) 
                      VALUES ($id_pelanggan, '$tgl_sewa', '$tgl_kembali', $total_bayar_akhir, 'disewa')";
        mysqli_query($conn, $query_trx);
        $id_transaksi = mysqli_insert_id($conn);

        // 3. Simpan Detail & Update Stok
        foreach ($cart_items as $id_barang => $item) {
            $id_barang = (int)$id_barang;
            $qty = (int)$item['qty'];
            $sub_item_akhir = (float)$item['subtotal'] * $days;

            // Cek Stok untuk keamanan (FOR UPDATE mengunci baris stok agar tidak berubah saat transaksi)
            $res_check = mysqli_query($conn, "SELECT stok, nama_barang FROM barang WHERE id = $id_barang FOR UPDATE");
            $b = mysqli_fetch_assoc($res_check);

            if (!$b) {
                throw new Exception("Barang dengan ID $id_barang tidak ditemukan.");
            }

            if ($b['stok'] < $qty) {
                throw new Exception("Stok untuk " . $b['nama_barang'] . " tidak cukup. Tersedia: " . $b['stok']);
            }

            // Simpan Detail
            $query_det = "INSERT INTO detail_transaksi (id_transaksi, id_barang, qty, subtotal) 
                          VALUES ($id_transaksi, $id_barang, $qty, $sub_item_akhir)";
            mysqli_query($conn, $query_det);

            // Potong Stok
            $query_upd = "UPDATE barang SET stok = stok - $qty WHERE id = $id_barang";
            mysqli_query($conn, $query_upd);
        }

        // Semua OK
        mysqli_commit($conn);
        
        // Pindah ke cetak struk
        header("Location: cetak_struk.php?id=" . $id_transaksi);
        exit;

    } catch (Exception $e) {
        if (isset($conn)) mysqli_rollback($conn);
        
        // Tampilkan error yang sangat jelas agar admin tahu masalahnya
        echo "<div style='padding:20px; background:#ffebee; color:#c62828; border:1px solid #ef9a9a; border-radius:8px; font-family:sans-serif; margin:20px;'>";
        echo "<h3 style='margin-top:0;'>Gagal Menyimpan Transaksi</h3>";
        echo "<p>Pesan Error: <b>" . $e->getMessage() . "</b></p>";
        echo "<hr style='border:0; border-top:1px solid #ef9a9a;'>";
        echo "<a href='tambah.php' style='display:inline-block; padding:10px 20px; background:#c62828; color:white; text-decoration:none; border-radius:5px;'>KEMBALI DAN COBA LAGI</a>";
        echo "</div>";
        exit;
    }
}
