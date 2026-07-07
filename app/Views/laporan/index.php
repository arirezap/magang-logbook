<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Laporan Global Logbook Magang</h3>
        <p class="text-muted">Pantau keseluruhan aktivitas logbook harian Taruna secara <em>real-time</em>.</p>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100 rounded-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-white-50">Total Logbook</h6>
                    <h2 class="mb-0 fw-bold"><?= $total ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100 rounded-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-white-50">Disetujui</h6>
                    <h2 class="mb-0 fw-bold"><?= $disetujui ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100 rounded-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-black-50">Menunggu Validasi</h6>
                    <h2 class="mb-0 fw-bold"><?= $pending ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white h-100 rounded-4">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold text-white-50">Revisi / Ditolak</h6>
                    <h2 class="mb-0 fw-bold"><?= $revisi_ditolak ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="card-title fw-bold text-muted mb-3"><i class="fas fa-filter"></i> Filter Pencarian</h6>
            <form method="get" action="<?= base_url('laporan') ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="tanggal" class="form-label text-muted small fw-bold">Tanggal Pelaporan</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= esc($filterTanggal ?? '') ?>">
                </div>
                
                <?php if (in_array($userRole, ['superadmin', 'pejabat'])): ?>
                <div class="col-md-3">
                    <label for="prodi" class="form-label text-muted small fw-bold">Program Studi</label>
                    <select class="form-select" id="prodi" name="prodi">
                        <option value="">-- Semua Prodi --</option>
                        <?php foreach ($prodiList as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($filterProdi == $p['id']) ? 'selected' : '' ?>><?= esc($p['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-3">
                    <label for="kelas" class="form-label text-muted small fw-bold">Kelas</label>
                    <select class="form-select" id="kelas" name="kelas">
                        <option value="">-- Semua Kelas --</option>
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= esc($k['kelas']) ?>" <?= ($filterKelas == $k['kelas']) ? 'selected' : '' ?>><?= esc($k['kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="<?= in_array($userRole, ['superadmin', 'pejabat']) ? 'col-md-3' : 'col-md-6' ?>">
                    <label for="nama" class="form-label text-muted small fw-bold">Nama Taruna</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Cari nama taruna..." value="<?= esc($filterNama ?? '') ?>">
                </div>

                <div class="col-12 d-flex justify-content-end mt-4">
                    <a href="<?= base_url('laporan') ?>" class="btn btn-light border px-4 me-2"><i class="fas fa-undo"></i> Reset</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-search"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="20%">Taruna</th>
                            <th class="py-3" width="15%">Tanggal</th>
                            <th class="py-3" width="30%">Cuplikan Kegiatan</th>
                            <th class="py-3" width="15%">Pembimbing</th>
                            <th class="px-4 py-3" width="15%">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logbooks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                <h5>Belum ada laporan yang masuk.</h5>
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
                                    <div>
                                        <?= esc($log['kegiatan']) ?>
                                    </div>
                                    <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="small text-primary mt-1 d-block text-decoration-none">
                                        <i class="fas fa-link"></i> Lihat Dokumentasi
                                    </a>
                                </td>
                                <td class="py-3">
                                    <div class="small text-muted">
                                        <?= !empty($log['nama_pembimbing']) ? esc($log['nama_pembimbing']) : 'Belum Ditentukan' ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-2"><?= $statusText ?></span>
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
