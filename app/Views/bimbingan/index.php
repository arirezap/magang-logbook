<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 shadow-sm">
            <i class="bi bi-people-fill fs-3"></i>
        </div>
        <div>
            <h2 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">Daftar Taruna Bimbingan</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Kelola dan pantau taruna yang berada di bawah bimbingan Anda.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 bg-white border px-4 py-2 rounded-pill shadow-sm">
        <i class="bi bi-person-badge text-primary fs-5"></i>
        <span class="fw-bold text-dark" style="font-size: 0.9rem;" id="totalCountBadge">Total: <?= esc($totalTaruna) ?> Taruna</span>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="card-title fw-bold text-muted mb-3"><i class="bi bi-funnel"></i> Filter & Pencarian</h6>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="search" class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Pencarian Taruna</label>
                <input type="text" class="form-control form-control-custom" id="search" placeholder="Cari berdasarkan nama atau NIT...">
            </div>
            <div class="col-12 col-md-6">
                <label for="tempat_magang" class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tempat Magang</label>
                <select class="form-select form-select-custom" id="tempat_magang">
                    <option value="">-- Semua Tempat Magang --</option>
                    <?php foreach($tempatMagangList as $tm): ?>
                        <option value="<?= esc($tm) ?>"><?= esc($tm) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Data Container -->
<div id="dataContainer" class="row g-4 mb-4">
    <!-- Cards will be appended here -->
</div>

<!-- Initial Empty State -->
<div id="emptyState" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="display: none;">
    <div class="card-body text-center py-5 px-4">
        <div class="mb-3" style="font-size: 4rem; opacity: 0.15;">👥</div>
        <h4 class="fw-bold text-muted">Belum Ada Taruna</h4>
        <p class="text-muted mb-0">Tidak ada taruna yang cocok dengan kriteria pencarian saat ini.</p>
    </div>
</div>

<!-- Loading Spinner & Skeletons -->
<div id="loadingIndicator" class="d-none">
    <div class="row g-4">
        <?php for($i=0; $i<6; $i++): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="skeleton-box rounded-circle flex-shrink-0" style="width:50px;height:50px;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-box mb-2" style="height: 18px; width: 80%; border-radius: 4px;"></div>
                            <div class="skeleton-box" style="height: 14px; width: 50%; border-radius: 4px;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="skeleton-box mb-2" style="height: 12px; width: 40%; border-radius: 4px;"></div>
                        <div class="skeleton-box mb-1" style="height: 16px; width: 70%; border-radius: 4px;"></div>
                        <div class="skeleton-box" style="height: 14px; width: 30%; border-radius: 4px;"></div>
                    </div>
                    <div class="mb-4">
                        <div class="skeleton-box mb-2" style="height: 12px; width: 40%; border-radius: 4px;"></div>
                        <div class="skeleton-box" style="height: 16px; width: 90%; border-radius: 4px;"></div>
                    </div>
                    <div class="skeleton-box w-100" style="height: 40px; border-radius: 50rem;"></div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>

<!-- End of Results Marker -->
<div id="endIndicator" class="text-center py-4 text-muted fw-semibold d-none" style="font-size: 0.9rem;">
    <i class="bi bi-check-circle-fill text-success me-1"></i> Semua data telah dimuat.
</div>

<style>
/* CSS Tambahan untuk UI UX Pro Max */
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
    cursor: pointer;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08) !important;
}

/* Skeleton Loading Animation */
.skeleton-box {
    background: #e2e8f0;
    position: relative;
    overflow: hidden;
}
.skeleton-box::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    transform: translateX(-100%);
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.6) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let page = 1;
    let hasMore = true;
    let isLoading = false;
    let timeoutId = null;

    const container = document.getElementById('dataContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const endIndicator = document.getElementById('endIndicator');
    const emptyState = document.getElementById('emptyState');
    const totalCountBadge = document.getElementById('totalCountBadge');
    
    const searchInput = document.getElementById('search');
    const tempatMagangSelect = document.getElementById('tempat_magang');

    function fetchTarunas(isReset = false) {
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
        
        const searchValue = searchInput.value;
        const tempatValue = tempatMagangSelect.value;
        
        fetch(`<?= base_url('/bimbingan/loadData') ?>?page=${page}&search=${encodeURIComponent(searchValue)}&tempat_magang=${encodeURIComponent(tempatValue)}`)
            .then(response => response.json())
            .then(res => {
                loadingIndicator.classList.add('d-none');
                isLoading = false;

                if (res.status === 'success') {
                    totalCountBadge.textContent = `Total: ${res.total} Taruna`;
                    
                    if (res.data.length === 0 && isReset) {
                        emptyState.style.display = 'block';
                    } else {
                        res.data.forEach(taruna => {
                            container.appendChild(createCard(taruna));
                        });
                    }

                    hasMore = res.hasMore;
                    if (!hasMore && res.total > 0) {
                        endIndicator.classList.remove('d-none');
                    }
                    
                    page++;
                }
            })
            .catch(error => {
                console.error("Error loading data:", error);
                loadingIndicator.classList.add('d-none');
                isLoading = false;
            });
    }

    function getInitials(name) {
        return name.substring(0, 1).toUpperCase();
    }

    function createCard(taruna) {
        const linkValidasi = `<?= base_url('/validasi?nama=') ?>${encodeURIComponent(taruna.nama)}`;
        const prodi = taruna.nama_prodi || '-';
        const kelas = taruna.kelas || '-';
        const tempatMagang = taruna.tempat_magang || '-';
        
        const col = document.createElement('div');
        col.className = 'col-12 col-md-6 col-lg-4';
        
        col.innerHTML = `
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 hover-lift" onclick="window.location='${linkValidasi}'">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0 shadow-sm"
                             style="width:50px;height:50px;background:linear-gradient(135deg,#1a56db,#0b2545);font-size:1.2rem;">
                            ${getInitials(taruna.nama)}
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <h6 class="fw-bold text-truncate mb-1" style="font-size:1.05rem;" title="${taruna.nama}">${taruna.nama}</h6>
                            <span class="badge bg-light text-dark border px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                <i class="bi bi-person-vcard me-1"></i>${taruna.nomor_induk}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">PROGRAM STUDI & KELAS</div>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">${prodi}</div>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">Kelas: ${kelas}</div>
                    </div>

                    <div class="mb-4 flex-grow-1">
                        <div class="text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">TEMPAT MAGANG</div>
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
                            <span class="fw-medium text-dark" style="font-size: 0.9rem; line-height: 1.4;">${tempatMagang}</span>
                        </div>
                    </div>
                    
                    <a href="${linkValidasi}" class="btn btn-primary-custom w-100 rounded-pill py-2 fw-bold shadow-sm" onclick="event.stopPropagation();">
                        <i class="bi bi-journal-check me-2"></i> Tinjau Logbook
                    </a>
                </div>
            </div>
        `;
        return col;
    }

    // Infinite Scroll
    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
            fetchTarunas();
        }
    });

    // Filtering logic with debounce
    function triggerFilter() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fetchTarunas(true), 300);
    }

    searchInput.addEventListener('input', triggerFilter);
    tempatMagangSelect.addEventListener('change', triggerFilter);

    // Initial Load
    fetchTarunas(true);
});
</script>

<?= $this->endSection() ?>
