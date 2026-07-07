<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Data Pengguna</h3>
            <p class="text-muted">Kelola akun Taruna dan Dosen Pembimbing.</p>
        </div>
        <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
        <div>
            <a href="<?= base_url('/users/create') ?>" class="btn btn-primary-custom px-4 fw-bold shadow-sm">
                <i class="fas fa-user-plus me-2"></i> Tambah Pengguna
            </a>
        </div>
        <?php endif; ?>
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
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="py-3">Nomor Induk</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Prodi & Kelas</th>
                            <th class="py-3">Pembimbing</th>
                            <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data pengguna.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="px-4 py-3 fw-bold text-dark"><?= esc($user['nama']) ?></td>
                                <td class="py-3 text-muted"><?= esc($user['nomor_induk']) ?></td>
                                <td class="py-3">
                                    <?php if($user['role'] == 'taruna'): ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">Taruna</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">Dosen</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <div class="small text-muted"><?= esc($user['nama_prodi']) ?></div>
                                    <?php if(!empty($user['kelas'])): ?>
                                        <div class="fw-bold">Kelas <?= esc($user['kelas']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?= !empty($user['nama_pembimbing']) ? esc($user['nama_pembimbing']) : '-' ?>
                                </td>
                                <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= base_url('/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <a href="<?= base_url('/users/delete/' . $user['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua logbooknya juga akan ikut terhapus!');">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
