<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="mb-4">
        <a href="<?= base_url('/logbook') ?>" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
        </a>
        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Tambah Logbook Harian</h3>
        <p class="text-muted m-0" style="font-size: 0.9rem;">Isi form di bawah ini dengan lengkap dan teliti untuk melaporkan kegiatan magang Anda.</p>
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

    <!-- Main Layout Grid -->
    <div class="row g-4">
        <!-- Form Side -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form action="<?= base_url('/logbook/store') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <!-- Tanggal Kegiatan -->
                        <div class="mb-4">
                            <label for="tanggal" class="form-label-custom">Tanggal Kegiatan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-custom" id="tanggal" name="tanggal" value="<?= old('tanggal') ?? date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <!-- Deskripsi Kegiatan -->
                        <div class="mb-4">
                            <label for="kegiatan" class="form-label-custom">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-custom" id="kegiatan" name="kegiatan" rows="6" placeholder="Contoh: Mengikuti apel pagi, mendata kendaraan uji KIR, lalu menyusun laporan arus lalu lintas harian di persimpangan jalan..." required><?= old('kegiatan') ?></textarea>
                            <div class="form-text-custom">Ceritakan kegiatan magang Anda hari ini secara detail dan terstruktur (minimal 10 karakter).</div>
                        </div>

                        <!-- Upload Dokumentasi -->
                        <div class="mb-5">
                            <label for="dokumentasi" class="form-label-custom">Upload Bukti Dokumentasi <span class="text-danger">*</span></label>
                            <input type="file" class="form-control form-control-custom" id="dokumentasi" name="dokumentasi" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text-custom text-primary d-flex align-items-start gap-2 mt-2">
                                <i class="bi bi-info-circle-fill flex-shrink-0 mt-0.5"></i>
                                <span>Format yang diizinkan: <strong>JPG, JPEG, PNG, PDF</strong>. Ukuran maksimal: <strong>5MB</strong>.</span>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                            <a href="<?= base_url('/logbook') ?>" class="btn btn-light px-4 py-2 border rounded-3 fw-semibold text-muted">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 fw-semibold rounded-3 shadow-sm border-0 bg-primary-custom">
                                <i class="bi bi-send-fill"></i> Kirim Laporan
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
                        <i class="bi bi-lightbulb-fill text-warning"></i> Tips Pengisian
                    </h5>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8; list-style-type: square;">
                        <li class="mb-2">Isi logbook sesegera mungkin di hari yang sama dengan kegiatan untuk menjaga akurasi laporan Anda.</li>
                        <li class="mb-2">Deskripsikan peran aktif Anda secara detail, sertakan subjek pekerjaan dan hasil yang dicapai.</li>
                        <li class="mb-2">Pastikan foto bukti dokumentasi terunggah dengan baik di folder Google Drive dan tautan tidak terkunci.</li>
                        <li>Dosen pembimbing sewaktu-waktu dapat menolak atau meminta revisi jika laporan kurang jelas. Pantau status logbook Anda berkala.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
