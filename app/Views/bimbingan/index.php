<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 shadow-sm">
            <i class="bi bi-people-fill fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Daftar Taruna Bimbingan</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Kelola dan pantau taruna yang berada di bawah bimbingan Anda.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 bg-white border px-4 py-2 rounded-pill shadow-sm">
        <i class="bi bi-person-badge text-primary fs-5"></i>
        <span class="fw-bold text-dark" style="font-size: 0.9rem;">Total: <?= count($tarunas) ?> Taruna</span>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <?php if(empty($tarunas)): ?>
            <div class="text-center py-5 px-4">
                <div class="mb-3" style="font-size: 4rem; opacity: 0.15;">👥</div>
                <h4 class="fw-bold text-muted">Belum Ada Taruna</h4>
                <p class="text-muted mb-0">Saat ini belum ada taruna yang ditugaskan kepada Anda sebagai pembimbing.</p>
            </div>
        <?php else: ?>

            <!-- Desktop View (Table) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 2px solid #e8eeff;">
                        <tr>
                            <th class="text-center px-3 py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 5%;">NO</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 35%;">PROFIL TARUNA</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 25%;">PROGRAM STUDI & KELAS</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 25%;">TEMPAT MAGANG</th>
                            <th class="px-4 py-3 fw-bold text-muted text-center" style="font-size: 0.8rem; letter-spacing: 0.5px; width: 10%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($tarunas as $taruna): ?>
                            <?php 
                                $initial = strtoupper(substr($taruna['nama'], 0, 1));
                                $linkValidasi = base_url('/validasi?nama=' . urlencode($taruna['nama']));
                            ?>
                            <tr class="border-bottom border-light hover-shadow-sm transition-all cursor-pointer" onclick="window.location='<?= $linkValidasi ?>'">
                                <td class="text-center text-muted fw-semibold" style="font-size: 0.9rem;"><?= $no++ ?></td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                                             style="width:45px;height:45px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:1.1rem;">
                                            <?= $initial ?>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="<?= $linkValidasi ?>" class="text-decoration-none text-dark hover-text-primary">
                                                <div class="fw-bold text-truncate" style="font-size:1rem; max-width: 250px;" title="<?= esc($taruna['nama']) ?>">
                                                    <?= esc($taruna['nama']) ?>
                                                </div>
                                            </a>
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                                    <i class="bi bi-person-vcard me-1"></i><?= esc($taruna['nomor_induk']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.9rem;"><?= esc($taruna['nama_prodi'] ?? '-') ?></div>
                                    <div class="text-muted" style="font-size: 0.85rem;">
                                        Kelas: <span class="fw-medium text-dark"><?= esc($taruna['kelas'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light text-primary p-2 rounded-circle">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <span class="fw-medium text-dark" style="font-size: 0.9rem;"><?= esc($taruna['tempat_magang'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="<?= $linkValidasi ?>" class="btn btn-sm btn-primary-custom rounded-pill px-3 py-2 fw-semibold shadow-sm hover-lift" title="Tinjau Logbook <?= esc($taruna['nama']) ?>" onclick="event.stopPropagation();">
                                        <i class="bi bi-journal-check me-1"></i> Logbook
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile / Tablet View (Cards) -->
            <div class="d-lg-none p-3 d-flex flex-column gap-3 bg-light bg-opacity-50">
                <?php foreach($tarunas as $taruna): ?>
                    <?php 
                        $initial = strtoupper(substr($taruna['nama'], 0, 1));
                        $linkValidasi = base_url('/validasi?nama=' . urlencode($taruna['nama']));
                    ?>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden cursor-pointer hover-lift" onclick="window.location='<?= $linkValidasi ?>'">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                                     style="width:50px;height:50px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:1.2rem;">
                                    <?= $initial ?>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <a href="<?= $linkValidasi ?>" class="text-decoration-none text-dark hover-text-primary">
                                        <h6 class="fw-bold text-truncate mb-1" style="font-size:1.05rem;"><?= esc($taruna['nama']) ?></h6>
                                    </a>
                                    <span class="badge bg-light text-dark border px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                        <i class="bi bi-person-vcard me-1"></i><?= esc($taruna['nomor_induk']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">PROGRAM STUDI & KELAS</div>
                                <div class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= esc($taruna['nama_prodi'] ?? '-') ?></div>
                                <div class="text-muted mt-1" style="font-size: 0.85rem;">Kelas: <?= esc($taruna['kelas'] ?? '-') ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">TEMPAT MAGANG</div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;"><?= esc($taruna['tempat_magang'] ?? '-') ?></span>
                                </div>
                            </div>
                            
                            <a href="<?= $linkValidasi ?>" class="btn btn-primary-custom w-100 rounded-pill py-2 fw-bold shadow-sm" onclick="event.stopPropagation();">
                                <i class="bi bi-journal-check me-2"></i> Tinjau Logbook Taruna
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>

<style>
/* CSS Tambahan untuk UI UX Pro Max */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
}
.hover-shadow-sm:hover {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
    background-color: #fcfdff;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.cursor-pointer {
    cursor: pointer;
}
.hover-text-primary:hover {
    color: #1a56db !important;
}
</style>

<?= $this->endSection() ?>
