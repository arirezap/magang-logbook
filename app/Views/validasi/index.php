<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Flatpickr CSS & SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* CSS Tambahan untuk UI UX Pro Max */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}
.skeleton {
    background: #e2e5e7;
    background-image: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
    background-size: 40px 100%;
    background-repeat: no-repeat;
    background-position: left -40px top 0;
    animation: shine 1.2s ease infinite;
}
@keyframes shine {
    to {
        background-position: right -40px top 0;
    }
}
.badge-status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
.badge-status-disetujui { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.badge-status-revisi { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
.badge-status-ditolak { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Validasi Logbook Taruna</h2>
        <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Tinjau dan berikan penilaian pada laporan harian anak bimbingan Anda.</p>
    </div>
</div>

<!-- Filter Panel -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form id="filterForm" onsubmit="event.preventDefault(); fetchLogbooks(true);">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">TANGGAL</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-white border-secondary-subtle px-2"><i class="bi bi-calendar-range text-muted"></i></span>
                        <input type="text" class="form-control border-secondary-subtle border-start-0 bg-white ps-1" id="dateRangePicker" name="tanggal" placeholder="Semua Waktu" value="<?= esc($filterTanggal ?? '') ?>" readonly onchange="fetchLogbooks(true)">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">PENCARIAN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-secondary-subtle"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-secondary-subtle border-start-0 shadow-none" id="nama" name="nama" placeholder="Nama / No. Taruna" value="<?= esc($filterNama ?? '') ?>" onkeyup="debounceFetch()">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold text-dark mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">STATUS</label>
                    <select id="statusFilter" name="status" class="form-select form-control-custom" onchange="fetchLogbooks(true)">
                        <option value="">Semua Status</option>
                        <option value="pending"   <?= ($filterStatus ?? '') === 'pending'   ? 'selected' : '' ?>>⏳ Pending</option>
                        <option value="disetujui" <?= ($filterStatus ?? '') === 'disetujui' ? 'selected' : '' ?>>✅ Disetujui</option>
                        <option value="revisi"    <?= ($filterStatus ?? '') === 'revisi'    ? 'selected' : '' ?>>🔄 Perlu Revisi</option>
                        <option value="ditolak"   <?= ($filterStatus ?? '') === 'ditolak'   ? 'selected' : '' ?>>❌ Ditolak</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label d-none d-md-block mb-2">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill fw-medium shadow-sm rounded-3 d-flex align-items-center justify-content-center py-2">
                            <i class="bi bi-funnel-fill me-2"></i> Terapkan
                        </button>
                        <button type="button" class="btn btn-light border-secondary-subtle btn-sm rounded-3 d-flex align-items-center justify-content-center px-3 py-2" onclick="resetFilters()" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise text-secondary"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Container for List Cards -->
<div id="logbookContainer" class="d-flex flex-column gap-3 mb-4">
    <!-- Logbooks will be injected here via JS -->
</div>

<!-- Loading Skeleton -->
<div id="loadingIndicator" class="d-none">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle skeleton flex-shrink-0" style="width:45px;height:45px;"></div>
                <div class="flex-grow-1">
                    <div class="skeleton mb-2" style="height: 20px; width: 40%; border-radius: 4px;"></div>
                    <div class="skeleton mb-3" style="height: 15px; width: 30%; border-radius: 4px;"></div>
                    <div class="skeleton mb-2" style="height: 15px; width: 100%; border-radius: 4px;"></div>
                    <div class="skeleton" style="height: 15px; width: 80%; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="text-center py-5 px-4 bg-white rounded-4 border shadow-sm" style="display: none;">
    <div class="mb-3" style="font-size: 3.5rem; opacity: 0.2;">📋</div>
    <h5 class="fw-semibold text-muted">Tidak ada laporan ditemukan</h5>
    <p class="text-muted small mb-0">Belum ada taruna bimbingan yang mengumpulkan logbook atau kriteria pencarian tidak cocok.</p>
</div>

<!-- End Indicator -->
<div id="endIndicator" class="text-center py-4 d-none">
    <span class="text-muted fw-semibold small bg-light px-4 py-2 rounded-pill border">Selesai • Tidak ada data lagi</span>
</div>


<!-- ============================================================
     SINGLE MODAL DETAIL — rendered outside DOM
     ============================================================ -->
<div class="modal fade" id="modalTinjauGlobal" tabindex="-1" aria-labelledby="modalTinjauLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <!-- Header -->
            <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTinjauLabel">Detail Laporan Harian</h5>
                        <div class="text-muted" style="font-size: 0.85rem;">Informasi lengkap kegiatan magang taruna</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-4">
                <!-- Info Row -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <div class="rounded-4 p-3 border border-light shadow-sm h-100" style="background: linear-gradient(to right, #ffffff, #f8f9ff);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-badge text-primary fs-5"></i>
                                <div class="text-muted fw-bold" style="font-size:0.75rem; letter-spacing:1px;">PROFIL TARUNA</div>
                            </div>
                            <div class="fw-bold text-dark fs-6" id="modal-nama">—</div>
                            <div class="text-muted" style="font-size:0.85rem;" id="modal-notar-kelas">—</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="rounded-4 p-3 border border-light shadow-sm h-100" style="background: linear-gradient(to right, #ffffff, #f8f9ff);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-calendar-check text-primary fs-5"></i>
                                <div class="text-muted fw-bold" style="font-size:0.75rem; letter-spacing:1px;">WAKTU PELAKSANAAN</div>
                            </div>
                            <div class="fw-bold text-dark fs-6" id="modal-tanggal">—</div>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan -->
                <div class="rounded-4 p-4 mb-4 border shadow-sm" style="background: #ffffff; border-color: #e8eeff !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-card-text text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Uraian Kegiatan</h6>
                    </div>
                    <p class="mb-0 text-dark" id="modal-kegiatan" style="white-space:pre-wrap; font-size:0.95rem; line-height:1.8;">—</p>
                </div>

                <!-- Dokumentasi -->
                <div class="rounded-4 p-4 border shadow-sm" style="background: #ffffff; border-color: #e8eeff !important;">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-images text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Lampiran & Dokumentasi</h6>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;" id="modal-docs-desc">Taruna melampirkan file dokumentasi:</p>
                    <a id="modal-link-docs" href="#" target="_blank" rel="noopener noreferrer"
                       class="btn btn-primary-custom rounded-pill px-4 py-2 shadow-sm fw-semibold hover-lift">
                        <i class="bi bi-box-arrow-up-right me-2" id="modal-docs-icon"></i><span id="modal-docs-text">Buka Bukti Dokumentasi</span>
                    </a>
                </div>
            </div>
            
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>


<!-- CSRF Data for Fetch -->
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

<script>
    let page = 1;
    let isLoading = false;
    let hasMore = true;
    let debounceTimer;

    const container = document.getElementById('logbookContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const emptyState = document.getElementById('emptyState');
    const endIndicator = document.getElementById('endIndicator');
    
    // Filters
    const dateRangePicker = document.getElementById('dateRangePicker');
    const searchInput = document.getElementById('nama');
    const statusFilter = document.getElementById('statusFilter');

    // Toast configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "id"
        });

        fetchLogbooks(true); // load initial data
        
        // Infinite scroll
        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
                fetchLogbooks(false);
            }
        });

        // Initialize modal
        const modalEl = document.getElementById('modalTinjauGlobal');
        if (modalEl && modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }
    });

    function debounceFetch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchLogbooks(true);
        }, 500);
    }

    function resetFilters() {
        dateRangePicker.value = '';
        searchInput.value = '';
        statusFilter.value = '';
        fetchLogbooks(true);
    }

    function fetchLogbooks(isReset = false) {
        if (isLoading || (!hasMore && !isReset)) return;
        
        isLoading = true;
        
        if (isReset) {
            page = 1;
            container.innerHTML = '';
            emptyState.style.display = 'none';
            endIndicator.classList.add('d-none');
            hasMore = true;
        }

        loadingIndicator.classList.remove('d-none');
        
        const tanggal = dateRangePicker.value;
        const nama = searchInput.value;
        const status = statusFilter.value;
        
        const url = `<?= base_url('/validasi/loadData') ?>?page_logbooks=${page}&tanggal=${encodeURIComponent(tanggal)}&nama=${encodeURIComponent(nama)}&status=${encodeURIComponent(status)}`;
        
        fetch(url)
            .then(res => res.json())
            .then(res => {
                loadingIndicator.classList.add('d-none');
                isLoading = false;

                if (res.data) {
                    if (res.data.length === 0 && isReset) {
                        emptyState.style.display = 'block';
                    } else {
                        res.data.forEach(log => {
                            container.appendChild(createLogbookCard(log));
                        });
                    }

                    hasMore = res.hasMore;
                    if (!hasMore && !isReset && res.data.length > 0) {
                        endIndicator.classList.remove('d-none');
                    }
                    if (!hasMore && isReset && res.data.length > 0) {
                        endIndicator.classList.remove('d-none');
                    }
                    
                    page++;

                    // Auto-fill list if the screen is not full yet and there is more data
                    if (hasMore && document.body.offsetHeight < window.innerHeight + 100) {
                        setTimeout(() => fetchLogbooks(false), 100);
                    }
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                loadingIndicator.classList.add('d-none');
                isLoading = false;
            });
    }

    function createLogbookCard(log) {
        const col = document.createElement('div');
        col.className = 'card border-0 shadow-sm rounded-4 overflow-hidden mb-2';
        
        let badgeClass, statusIcon;
        switch (log.status) {
            case 'disetujui': badgeClass = 'badge-status-disetujui'; statusIcon = 'bi-check-circle-fill'; break;
            case 'revisi':    badgeClass = 'badge-status-revisi';    statusIcon = 'bi-arrow-repeat'; break;
            case 'ditolak':   badgeClass = 'badge-status-ditolak';   statusIcon = 'bi-x-circle-fill'; break;
            default:          badgeClass = 'badge-status-pending';   statusIcon = 'bi-hourglass-split';
        }

        const initial = (log.nama_taruna || 'A').substring(0, 1).toUpperCase();
        const dateObj = new Date(log.tanggal);
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        const dateStr = dateObj.toLocaleDateString('id-ID', options);

        let kelasTampil = '';
        if (log.nama_prodi) {
            const namaProdi = log.nama_prodi.toUpperCase();
            let singkatanProdi = '';
            if (namaProdi === 'REKAYASA SISTEM TRANSPORTASI JALAN') singkatanProdi = 'RSTJ';
            else if (namaProdi === 'TEKNOLOGI REKAYASA OTOMOTIF') singkatanProdi = 'TRO';
            else if (namaProdi === 'TEKNOLOGI OTOMOTIF') singkatanProdi = 'TO';
            else singkatanProdi = namaProdi.split(' ').map(w => w[0]).join('');
            
            kelasTampil = singkatanProdi + (log.kelas ? ' ' + log.kelas.toUpperCase() : '');
        }

        let docsHTML = '';
        if (log.dokumentasi) {
            if (log.dokumentasi.startsWith('http')) {
                docsHTML = `<a href="${log.dokumentasi}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold shadow-sm hover-lift" style="font-size:0.75rem; padding: 0.15rem 0.6rem;"><i class="bi bi-google-drive me-1"></i> Lihat Bukti</a>`;
            } else {
                docsHTML = `<a href="<?= base_url('uploads/logbook/') ?>${log.dokumentasi}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold shadow-sm hover-lift" style="font-size:0.75rem; padding: 0.15rem 0.6rem;"><i class="bi bi-file-earmark-check me-1"></i> Lihat Bukti</a>`;
            }
        }

        col.innerHTML = `
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Left: Taruna Info & Kegiatan -->
                    <div class="col-12 col-lg-7 p-3 border-bottom border-lg-bottom-0 border-lg-end border-light d-flex flex-column">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-light">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                                 style="width:38px;height:38px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:0.9rem;">
                                ${initial}
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark text-truncate" style="font-size:0.95rem;">${log.nama_taruna}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <span class="fw-medium">${log.notar_taruna}</span> &bull; ${kelasTampil}
                                </div>
                            </div>
                            <div class="ms-auto d-lg-none">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 shadow-sm" onclick='showDetail(${JSON.stringify(log).replace(/'/g, "&apos;")})' title="Detail">
                                    <i class="bi bi-eye-fill" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-calendar3 me-1"></i>${dateStr}</span>
                        </div>
                        <div class="text-muted text-start flex-grow-1 mt-1" style="font-size:0.85rem; line-height: 1.4; white-space: pre-wrap;">${log.kegiatan}</div>
                        
                        ${docsHTML ? `<div class="mt-2 text-start">${docsHTML}</div>` : ''}
                    </div>
                    
                    <!-- Right: Form Validasi -->
                    <div class="col-12 col-lg-5 p-3 bg-light bg-opacity-50 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.7rem; letter-spacing: 0.5px;">UPDATE VALIDASI</label>
                                <span class="badge ${badgeClass} py-1 px-2 rounded-pill flex-shrink-0 status-badge-${log.id}" title="Status Saat Ini">
                                    <i class="bi ${statusIcon}"></i>
                                </span>
                            </div>
                            <form id="form-validasi-${log.id}" onsubmit="submitValidasi(event, ${log.id})" class="m-0">
                                <div class="mb-2">
                                    <select name="status" class="form-select form-select-sm fw-semibold shadow-none border-secondary-subtle" onchange="submitValidasi(event, ${log.id})">
                                        <option value="pending"   ${log.status === 'pending' ? 'selected' : ''}>⏳ Pending</option>
                                        <option value="disetujui" ${log.status === 'disetujui' ? 'selected' : ''}>✅ Disetujui</option>
                                        <option value="revisi"    ${log.status === 'revisi' ? 'selected' : ''}>🔄 Revisi</option>
                                        <option value="ditolak"   ${log.status === 'ditolak' ? 'selected' : ''}>❌ Ditolak</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-sm shadow-sm">
                                    <input type="text" name="catatan_pembimbing" class="form-control border-secondary-subtle bg-white" 
                                           placeholder="Ketik catatan..." value="${log.catatan_pembimbing || ''}">
                                    <button type="submit" class="btn btn-primary" title="Simpan"><i class="bi bi-send"></i></button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="mt-2 text-end d-none d-lg-block">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold shadow-sm hover-lift" style="font-size: 0.75rem;" onclick='showDetail(${JSON.stringify(log).replace(/'/g, "&apos;")})'>
                                <i class="bi bi-eye-fill me-1"></i> Detail Lengkap
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return col;
    }

    function submitValidasi(event, id) {
        event.preventDefault();
        
        const form = document.getElementById(`form-validasi-${id}`);
        const formData = new FormData(form);
        const status = formData.get('status');
        
        // Optimistic UI update (update badge temporarily)
        const badge = document.querySelector(`.status-badge-${id}`);
        if(badge) {
            badge.className = `badge badge-status-${status} py-1 px-2 rounded-pill flex-shrink-0 status-badge-${id}`;
            let icon = 'bi-hourglass-split';
            if(status === 'disetujui') icon = 'bi-check-circle-fill';
            if(status === 'revisi') icon = 'bi-arrow-repeat';
            if(status === 'ditolak') icon = 'bi-x-circle-fill';
            badge.innerHTML = `<i class="bi ${icon}"></i>`;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch(`<?= base_url('/validasi/update/') ?>${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                Toast.fire({
                    icon: 'success',
                    title: res.message
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: res.message || 'Terjadi kesalahan saat menyimpan data.'
                });
                // Note: To be extremely robust, we would rollback the UI badge here on failure.
            }
        })
        .catch(err => {
            Toast.fire({
                icon: 'error',
                title: 'Koneksi terputus. Gagal menyimpan data.'
            });
        });
    }

    function showDetail(log) {
        const bsModal = new bootstrap.Modal(document.getElementById('modalTinjauGlobal'));
        
        const dateObj = new Date(log.tanggal);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        
        let kelasTampil = '';
        if (log.nama_prodi) {
            const namaProdi = log.nama_prodi.toUpperCase();
            let singkatanProdi = '';
            if (namaProdi === 'REKAYASA SISTEM TRANSPORTASI JALAN') singkatanProdi = 'RSTJ';
            else if (namaProdi === 'TEKNOLOGI REKAYASA OTOMOTIF') singkatanProdi = 'TRO';
            else if (namaProdi === 'TEKNOLOGI OTOMOTIF') singkatanProdi = 'TO';
            else singkatanProdi = namaProdi.split(' ').map(w => w[0]).join('');
            
            kelasTampil = singkatanProdi + (log.kelas ? ' ' + log.kelas.toUpperCase() : '');
        }

        document.getElementById('modal-nama').textContent         = log.nama_taruna;
        document.getElementById('modal-notar-kelas').textContent  = log.notar_taruna + (kelasTampil ? ' • ' + kelasTampil : '');
        document.getElementById('modal-tanggal').textContent      = dateObj.toLocaleDateString('id-ID', options);
        document.getElementById('modal-kegiatan').textContent     = log.kegiatan;
        
        const docsLink = document.getElementById('modal-link-docs');
        const docsText = document.getElementById('modal-docs-text');
        const docsIcon = document.getElementById('modal-docs-icon');
        const docsDesc = document.getElementById('modal-docs-desc');

        if (log.dokumentasi) {
            if (log.dokumentasi.startsWith('http')) {
                docsLink.href = log.dokumentasi;
                docsText.textContent = "Buka Tautan Google Drive";
                docsIcon.className = "bi bi-google me-2";
                docsDesc.textContent = "Taruna telah melampirkan tautan Google Drive:";
            } else {
                docsLink.href = "<?= base_url('uploads/logbook/') ?>" + log.dokumentasi;
                docsText.textContent = "Lihat File Bukti";
                docsIcon.className = "bi bi-file-earmark-check me-2";
                docsDesc.textContent = "Taruna telah mengunggah file bukti dokumentasi:";
            }
            docsLink.style.display = "inline-block";
        } else {
            docsLink.style.display = "none";
            docsDesc.textContent = "Taruna tidak melampirkan bukti dokumentasi.";
        }

        bsModal.show();
    }
</script>

<?= $this->endSection() ?>
