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
        $logbooks = $this->logbookModel->getLogbooksForPembimbing($pembimbing_id);

        $data = [
            'title'    => 'Validasi Logbook Taruna',
            'logbooks' => $logbooks
        ];

        return view('validasi/index', $data);
    }

    public function action($id)
    {
        // Pastikan hanya pembimbing yang bisa melakukan aksi validasi
        if (session()->get('role') !== 'pembimbing') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        if (!$logbook) {
            return redirect()->to('/validasi')->with('error', 'Data logbook tidak ditemukan.');
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
