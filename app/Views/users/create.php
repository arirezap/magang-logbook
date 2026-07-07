<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">Tambah Pengguna Baru</h3>
        <p class="text-muted">Lengkapi form di bawah ini untuk membuat akun.</p>
    </div>

    <?php if(session()->getFlashdata('validation')): ?>
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-circle me-2"></i>Terdapat Kesalahan:</h6>
            <ul class="mb-0">
            <?php foreach(session()->getFlashdata('validation') as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="<?= base_url('/users/store') ?>" method="POST">
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold">Pilih Peran (Role) <span class="text-danger">*</span></label>
                        <select class="form-select bg-light" name="role" id="roleSelect" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="taruna">Taruna</option>
                            <option value="pembimbing">Dosen Pembimbing</option>
                            <option value="admin_prodi">Admin Prodi</option>
                            <?php if($userRole == 'superadmin'): ?>
                                <option value="pejabat">Pejabat / Direktur</option>
                                <option value="superadmin">Superadmin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nomor Induk (NOTAR/NIP/Username) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="nomor_induk" value="<?= old('nomor_induk') ?>" required placeholder="Masukkan NIP atau NOTAR">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="nama" value="<?= old('nama') ?>" required placeholder="Nama Lengkap dengan Gelar">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Password Akun <span class="text-danger">*</span></label>
                        <input type="password" class="form-control bg-light" name="password" required placeholder="Minimal 6 karakter">
                        <div class="form-text">Bisa menggunakan 'password123' untuk default sementara.</div>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <!-- Dynamic Fields: Disembunyikan secara default kecuali role membutuhkan -->
                <div id="dynamicFields" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0" id="prodiWrapper">
                            <label class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
                            <?php if($userRole == 'admin_prodi'): ?>
                                <!-- Jika admin prodi, kunci ke prodinya sendiri (readonly) -->
                                <select class="form-select bg-light" disabled>
                                    <option>Prodi Anda Sendiri (Otomatis Tersimpan)</option>
                                </select>
                            <?php else: ?>
                                <select class="form-select bg-light" name="prodi_id" id="prodiSelect">
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php foreach($prodiList as $prodi): ?>
                                        <option value="<?= $prodi['id'] ?>"><?= esc($prodi['nama_prodi']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6" id="pembimbingWrapper">
                            <label class="form-label fw-bold">Dosen Pembimbing</label>
                            <select class="form-select bg-light" name="pembimbing_id">
                                <option value="">-- Pilih Dosen (Opsional) --</option>
                                <?php foreach($pembimbingList as $dsn): ?>
                                    <option value="<?= $dsn['id'] ?>"><?= esc($dsn['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3" id="tarunaExtraWrapper">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Jenjang</label>
                            <select class="form-select bg-light" name="jenjang">
                                <option value="">Pilih Jenjang</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Kelas</label>
                            <input type="text" class="form-control bg-light" name="kelas" placeholder="Misal: A, B">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tempat Magang</label>
                            <input type="text" class="form-control bg-light" name="tempat_magang" placeholder="Instansi/Perusahaan">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="<?= base_url('/users') ?>" class="btn btn-light px-4 py-2 fw-bold text-muted me-md-2">Batal</a>
                    <button type="submit" class="btn btn-primary-custom px-5 py-2 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Pengguna
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
            const pembimbingWrapper = document.getElementById('pembimbingWrapper');
            const tarunaExtraWrapper = document.getElementById('tarunaExtraWrapper');
            const prodiSelect = document.getElementById('prodiSelect');

            roleSelect.addEventListener('change', function() {
                let role = this.value;

                if (role === '') {
                    dynamicFields.style.display = 'none';
                    return;
                }

                dynamicFields.style.display = 'block';

                if (role === 'taruna') {
                    // Taruna butuh Prodi, Pembimbing, dan Extra
                    prodiWrapper.style.display = 'block';
                    pembimbingWrapper.style.display = 'block';
                    tarunaExtraWrapper.style.display = 'flex';
                    if(prodiSelect) prodiSelect.required = true;
                } 
                else if (role === 'pembimbing' || role === 'admin_prodi') {
                    // Dosen/Admin butuh Prodi saja
                    prodiWrapper.style.display = 'block';
                    pembimbingWrapper.style.display = 'none';
                    tarunaExtraWrapper.style.display = 'none';
                    if(prodiSelect) prodiSelect.required = true;
                } 
                else {
                    // Pejabat / Superadmin tidak butuh apapun
                    prodiWrapper.style.display = 'none';
                    pembimbingWrapper.style.display = 'none';
                    tarunaExtraWrapper.style.display = 'none';
                    if(prodiSelect) prodiSelect.required = false;
                }
            });
        });
    </script>
<?= $this->endSection() ?>
