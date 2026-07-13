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
            <div class="brand-side-logo d-flex align-items-center gap-2">
                <img src="<?= base_url('images/logo-pktj.png') ?>" alt="Logo PKTJ" style="height: 45px; width: auto; object-fit: contain;">
                <span class="fs-4 fw-bold">PKTJ LOGBOOK</span>
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
                <div class="d-flex d-lg-none align-items-center gap-2 mb-4 justify-content-center">
                    <img src="<?= base_url('images/logo-pktj.png') ?>" alt="Logo PKTJ" style="height: 40px; width: auto; object-fit: contain;">
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
                        <div class="d-flex justify-content-between align-items-end mb-2 w-100">
                            <label for="password" class="mb-0 pb-0">Password</label>
                            <a href="#" id="forgotPasswordBtn" class="text-primary text-decoration-none" style="font-size: 0.85rem; font-weight: 500; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Lupa password?</a>
                        </div>
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
                        Login <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Password Visibility Toggle & Forgot Password Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Password
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePasswordBtn');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            });

            // Forgot Password Popup
            document.getElementById('forgotPasswordBtn').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Lupa Password?',
                    html: '<p style="color: #6c757d; font-size: 0.95rem; margin-top: 10px;">Silakan hubungi <b>Administrator (Bagian Akademik / Prodi)</b> untuk melakukan reset password akun Anda ke default.</p>',
                    icon: 'info',
                    iconColor: '#0d6efd',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        popup: 'rounded-4 shadow-sm border-0',
                        confirmButton: 'btn btn-primary px-4 py-2 rounded-3 fw-medium'
                    },
                    buttonsStyling: false
                });
            });
        });
    </script>
</body>
</html>
