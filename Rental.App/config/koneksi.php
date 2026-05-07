<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "rental_outdoor";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

session_start();

function base_url($path = "") {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . "://" . $host . "/crud.app/" . $path;
}

function flash($name = "", $message = "", $class = "alert-success") {
    if (!empty($name)) {
        if (!empty($message)) {
            $_SESSION['flash'][$name] = [
                'message' => $message,
                'class' => $class
            ];
        } else {
            if (isset($_SESSION['flash'][$name])) {
                $flash = $_SESSION['flash'][$name];
                unset($_SESSION['flash'][$name]);
                return '<div class="alert ' . $flash['class'] . ' alert-dismissible fade show" role="alert">
                            ' . $flash['message'] . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
            }
        }
    }
}
?>
