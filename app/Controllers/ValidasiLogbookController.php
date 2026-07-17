<?php

namespace App\Controllers;

use App\Models\LogbookModel;

class ValidasiLogbookController extends BaseController
{
    protected $logbookModel;

    public function __construct()
    {
        $this->logbookModel = new LogbookModel();
    }

    public function index()
    {
        $role = strtolower(session()->get('role'));
        $role_kedua = strtolower(session()->get('role_kedua') ?? '');
        
        // Fitur ini khusus untuk Dosen Pembimbing (baik sebagai role utama maupun role_kedua)
        if ($role != 'pembimbing' && $role_kedua != 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Dosen Pembimbing.');
        }

        $pembimbing_id = session()->get('id');

        // Baca parameter filter dari GET
        $filterTanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $filterNama   = $this->request->getGet('nama');
        $filterStatus = $this->request->getGet('status');
        $perPage      = $this->request->getGet('per_page') ?? 10;

        $data = [
            'title'        => 'Validasi Logbook Taruna',
            'filterTanggal'=> $filterTanggal,
            'filterNama'   => $filterNama,
            'filterStatus' => $filterStatus,
            'perPage'      => $perPage,
        ];

        return view('validasi/index', $data);
    }

    public function loadData()
    {
        $role = strtolower(session()->get('role'));
        $role_kedua = strtolower(session()->get('role_kedua') ?? '');
        
        if ($role != 'pembimbing' && $role_kedua != 'pembimbing') {
            return $this->response->setJSON(['error' => 'Akses ditolak']);
        }

        $pembimbing_id = session()->get('id');

        $filterTanggal = $this->request->getGet('tanggal');
        $filterNama   = $this->request->getGet('nama');
        $filterStatus = $this->request->getGet('status');
        $perPage      = 20;
        $page         = $this->request->getGet('page') ?? 1;

        // CodeIgniter 4 paginate automatically uses 'page' query param
        $logbooks = $this->logbookModel->getLogbooksForPembimbing($pembimbing_id, $filterTanggal, $filterNama, $filterStatus, $perPage);
        $pager = $this->logbookModel->pager;

        $hasMore = $pager->getCurrentPage('logbooks') < $pager->getPageCount('logbooks');

        return $this->response->setJSON([
            'data' => $logbooks,
            'hasMore' => $hasMore
        ]);
    }

    public function updateStatus($id)
    {
        $role = strtolower(session()->get('role'));
        $role_kedua = strtolower(session()->get('role_kedua') ?? '');
        
        // Fitur ini khusus untuk Dosen Pembimbing
        if ($role != 'pembimbing' && $role_kedua != 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Dosen Pembimbing.');
        }$logbook = $this->logbookModel->find($id);

        if (!$logbook) {
            return redirect()->to('/validasi')->with('error', 'Data logbook tidak ditemukan.');
        }

        // Security check: Pastikan logbook ini milik taruna di bawah pembimbing yang login
        $penugasanModel = new \App\Models\PenugasanMagangModel();
        $penugasan = $penugasanModel->find($logbook['penugasan_id']);
        
        if (!$penugasan || $penugasan['pembimbing_id'] != session()->get('id')) {
            return redirect()->to('/validasi')->with('error', 'Akses ditolak. Logbook ini bukan milik taruna bimbingan Anda.');
        }

        // Tangkap input dari Modal
        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan_pembimbing');

        // Validasi array status agar aman
        if (!in_array($status, ['disetujui', 'revisi', 'ditolak'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Status validasi tidak sah.']);
            }
            return redirect()->back()->with('error', 'Status validasi tidak sah.');
        }

        // Lakukan update ke database
        $this->logbookModel->update($id, [
            'status'             => $status,
            'catatan_pembimbing' => $catatan
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Validasi berhasil disimpan.']);
        }

        return redirect()->to('/validasi')->with('success', 'Validasi berhasil disimpan!');
    }
}
