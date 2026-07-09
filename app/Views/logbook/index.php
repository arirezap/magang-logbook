<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Riwayat Logbook</h2>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Catatan dan riwayat pengisian kegiatan harian magang Anda.</p>
        </div>
        <a href="<?= base_url('/logbook/create') ?>" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-semibold rounded-3 shadow-sm border-0 bg-primary-custom">
            <i class="bi bi-plus-circle-fill"></i> Tambah Logbook
        </a>
    </div>

    <!-- Alert Notifications -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert-premium alert-success-premium mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
            <button type="button" class="btn-close shadow-none border-0" data-bs-dismiss="alert" aria-label="Close" style="background: none; font-size: 1.15rem; color: inherit;">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Logbook Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="px-4 py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 15%;">Tanggal</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 45%;">Kegiatan & Catatan</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 15%;">Dokumentasi</th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 10%;">Status</th>
                            <th class="px-4 py-3 text-center" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 text-light mb-3 d-block"></i>
                                <h5 class="fw-bold text-dark">Belum ada riwayat logbook</h5>
                                <p class="small text-muted mb-0">Silakan tambahkan logbook harian pertama Anda dengan tombol di kanan atas.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($logbooks as $log): ?>
                                <?php
                                    // Status Badge mapping
                                    $badgeClass = 'badge-status-pending';
                                    $statusText = 'Menunggu Validasi';
                                    
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
                                ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                        <?= date('d', strtotime($log['tanggal'])) ?>
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.8rem;">
                                        <?= date('M Y', strtotime($log['tanggal'])) ?>
                                    </div>
                                </td>
                                <td class="py-3 pe-4">
                                    <!-- Full Activity Description without cut-off -->
                                    <div class="kegiatan-text"><?= esc($log['kegiatan']) ?></div>
                                    
                                    <!-- Full Advisor Note if exists -->
                                    <?php if(!empty($log['catatan_pembimbing'])): ?>
                                        <div class="catatan-pembimbing-box d-flex align-items-start gap-2">
                                            <i class="bi bi-info-circle-fill fs-6 flex-shrink-0 mt-0.5"></i>
                                            <div>
                                                <strong>Catatan Pembimbing:</strong> <?= esc($log['catatan_pembimbing']) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-primary-subtle text-primary border-0 rounded-3 px-3 py-1.5 d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem; font-weight: 600;">
                                        <i class="bi bi-google-drive"></i> GDrive <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                    </a>
                                </td>
                                <td class="py-3">
                                    <span class="<?= $badgeClass ?> text-nowrap"><?= $statusText ?></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if(in_array($log['status'], ['pending', 'revisi', 'ditolak'])): ?>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?= base_url('/logbook/edit/' . $log['id']) ?>" class="btn btn-sm btn-action-edit d-flex align-items-center gap-1 py-1.5 px-3 border-0">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="<?= base_url('/logbook/delete/' . $log['id']) ?>" 
                                               class="btn btn-sm btn-action-delete d-flex align-items-center gap-1 py-1.5 px-3 border-0" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus logbook ini? Tindakan ini tidak dapat dibatalkan.');">
                                                <i class="bi bi-trash-fill"></i> Hapus
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small d-inline-flex align-items-center gap-1"><i class="bi bi-lock-fill"></i> Terkunci</span>
                                    <?php endif; ?>
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
