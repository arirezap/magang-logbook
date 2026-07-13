<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 shadow-sm">
            <i class="bi bi-person-vcard-fill fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Input Data Dosen Pembimbing</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Kelola data dosen pembimbing magang dan penempatan prodinya.</p>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary-custom rounded-pill px-4 py-2 fw-semibold shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#modalTambahDosen">
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

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="<?= base_url('/input-data-dosen') ?>" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">PENCARIAN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="nama" class="form-control border-secondary-subtle border-start-0 shadow-none" placeholder="Pencarian..." value="<?= esc($filterNama ?? '') ?>">
                    </div>
                </div>
                <?php if(in_array($userRole, ['direktur', 'wadir', 'kabag', 'superadmin'])): ?>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem; letter-spacing: 0.5px;">PROGRAM STUDI</label>
                    <select name="prodi_id" class="form-select border-secondary-subtle shadow-none">
                        <option value="">-- Semua Program Studi --</option>
                        <?php foreach($prodiList as $pd): ?>
                            <option value="<?= esc($pd['id']) ?>" <?= $filterProdi == $pd['id'] ? 'selected' : '' ?>><?= esc($pd['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom w-100 rounded-pill fw-semibold shadow-sm"><i class="bi bi-filter me-1"></i> Filter</button>
                    <?php if(!empty($filterProdi) || !empty($filterNama)): ?>
                        <a href="<?= base_url('/input-data-dosen') ?>" class="btn btn-outline-danger rounded-pill fw-semibold shadow-sm px-3"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <?php if(empty($dosenList)): ?>
            <div class="text-center py-5 px-4">
                <div class="mb-3" style="font-size: 4rem; opacity: 0.15;">👨‍🏫</div>
                <h4 class="fw-bold text-muted">Belum Ada Data Dosen</h4>
                <p class="text-muted mb-0">Silakan klik tombol "Tambah Manual" atau "Import Excel" untuk menambahkan data dosen.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%); border-bottom: 2px solid #e8eeff;">
                        <tr>
                            <th class="px-4 py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">DOSEN PEMBIMBING</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">PROGRAM STUDI</th>
                            <th class="py-3 fw-bold text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px;">TANGGAL DITAMBAHKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dosenList as $d): ?>
                            <tr class="border-bottom border-light hover-shadow-sm transition-all">
                                <td class="px-4 py-3">
                                    <div class="fw-bold text-dark" style="font-size:0.95rem;"><?= esc($d['nama']) ?></div>
                                    <div class="text-muted" style="font-size:0.8rem;"><i class="bi bi-person-vcard me-1"></i>NIP: <?= esc($d['nomor_induk']) ?></div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light text-primary p-2 rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <span class="fw-semibold text-dark" style="font-size: 0.9rem;"><?= esc($d['nama_prodi'] ?? 'Umum / Semua Prodi') ?></span>
                                    </div>
                                </td>
                                <td class="py-3 text-muted" style="font-size: 0.85rem;">
                                    <?= date('d M Y, H:i', strtotime($d['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Dosen Manual -->
<div class="modal fade" id="modalTambahDosen" tabindex="-1" aria-labelledby="modalTambahDosenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-light px-4 py-3">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahDosenLabel">Tambah Dosen Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/input-data-dosen/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Nama Lengkap (beserta Gelar) <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control shadow-none border-secondary-subtle" placeholder="Contoh: Dr. Budi Santoso, M.T." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">NIP / Nomor Induk <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_induk" class="form-control shadow-none border-secondary-subtle" placeholder="Masukkan NIP" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.85rem;">Program Studi <span class="text-muted">(Opsional)</span></label>
                        <select name="prodi_id" class="form-select shadow-none border-secondary-subtle">
                            <option value="">-- Umum / Semua Prodi --</option>
                            <?php foreach($prodiList as $pd): ?>
                                <option value="<?= $pd['id'] ?>"><?= esc($pd['nama_prodi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Akun baru akan dibuat dengan <strong>Password Default sama dengan NIP</strong>.</div>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold shadow-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom rounded-pill px-4 fw-semibold shadow-sm hover-lift">
                        <i class="bi bi-save-fill me-1"></i> Simpan Data
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
                <h5 class="modal-title fw-bold text-dark" id="modalImportExcelLabel">Import Dosen Pembimbing (Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/input-data-dosen/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body px-4 py-4">
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center p-3 mb-4">
                        <i class="bi bi-info-circle-fill me-3 fs-3 text-info"></i>
                        <div style="font-size: 0.85rem;">
                            Sistem secara otomatis mendeteksi <strong>Prodi</strong> berdasarkan teks (RSTJ, TRO, TO). Password default otomatis disamakan dengan NIP.
                        </div>
                    </div>
                    
                    <div class="mb-3 text-center">
                        <a href="<?= base_url('/input-data-dosen/template') ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold px-3">
                            <i class="bi bi-download me-1"></i> Download Template Excel
                        </a>
                    </div>
                    <hr>
                    
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

<?= $this->endSection() ?>
