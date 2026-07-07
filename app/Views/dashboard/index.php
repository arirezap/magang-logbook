<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Dashboard</h3>
        <?php
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $tanggalIndo = date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
        ?>
        <span class="text-muted"><?= $tanggalIndo ?></span>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="border-left: 5px solid #0d47a1 !important;">
                <div class="card-body p-4">
                    <h4 class="card-title fw-bold mb-3" style="color: #0d47a1;">Selamat Datang, <?= esc($user['nama']) ?>!</h4>
                    
                    <?php if($user['role'] == 'taruna'): ?>
                        <p class="card-text text-muted" style="font-size: 1.1rem;">Ini adalah halaman utama logbook magang Anda. Pastikan Anda mengisi kegiatan harian secara rutin dan mendapatkan validasi dari pembimbing.</p>
                        <hr class="my-4">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.8rem;">NOTAR</h6>
                                    <h5 class="fw-bold m-0 text-dark"><?= esc($user['nomor_induk']) ?></h5>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Tempat Magang</h6>
                                    <h5 class="fw-bold m-0 text-dark"><?= esc($user['tempat_magang']) ?? '-' ?></h5>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Jenjang / Kelas</h6>
                                    <h5 class="fw-bold m-0 text-dark"><?= esc($user['jenjang'] ?? '-') ?> - <?= esc($user['kelas_lengkap'] ?? '-') ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php elseif($user['role'] == 'pembimbing'): ?>
                        <p class="card-text text-muted" style="font-size: 1.1rem;">Sebagai pembimbing, Anda bertugas untuk memvalidasi dan memberikan arahan terkait kegiatan magang taruna bimbingan Anda.</p>
                    <?php elseif(in_array($user['role'], ['admin_prodi', 'pejabat'])): ?>
                        <p class="card-text text-muted" style="font-size: 1.1rem;">Gunakan menu di sidebar untuk memantau rekapitulasi data dan mengelola pengguna pada sistem ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
