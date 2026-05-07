<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin OutdoorRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --gojek-green: #00AA13;
            --gojek-dark-green: #00880D;
            --gojek-black: #1C1C1C;
            --gojek-gray: #F2F2F2;
            --primary: var(--gojek-green);
            --dark: var(--gojek-black);
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FFFFFF;
            color: var(--gojek-black);
            margin: 0;
            padding-bottom: 90px;
        }

        /* --- Sidebar Desktop Fix --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: white;
            z-index: 1000;
            padding: 1.5rem 1.2rem;
            border-right: 1px solid #E8E8E8;
        }

        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0 !important;
            }

            body {
                background-color: var(--gojek-gray);
            }
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 0.5rem 2rem;
            text-decoration: none;
            color: var(--gojek-black);
            height: 60px;
            /* Standardize header height */
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            background: var(--gojek-green);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.4rem;
            color: white;
            flex-shrink: 0;
            /* Prevent logo from squeezing */
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0;
            line-height: 1;
            white-space: nowrap;
            /* Keep name in one line */
        }

        .nav-link-desktop {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem 1rem;
            color: #4A4A4A;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .nav-link-desktop.active {
            background: #E9F7EA;
            color: var(--gojek-green);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            height: 70px;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #E8E8E8;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .content-padding {
            padding: 2rem;
        }

        /* --- Mobile Bottom Nav --- */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 8px 10px 25px;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
            z-index: 2000;
            border-top: 1px solid #E8E8E8;
        }

        @media (max-width: 991px) {
            .mobile-nav {
                display: flex;
                justify-content: space-around;
                align-items: center;
            }
        }

        .mobile-nav-item {
            text-decoration: none;
            color: #4A4A4A;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            flex: 1;
        }

        .mobile-nav-item.active {
            color: var(--gojek-green);
        }

        /* --- Input Style Fix --- */
        .form-control-gojek,
        .form-control,
        .form-select {
            background-color: #F6F6F6 !important;
            border: 2px solid #F6F6F6 !important;
            border-radius: 14px !important;
            padding: 12px 18px !important;
            font-weight: 600 !important;
            color: #1C1C1C !important;
            transition: 0.2s ease;
        }

        .form-control-gojek:focus,
        .form-control:focus,
        .form-select:focus {
            background-color: #FFFFFF !important;
            border-color: var(--gojek-green) !important;
            box-shadow: none !important;
        }

        /* Date Input Fix: Prevent Icon & Text Clashing */
        .date-input-container {
            position: relative;
            width: 100%;
        }

        .date-input-container i {
            position: absolute;
            top: 50%;
            left: 18px;
            transform: translateY(-50%);
            z-index: 5;
            pointer-events: none;
        }

        .date-input-container input[type="date"] {
            padding-left: 50px !important;
            /* Give space for icon */
        }

        /* --- Gojek Components --- */
        .card-modern {
            background: white;
            border: 1px solid #E8E8E8;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .btn-gojek {
            background: var(--gojek-green);
            color: white;
            border: none;
            border-radius: 100px;
            padding: 10px 24px;
            font-weight: 700;
        }

        .badge-gojek {
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
        }

        .bg-gojek-soft {
            background-color: #E9F7EA;
            color: var(--gojek-green);
        }

        /* --- Profile Dropdown --- */
        .profile-dropdown .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="<?= base_url('index.php') ?>" class="sidebar-brand">
            <div class="brand-logo">R</div>
            <h1 class="brand-name">OutdoorRent</h1>
        </a>
        <nav class="mt-4">
            <a href="<?= base_url('index.php') ?>"
                class="nav-link-desktop <?= (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], 'transaksi') === false) ? 'active' : '' ?>">
                <i class="bi bi-house-door-fill"></i> Beranda
            </a>
            <a href="<?= base_url('barang/index.php') ?>"
                class="nav-link-desktop <?= (strpos($_SERVER['PHP_SELF'], 'barang') !== false) ? 'active' : '' ?>">
                <i class="bi bi-grid-fill"></i> Layanan Barang
            </a>
            <a href="<?= base_url('transaksi/index.php') ?>"
                class="nav-link-desktop <?= (strpos($_SERVER['PHP_SELF'], 'transaksi') !== false) ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff"></i> Pesanan
            </a>
            <a href="<?= base_url('pelanggan/index.php') ?>"
                class="nav-link-desktop <?= (strpos($_SERVER['PHP_SELF'], 'pelanggan') !== false) ? 'active' : '' ?>">
                <i class="bi bi-person-fill"></i> Pelanggan
            </a>
            <hr class="my-4" style="border-color: #E8E8E8;">
            <a href="<?= base_url('auth/logout.php') ?>" class="nav-link-desktop text-danger">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </nav>
    </div>

    <div class="mobile-nav">
        <a href="<?= base_url('index.php') ?>"
            class="mobile-nav-item <?= (basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], 'transaksi') === false) ? 'active' : '' ?>">
            <i class="bi bi-house-door-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="<?= base_url('barang/index.php') ?>"
            class="mobile-nav-item <?= (strpos($_SERVER['PHP_SELF'], 'barang') !== false) ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i>
            <span>Barang</span>
        </a>
        <a href="<?= base_url('transaksi/index.php') ?>"
            class="mobile-nav-item <?= (strpos($_SERVER['PHP_SELF'], 'transaksi') !== false) ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Pesanan</span>
        </a>
        <a href="<?= base_url('pelanggan/index.php') ?>"
            class="mobile-nav-item <?= (strpos($_SERVER['PHP_SELF'], 'pelanggan') !== false) ? 'active' : '' ?>">
            <i class="bi bi-person-fill"></i>
            <span>Profil</span>
        </a>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <div class="bg-gojek-soft p-2 rounded-circle me-3 d-lg-none"
                    onclick="document.querySelector('input[placeholder*=\'Cari\']')?.focus();" style="cursor: pointer;">
                    <i class="bi bi-search"></i>
                </div>
                <h6 class="fw-800 mb-0">OutdoorRent <span class="text-success">Admin</span></h6>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <p class="mb-0 fw-800 small lh-1"><?= $_SESSION['user']['nama_lengkap'] ?></p>
                    <small class="text-muted" style="font-size: 10px;">Gojek-Style Partner</small>
                </div>

                <div class="dropdown profile-dropdown">
                    <div class="dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user']['nama_lengkap']) ?>&background=00AA13&color=fff&bold=true"
                            class="rounded-circle shadow-sm" width="36" height="36">
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0">
                        <li class="dropdown-header d-md-none">
                            <h6 class="mb-0 fw-bold"><?= $_SESSION['user']['nama_lengkap'] ?></h6>
                            <small class="text-muted">Administrator</small>
                        </li>
                        <li class="d-md-none">
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout.php') ?>">
                                <i class="bi bi-power me-2"></i> Keluar (Logout)
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="content-padding">