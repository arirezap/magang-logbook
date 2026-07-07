<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Validasi Logbook Taruna</h3>
        <p class="text-muted">Tinjau dan berikan penilaian pada laporan harian anak bimbingan Anda.</p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="20%">Taruna</th>
                            <th class="py-3" width="15%">Tanggal</th>
                            <th class="py-3" width="30%">Cuplikan Kegiatan</th>
                            <th class="py-3" width="20%">Status Validasi</th>
                            <th class="px-4 py-3" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <h5>Belum ada laporan masuk.</h5>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($logbooks as $log): ?>
                                <?php
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
                                        $statusText = 'Revisi';
                                    }
                                    
                                    // Singkatan Kelas
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
                                    <div class="fw-bold text-dark"><?= esc($log['nama_taruna']) ?></div>
                                    <div class="small text-muted"><?= esc($log['notar_taruna']) ?> &bull; <?= $kelasTampil ?></div>
                                </td>
                                <td class="py-3 fw-medium">
                                    <?= date('d M Y', strtotime($log['tanggal'])) ?>
                                </td>
                                <td class="py-3">
                                    <div class="text-truncate" style="max-width: 200px;" title="<?= esc($log['kegiatan']) ?>">
                                        <?= esc($log['kegiatan']) ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= $statusText ?></span>
                                    <?php if($log['status'] == 'pending'): ?>
                                        <span class="ms-1 small text-danger fw-bold"><i class="fas fa-circle"></i> Baru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <!-- Tombol trigger modal -->
                                    <button type="button" class="btn btn-sm btn-primary-custom rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTinjau<?= $log['id'] ?>">
                                        <i class="fas fa-search me-1"></i> Tinjau
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Tinjau untuk setiap logbook -->
                            <div class="modal fade" id="modalTinjau<?= $log['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $log['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold" id="modalLabel<?= $log['id'] ?>">Tinjau Laporan Harian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <p class="text-muted small mb-1">Nama Taruna</p>
                                                    <div class="fw-bold text-dark"><?= esc($log['nama_taruna']) ?> (<?= esc($log['notar_taruna']) ?>)</div>
                                                </div>
                                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                                    <p class="text-muted small mb-1">Tanggal Kegiatan</p>
                                                    <div class="fw-bold text-primary"><?= date('d F Y', strtotime($log['tanggal'])) ?></div>
                                                </div>
                                            </div>

                                            <div class="bg-light p-3 rounded-3 mb-4 border border-light">
                                                <p class="text-muted small mb-2 fw-bold">Deskripsi Kegiatan:</p>
                                                <p class="mb-0 text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?= esc($log['kegiatan']) ?></p>
                                            </div>

                                            <div class="mb-4">
                                                <p class="text-muted small mb-2 fw-bold">Bukti Dokumentasi:</p>
                                                <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                                                    <i class="fas fa-external-link-alt me-2"></i> Buka Link Google Drive
                                                </a>
                                            </div>

                                            <hr class="text-muted opacity-25 my-4">

                                            <form action="<?= base_url('/validasi/action/' . $log['id']) ?>" method="POST">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-dark">Keputusan Validasi <span class="text-danger">*</span></label>
                                                    <div class="d-flex gap-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="status" id="statusSetuju<?= $log['id'] ?>" value="disetujui" <?= ($log['status'] == 'disetujui') ? 'checked' : '' ?> required>
                                                            <label class="form-check-label text-success fw-bold" for="statusSetuju<?= $log['id'] ?>">
                                                                <i class="fas fa-check-circle"></i> Setujui
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="status" id="statusRevisi<?= $log['id'] ?>" value="revisi" <?= ($log['status'] == 'revisi') ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-warning text-dark fw-bold" for="statusRevisi<?= $log['id'] ?>">
                                                                <i class="fas fa-edit"></i> Revisi
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="status" id="statusTolak<?= $log['id'] ?>" value="ditolak" <?= ($log['status'] == 'ditolak') ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-danger fw-bold" for="statusTolak<?= $log['id'] ?>">
                                                                <i class="fas fa-times-circle"></i> Tolak
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4 mt-3">
                                                    <label for="catatan<?= $log['id'] ?>" class="form-label fw-bold text-dark">Catatan untuk Taruna (Opsional)</label>
                                                    <textarea class="form-control bg-light" id="catatan<?= $log['id'] ?>" name="catatan_pembimbing" rows="3" placeholder="Berikan alasan jika ditolak atau meminta revisi..."><?= esc($log['catatan_pembimbing']) ?></textarea>
                                                </div>

                                                <div class="d-grid mt-2">
                                                    <button type="submit" class="btn btn-primary-custom fw-bold py-2 shadow-sm rounded-3">
                                                        Simpan Validasi
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
