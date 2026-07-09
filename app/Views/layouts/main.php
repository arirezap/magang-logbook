<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Logbook Magang' ?> - PKTJ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('images/logo-pktj.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('images/logo-pktj.png') ?>">

</head>
<body class="bg-light">
    
    <!-- Topbar Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4 px-md-5">
            <a class="navbar-brand d-flex align-items-center gap-2 me-4" href="<?= base_url('/dashboard') ?>">
                <img src="<?= base_url('images/logo-pktj.png') ?>" alt="Logo PKTJ" style="height: 38px; width: auto;">
                <span class="fw-bold text-white" style="font-size: 1.15rem; letter-spacing: 0.5px;">LOGBOOK PKTJ</span>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler text-white border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
 
            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $current_route = current_url(true)->getSegment(1) ?? 'dashboard'; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'dashboard' || $current_route == '') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- Menu Khusus Taruna -->
                    <?php if(strtolower(session()->get('role')) == 'taruna'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'logbook') ? 'active' : '' ?>" href="<?= base_url('/logbook') ?>">
                            Isi Logbook
                        </a>
                    </li>
                    <?php endif; ?>
 
                    <!-- Menu Khusus Pembimbing -->
                    <?php if(strtolower(session()->get('role')) == 'pembimbing'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'validasi') ? 'active' : '' ?>" href="<?= base_url('/validasi') ?>">
                            Validasi Logbook
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'bimbingan') ? 'active' : '' ?>" href="<?= base_url('/bimbingan') ?>">
                            Taruna Bimbingan
                        </a>
                    </li>
                    <?php endif; ?>
 
                    <!-- Menu Khusus Admin/Pejabat/Superadmin -->
                    <?php if(in_array(strtolower(session()->get('role')), ['admin_prodi', 'pejabat', 'superadmin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'users') ? 'active' : '' ?>" href="<?= base_url('/users') ?>">
                            Data Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'laporan') ? 'active' : '' ?>" href="<?= base_url('/laporan') ?>">
                            Laporan Global
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
 
                <!-- Clickable Profile Avatar Dropdown -->
                <div class="dropdown mt-3 mt-lg-0">
                    <a href="#" class="d-flex align-items-center user-profile text-decoration-none border-0" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: none; outline: none; box-shadow: none;">
                        <div class="text-end d-none d-lg-block me-2">
                            <div class="fw-bold text-white mb-0" style="font-size: 0.85rem; line-height: 1.2;"><?= esc(session()->get('nama')) ?></div>
                            <div class="text-white-50" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;"><?= esc(session()->get('role')) ?></div>
                        </div>
                        <div class="user-avatar shadow-sm">
                            <?= strtoupper(substr(session()->get('nama'), 0, 1)) ?>
                        </div>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" aria-labelledby="profileDropdown">
                        <li>
                            <div class="px-3 py-2 border-bottom d-lg-none">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= esc(session()->get('nama')) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;"><?= esc(session()->get('role')) ?></div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="<?= base_url('/profile') ?>">
                                <i class="bi bi-person me-2"></i> Profil Saya
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="<?= base_url('/logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
 
    <!-- Page Content -->
    <div id="content" class="container-fluid px-4 px-md-5 pb-5" style="margin-top: 2rem;">
        <?= $this->renderSection('content') ?>
    </div>
 
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
