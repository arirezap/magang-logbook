<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Validasi Logbook Taruna</h2>
        <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Tinjau dan berikan penilaian pada laporan harian anak bimbingan Anda.</p>
    </div>
    <?php
        $pendingCount = 0;
        foreach ($logbooks as $l) { if ($l['status'] === 'pending') $pendingCount++; }
    ?>
    <?php if ($pendingCount > 0): ?>
    <div class="d-flex align-items-center gap-2 bg-warning bg-opacity-10 border border-warning border-opacity-25 px-4 py-2 rounded-pill shadow-sm">
        <i class="bi bi-hourglass-split text-warning fs-5"></i>
        <span class="fw-semibold text-warning-emphasis" style="font-size: 0.9rem;"><?= $pendingCount ?> Laporan Menunggu</span>
    </div>
    <?php endif; ?>
</div>

<!-- Flash Messages -->
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success-premium alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter Panel -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="<?= base_url('/validasi') ?>" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">TANGGAL</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-white border-secondary-subtle px-2"><i class="bi bi-calendar-range text-muted"></i></span>
                        <input type="text" class="form-control border-secondary-subtle border-start-0 bg-white ps-1" id="dateRangePicker" name="tanggal" placeholder="Pilih Tanggal..." value="<?= esc($filterTanggal ?? date('Y-m-d')) ?>" readonly>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">PENCARIAN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-secondary-subtle border-start-0 shadow-none" id="nama" name="nama" placeholder="Pencarian..." value="<?= esc($filterNama ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">STATUS</label>
                    <select name="status" class="form-select form-control-custom">
                        <option value="">Semua Status</option>
                        <option value="pending"   <?= ($filterStatus ?? '') === 'pending'   ? 'selected' : '' ?>>⏳ Pending</option>
                        <option value="disetujui" <?= ($filterStatus ?? '') === 'disetujui' ? 'selected' : '' ?>>✅ Disetujui</option>
                        <option value="revisi"    <?= ($filterStatus ?? '') === 'revisi'    ? 'selected' : '' ?>>🔄 Perlu Revisi</option>
                        <option value="ditolak"   <?= ($filterStatus ?? '') === 'ditolak'   ? 'selected' : '' ?>>❌ Ditolak</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label d-none d-md-block mb-2">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill fw-medium shadow-sm rounded-3 d-flex align-items-center justify-content-center py-2">
                            <i class="bi bi-funnel-fill me-2"></i> Terapkan
                        </button>
                        <a href="<?= base_url('/validasi') ?>" class="btn btn-light border-secondary-subtle btn-sm rounded-3 d-flex align-items-center justify-content-center px-3 py-2" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise text-secondary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <?php if(empty($logbooks)): ?>
        <div class="text-center py-5 px-4">
            <div class="mb-3" style="font-size: 3.5rem; opacity: 0.2;">📋</div>
            <h5 class="fw-semibold text-muted">Tidak ada laporan ditemukan</h5>
            <p class="text-muted small mb-0">
                <?php if(!empty($filterNama) || !empty($filterStatus)): ?>
                    Silakan ubah kriteria pencarian Anda.
                <?php else: ?>
                    Belum ada taruna bimbingan yang mengumpulkan logbook.
                <?php endif; ?>
            </p>
        </div>
        <?php else: ?>

        <!-- Desktop View (Table) -->
        <div class="table-responsive d-none d-xl-block">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 2px solid #e8eeff;">
                    <tr>
                        <th class="px-4 py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 20%;">TARUNA</th>
                        <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 45%;">KEGIATAN</th>
                        <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 25%;">UPDATE VALIDASI</th>
                        <th class="px-4 py-3 fw-bold text-muted text-center" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 10%;">DETAIL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logbooks as $log): ?>
                        <?php
                            // Badge status
                            switch ($log['status']) {
                                case 'disetujui': $badgeClass = 'badge-status-disetujui'; $statusText = 'Disetujui'; $statusIcon = 'bi-check-circle-fill'; break;
                                case 'revisi':    $badgeClass = 'badge-status-revisi';    $statusText = 'Revisi'; $statusIcon = 'bi-arrow-repeat'; break;
                                case 'ditolak':   $badgeClass = 'badge-status-ditolak';   $statusText = 'Ditolak'; $statusIcon = 'bi-x-circle-fill'; break;
                                default:          $badgeClass = 'badge-status-pending';   $statusText = 'Pending'; $statusIcon = 'bi-hourglass-split';
                            }
                            // Singkatan kelas
                            $singkatanProdi = '';
                            if (!empty($log['nama_prodi'])) {
                                $np = strtoupper(trim($log['nama_prodi']));
                                if ($np === 'REKAYASA SISTEM TRANSPORTASI JALAN') $singkatanProdi = 'RSTJ';
                                elseif ($np === 'TEKNOLOGI REKAYASA OTOMOTIF')    $singkatanProdi = 'TRO';
                                elseif ($np === 'TEKNOLOGI OTOMOTIF')             $singkatanProdi = 'TO';
                                else { foreach (explode(' ', $np) as $w) $singkatanProdi .= substr($w, 0, 1); }
                            }
                            $kelasTampil = $singkatanProdi . (!empty($log['kelas']) ? ' ' . strtoupper($log['kelas']) : '');
                            $initial = strtoupper(substr($log['nama_taruna'], 0, 1));
                        ?>
                        <tr class="border-bottom border-light hover-shadow-sm transition-all">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                                         style="width:45px;height:45px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:1rem;">
                                        <?= $initial ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate" style="font-size:0.95rem; max-width: 200px;" title="<?= esc($log['nama_taruna']) ?>">
                                            <?= esc($log['nama_taruna']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size:0.8rem;">
                                            <span class="fw-medium"><?= esc($log['notar_taruna']) ?></span> &bull; <?= $kelasTampil ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="mb-1 text-start">
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($log['tanggal'])) ?></span>
                                </div>
                                <div class="text-muted kegiatan-text text-start" style="font-size:0.85rem; white-space: pre-wrap;" title="<?= esc($log['kegiatan']) ?>"><?= esc($log['kegiatan']) ?></div>
                                <?php if(!empty($log['dokumentasi'])): ?>
                                <div class="mt-2 text-start">
                                    <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold shadow-sm hover-lift" style="font-size:0.75rem; padding: 0.15rem 0.6rem;"><i class="bi bi-link-45deg"></i> Lihat Bukti</a>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <form action="<?= base_url('/validasi/action/' . $log['id']) ?>" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <select name="status" class="form-select form-select-sm fw-semibold shadow-none border-secondary-subtle flex-grow-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                                                <option value="pending"   <?= $log['status'] === 'pending'   ? 'selected' : '' ?>>⏳ Pending</option>
                                                <option value="disetujui" <?= $log['status'] === 'disetujui' ? 'selected' : '' ?>>✅ Disetujui</option>
                                                <option value="revisi"    <?= $log['status'] === 'revisi'    ? 'selected' : '' ?>>🔄 Revisi</option>
                                                <option value="ditolak"   <?= $log['status'] === 'ditolak'   ? 'selected' : '' ?>>❌ Ditolak</option>
                                            </select>
                                            <!-- Current Status Badge Info -->
                                            <span class="badge <?= $badgeClass ?> py-2 px-2 rounded-pill flex-shrink-0" title="Status Saat Ini">
                                                <i class="bi <?= $statusIcon ?>"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="catatan_pembimbing" class="form-control form-control-sm border-secondary-subtle bg-light" 
                                               placeholder="Catatan (Enter utk simpan)..." value="<?= esc($log['catatan_pembimbing']) ?>" style="font-size: 0.75rem;" onchange="this.form.submit()">
                                    </div>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm btn-tinjau hover-lift"
                                        style="font-size: 0.75rem;"
                                        data-id="<?= $log['id'] ?>"
                                        data-nama="<?= esc($log['nama_taruna']) ?>"
                                        data-notar="<?= esc($log['notar_taruna']) ?>"
                                        data-kelas="<?= esc($kelasTampil) ?>"
                                        data-tanggal="<?= date('d F Y', strtotime($log['tanggal'])) ?>"
                                        data-kegiatan="<?= esc(addslashes($log['kegiatan'])) ?>"
                                        data-dokumentasi="<?= esc($log['dokumentasi']) ?>">
                                    <i class="bi bi-eye-fill me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile / Tablet View (Cards) -->
        <div class="d-xl-none p-3 d-flex flex-column gap-3 bg-light bg-opacity-50">
            <?php foreach($logbooks as $log): ?>
                <?php
                    // Badge status
                    switch ($log['status']) {
                        case 'disetujui': $badgeClass = 'badge-status-disetujui'; $statusText = 'Disetujui'; $statusIcon = 'bi-check-circle-fill'; break;
                        case 'revisi':    $badgeClass = 'badge-status-revisi';    $statusText = 'Revisi'; $statusIcon = 'bi-arrow-repeat'; break;
                        case 'ditolak':   $badgeClass = 'badge-status-ditolak';   $statusText = 'Ditolak'; $statusIcon = 'bi-x-circle-fill'; break;
                        default:          $badgeClass = 'badge-status-pending';   $statusText = 'Pending'; $statusIcon = 'bi-hourglass-split';
                    }
                    $singkatanProdi = '';
                    if (!empty($log['nama_prodi'])) {
                        $np = strtoupper(trim($log['nama_prodi']));
                        if ($np === 'REKAYASA SISTEM TRANSPORTASI JALAN') $singkatanProdi = 'RSTJ';
                        elseif ($np === 'TEKNOLOGI REKAYASA OTOMOTIF')    $singkatanProdi = 'TRO';
                        elseif ($np === 'TEKNOLOGI OTOMOTIF')             $singkatanProdi = 'TO';
                        else { foreach (explode(' ', $np) as $w) $singkatanProdi .= substr($w, 0, 1); }
                    }
                    $kelasTampil = $singkatanProdi . (!empty($log['kelas']) ? ' ' . strtoupper($log['kelas']) : '');
                    $initial = strtoupper(substr($log['nama_taruna'], 0, 1));
                ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <!-- Card Header -->
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                                 style="width:40px;height:40px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:0.9rem;">
                                <?= $initial ?>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate" style="font-size:0.95rem;"><?= esc($log['nama_taruna']) ?></div>
                                <div class="text-muted" style="font-size:0.75rem;"><?= esc($log['notar_taruna']) ?> &bull; <?= $kelasTampil ?></div>
                            </div>
                        </div>
                        <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1 shadow-sm"><i class="bi <?= $statusIcon ?>"></i></span>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border px-2 py-1 mb-2"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y', strtotime($log['tanggal'])) ?></span>
                            <p class="text-dark mb-0 text-start" style="font-size:0.85rem; white-space:pre-wrap;"><?= esc($log['kegiatan']) ?></p>
                            <?php if(!empty($log['dokumentasi'])): ?>
                            <div class="mt-2 text-start">
                                <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold shadow-sm" style="font-size:0.75rem; padding: 0.15rem 0.6rem;"><i class="bi bi-link-45deg"></i> Lihat Bukti</a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-3 p-3 bg-light rounded-3 border">
                            <form action="<?= base_url('/validasi/action/' . $log['id']) ?>" method="POST" class="m-0">
                                <?= csrf_field() ?>
                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.75rem;">UPDATE VALIDASI</label>
                                <div class="mb-2">
                                    <select name="status" class="form-select form-select-sm fw-semibold shadow-none border-secondary-subtle" onchange="this.form.submit()">
                                        <option value="pending"   <?= $log['status'] === 'pending'   ? 'selected' : '' ?>>⏳ Pending</option>
                                        <option value="disetujui" <?= $log['status'] === 'disetujui' ? 'selected' : '' ?>>✅ Disetujui</option>
                                        <option value="revisi"    <?= $log['status'] === 'revisi'    ? 'selected' : '' ?>>🔄 Revisi</option>
                                        <option value="ditolak"   <?= $log['status'] === 'ditolak'   ? 'selected' : '' ?>>❌ Ditolak</option>
                                    </select>
                                </div>
                                <input type="text" name="catatan_pembimbing" class="form-control form-control-sm border-secondary-subtle" 
                                       placeholder="Catatan (Enter utk simpan)..." value="<?= esc($log['catatan_pembimbing']) ?>" style="font-size: 0.8rem;" onchange="this.form.submit()">
                            </form>
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                        <button type="button"
                                class="btn btn-outline-primary w-100 rounded-pill fw-semibold shadow-sm btn-tinjau"
                                data-id="<?= $log['id'] ?>"
                                data-nama="<?= esc($log['nama_taruna']) ?>"
                                data-notar="<?= esc($log['notar_taruna']) ?>"
                                data-kelas="<?= esc($kelasTampil) ?>"
                                data-tanggal="<?= date('d F Y', strtotime($log['tanggal'])) ?>"
                                data-kegiatan="<?= esc(addslashes($log['kegiatan'])) ?>"
                                data-dokumentasi="<?= esc($log['dokumentasi']) ?>">
                            <i class="bi bi-file-text-fill me-1"></i> Lihat Detail Laporan
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer Summary -->
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2"
             style="background: #f8f9ff; border-radius: 0 0 1rem 1rem;">
            <span class="text-muted fw-medium" style="font-size:0.85rem;">
                Menampilkan data halaman ini dari total keseluruhan.
            </span>
            <?php if(!empty($filterNama) || !empty($filterStatus)): ?>
            <a href="<?= base_url('/validasi') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm" style="font-size:0.8rem;">
                <i class="bi bi-x-circle-fill me-1"></i>Hapus Pencarian
            </a>
            <?php endif; ?>
        </div>
        
        <div class="w-100 mt-4 mb-2 d-flex justify-content-center">
            <?= $pager->links('logbooks', 'custom_pagination') ?>
        </div>

        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     SINGLE MODAL DETAIL — rendered outside DOM
     Hanya untuk menampilkan detail, form validasi dipindah ke tabel
     ============================================================ -->
