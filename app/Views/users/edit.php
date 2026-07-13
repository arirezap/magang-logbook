<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Edit Pengguna</h3>
        <p class="text-muted">Perbarui data akun pengguna di bawah ini.</p>
    </div>

    <!-- Validation Errors -->
    <?php if(session()->getFlashdata('validation')): ?>
        <div class="alert-premium mb-4 flex-column align-items-start gap-2">
            <div class="d-flex align-items-center gap-2 fw-bold text-danger mb-1" style="font-size: 0.95rem;">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>Terdapat Kesalahan Pengisian Form:</span>
            </div>
            <ul class="mb-0 ps-3 text-danger-emphasis small">
                <?php foreach(session()->getFlashdata('validation') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4 p-md-5">
            <form action="<?= base_url('/users/update/' . $userEdit['id']) ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- Row 1: Role and Nomor Induk -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label-custom">Pilih Peran (Role) <span class="text-danger">*</span></label>
                        <select class="form-select form-select-custom" name="role" id="roleSelect" required>
                            <option value="taruna" <?= ($userEdit['role'] == 'taruna') ? 'selected' : '' ?>>Taruna</option>
                            <option value="pembimbing" <?= ($userEdit['role'] == 'pembimbing') ? 'selected' : '' ?>>Dosen Pembimbing</option>
                            <option value="admin_prodi" <?= ($userEdit['role'] == 'admin_prodi') ? 'selected' : '' ?>>Admin Prodi</option>
                            <option value="kaprodi" <?= ($userEdit['role'] == 'kaprodi') ? 'selected' : '' ?>>Kaprodi</option>
                            <?php if($userRole == 'superadmin'): ?>
                                <option value="direktur" <?= ($userEdit['role'] == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                                <option value="wadir" <?= ($userEdit['role'] == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                                <option value="kabag" <?= ($userEdit['role'] == 'kabag') ? 'selected' : '' ?>>Kepala Bagian</option>
                                <option value="superadmin" <?= ($userEdit['role'] == 'superadmin') ? 'selected' : '' ?>>Superadmin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label-custom">Nomor Induk (NOTAR/NIP/Username) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" name="nomor_induk" value="<?= esc(old('nomor_induk', $userEdit['nomor_induk'])) ?>" required placeholder="Masukkan NIP, NOTAR, atau nama akun">
                    </div>
                </div>

                <!-- Row 2: Nama and Password -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-custom" name="nama" value="<?= esc(old('nama', $userEdit['nama'])) ?>" required placeholder="Nama Lengkap dengan Gelar">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label-custom">Password Baru (Opsional)</label>
                        <input type="password" class="form-control form-control-custom" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text-custom">Isi kolom ini hanya jika Anda ingin me-reset password pengguna.</div>
                    </div>
                </div>

                <!-- Dynamic Fields: Disembunyikan secara default kecuali role membutuhkan -->
                <div id="dynamicFields" style="display: none;">
                    <hr class="my-4 text-muted opacity-25">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6" id="prodiWrapper">
                            <label class="form-label-custom">Program Studi <span class="text-danger">*</span></label>
                            <?php if($userRole == 'admin_prodi'): ?>
                                <select class="form-select form-select-custom" disabled>
                                    <option>Prodi Anda Sendiri (Otomatis Tersimpan)</option>
                                </select>
                            <?php else: ?>
                                <select class="form-select form-select-custom" name="prodi_id" id="prodiSelect">
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach($prodiList as $prodi): ?>
                                        <option value="<?= $prodi['id'] ?>" <?= ($userEdit['prodi_id'] == $prodi['id']) ? 'selected' : '' ?>><?= esc($prodi['nama_prodi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pembimbing ID dihilangkan dari tabel users -->
                    </div>

                    <div class="row g-4 mb-4" id="tarunaExtraWrapper">
                        <div class="col-12 col-md-6">
                            <label class="form-label-custom">Jenjang</label>
                            <select class="form-select form-select-custom" name="jenjang">
                                <option value="">Pilih Jenjang</option>
                                <option value="D3" <?= ($userEdit['jenjang'] == 'D3') ? 'selected' : '' ?>>D3</option>
                                <option value="D4" <?= ($userEdit['jenjang'] == 'D4') ? 'selected' : '' ?>>D4</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-custom">Kelas</label>
                            <input type="text" class="form-control form-control-custom" name="kelas" value="<?= esc(old('kelas', $userEdit['kelas'])) ?>" placeholder="Misal: A, B">
                        </div>
                    </div>
                </div>

                <!-- Form Footer Actions -->
                <div class="d-flex justify-content-end gap-2 mt-5 flex-wrap">
                    <a href="<?= base_url('/users') ?>" class="btn btn-light px-4 py-2 border rounded-3 fw-semibold text-muted">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 px-5 py-2 fw-semibold rounded-3 shadow-sm border-0 bg-primary-custom">
                        <i class="bi bi-save"></i> Perbarui Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Form Cerdas (Dynamic Show/Hide) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            const dynamicFields = document.getElementById('dynamicFields');
            const prodiWrapper = document.getElementById('prodiWrapper');
            const tarunaExtraWrapper = document.getElementById('tarunaExtraWrapper');
            const prodiSelect = document.getElementById('prodiSelect');

            function updateFields() {
                let role = roleSelect.value;

                if (role === '') {
                    dynamicFields.style.display = 'none';
                    return;
                }

                dynamicFields.style.display = 'block';

                if (role === 'taruna') {
                    prodiWrapper.style.display = 'block';
                    tarunaExtraWrapper.style.display = 'flex';
                    if(prodiSelect) prodiSelect.required = true;
                } 
                else if (role === 'pembimbing' || role === 'admin_prodi') {
                    dynamicFields.style.display = 'block';
                    prodiWrapper.style.display = 'block';
                    tarunaExtraWrapper.style.display = 'none';
                    if(prodiSelect) prodiSelect.required = true;
                } 
                else {
                    prodiWrapper.style.display = 'none';
                    tarunaExtraWrapper.style.display = 'none';
                    if(prodiSelect) prodiSelect.required = false;
                }
            }

            // Run on load
            updateFields();

            // Run on change
            roleSelect.addEventListener('change', updateFields);
        });
    </script>
<?= $this->endSection() ?>
