<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .page-header {
            background-color: #0F172A;
            color: #FFFFFF;
            padding: 2.5rem 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        .filter-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            margin-bottom: 2rem;
        }
        .clean-input {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .clean-input:focus {
            background-color: #FFFFFF;
            border-color: #1E3A5F;
            box-shadow: 0 0 0 4px rgba(30, 58, 95, 0.1);
        }
        .logbook-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .logbook-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            border-color: #CBD5E1;
        }
        .logbook-date {
            font-weight: 700;
            color: #0F172A;
            font-size: 1.1rem;
        }
        .logbook-time {
            color: #64748B;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .status-menunggu { background-color: #FEF3C7; color: #D97706; }
        .status-disetujui { background-color: #D1FAE5; color: #059669; }
        .status-ditolak { background-color: #FEE2E2; color: #DC2626; }
        
        /* Flatpickr Custom Dots */
        .date-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            position: absolute;
            bottom: 3px;
            left: 50%;
            transform: translateX(-50%);
        }
        .indicator-green { background-color: #059669; }
        .indicator-yellow { background-color: #D97706; }
        .indicator-red { background-color: #DC2626; }
        .flatpickr-day { position: relative; }
    </style>

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-4 shadow-sm">
        <div>
            <h2 class="page-title m-0">Riwayat Logbook</h2>
            <p class="page-subtitle m-0">Catatan dan riwayat pengisian kegiatan harian magang Anda.</p>
        </div>
        <a href="<?= base_url('/logbook/create') ?>" class="btn btn-light fw-bold text-dark px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.95rem;">
            <i class="bi bi-plus-circle-fill text-primary"></i> Isi Logbook Baru
        </a>
    </div>

    <!-- Alert Notifications -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="filter-card">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Pilih Rentang Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="text" id="tanggal_filter" name="tanggal_filter" class="form-control clean-input border-start-0 ps-0 bg-white" placeholder="Pilih Tanggal..." value="<?= esc($tanggal_filter ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Status Validasi</label>
                <select name="status" class="form-select clean-input bg-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu" <?= ($status ?? '') == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="disetujui" <?= ($status ?? '') == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= ($status ?? '') == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold py-2 rounded-3" style="background-color: #1E3A5F; border-color: #1E3A5F;">
                    <i class="bi bi-search me-1"></i> Terapkan
                </button>
                <a href="<?= base_url('/logbook') ?>" class="btn btn-light border py-2 rounded-3" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Logbook List -->
    <?php if(empty($logbooks)): ?>
        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <i class="bi bi-journal-x text-muted mb-3" style="font-size: 3rem;"></i>
            <h5 class="fw-bold text-dark">Belum ada riwayat logbook</h5>
            <p class="text-muted">Riwayat pada tanggal atau status tersebut tidak ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($logbooks as $log): ?>
                <div class="col-12">
                    <div class="logbook-card">
                        <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light p-3 rounded-3 text-center border" style="min-width: 80px;">
                                    <div class="fw-bold text-dark fs-4 lh-1"><?= date('d', strtotime($log['tanggal'])) ?></div>
                                    <div class="text-secondary small fw-semibold text-uppercase mt-1"><?= date('M Y', strtotime($log['tanggal'])) ?></div>
                                </div>
                                <div>
                                    <div class="logbook-date mb-1"><?= esc($log['kegiatan']) ?></div>
                                    <?php if(!empty($log['dokumentasi'])): ?>
                                        <div class="logbook-time">
                                            <a href="<?= esc($log['dokumentasi']) ?>" target="_blank" class="text-decoration-none text-primary">
                                                <i class="bi bi-link-45deg me-1"></i> Lihat Dokumentasi
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column align-items-md-end gap-2">
                                <span class="status-badge status-<?= esc($log['status']) ?>">
                                    <?php if($log['status'] == 'disetujui'): ?>
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                    <?php elseif($log['status'] == 'ditolak'): ?>
                                        <i class="bi bi-x-circle-fill me-1"></i>
                                    <?php else: ?>
                                        <i class="bi bi-hourglass-split me-1"></i>
                                    <?php endif; ?>
                                    <?= esc(ucfirst($log['status'])) ?>
                                </span>
                                
                                <div class="d-flex gap-2 mt-2">
                                    <a href="<?= base_url('/logbook/edit/'.$log['id']) ?>" class="btn btn-sm btn-light border text-primary fw-semibold rounded-2 px-3">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="<?= base_url('/logbook/delete/'.$log['id']) ?>" class="btn btn-sm btn-light border text-danger fw-semibold rounded-2 px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus logbook ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($log['catatan_pembimbing'])): ?>
                            <div class="mt-3 p-3 bg-light rounded-3 border-start border-4 border-warning">
                                <div class="small fw-bold text-dark mb-1"><i class="bi bi-chat-dots text-warning me-1"></i> Catatan Pembimbing:</div>
                                <div class="text-secondary small"><?= esc($log['catatan_pembimbing']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const calendarData = <?= $calendarData ?>; // JSON from PHP
            const tanggalMulaiMagang = <?= $tanggal_mulai ? "'".$tanggal_mulai."'" : 'null' ?>;
            const today = new Date();
            today.setHours(0,0,0,0);
            
            let startDateParsed = null;
            if(tanggalMulaiMagang) {
                startDateParsed = new Date(tanggalMulaiMagang);
                startDateParsed.setHours(0,0,0,0);
            }

            flatpickr("#tanggal_filter", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "id",
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const y = dayElem.dateObj.getFullYear();
                    const m = String(dayElem.dateObj.getMonth() + 1).padStart(2, '0');
                    const d = String(dayElem.dateObj.getDate()).padStart(2, '0');
                    const dateStr = `${y}-${m}-${d}`;
                    
                    let statusColorClass = null;

                    // 1. Cek apakah ada di calendarData
                    if (calendarData[dateStr]) {
                        if (calendarData[dateStr] === 'disetujui') {
                            statusColorClass = 'indicator-green';
                        } else if (calendarData[dateStr] === 'menunggu') {
                            statusColorClass = 'indicator-yellow';
                        } else if (calendarData[dateStr] === 'ditolak') {
                            statusColorClass = 'indicator-red';
                        }
                    } 
                    // 2. Jika tidak ada di data, cek apakah berada dalam rentang magang (merah)
                    else {
                        const cellDate = dayElem.dateObj;
                        cellDate.setHours(0,0,0,0);
                        
                        if (startDateParsed && cellDate >= startDateParsed && cellDate <= today) {
                            const dayOfWeek = cellDate.getDay();
                            if(dayOfWeek !== 0 && dayOfWeek !== 6) { // Kita abaikan Sabtu/Minggu
                                statusColorClass = 'indicator-red';
                            }
                        }
                    }

                    if (statusColorClass) {
                        dayElem.innerHTML += `<span class="date-indicator ${statusColorClass}"></span>`;
                    }
                }
            });
        });
    </script>
<?= $this->endSection() ?>