<div class="modal fade" id="modalTinjauGlobal" tabindex="-1" aria-labelledby="modalTinjauLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTinjauLabel">Detail Laporan Harian</h5>
                        <div class="text-muted" style="font-size: 0.85rem;">Informasi lengkap kegiatan magang taruna</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-4">
                <!-- Info Row -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <div class="rounded-4 p-3 border border-light shadow-sm h-100" style="background: linear-gradient(to right, #ffffff, #f8f9ff);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-badge text-primary fs-5"></i>
                                <div class="text-muted fw-bold" style="font-size:0.75rem; letter-spacing:1px;">PROFIL TARUNA</div>
                            </div>
                            <div class="fw-bold text-dark fs-6" id="modal-nama">—</div>
                            <div class="text-muted" style="font-size:0.85rem;" id="modal-notar-kelas">—</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="rounded-4 p-3 border border-light shadow-sm h-100" style="background: linear-gradient(to right, #ffffff, #f8f9ff);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-calendar-check text-primary fs-5"></i>
                                <div class="text-muted fw-bold" style="font-size:0.75rem; letter-spacing:1px;">WAKTU PELAKSANAAN</div>
                            </div>
                            <div class="fw-bold text-dark fs-6" id="modal-tanggal">—</div>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan -->
                <div class="rounded-4 p-4 mb-4 border shadow-sm" style="background: #ffffff; border-color: #e8eeff !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-card-text text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Uraian Kegiatan</h6>
                    </div>
                    <p class="mb-0 text-dark" id="modal-kegiatan" style="white-space:pre-wrap; font-size:0.95rem; line-height:1.8;">—</p>
                </div>

                <!-- Dokumentasi -->
                <div class="rounded-4 p-4 border shadow-sm" style="background: #ffffff; border-color: #e8eeff !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-images text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Lampiran & Dokumentasi</h6>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">Taruna telah melampirkan file dokumentasi pada tautan Google Drive berikut:</p>
                    <a id="modal-link-docs" href="#" target="_blank" rel="noopener noreferrer"
                       class="btn btn-primary-custom rounded-pill px-4 py-2 shadow-sm fw-semibold hover-lift">
                        <i class="bi bi-google me-2"></i>Buka Tautan Google Drive
                    </a>
                </div>
            </div>
            
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Tambahan untuk UI UX Pro Max */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}
.hover-shadow-sm:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    background-color: #fcfdff;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pindahkan modal ke <body> agar tidak terpengaruh stacking context tabel/card (fix mobile)
    const modalEl = document.getElementById('modalTinjauGlobal');
    if (modalEl && modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }

    const bsModal = new bootstrap.Modal(modalEl, { keyboard: true });

    document.querySelectorAll('.btn-tinjau').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const d = btn.dataset;

            // Isi konten modal (hanya detail, form validasi sudah di tabel)
            document.getElementById('modal-nama').textContent         = d.nama;
            document.getElementById('modal-notar-kelas').textContent  = d.notar + (d.kelas ? ' • ' + d.kelas : '');
            document.getElementById('modal-tanggal').textContent      = d.tanggal;
            document.getElementById('modal-kegiatan').textContent     = d.kegiatan;
            document.getElementById('modal-link-docs').href           = d.dokumentasi || '#';

            bsModal.show();
        });
    });
});
</script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "id"
        });
    });
</script>

<?= $this->endSection() ?>
