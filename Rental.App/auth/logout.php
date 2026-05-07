<?php
require_once '../config/koneksi.php';
session_destroy();
header("Location: " . base_url('auth/login.php'));
exit;
?>
