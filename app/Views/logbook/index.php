<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Riwayat Logbook</h3>
            <p class="text-muted mb-0">Catatan kegiatan harian magang Anda.</p>
        </div>
        <a href="<?= base_url('/logbook/create') ?>" class="btn btn-primary-custom fw-bold px-4">
            <i class="fas fa-plus me-2"></i> Tambah Logbook
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="15%">Tanggal</th>
                            <th class="py-3" width="35%">Kegiatan</th>
                            <th class="py-3" width="20%">Bukti Dokumentasi</th>
                            <th class="py-3" width="15%">Status</th>
                            <th class="px-4 py-3" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                <h5>Belum ada logbook.</h5>
                                <p>Silakan tambah logbook harian pertama Anda.</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($logbooks as $log): ?>
                                <?php
                                    // Format status badge
                                    $badgeClass = 'bg-warning text-dark';
                                    $statusText = 'Pending';
                                    if ($log['status'] == 'disetujui') {
                                        $badgeClass = 'bg-success';
                                        $statusText = 'Disetujui';
                                    } elseif ($log['status'] == 'ditolak') {
                                        $badgeClass = 'bg-danger';
                                        $statusText = 'Ditolak';
                                    } elseif ($log['status'] == 'revisi') {
                                        $badgeClass = 'bg-danger';
                                        $statusText = 'Perlu Revisi';
                                    }
                                ?>
                            <tr>
                                <td class="px-4 py-3 fw-medium">
                                    <?= date('d M Y', strtotime($log['tanggal'])) ?>
                                </td>
                                <td class="py-3">
                                    <div class="text-truncate" style="max-width: 250px;" title="<?= esc($log['kegiatan']) ?>">
                                        <?= esc($log['kegiatan']) ?>
                                    </div>
                                    <?php if(!empty($log['catatan_pembimbing'])): ?>
                                        <div class="small text-danger mt-1">
                                            <strong>Catatan:</strong> <?= esc($log['catatan_pembimbing']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-external-link-alt"></i> Buka GDrive
                                    </a>
                                </td>
                                <td class="py-3">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= $statusText ?></span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if(in_array($log['status'], ['pending', 'revisi', 'ditolak'])): ?>
                                        <a href="<?= base_url('/logbook/edit/' . $log['id']) ?>" class="btn btn-sm btn-light text-primary border">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= base_url('/logbook/delete/' . $log['id']) ?>" class="btn btn-sm btn-light text-danger border ms-1" onclick="return confirm('Apakah Anda yakin ingin menghapus logbook ini? Tindakan ini tidak dapat dibatalkan.');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-lock"></i> Terkunci</span>
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
