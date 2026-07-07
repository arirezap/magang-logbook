<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Logbook Magang' ?> - PKTJ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (Icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
</head>
<body>
    
    <!-- Topbar Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand me-4" href="<?= base_url('/dashboard') ?>">Logbook PKTJ</a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler text-white border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php $current_route = current_url(true)->getSegment(1) ?? 'dashboard'; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'dashboard' || $current_route == '') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Menu Khusus Taruna -->
                    <?php if(strtolower(session()->get('role')) == 'taruna'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'logbook') ? 'active' : '' ?>" href="<?= base_url('/logbook') ?>"><i class="fas fa-book me-1"></i> Isi Logbook</a>
                    </li>
                    <?php endif; ?>

                    <!-- Menu Khusus Pembimbing -->
                    <?php if(strtolower(session()->get('role')) == 'pembimbing'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'validasi') ? 'active' : '' ?>" href="<?= base_url('/validasi') ?>"><i class="fas fa-check-square me-1"></i> Validasi Logbook</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'bimbingan') ? 'active' : '' ?>" href="<?= base_url('/bimbingan') ?>"><i class="fas fa-user-graduate me-1"></i> Taruna Bimbingan</a>
                    </li>
                    <?php endif; ?>

                    <!-- Menu Khusus Admin/Pejabat/Superadmin -->
                    <?php if(in_array(strtolower(session()->get('role')), ['admin_prodi', 'pejabat', 'superadmin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'users') ? 'active' : '' ?>" href="<?= base_url('/users') ?>"><i class="fas fa-users me-1"></i> Data Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'laporan') ? 'active' : '' ?>" href="<?= base_url('/laporan') ?>"><i class="fas fa-chart-bar me-1"></i> Laporan Global</a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- User Profile & Logout -->
                <div class="d-flex user-profile align-items-center mt-3 mt-lg-0 border-lg-start border-light ps-lg-3">
                    <div class="text-end d-none d-lg-block me-2">
                        <div class="fw-bold text-white" style="font-size: 0.85rem; line-height: 1.2;"><?= session()->get('nama') ?></div>
                        <div class="text-white-50" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 500;"><?= session()->get('role') ?></div>
                    </div>
                    <div class="user-avatar shadow-sm">
                        <?= strtoupper(substr(session()->get('nama'), 0, 1)) ?>
                    </div>
                    <a href="<?= base_url('/logout') ?>" class="btn btn-warning btn-sm ms-3 fw-bold shadow-sm" style="color: #0d47a1;">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="content" class="container-fluid px-4 pb-4">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
