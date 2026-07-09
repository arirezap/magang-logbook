<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Dashboard</h2>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Selamat datang kembali di portal Logbook PKTJ.</p>
        </div>
        <?php
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $tanggalIndo = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
        ?>
        <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-3 shadow-sm border">
            <i class="bi bi-calendar3 text-primary"></i>
            <span class="fw-semibold text-secondary" style="font-size: 0.88rem;"><?= $tanggalIndo ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 welcome-gradient-card">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 z-2">
                            <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;"><?= esc(session()->get('role')) ?></span>
                            <h3 class="card-title fw-bold text-white mb-2" style="font-size: 2.2rem; letter-spacing: -0.5px;">Selamat Datang, <?= esc($user['nama']) ?>!</h3>
                            
                            <?php if($user['role'] == 'taruna'): ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1.05rem; max-width: 600px; line-height: 1.6;">
                                    Ini adalah halaman utama logbook magang Anda. Pastikan Anda mencatat setiap kegiatan harian secara rutin dan memantau status validasi dari dosen pembimbing.
                                </p>
                            <?php elseif($user['role'] == 'pembimbing'): ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1.05rem; max-width: 600px; line-height: 1.6;">
                                    Sebagai dosen pembimbing, Anda bertugas untuk memantau, memeriksa, dan memvalidasi logbook kegiatan harian taruna bimbingan Anda secara berkala.
                                </p>
                            <?php else: ?>
                                <p class="card-text text-white-50 mb-0" style="font-size: 1.05rem; max-width: 600px; line-height: 1.6;">
                                    Selamat bekerja! Anda memiliki hak akses penuh untuk mengelola pengguna, memantau laporan global, dan memastikan kelancaran administrasi magang taruna.
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-end z-2">
                            <i class="bi bi-person-workspace text-white-50" style="font-size: 7.5rem; opacity: 0.15;"></i>
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

        <?php elseif($user['role'] == 'pembimbing'): ?>
            <!-- Stats Widgets for Pembimbing -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-primary" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 500;">Taruna Bimbingan Anda</div>
                                    <h3 class="fw-bold m-0 text-dark"><?= $stats['total_taruna'] ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
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
                                    <h3 class="fw-bold m-0 text-dark"><?= $stats['pending_validasi'] ?> <span class="fs-6 fw-normal text-muted">Laporan</span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Stats Widgets for Admins / Pejabat -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="card dashboard-stat-card">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                                <div class="stat-icon-wrapper stat-primary">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Total Taruna</div>
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['total_taruna'] ?></h4>
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
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['total_pembimbing'] ?></h4>
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
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['logbook_hari_ini'] ?></h4>
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
                                    <h4 class="fw-bold m-0 text-dark"><?= $stats['pending_logbook'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>
