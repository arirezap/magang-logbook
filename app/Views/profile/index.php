<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Profil Pengguna</h3>
        <p class="text-muted m-0" style="font-size: 0.9rem;">Kelola dan lihat informasi detail akun Anda.</p>
    </div>

    <div class="row g-4">
        <!-- Profile Main Card -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100">
                <div class="card-body">
                    <!-- Big Avatar Circle -->
                    <div class="mx-auto bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-4" 
                         style="width: 90px; height: 90px; font-size: 2.8rem; font-weight: 800; border: 4px solid #f8fafc;">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;"><?= esc($user['nama']) ?></h4>
                    <span class="badge bg-primary text-white text-uppercase px-3 py-2 rounded-pill mb-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <?= esc($user['role']) ?>
                    </span>
                    
                    <hr class="my-4 text-muted opacity-25">
                    
                    <div class="text-start">
                        <div class="mb-3">
                            <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Nomor Induk / Identitas</span>
                            <span class="fw-bold text-dark fs-5"><?= esc($user['nomor_induk']) ?></span>
                        </div>
                        
                        <?php if(!empty($user['nama_prodi'])): ?>
                        <div class="mb-3">
                            <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Program Studi</span>
                            <span class="fw-semibold text-secondary"><?= esc($user['nama_prodi']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Profile Card -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom" style="letter-spacing: -0.3px;">Informasi Detail Akun</h5>
                    
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Nama Lengkap</span>
                                <span class="fw-bold text-dark"><?= esc($user['nama']) ?></span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Role Pengguna</span>
                                <span class="fw-bold text-dark text-capitalize"><?= esc($user['role']) ?></span>
                            </div>
                        </div>

                        <?php if(strtolower($user['role']) == 'taruna'): ?>
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Kelas & Jenjang</span>
                                    <span class="fw-bold text-dark"><?= esc($user['jenjang'] ?? '-') ?> - <?= esc($user['kelas'] ?? '-') ?></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Dosen Pembimbing</span>
                                    <span class="fw-bold text-dark"><?= !empty($user['nama_pembimbing']) ? esc($user['nama_pembimbing']) : 'Belum Ditentukan' ?></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <span class="text-muted small d-block fw-semibold mb-1 text-uppercase">Lokasi / Tempat Magang</span>
                                    <span class="fw-bold text-dark d-flex align-items-center gap-2 mt-1">
                                        <i class="bi bi-building text-primary"></i> <?= esc($user['tempat_magang'] ?? '-') ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <a href="<?= base_url('/dashboard') ?>" class="btn btn-light border px-4 rounded-3 fw-semibold">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
