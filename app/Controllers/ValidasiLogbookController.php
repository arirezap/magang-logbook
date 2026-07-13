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
        // Pastikan hanya pembimbing yang bisa mengakses
        if (session()->get('role') !== 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Dosen Pembimbing.');
        }

        $pembimbing_id = session()->get('id');

        // Baca parameter filter dari GET
        $filterTanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $filterNama   = $this->request->getGet('nama');
        $filterStatus = $this->request->getGet('status');
        $perPage      = $this->request->getGet('per_page') ?? 10;

        $logbooks = $this->logbookModel->getLogbooksForPembimbing($pembimbing_id, $filterTanggal, $filterNama, $filterStatus, $perPage);
        $pager    = $this->logbookModel->pager;

        $data = [
            'title'        => 'Validasi Logbook Taruna',
            'logbooks'     => $logbooks,
            'filterTanggal'=> $filterTanggal,
            'filterNama'   => $filterNama,
            'filterStatus' => $filterStatus,
            'perPage'      => $perPage,
            'pager'        => $pager,
        ];

        return view('validasi/index', $data);
    }

    public function updateStatus($id)
    {
        // Pastikan hanya pembimbing yang bisa melakukan aksi validasi
        if (session()->get('role') !== 'pembimbing') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

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
            return redirect()->back()->with('error', 'Status validasi tidak sah.');
        }

        // Lakukan update ke database
        $this->logbookModel->update($id, [
            'status'             => $status,
            'catatan_pembimbing' => $catatan
        ]);

        return redirect()->to('/validasi')->with('success', 'Validasi berhasil disimpan!');
    }
}
