<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="mb-4">
        <a href="<?= base_url('/logbook') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
        </a>
        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Edit Logbook Harian</h3>
        <p class="text-muted m-0" style="font-size: 0.9rem;">Perbarui data logbook Anda. Status laporan akan kembali menjadi &quot;Pending&quot; setelah disimpan untuk divalidasi ulang.</p>
    </div>

    <!-- Validation Errors -->
    <?php if(session()->getFlashdata('validation')): ?>
        <div class="alert-premium mb-4 flex-column align-items-start gap-2">
            <div class="d-flex align-items-center gap-2 fw-bold text-danger mb-1" style="font-size: 0.95rem;">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>Terdapat Kesalahan Pengisian Form:</span>
            </div>
            <ul class="mb-0 ps-3 text-danger-emphasis small">
                <?php foreach(session()->getFlashdata('validation') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Revision Comment Alert -->
    <?php if(!empty($logbook['catatan_pembimbing'])): ?>
        <div class="alert-premium alert-warning-premium mb-4 d-flex align-items-start gap-2 border-warning border-start border-4">
            <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-0.5"></i>
            <div>
                <strong class="text-dark">Catatan Revisi dari Pembimbing:</strong>
                <div class="mt-1 small text-dark-emphasis"><?= esc($logbook['catatan_pembimbing']) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Layout Grid -->
    <div class="row g-4">
        <!-- Form Side -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= base_url('/logbook/update/' . $logbook['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <!-- Tanggal Kegiatan -->
                        <div class="mb-4">
                            <label for="tanggal" class="form-label-custom">Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" value="<?= old('tanggal') ?? substr($logbook['tanggal'], 0, 10) ?>" max="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <!-- Deskripsi Kegiatan -->
                        <div class="mb-4">
                            <label for="kegiatan" class="form-label-custom">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-custom" id="kegiatan" name="kegiatan" rows="6" placeholder="Ceritakan kegiatan magang Anda hari ini..." required><?= old('kegiatan') ?? esc($logbook['kegiatan']) ?></textarea>
                        </div>

                        <!-- Link Dokumentasi -->
                        <div class="mb-5">
                            <label for="dokumentasi" class="form-label-custom">Link Dokumentasi (Google Drive) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0" style="border: 1.5px solid #e2e8f0; border-radius: 10px 0 0 10px;"><i class="bi bi-google-drive"></i></span>
                                <input type="url" class="form-control form-control-custom border-start-0 ps-2" id="dokumentasi" name="dokumentasi" placeholder="https://drive.google.com/..." value="<?= old('dokumentasi') ?? esc($logbook['dokumentasi']) ?>" required style="border-radius: 0 10px 10px 0 !important;">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            <a href="<?= base_url('/logbook') ?>" class="btn btn-light px-4 py-2 border rounded-3 fw-semibold text-muted">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 fw-semibold rounded-3 shadow-sm border-0 bg-primary-custom">
                                <i class="bi bi-save"></i> Perbarui Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Info Tips Side -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white border-start border-4 border-warning h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="letter-spacing: -0.3px;">
                        <i class="bi bi-lightbulb-fill text-warning"></i> Tips Revisi
                    </h5>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8; list-style-type: square;">
                        <li class="mb-2">Perhatikan baik-baik catatan perbaikan dari dosen pembimbing Anda di atas sebelum mengubah isi laporan.</li>
                        <li class="mb-2">Perbaiki poin-poin yang diminta, lalu periksa kembali apakah bukti link GDrive sudah benar-benar dapat diakses umum.</li>
                        <li>Setelah menekan tombol simpan, laporan Anda akan berganti status menjadi "Pending" (Menunggu Validasi) kembali agar dosen Anda dapat memeriksanya ulang.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
