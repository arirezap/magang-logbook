<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Main Content -->
    <div class="row g-4 mt-2">
        <!-- Welcome Card -->
        <div class="col-12">
            <?php
                $bulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $tanggalIndo = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
            ?>
            <div class="card border-0 shadow-sm rounded-4 welcome-gradient-card position-relative overflow-hidden">
                <!-- Floating Date Badge (Desktop) -->
                <div class="position-absolute top-0 end-0 m-4 d-none d-md-flex align-items-center gap-2 bg-white bg-opacity-25 px-3 py-2 rounded-pill" style="backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="bi bi-calendar-event text-white"></i>
                    <span class="fw-medium text-white" style="font-size: 0.85rem; letter-spacing: 0.5px;"><?= $tanggalIndo ?></span>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 z-2">
                            <span class="badge bg-white text-primary fw-bold mb-3 px-3 py-2 rounded-pill text-uppercase shadow-sm" style="font-size: 0.72rem; letter-spacing: 1px;"><?= esc(session()->get('role')) ?></span>
                            <h3 class="card-title fw-bold text-white mb-3" style="font-size: 2.2rem; letter-spacing: -0.5px;">Selamat Datang, <?= esc($user['nama']) ?>!</h3>
                            
                            <?php if($user['role'] == 'taruna'): ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1rem; max-width: 650px; line-height: 1.6; font-weight: 400;">
                                    Ini adalah dasbor utama Logbook Magang Anda. Catat setiap kegiatan harian secara rutin dan pantau status validasi dari dosen pembimbing dengan mudah.
                                </p>
                            <?php elseif($user['role'] == 'pembimbing'): ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1rem; max-width: 650px; line-height: 1.6; font-weight: 400;">
                                    Sebagai dosen pembimbing, Anda bertugas untuk memantau, memeriksa, dan memvalidasi logbook kegiatan harian taruna bimbingan Anda secara berkala.
                                </p>
                            <?php else: ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1rem; max-width: 650px; line-height: 1.6; font-weight: 400;">
                                    Selamat bekerja! Anda memiliki hak akses penuh untuk mengelola pengguna, memantau laporan global, dan memastikan kelancaran administrasi magang.
                                </p>
                            <?php endif; ?>
                            
                            <!-- Mobile Date Badge -->
                            <div class="d-flex d-md-none align-items-center gap-2 mt-4 bg-white bg-opacity-25 px-3 py-2 rounded-pill d-inline-flex" style="backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2);">
                                <i class="bi bi-calendar-event text-white"></i>
                                <span class="fw-medium text-white" style="font-size: 0.85rem; letter-spacing: 0.5px;"><?= $tanggalIndo ?></span>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-end z-2 position-absolute" style="right: -20px; bottom: -30px;">
                            <i class="bi bi-person-workspace text-white" style="font-size: 12rem; opacity: 0.08;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-based Bento Widgets and Stats -->
        <?php if($user['role'] == 'taruna'): ?>
            <!-- Stats Widgets -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-primary">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Total Laporan</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['total'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Disetujui</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['disetujui'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Pending</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['pending'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-danger">
                                    <i class="bi bi-exclamation-octagon-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Revisi</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['revisi'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Info Bento Grid -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4" style="letter-spacing: -0.3px;">Informasi Akademik & Magang</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="info-bento-box d-flex align-items-start gap-3">
                                    <i class="bi bi-card-text text-primary fs-3"></i>
                                    <div>
                                        <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">NOMOR TARUNA (NOTAR)</div>
                                        <h5 class="fw-bold text-dark m-0 mt-1"><?= esc($user['nomor_induk']) ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="info-bento-box d-flex align-items-start gap-3">
                                    <i class="bi bi-mortarboard text-primary fs-3"></i>
                                    <div>
                                        <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">PROGRAM STUDI / KELAS</div>
                                        <h5 class="fw-bold text-dark m-0 mt-1"><?= esc($user['jenjang'] ?? '-') ?> - <?= esc($user['kelas_lengkap'] ?? '-') ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="info-bento-box d-flex align-items-start gap-3">
                                    <i class="bi bi-building text-primary fs-3"></i>
                                    <div>
                                        <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">LOKASI / TEMPAT MAGANG</div>
                                        <h5 class="fw-bold text-dark m-0 mt-1"><?= esc($user['tempat_magang'] ?? '-') ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if(!empty($stats_bimbingan)): ?>
            <!-- Stats Widgets for Pembimbing -->
            <div class="col-12 mt-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary"></i> Statistik Bimbingan Saya</h5>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-primary" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 500;">Taruna Bimbingan Anda</div>
                                    <h3 class="fw-bold m-0 text-dark"><?= $stats_bimbingan['total_taruna'] ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-warning" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 500;">Logbook Menunggu Validasi</div>
                                    <h3 class="fw-bold m-0 text-dark"><?= $stats_bimbingan['pending_validasi'] ?> <span class="fs-6 fw-normal text-muted">Laporan</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(!empty($stats_monitoring)): ?>
            <!-- Stats Widgets for Admins / Pejabat -->
            <div class="col-12 mt-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-display text-primary"></i> Statistik Monitoring (Global)</h5>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-primary">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Total Taruna</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats_monitoring['total_taruna'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-success">
                                    <i class="bi bi-person-workspace"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Total Pembimbing</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats_monitoring['total_pembimbing'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-info">
                                    <i class="bi bi-journal-check"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Logbook Hari Ini</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats_monitoring['logbook_hari_ini'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-warning">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Laporan Pending</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats_monitoring['pending_logbook'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>
