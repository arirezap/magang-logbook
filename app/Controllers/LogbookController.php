<?php

namespace App\Controllers;

use App\Models\LogbookModel;
use App\Models\PenugasanMagangModel;

class LogbookController extends BaseController
{
    protected $logbookModel;

    public function __construct()
    {
        $this->logbookModel = new LogbookModel();
    }

    public function index()
    {
        // Pastikan hanya taruna yang bisa akses
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Halaman ini khusus untuk Taruna.');
        }

        $user_id = session()->get('id');
        $logbooks = $this->logbookModel->where('user_id', $user_id)
                                       ->orderBy('tanggal', 'DESC')
                                       ->findAll();

        $data = [
            'title'    => 'Riwayat Logbook Harian',
            'logbooks' => $logbooks
        ];

        return view('logbook/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Isi Logbook Harian'
        ];

        return view('logbook/create', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        // Validasi input
        // 'valid_url_strict' memastikan input benar-benar format URL
        $rules = [
            'tanggal'     => 'required|valid_date',
            'kegiatan'    => 'required|min_length[10]',
            'dokumentasi' => 'required|valid_url_strict'
        ];

        $messages = [
            'dokumentasi' => [
                'valid_url_strict' => 'Bukti dokumentasi harus berupa link URL Google Drive yang valid (awali dengan http/https).'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
        }

        // Mencegah input tanggal di masa depan
        $tanggalInput = $this->request->getPost('tanggal');
        if ($tanggalInput > date('Y-m-d')) {
            return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Tidak dapat mengisi logbook untuk tanggal di masa depan.']);
        }

        // Mencegah input laporan ganda di tanggal yang sama
        $existingLogbook = $this->logbookModel
                                ->where('user_id', session()->get('id'))
                                ->where('DATE(tanggal)', $this->request->getPost('tanggal'))
                                ->first();

        if ($existingLogbook) {
            return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Anda sudah mengisi laporan logbook pada tanggal tersebut.']);
        }

        $penugasanModel = new PenugasanMagangModel();
        $penugasan = $penugasanModel->getActivePenugasan(session()->get('id'));

        // Simpan data
        $this->logbookModel->save([
            'user_id'     => session()->get('id'),
            'penugasan_id'=> $penugasan ? $penugasan['id'] : null,
            'tanggal'     => $this->request->getPost('tanggal'),
            'kegiatan'    => $this->request->getPost('kegiatan'),
            'dokumentasi' => $this->request->getPost('dokumentasi'),
            'status'      => 'pending' // Default status menunggu validasi
        ]);

        return redirect()->to('/logbook')->with('success', 'Logbook harian berhasil ditambahkan! Menunggu validasi pembimbing.');
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        // Pastikan data ada, milik user yang login, dan status mengizinkan edit
        if (!$logbook || $logbook['user_id'] != session()->get('id') || !in_array($logbook['status'], ['pending', 'revisi', 'ditolak'])) {
            return redirect()->to('/logbook')->with('error', 'Data tidak ditemukan atau sudah tidak dapat diedit (Terkunci).');
        }

        $data = [
            'title'   => 'Edit Logbook Harian',
            'logbook' => $logbook
        ];

        return view('logbook/edit', $data);
    }

    public function update($id)
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        if (!$logbook || $logbook['user_id'] != session()->get('id') || !in_array($logbook['status'], ['pending', 'revisi', 'ditolak'])) {
            return redirect()->to('/logbook')->with('error', 'Data tidak ditemukan atau sudah tidak dapat diedit (Terkunci).');
        }

        $rules = [
            'tanggal'     => 'required|valid_date',
            'kegiatan'    => 'required|min_length[10]',
            'dokumentasi' => 'required|valid_url_strict'
        ];

        $messages = [
            'dokumentasi' => [
                'valid_url_strict' => 'Bukti dokumentasi harus berupa link URL Google Drive yang valid.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
        }

        // Mencegah input tanggal di masa depan
        $tanggalInput = $this->request->getPost('tanggal');
        if ($tanggalInput > date('Y-m-d')) {
            return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Tidak dapat mengisi logbook untuk tanggal di masa depan.']);
        }

        // Mencegah duplikasi tanggal saat update (kecuali tanggal miliknya sendiri)
        $existingLogbook = $this->logbookModel
                                ->where('user_id', session()->get('id'))
                                ->where('DATE(tanggal)', $this->request->getPost('tanggal'))
                                ->where('id !=', $id)
                                ->first();

        if ($existingLogbook) {
            return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Anda sudah memiliki laporan logbook lain pada tanggal tersebut.']);
        }

        // Validasi Khusus: Jika statusnya revisi atau ditolak, Taruna WAJIB melakukan perubahan data
        if (in_array($logbook['status'], ['revisi', 'ditolak'])) {
            $inputTanggal = $this->request->getPost('tanggal');
            $inputKegiatan = $this->request->getPost('kegiatan');
            $inputDokumentasi = $this->request->getPost('dokumentasi');

            if ($logbook['tanggal'] == $inputTanggal && $logbook['kegiatan'] == $inputKegiatan && $logbook['dokumentasi'] == $inputDokumentasi) {
                return redirect()->back()->withInput()->with('error', 'Anda harus memperbaiki atau melakukan perubahan pada laporan sebelum mengirim ulang!');
            }
        }

        $this->logbookModel->update($id, [
            'tanggal'     => $this->request->getPost('tanggal'),
            'kegiatan'    => $this->request->getPost('kegiatan'),
            'dokumentasi' => $this->request->getPost('dokumentasi'),
            'status'      => 'pending' // Reset status ke pending
        ]);

        return redirect()->to('/logbook')->with('success', 'Logbook harian berhasil diperbarui dan dikirim ulang untuk divalidasi.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        // Taruna tidak bisa menghapus logbook yang sudah disetujui
        if (!$logbook || $logbook['user_id'] != session()->get('id') || $logbook['status'] == 'disetujui') {
            return redirect()->to('/logbook')->with('error', 'Data tidak ditemukan atau sudah tidak dapat dihapus (Terkunci).');
        }

        $this->logbookModel->delete($id);
        return redirect()->to('/logbook')->with('success', 'Logbook harian berhasil dihapus.');
    }
}
