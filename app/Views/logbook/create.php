<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <a href="<?= base_url('/logbook') ?>" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <h3 class="fw-bold text-dark mt-2 m-0">Tambah Logbook Harian</h3>
        <p class="text-muted">Isi form di bawah ini dengan lengkap dan teliti.</p>
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

                    <form action="<?= base_url('/logbook/store') ?>" method="POST">
                        <div class="mb-4">
                            <label for="tanggal" class="form-label fw-bold text-dark">Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg bg-light" id="tanggal" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="kegiatan" class="form-label fw-bold text-dark">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-light" id="kegiatan" name="kegiatan" rows="5" placeholder="Contoh: Mengikuti apel pagi, mendata kendaraan uji KIR, lalu menyusun laporan arus lalu lintas..." required><?= old('kegiatan') ?></textarea>
                            <div class="form-text">Ceritakan kegiatan magang Anda hari ini secara detail (minimal 10 karakter).</div>
                        </div>

                        <div class="mb-5">
                            <label for="dokumentasi" class="form-label fw-bold text-dark">Link Dokumentasi (Google Drive) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fab fa-google-drive"></i></span>
                                <input type="url" class="form-control form-control-lg border-start-0 ps-0 bg-light" id="dokumentasi" name="dokumentasi" placeholder="https://drive.google.com/..." value="<?= old('dokumentasi') ?>" required>
                            </div>
                            <div class="form-text text-primary mt-2">
                                <i class="fas fa-info-circle"></i> Pastikan link Google Drive sudah di-setting <strong>"Anyone with the link / Siapa saja yang memiliki link"</strong>.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('/logbook') ?>" class="btn btn-light px-4 py-2 fw-bold text-muted me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary-custom px-5 py-2 fw-bold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm rounded-4 bg-light border-start border-4 border-warning">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> Tips Pengisian</h5>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8;">
                        <li>Isi logbook di hari yang sama dengan kegiatan untuk menghindari lupa.</li>
                        <li>Deskripsikan apa yang Anda kerjakan, bukan hanya apa yang Anda lihat.</li>
                        <li>Pastikan foto yang di-upload ke GDrive adalah foto relevan dan tidak buram.</li>
                        <li>Cek kembali status logbook Anda secara berkala, dosen mungkin meminta revisi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
