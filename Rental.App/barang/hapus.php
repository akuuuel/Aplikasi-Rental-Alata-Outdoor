<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT foto FROM barang WHERE id = $id";
$barang = mysqli_fetch_assoc(mysqli_query($conn, $query));

if ($barang) {
    if ($barang['foto'] && file_exists("../assets/img/" . $barang['foto'])) {
        unlink("../assets/img/" . $barang['foto']);
    }
    
    $delete = "DELETE FROM barang WHERE id = $id";
    if (mysqli_query($conn, $delete)) {
        flash('success', 'Barang berhasil dihapus!');
    } else {
        flash('error', 'Gagal menghapus barang: ' . mysqli_error($conn), 'alert-danger');
    }
}

header("Location: index.php");
exit;
?>
