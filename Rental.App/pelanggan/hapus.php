<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . base_url('auth/login.php'));
    exit;
}

$id = (int)$_GET['id'];
$delete = "DELETE FROM pelanggan WHERE id = $id";

if (mysqli_query($conn, $delete)) {
    flash('success', 'Pelanggan berhasil dihapus!');
} else {
    flash('error', 'Gagal menghapus pelanggan: ' . mysqli_error($conn), 'alert-danger');
}

header("Location: index.php");
exit;
?>
