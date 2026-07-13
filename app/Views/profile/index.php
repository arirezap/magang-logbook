<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <style>
        .profile-header {
            background-color: #0F172A;
            color: #FFFFFF;
            padding: 3rem 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: #059669;
            color: #FFFFFF;
            font-size: 3.5rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 6px solid rgba(255, 255, 255, 0.1);
        }
        .profile-name {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 900;
            letter-spacing: -0.05em;
            margin-bottom: 0.25rem;
        }
        .profile-role {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.85rem;
        }
        .clean-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #FFFFFF;
            padding: 2rem;
            height: 100%;
        }
        .clean-input {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .clean-input:focus {
            background-color: #FFFFFF;
            border-color: #1E3A5F;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }
        .clean-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .password-toggle {
            cursor: pointer;
            color: #64748B;
        }
        .password-toggle:hover {
            color: #0F172A;
        }
        .btn-modern {
            background-color: #1E3A5F;
            color: #FFFFFF;
            font-weight: 600;
            padding: 0.8rem 2rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-modern:hover {
            background-color: #0F172A;
            transform: translateY(-2px);
            color: #FFFFFF;
        }
    </style>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('validation')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> Terdapat kesalahan pada form:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('validation') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="profile-header shadow-sm">
        <div class="profile-avatar">
            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
        </div>
        <div>
            <h1 class="profile-name"><?= esc($user['nama']) ?></h1>
            <div class="profile-role"><?= esc($user['role']) ?> &bull; <?= esc($user['nomor_induk']) ?></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Detail Info Card -->
        <div class="col-12 col-lg-6">
            <div class="clean-card">
                <h4 class="fw-bold mb-4" style="color: #0F172A;">Informasi Akun</h4>
                
                <div class="row g-4">
                    <?php if(!empty($user['nama_prodi'])): ?>
                    <div class="col-12">
                        <label class="clean-label">Program Studi</label>
                        <div class="fs-5 fw-semibold text-dark"><?= esc($user['nama_prodi']) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if(strtolower($user['role']) == 'taruna'): ?>
                        <div class="col-6">
                            <label class="clean-label">Kelas & Jenjang</label>
                            <div class="fs-5 fw-semibold text-dark"><?= esc($user['jenjang'] ?? '-') ?> - <?= esc($user['kelas'] ?? '-') ?></div>
                        </div>
                        <div class="col-6">
                            <label class="clean-label">Dosen Pembimbing</label>
                            <div class="fs-5 fw-semibold text-dark"><?= !empty($user['nama_pembimbing']) ? esc($user['nama_pembimbing']) : 'Belum Ditentukan' ?></div>
                        </div>
                        <div class="col-12">
                            <label class="clean-label">Tempat Magang</label>
                            <div class="fs-5 fw-semibold text-dark"><i class="bi bi-building me-2 text-primary"></i><?= esc($user['tempat_magang'] ?? '-') ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-5">
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-light border px-4 rounded-3 fw-semibold text-dark text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Security / Password Card -->
        <div class="col-12 col-lg-6">
            <div class="clean-card">
                <h4 class="fw-bold mb-4" style="color: #0F172A;">Keamanan Akun</h4>
                <p class="text-muted small mb-4">Ganti password secara berkala untuk menjaga keamanan akun Anda.</p>

                <form action="<?= base_url('/profile/update-password') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="clean-label">Password Lama</label>
                        <div class="input-group">
                            <input type="password" name="old_password" class="form-control clean-input" required placeholder="Masukkan password lama">
                            <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword(this, 'old_password')">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="clean-label">Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control clean-input" required minlength="6" placeholder="Minimal 6 karakter">
                            <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword(this, 'new_password')">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="clean-label">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control clean-input" required minlength="6" placeholder="Ulangi password baru">
                            <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword(this, 'confirm_password')">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-modern w-100 d-flex justify-content-center align-items-center gap-2">
                        <i class="bi bi-shield-lock"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(element, inputName) {
        const input = document.querySelector(`input[name="${inputName}"]`);
        const icon = element.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
    </script>
<?= $this->endSection() ?>
