<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Laporan Global Logbook Magang</h2>
        <p class="text-muted m-0" style="font-size: 0.9rem;">Pantau keseluruhan aktivitas pengisian logbook harian Taruna secara <em>real-time</em>.</p>
    </div>

    <!-- Statistik Cards (Bento Tinted Style) -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4" style="background-color: rgba(30, 58, 138, 0.04); border-color: #1e3a8a !important;">
                <div class="card-body py-3 px-4">
                    <h6 class="text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Logbook</h6>
                    <h2 class="mb-0 fw-bold" style="color: #1e3a8a; font-size: 1.8rem;"><?= $total ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4" style="background-color: rgba(25, 135, 84, 0.04); border-color: #198754 !important;">
                <div class="card-body py-3 px-4">
                    <h6 class="text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Disetujui</h6>
                    <h2 class="mb-0 fw-bold" style="color: #198754; font-size: 1.8rem;"><?= $disetujui ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4" style="background-color: rgba(255, 193, 7, 0.08); border-color: #d97706 !important;">
                <div class="card-body py-3 px-4">
                    <h6 class="text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Menunggu Validasi</h6>
                    <h2 class="mb-0 fw-bold" style="color: #d97706; font-size: 1.8rem;"><?= $pending ?></h2>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4" style="background-color: rgba(220, 53, 69, 0.04); border-color: #dc3545 !important;">
                <div class="card-body py-3 px-4">
                    <h6 class="text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Revisi / Ditolak</h6>
                    <h2 class="mb-0 fw-bold" style="color: #dc3545; font-size: 1.8rem;"><?= $revisi_ditolak ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="card-title fw-bold text-muted mb-3"><i class="bi bi-funnel"></i> Filter Pencarian</h6>
            <form method="get" action="<?= base_url('laporan') ?>" class="row g-3">
                <div class="col-12 <?= in_array($userRole, ['superadmin', 'pejabat']) ? 'col-md-2' : 'col-md-3' ?>">
                    <label for="tanggal" class="form-label-custom">Tanggal Pelaporan</label>
                    <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" value="<?= esc($filterTanggal ?? '') ?>">
                </div>
                
                <?php if (in_array($userRole, ['superadmin', 'pejabat'])): ?>
                <div class="col-12 col-md-3">
                    <label for="prodi" class="form-label-custom">Program Studi</label>
                    <select class="form-select form-select-custom" id="prodi" name="prodi">
                        <option value="">-- Semua Prodi --</option>
                        <?php foreach ($prodiList as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($filterProdi == $p['id']) ? 'selected' : '' ?>><?= esc($p['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-12 <?= in_array($userRole, ['superadmin', 'pejabat']) ? 'col-md-2' : 'col-md-3' ?>">
                    <label for="kelas" class="form-label-custom">Kelas</label>
                    <select class="form-select form-select-custom" id="kelas" name="kelas">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= esc($k['kelas']) ?>" <?= ($filterKelas == $k['kelas']) ? 'selected' : '' ?>><?= esc($k['kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="nama" class="form-label-custom">Pencarian</label>
                    <input type="text" class="form-control form-control-custom" id="nama" name="nama" placeholder="Pencarian..." value="<?= esc($filterNama ?? '') ?>">
                </div>

                <div class="col-12 <?= in_array($userRole, ['superadmin', 'pejabat']) ? 'col-md-2' : 'col-md-3' ?>">
                    <label for="status" class="form-label-custom">Status Validasi</label>
                    <select class="form-select form-select-custom" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" <?= ($filterStatus == 'pending') ? 'selected' : '' ?>>Pending / Menunggu</option>
                        <option value="disetujui" <?= ($filterStatus == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                        <option value="revisi" <?= ($filterStatus == 'revisi') ? 'selected' : '' ?>>Perlu Revisi</option>
                        <option value="ditolak" <?= ($filterStatus == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('laporan') ?>" class="btn btn-light border px-4 rounded-3 fw-semibold text-muted">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold border-0 bg-primary-custom">
                        <i class="bi bi-search"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="px-4 py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 22%;">Taruna</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 13%;">Tanggal</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 40%;">Deskripsi Kegiatan</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 15%;">Pembimbing</th>
                            <th class="px-4 py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 10%;">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 text-light mb-3 d-block"></i>
                                <span>Belum ada data laporan logbook yang sesuai filter.</span>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($logbooks as $log): ?>
                                <?php
                                    // Status Badges
                                    $badgeClass = 'badge-status-pending';
                                    $statusText = 'Pending';
                                    if ($log['status'] == 'disetujui') {
                                        $badgeClass = 'badge-status-disetujui';
                                        $statusText = 'Disetujui';
                                    } elseif ($log['status'] == 'ditolak') {
                                        $badgeClass = 'badge-status-revisi';
                                        $statusText = 'Ditolak';
                                    } elseif ($log['status'] == 'revisi') {
                                        $badgeClass = 'badge-status-revisi';
                                        $statusText = 'Perlu Revisi';
                                    }
                                    
                                    // Singkatan Nama Prodi
                                    $singkatanProdi = '';
                                    if (!empty($log['nama_prodi'])) {
                                        $namaProdi = strtoupper(trim($log['nama_prodi']));
                                        if ($namaProdi == 'REKAYASA SISTEM TRANSPORTASI JALAN') $singkatanProdi = 'RSTJ';
                                        elseif ($namaProdi == 'TEKNOLOGI REKAYASA OTOMOTIF') $singkatanProdi = 'TRO';
                                        elseif ($namaProdi == 'TEKNOLOGI OTOMOTIF') $singkatanProdi = 'TO';
                                        else {
                                            $words = explode(' ', $namaProdi);
                                            foreach ($words as $w) $singkatanProdi .= substr($w, 0, 1);
                                        }
                                    }
                                    $kelasTampil = $singkatanProdi . (!empty($log['kelas']) ? ' ' . strtoupper($log['kelas']) : '');
                                ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Initial Circle -->
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary shadow-sm flex-shrink-0" 
                                             style="width: 38px; height: 38px; font-size: 0.95rem; border: 1px solid rgba(0,0,0,0.05);">
                                            <?= strtoupper(substr($log['nama_taruna'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0" style="font-size: 0.92rem;"><?= esc($log['nama_taruna']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.78rem;"><?= esc($log['notar_taruna']) ?> &bull; <?= $kelasTampil ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-secondary" style="font-size: 0.9rem;">
                                    <?= date('d M Y', strtotime($log['tanggal'])) ?>
                                </td>
                                <td class="py-3 pe-4">
                                    <!-- Full Activity Description without cut-off -->
                                    <div class="kegiatan-text"><?= esc($log['kegiatan']) ?></div>
                                    
                                    <!-- Link Dokumentasi -->
                                    <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-primary-subtle text-primary border-0 rounded-3 px-3 py-1.5 d-inline-flex align-items-center gap-1.5 mt-2" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="bi bi-google-drive"></i> GDrive <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                </td>
                                <td class="py-3 text-secondary" style="font-size: 0.9rem;">
                                    <?= !empty($log['nama_pembimbing']) ? esc($log['nama_pembimbing']) : '-' ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="<?= $badgeClass ?> text-nowrap"><?= $statusText ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
