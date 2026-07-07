<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Daftar Taruna Bimbingan</h3>
            <p class="text-muted">Berikut adalah daftar seluruh Taruna yang berada di bawah bimbingan Anda.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <?php if(empty($tarunas)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-muted">Belum Ada Taruna</h5>
                    <p class="text-muted mb-0">Saat ini belum ada taruna yang ditugaskan kepada Anda sebagai pembimbing.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Taruna</th>
                                <th>NOTAR</th>
                                <th>Program Studi</th>
                                <th>Jenjang / Kelas</th>
                                <th>Tempat Magang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($tarunas as $taruna): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-sm me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 35px; height: 35px; font-weight: bold;">
                                            <?= strtoupper(substr($taruna['nama'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($taruna['nama']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= esc($taruna['nomor_induk']) ?></span></td>
                                <td><?= esc($taruna['nama_prodi'] ?? '-') ?></td>
                                <td><?= esc($taruna['jenjang'] ?? '-') ?> / <?= esc($taruna['kelas'] ?? '-') ?></td>
                                <td><?= esc($taruna['tempat_magang'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
