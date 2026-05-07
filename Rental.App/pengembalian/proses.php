<?php
require_once '../config/koneksi.php';

// Cek sesi login
if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_transaksi = (int)$_POST['id_transaksi'];
    $tgl_dikembalikan = $_POST['tgl_dikembalikan'];
    $denda = (float)$_POST['denda'];

    // Mulai Transaksi Database agar aman (Atomicity)
    mysqli_begin_transaction($conn);

    try {
        // 1. Simpan data ke tabel pengembalian
        // Pastikan tabel pengembalian sudah ada
        $query_p = "INSERT INTO pengembalian (id_transaksi, tgl_dikembalikan, denda) 
                    VALUES ($id_transaksi, '$tgl_dikembalikan', $denda)";
        
        if (!mysqli_query($conn, $query_p)) {
            throw new Exception("Gagal simpan data pengembalian: " . mysqli_error($conn));
        }

        // 2. Update status transaksi jadi 'kembali'
        $query_t = "UPDATE transaksi SET status_transaksi = 'kembali' WHERE id = $id_transaksi";
        if (!mysqli_query($conn, $query_t)) {
            throw new Exception("Gagal update status transaksi: " . mysqli_error($conn));
        }

        // 3. Kembalikan stok barang yang disewa
        $detail_trx = mysqli_query($conn, "SELECT id_barang, qty FROM detail_transaksi WHERE id_transaksi = $id_transaksi");
        while ($dt = mysqli_fetch_assoc($detail_trx)) {
            $id_barang = $dt['id_barang'];
            $qty = (int)$dt['qty'];
            
            $query_s = "UPDATE barang SET stok = stok + $qty WHERE id = $id_barang";
            if (!mysqli_query($conn, $query_s)) {
                throw new Exception("Gagal update stok barang: " . mysqli_error($conn));
            }
        }

        // Jika semua OK, commit transaksi
        mysqli_commit($conn);
        
        flash('success', 'Berhasil! Barang telah kembali dan stok otomatis terupdate.');
        header("Location: ../transaksi/index.php");
        exit;

    } catch (Exception $e) {
        // Jika ada yang gagal, batalkan semua perubahan (Rollback)
        mysqli_rollback($conn);
        flash('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        header("Location: tambah.php?id_transaksi=" . $id_transaksi);
        exit;
    }
} else {
    header("Location: ../transaksi/index.php");
    exit;
}