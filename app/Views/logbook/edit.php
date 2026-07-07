<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <a href="<?= base_url('/logbook') ?>" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <h3 class="fw-bold text-dark mt-2 m-0">Edit Logbook Harian</h3>
        <p class="text-muted">Perbarui data logbook Anda. Status laporan akan kembali menjadi "Pending" setelah disimpan.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <?php if(session()->getFlashdata('validation')): ?>
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0">
                                <?php foreach(session()->getFlashdata('validation') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($logbook['catatan_pembimbing'])): ?>
                        <div class="alert alert-warning rounded-3 mb-4 border-warning border-start border-4">
                            <strong><i class="fas fa-exclamation-triangle"></i> Catatan Revisi dari Pembimbing:</strong><br>
                            <?= esc($logbook['catatan_pembimbing']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/logbook/update/' . $logbook['id']) ?>" method="POST">
                        <div class="mb-4">
                            <label for="tanggal" class="form-label fw-bold text-dark">Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <!-- format datetime ke Y-m-d agar bisa terbaca oleh input type date -->
                            <input type="date" class="form-control form-control-lg bg-light" id="tanggal" name="tanggal" value="<?= old('tanggal') ?? substr($logbook['tanggal'], 0, 10) ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="kegiatan" class="form-label fw-bold text-dark">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light" id="kegiatan" name="kegiatan" rows="5" placeholder="Ceritakan kegiatan magang Anda hari ini..." required><?= old('kegiatan') ?? esc($logbook['kegiatan']) ?></textarea>
                        </div>

                        <div class="mb-5">
                            <label for="dokumentasi" class="form-label fw-bold text-dark">Link Dokumentasi (Google Drive) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fab fa-google-drive"></i></span>
                                <input type="url" class="form-control form-control-lg border-start-0 ps-0 bg-light" id="dokumentasi" name="dokumentasi" placeholder="https://drive.google.com/..." value="<?= old('dokumentasi') ?? esc($logbook['dokumentasi']) ?>" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('/logbook') ?>" class="btn btn-light px-4 py-2 fw-bold text-muted me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary-custom px-5 py-2 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Perbarui Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm rounded-4 bg-light border-start border-4 border-warning">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> Tips Revisi</h5>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8;">
                        <li>Perhatikan catatan revisi dari dosen pembimbing Anda sebelum menyimpan.</li>
                        <li>Pastikan link GDrive bisa diakses publik (tidak di-lock).</li>
                        <li>Status logbook akan otomatis berubah kembali menjadi "Pending" agar bisa divalidasi ulang oleh dosen.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
