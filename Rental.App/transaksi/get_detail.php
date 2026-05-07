<?php
require_once '../config/koneksi.php';

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$id = (int)$_GET['id'];

// Ambil data detail dan join ke master barang untuk mendapatkan Nama dan Harga Sewa Dasar
$query = "SELECT dt.qty, dt.subtotal, b.nama_barang, b.harga_sewa
          FROM detail_transaksi dt
          INNER JOIN barang b ON dt.id_barang = b.id 
          WHERE dt.id_transaksi = $id";

$result = mysqli_query($conn, $query);

$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'nama_barang' => $row['nama_barang'],
            'qty' => (int)$row['qty'],
            'harga_at_rental' => (int)$row['harga_sewa'],
            'subtotal' => (int)$row['subtotal']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($items);
