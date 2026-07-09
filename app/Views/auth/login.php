<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Logbook Magang PKTJ</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Style CSS -->
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('images/logo-pktj.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('images/logo-pktj.png') ?>">
</head>
<body>

    <div class="login-split-container w-100">
        <!-- Brand / Visual Side (Left) -->
        <div class="login-brand-side col-lg-6 d-none d-lg-flex">
            <div class="brand-side-logo">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>PKTJ LOGBOOK</span>
            </div>
            
            <div class="brand-side-content">
                <h1 class="brand-side-title">Logbook Magang Harian Taruna</h1>
                <p class="brand-side-subtitle">Sistem Informasi Pelaporan dan Pemantauan Kegiatan Praktik Kerja Lapangan (PKL) Taruna Politeknik Keselamatan Transportasi Jalan secara Real-Time dan Akurat.</p>
                <div style="height: 4px; background-color: #ffca28; width: 60px; border-radius: 2px; margin-top: 24px;"></div>
            </div>
            
            <div class="brand-side-footer">
                &copy; <?= date('Y') ?> Politeknik Keselamatan Transportasi Jalan. All rights reserved.
            </div>
        </div>

        <!-- Form Side (Right) -->
        <div class="login-form-side col-12 col-lg-6">
            <div class="login-form-wrapper">
                <!-- Mobile Logo (shown only on mobile/tablet) -->
                <div class="d-flex d-lg-none align-items-center gap-2 mb-4 justify-content-center text-primary">
                    <i class="bi bi-journal-bookmark-fill fs-2 text-warning"></i>
                    <span class="fs-4 fw-bold text-dark">PKTJ LOGBOOK</span>
                </div>

                <h2 class="login-form-title">Selamat Datang</h2>
                <p class="login-form-subtitle">Silakan masuk menggunakan akun Anda untuk mengelola logbook magang.</p>

                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert-premium">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('/login/process') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <!-- Username/Nomor Induk Field -->
                    <div class="input-group-custom">
                        <label for="nomor_induk">Nomor Induk (NOTAR / NIP)</label>
                        <div class="input-container">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" 
                                   class="input-field" 
                                   id="nomor_induk" 
                                   name="nomor_induk" 
                                   placeholder="Masukkan NOTAR atau NIP..." 
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group-custom">
                        <label for="password">Password</label>
                        <div class="input-container">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" 
                                   class="input-field" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password Anda..." 
                                   required>
                            <button type="button" class="password-toggle" id="togglePasswordBtn">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        Masuk ke Sistem <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Password Visibility Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePasswordBtn');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            toggleButton.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the eye / eye-slash icon
                if (type === 'text') {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            });
        });
    </script>
</body>
</html>
