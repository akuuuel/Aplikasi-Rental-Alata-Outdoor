<?php
require_once '../config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: " . base_url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role' => $user['role']
            ];
            header("Location: " . base_url('index.php'));
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OutdoorRent Gojek Edition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FFFFFF;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: #00AA13;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2.2rem;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(0, 170, 19, 0.2);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1C1C1C;
            margin-bottom: 0.5rem;
        }

        .form-control-gojek {
            background: #F2F2F2;
            border: 2px solid #F2F2F2;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 600;
            transition: 0.2s;
        }

        .form-control-gojek:focus {
            background: white;
            border-color: #00AA13;
            box-shadow: none;
        }

        .btn-gojek {
            background: #00AA13;
            border: none;
            border-radius: 100px;
            padding: 14px;
            font-weight: 800;
            color: white;
            width: 100%;
            margin-top: 1rem;
            transition: 0.2s;
        }

        .btn-gojek:hover {
            background: #00880D;
            transform: scale(1.02);
            color: white;
        }

        .alert-gojek {
            background: #FFF2F2;
            border: none;
            color: #E91E63;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center">
            <div class="brand-logo">R</div>
            <h3 class="fw-800 mb-1">Masuk ke Akun</h3>
            <p class="text-muted small mb-5">OutdoorRent Admin <span class="text-success fw-bold">Partner</span></p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-gojek mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control-gojek w-100" placeholder="Username Anda" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control-gojek w-100" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-gojek shadow-sm">Masuk Sekarang</button>
        </form>
        
        <p class="text-center mt-5 text-muted small fw-600">
            &copy; 2026 OutdoorRent <span class="text-success">Gojek Edition</span>
        </p>
    </div>
</body>
</html>
