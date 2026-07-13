<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 shadow-sm">
            <i class="bi bi-briefcase-fill fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Input Data Taruna Magang</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Atur penempatan tempat magang, pembimbing, dan periode taruna.</p>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="submit" form="formBatchMigrate" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold shadow-sm hover-lift d-none" id="btnBatchMigrate" onclick="return confirm('Yakin ingin memigrasi taruna terpilih ke Magang Periode 2? (Tempat magang dan Dosen pembimbing akan disalin otomatis)')">
            <i class="bi bi-arrow-right-circle-fill me-1"></i> Migrasi ke Periode 2
        </button>
        <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary-custom rounded-pill px-4 py-2 fw-semibold shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#modalTambahPenugasan">
            <i class="bi bi-plus-lg me-1"></i> Tambah Manual
        </button>
    </div>
</div>

<!-- Flash Messages -->
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success-premium alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger-premium alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="<?= base_url('/input-data-taruna') ?>" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">PENCARIAN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="nama" class="form-control border-secondary-subtle border-start-0 shadow-none" placeholder="Pencarian..." value="<?= esc($filterNama ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">TAHUN AJARAN</label>
                    <select name="tahun_ajaran" class="form-select border-secondary-subtle shadow-none">
                        <option value="">-- Semua --</option>
                        <?php foreach($tahunList as $th): ?>
                            <option value="<?= esc($th['tahun_ajaran']) ?>" <?= $filterTahun === $th['tahun_ajaran'] ? 'selected' : '' ?>><?= esc($th['tahun_ajaran']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">PERIODE</label>
                    <select name="periode" class="form-select border-secondary-subtle shadow-none">
                        <option value="">-- Semua --</option>
                        <option value="1" <?= $filterPeriode == '1' ? 'selected' : '' ?>>Magang 1</option>
                        <option value="2" <?= $filterPeriode == '2' ? 'selected' : '' ?>>Magang 2</option>
                    </select>
                </div>
                <?php if(in_array($userRole, ['direktur', 'wadir', 'kabag', 'superadmin'])): ?>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">PROGRAM STUDI</label>
                    <select name="prodi_id" class="form-select border-secondary-subtle shadow-none">
                        <option value="">-- Semua Prodi --</option>
                        <?php foreach($prodiList as $pd): ?>
                            <option value="<?= esc($pd['id']) ?>" <?= $filterProdi == $pd['id'] ? 'selected' : '' ?>><?= esc($pd['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom w-100 rounded-pill fw-semibold shadow-sm"><i class="bi bi-filter me-1"></i> Filter</button>
                    <?php if(!empty($filterTahun) || !empty($filterPeriode) || !empty($filterNama) || !empty($filterProdi)): ?>
                        <a href="<?= base_url('/input-data-taruna') ?>" class="btn btn-outline-danger rounded-pill fw-semibold shadow-sm px-3"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <?php if(empty($penugasan)): ?>
            <div class="text-center py-5 px-4">
                <div class="mb-3" style="font-size: 4rem; opacity: 0.15;">🏢</div>
                <h4 class="fw-bold text-muted">Belum Ada Data Penugasan</h4>
                <p class="text-muted mb-0">Silakan klik tombol "Tambah Penugasan" untuk mulai menugaskan taruna.</p>
            </div>
        <?php else: ?>
            <form id="formBatchMigrate" method="POST" action="<?= base_url('/input-data-taruna/batch-migrate') ?>">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 2px solid #e8eeff;">
                        <tr>
                            <th class="px-4 py-3" style="width: 50px;">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">TARUNA</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">PERIODE & TAHUN AJARAN</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">TEMPAT MAGANG</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">PEMBIMBING</th>
                            <th class="px-4 py-3 fw-bold text-muted text-center" style="font-size: 0.8rem; letter-spacing: 0.5px;">STATUS & AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($penugasan as $p): ?>
                            <tr class="border-bottom border-light hover-shadow-sm transition-all <?= empty($p['penugasan_id']) ? '' : ($p['status_aktif'] ? '' : 'bg-light') ?>">
                                <td class="px-4 py-3">
                                    <?php if(!empty($p['penugasan_id']) && $p['status_aktif']): ?>
                                        <input class="form-check-input checkItem" type="checkbox" name="taruna_ids[]" value="<?= esc($p['taruna_id']) ?>">
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark" style="font-size:0.95rem;"><?= esc($p['nama_taruna']) ?></div>
                                    <div class="text-muted" style="font-size:0.8rem;"><i class="bi bi-person-vcard me-1"></i><?= esc($p['nomor_induk']) ?></div>
                                </td>
                                <td class="py-3">
                                    <?php if(!empty($p['penugasan_id'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                                Magang <?= esc($p['periode']) ?>
                                            </span>
                                            <span class="text-dark fw-medium" style="font-size: 0.9rem;"><?= esc($p['tahun_ajaran']) ?></span>
                                        </div>
                                        <?php if(!empty($p['tanggal_mulai']) && !empty($p['tanggal_selesai'])): ?>
                                        <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                            <?= date('d M Y', strtotime($p['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($p['tanggal_selesai'])) ?>
                                        </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic" style="font-size: 0.85rem;">Belum Ditugaskan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if(!empty($p['penugasan_id'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light text-primary p-2 rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </div>
                                            <span class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= esc($p['tempat_magang']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <div class="fw-medium text-dark" style="font-size: 0.9rem;">
                                        <?= !empty($p['penugasan_id']) ? esc($p['nama_pembimbing'] ?? 'Belum Ditentukan') : '<span class="text-muted">-</span>' ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if(empty($p['penugasan_id'])): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-3 py-1"><i class="bi bi-exclamation-circle me-1"></i> Kosong</span>
                                    <?php elseif($p['status_aktif']): ?>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditPenugasan<?= $p['penugasan_id'] ?>" title="Edit Dosen Pembimbing & Tempat">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><i class="bi bi-clock-history me-1"></i> Selesai/Riwayat</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Penugasan -->
<div class="modal fade" id="modalTambahPenugasan" tabindex="-1" aria-labelledby="modalTambahPenugasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahPenugasanLabel">Tambah Penugasan Magang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/input-data-taruna/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Taruna <span class="text-danger">*</span></label>
                        <select name="taruna_id" class="form-select shadow-none border-secondary-subtle" required>
                            <option value="">-- Pilih Taruna --</option>
                            <?php foreach($tarunaList as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= esc($t['nama']) ?> (<?= esc($t['nomor_induk']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="tahun_ajaran" class="form-select shadow-none border-secondary-subtle" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach($generatedTahunList as $thn): ?>
                                    <option value="<?= esc($thn) ?>" <?= ($thn == (date('Y').'/'.(date('Y')+1))) ? 'selected' : '' ?>><?= esc($thn) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Periode <span class="text-danger">*</span></label>
                            <select name="periode" class="form-select shadow-none border-secondary-subtle" required>
                                <option value="1">Magang 1</option>
                                <option value="2">Magang 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tempat Magang <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_magang" class="form-control shadow-none border-secondary-subtle" placeholder="Nama instansi/perusahaan tempat magang" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Dosen Pembimbing <span class="text-danger">*</span></label>
                        <select name="pembimbing_id" class="form-select shadow-none border-secondary-subtle" required>
                            <option value="">-- Pilih Dosen Pembimbing --</option>
                            <?php foreach($pembimbingList as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tgl. Mulai <span class="text-muted">(Opsional)</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control shadow-none border-secondary-subtle">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tgl. Selesai <span class="text-muted">(Opsional)</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control shadow-none border-secondary-subtle">
                        </div>
                    </div>
                    <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Penugasan ini akan otomatis berstatus <strong>Aktif</strong> dan menonaktifkan penugasan sebelumnya untuk taruna yang bersangkutan.</div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold shadow-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4 fw-semibold shadow-sm hover-lift">
                        <i class="bi bi-save-fill me-1"></i> Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalImportExcelLabel">Import Taruna Magang (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/input-data-taruna/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center p-3 mb-4">
                        <i class="bi bi-info-circle-fill me-3 fs-3 text-info"></i>
                        <div style="font-size: 0.85rem;">
                            Sistem secara otomatis mendeteksi <strong>Prodi</strong> (berdasarkan teks: RSTJ, TRO, TO) dan <strong>Dosen Pembimbing</strong> (berdasarkan NIP atau Nama). Taruna yang belum ada otomatis akan dibuatkan akun dengan password default menggunakan NOTAR.
                        </div>
                    </div>
                    
                    <div class="mb-3 text-center">
                        <a href="<?= base_url('/input-data-taruna/template') ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3">
                            <i class="bi bi-download me-1"></i> Download Template Excel
                        </a>
                    </div>
                    <hr>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="tahun_ajaran" class="form-select shadow-none border-secondary-subtle" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach($generatedTahunList as $thn): ?>
                                    <option value="<?= esc($thn) ?>" <?= ($thn == (date('Y').'/'.(date('Y')+1))) ? 'selected' : '' ?>><?= esc($thn) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Periode <span class="text-danger">*</span></label>
                            <select name="periode" class="form-select shadow-none border-secondary-subtle" required>
                                <option value="1">Magang 1</option>
                                <option value="2">Magang 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Upload File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" class="form-control shadow-none border-secondary-subtle" accept=".xls,.xlsx,.csv" required>
                        <div class="form-text mt-1" style="font-size: 0.75rem;">Maks. ukuran file 2MB. Format: .xls, .xlsx, atau .csv.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold shadow-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm hover-lift text-white">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* CSS Tambahan untuk UI UX Pro Max */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
}
.hover-shadow-sm:hover {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
    background-color: #fcfdff;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.alert-success-premium {
    background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
    color: #0f5132;
}
.alert-danger-premium {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
    color: #842029;
}
</style>

<!-- Loop Modal Edit Penugasan -->
<?php if(!empty($penugasan)): ?>
    <?php foreach($penugasan as $p): ?>
        <?php if(!empty($p['penugasan_id'])): ?>
            <div class="modal fade" id="modalEditPenugasan<?= $p['penugasan_id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header border-0 bg-light px-4 py-3">
                            <h5 class="modal-title fw-bold text-dark">Edit Penugasan Magang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= base_url('/input-data-taruna/update/' . $p['penugasan_id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="modal-body px-4 py-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Taruna</label>
                                    <input type="text" class="form-control bg-light text-muted" value="<?= esc($p['nama_taruna']) ?> (<?= esc($p['nomor_induk']) ?>)" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Periode & Tahun Ajaran</label>
                                    <input type="text" class="form-control bg-light text-muted" value="Magang <?= esc($p['periode']) ?> - <?= esc($p['tahun_ajaran']) ?>" readonly>
                                </div>
                                
                                <hr class="my-4 text-muted opacity-25">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Tempat Magang <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_magang" class="form-control shadow-none border-secondary-subtle" value="<?= esc($p['tempat_magang']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Dosen Pembimbing <span class="text-danger">*</span></label>
                                    <select name="pembimbing_id" class="form-select shadow-none border-secondary-subtle" required>
                                        <option value="">-- Pilih Pembimbing --</option>
                                        <?php foreach($pembimbingList as $dsn): ?>
                                            <option value="<?= $dsn['id'] ?>" <?= (($p['pembimbing_id'] ?? '') == $dsn['id']) ? 'selected' : '' ?>>
                                                <?= esc($dsn['nama']) ?> <?= $dsn['nomor_induk'] ? '('.esc($dsn['nomor_induk']).')' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0 bg-light px-4 py-3">
                                <button type="button" class="btn btn-light fw-semibold text-dark px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary-custom px-4 rounded-pill fw-semibold shadow-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkAll = document.getElementById('checkAll');
    const checkItems = document.querySelectorAll('.checkItem');
    const btnBatchMigrate = document.getElementById('btnBatchMigrate');

    function toggleBatchButton() {
        const anyChecked = Array.from(checkItems).some(checkbox => checkbox.checked);
        if (anyChecked) {
            btnBatchMigrate.classList.remove('d-none');
        } else {
            btnBatchMigrate.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBatchButton();
        });
    }

    checkItems.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkItems).every(cb => cb.checked);
            checkAll.checked = allChecked;
            toggleBatchButton();
        });
    });
});
</script>

<?= $this->endSection() ?>
