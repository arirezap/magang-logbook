<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Data Pengguna</h2>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Kelola data akun Taruna dan Dosen Pembimbing yang terdaftar.</p>
        </div>
        <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
        <div>
            <a href="<?= base_url('/users/create') ?>" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-semibold rounded-3 shadow-sm border-0 bg-primary-custom">
                <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Alert Notifications -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert-premium alert-success-premium mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
            <button type="button" class="btn-close shadow-none border-0" data-bs-dismiss="alert" aria-label="Close" style="background: none; font-size: 1.15rem; color: inherit;">&times;</button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert-premium mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
            <button type="button" class="btn-close shadow-none border-0" data-bs-dismiss="alert" aria-label="Close" style="background: none; font-size: 1.15rem; color: inherit;">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Filter Search Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="card-title fw-bold text-muted mb-3"><i class="bi bi-funnel"></i> Filter Pencarian</h6>
            <form method="get" action="<?= base_url('/users') ?>" class="row g-3">
                <div class="col-12 <?= in_array($userRole, ['superadmin', 'direktur', 'wadir', 'kabag']) ? 'col-md-4' : 'col-md-6' ?>">
                    <label for="nama" class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Pencarian</label>
                    <input type="text" class="form-control form-control-custom" id="nama" name="nama" placeholder="Pencarian..." value="<?= esc($filterNama ?? '') ?>">
                </div>

                <?php if (in_array($userRole, ['superadmin', 'direktur', 'wadir', 'kabag'])): ?>
                <div class="col-12 col-md-4">
                    <label for="prodi" class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Program Studi</label>
                    <select class="form-select form-select-custom" id="prodi" name="prodi">
                        <option value="">-- Semua Prodi --</option>
                        <?php foreach ($prodiList as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($filterProdi == $p['id']) ? 'selected' : '' ?>><?= esc($p['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-12 <?= in_array($userRole, ['superadmin', 'direktur', 'wadir', 'kabag']) ? 'col-md-4' : 'col-md-6' ?>">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Status / Peran</label>
                    <select class="form-select form-select-custom" id="role" name="role">
                        <option value="">-- Semua Peran --</option>
                        <option value="taruna" <?= ($filterRole == 'taruna') ? 'selected' : '' ?>>Taruna</option>
                        <option value="pembimbing" <?= ($filterRole == 'pembimbing') ? 'selected' : '' ?>>Dosen Pembimbing</option>
                        <option value="admin_prodi" <?= ($filterRole == 'admin_prodi') ? 'selected' : '' ?>>Admin Prodi</option>
                        <option value="kaprodi" <?= ($filterRole == 'kaprodi') ? 'selected' : '' ?>>Kaprodi</option>
                        <option value="direktur" <?= ($filterRole == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="wadir" <?= ($filterRole == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                        <option value="kabag" <?= ($filterRole == 'kabag') ? 'selected' : '' ?>>Kepala Bagian</option>
                        <option value="superadmin" <?= ($filterRole == 'superadmin') ? 'selected' : '' ?>>Superadmin</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('/users') ?>" class="btn btn-light border px-4 rounded-3 fw-semibold text-muted">
                        Reset
                    </a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold border-0 bg-primary-custom">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <?php
                        $currentSort = request()->getGet('sort') ?? 'nama';
                        $currentOrder = strtolower(request()->getGet('order') ?? 'asc');
                        $nextOrder = ($currentOrder == 'asc') ? 'desc' : 'asc';
                        
                        $buildSortUrl = function($field) use ($currentSort, $currentOrder, $nextOrder) {
                            $params = $_GET;
                            $params['sort'] = $field;
                            $params['order'] = ($currentSort == $field) ? $nextOrder : 'asc';
                            return current_url() . '?' . http_build_query($params);
                        };
                        
                        $sortIcon = function($field) use ($currentSort, $currentOrder) {
                            if ($currentSort == $field) {
                                return $currentOrder == 'asc' ? '<i class="bi bi-sort-alpha-down"></i>' : '<i class="bi bi-sort-alpha-down-alt"></i>';
                            }
                            return '<i class="bi bi-arrow-down-up text-muted opacity-25"></i>';
                        };
                    ?>
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="px-4 py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">
                                <a href="<?= $buildSortUrl('nama') ?>" class="text-decoration-none text-secondary d-flex align-items-center gap-2">Pengguna <?= $sortIcon('nama') ?></a>
                            </th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">
                                <a href="<?= $buildSortUrl('role') ?>" class="text-decoration-none text-secondary d-flex align-items-center gap-2">Peran <?= $sortIcon('role') ?></a>
                            </th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">
                                <a href="<?= $buildSortUrl('prodi') ?>" class="text-decoration-none text-secondary d-flex align-items-center gap-2">Prodi & Kelas <?= $sortIcon('prodi') ?></a>
                            </th>
                            <th class="py-3" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase;">
                                <a href="<?= $buildSortUrl('pembimbing') ?>" class="text-decoration-none text-secondary d-flex align-items-center gap-2">Pembimbing <?= $sortIcon('pembimbing') ?></a>
                            </th>
                            <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
                                <th class="px-4 py-3 text-center" style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; width: 220px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="<?= in_array($userRole, ['superadmin', 'admin_prodi']) ? 5 : 4 ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-people-fill fs-1 text-light mb-3 d-block"></i>
                                <span>Tidak ada data pengguna yang cocok dengan kriteria pencarian.</span>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- Initial Circle -->
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-primary shadow-sm" 
                                             style="width: 38px; height: 38px; font-size: 0.95rem; border: 1px solid rgba(0,0,0,0.05);">
                                            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;"><?= esc($user['nama']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.8rem;"><?= esc($user['nomor_induk']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1 align-items-start">
                                        <?php if(strtolower($user['role']) == 'taruna'): ?>
                                            <span class="badge-taruna text-nowrap">Taruna</span>
                                        <?php elseif(strtolower($user['role']) == 'pembimbing'): ?>
                                            <span class="badge-pembimbing text-nowrap">Dosen Pembimbing</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-3 px-3 py-2 text-nowrap" style="font-size: 0.82rem; font-weight: 600;">
                                                <?= esc(ucwords(str_replace('_', ' ', $user['role']))) ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($user['role_kedua'])): ?>
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-3 px-3 py-1 text-nowrap" style="font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(13, 110, 253, 0.1);">
                                                <i class="bi bi-briefcase me-1"></i><?= esc(ucwords(str_replace('_', ' ', $user['role_kedua']))) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-secondary" style="font-size: 0.9rem;"><?= esc($user['nama_prodi']) ?></div>
                                    <?php if(!empty($user['kelas'])): ?>
                                        <div class="fw-bold text-dark mt-0.5" style="font-size: 0.85rem;">Kelas <?= esc($user['kelas']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-secondary" style="font-size: 0.9rem;">
                                    <?= !empty($user['nama_pembimbing']) ? esc($user['nama_pembimbing']) : '-' ?>
                                </td>
                                <?php if(in_array($userRole, ['superadmin', 'admin_prodi'])): ?>
                                <td class="px-4 py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= base_url('/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-action-edit d-flex align-items-center gap-1 py-1.5 px-3 border-0">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <?php if (strtolower($user['role']) !== 'superadmin'): ?>
                                            <a href="<?= base_url('/users/delete/' . $user['id']) ?>" 
                                               class="btn btn-sm btn-action-delete d-flex align-items-center gap-1 py-1.5 px-3 border-0" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data logbook terkait akan ikut terhapus secara permanen!');">
                                                <i class="bi bi-trash-fill"></i> Hapus
                                            </a>
                                        <?php else: ?>
                                            <button disabled class="btn btn-sm btn-secondary d-flex align-items-center gap-1 py-1.5 px-3 border-0 opacity-50" title="Superadmin tidak dapat dihapus">
                                                <i class="bi bi-shield-lock-fill"></i> Hapus
                                            </button>
                                        <?php endif; ?>
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
        
        <!-- Pagination Links -->
        <div class="mt-4 mb-3 d-flex justify-content-center">
            <?= $pager->links('users', 'custom_pagination') ?>
        </div>
    </div>
<?= $this->endSection() ?>
