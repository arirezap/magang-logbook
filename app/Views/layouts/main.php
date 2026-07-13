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
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top" id="mainNavbar" style="transition: transform 0.3s ease-in-out;">
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
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 align-items-lg-center">
                    <?php $current_route = current_url(true)->getSegment(1) ?? 'dashboard'; ?>
                    
                    <!-- 1. Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'dashboard' || $current_route == '') ? 'active' : '' ?>" href="<?= base_url('/dashboard') ?>">
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- 2. Laporan -->
                    <?php if(in_array(strtolower(session()->get('role')), ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'laporan') ? 'active' : '' ?>" href="<?= base_url('/laporan') ?>">
                            Laporan
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- 3. Lainnya -->
                    <?php if(strtolower(session()->get('role')) == 'taruna'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_route == 'logbook') ? 'active' : '' ?>" href="<?= base_url('/logbook') ?>">
                            Isi Logbook
                        </a>
                    </li>
                    <?php endif; ?>
 
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
 
                    <?php if(in_array(strtolower(session()->get('role')), ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($current_route == 'input-data-taruna' || $current_route == 'input-data-dosen') ? 'active' : '' ?>" href="#" id="masterDataDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Master Data
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm rounded-3 mt-2" aria-labelledby="masterDataDropdown">
                            <li><a class="dropdown-item <?= ($current_route == 'input-data-taruna') ? 'active' : '' ?>" href="<?= base_url('/input-data-taruna') ?>">Input Data Taruna</a></li>
                            <li><a class="dropdown-item <?= ($current_route == 'input-data-dosen') ? 'active' : '' ?>" href="<?= base_url('/input-data-dosen') ?>">Input Data Dosen</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
 
                <!-- Right Side Action Icons -->
                <div class="d-flex align-items-center gap-4 mt-3 mt-lg-0 border-start border-light border-opacity-25 ps-4 ms-2">
                    <?php if(in_array(strtolower(session()->get('role')), ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])): ?>
                    <a href="<?= base_url('/users') ?>" class="text-white text-decoration-none fs-5 opacity-75 <?= ($current_route == 'users') ? 'opacity-100' : '' ?>" title="Pengaturan" style="transition: all 0.2s; outline: none;" onmouseover="this.classList.replace('opacity-75', 'opacity-100')" onmouseout="this.classList.replace('opacity-100', 'opacity-75')">
                        <i class="bi bi-gear-fill"></i>
                    </a>
                    <?php endif; ?>

                    <a href="<?= base_url('/profile') ?>" class="text-white text-decoration-none fs-5 opacity-75 <?= ($current_route == 'profile') ? 'opacity-100' : '' ?>" title="Profil Saya" style="transition: all 0.2s; outline: none;" onmouseover="this.classList.replace('opacity-75', 'opacity-100')" onmouseout="this.classList.replace('opacity-100', 'opacity-75')">
                        <i class="bi bi-person-fill"></i>
                    </a>

                    <a href="<?= base_url('/logout') ?>" class="text-white text-decoration-none fs-5 opacity-75" title="Keluar" style="transition: all 0.2s; outline: none;" onmouseover="this.classList.replace('opacity-75', 'opacity-100')" onmouseout="this.classList.replace('opacity-100', 'opacity-75')">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
 
    <!-- Page Content -->
    <div id="content" class="container-fluid px-4 px-md-5 pb-5" style="margin-top: 2rem;">
        <?= $this->renderSection('content') ?>
    </div>
 
    <!-- Back To Top Button -->
    <button type="button" class="btn btn-primary-custom rounded-circle shadow" id="btnBackToTop" style="position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; display: none; z-index: 1000; transition: opacity 0.3s ease, transform 0.3s ease;">
        <i class="bi bi-arrow-up fs-5"></i>
    </button>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- UI/UX Pro Max Scripts: Smart Scroll Navbar & Back to Top -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smart Scroll Navbar
            let lastScrollTop = 0;
            const navbar = document.getElementById('mainNavbar');
            
            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Jika scroll ke bawah lebih dari 50px
                if (scrollTop > lastScrollTop && scrollTop > 50) {
                    navbar.style.transform = 'translateY(-100%)';
                } 
                // Jika scroll ke atas
                else {
                    navbar.style.transform = 'translateY(0)';
                }
                lastScrollTop = scrollTop;
            });

            // Back to Top Button
            const btnBackToTop = document.getElementById('btnBackToTop');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    btnBackToTop.style.display = 'block';
                    // Animasi muncul sedikit
                    setTimeout(() => {
                        btnBackToTop.style.opacity = '1';
                        btnBackToTop.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    btnBackToTop.style.opacity = '0';
                    btnBackToTop.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        if (window.pageYOffset <= 300) {
                            btnBackToTop.style.display = 'none';
                        }
                    }, 300);
                }
            });

            btnBackToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
