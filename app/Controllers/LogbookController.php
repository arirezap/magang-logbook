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
        $tanggal_filter = $this->request->getGet('tanggal_filter');
        $status = $this->request->getGet('status');

        $builder = $this->logbookModel->where('user_id', $user_id);

        $start_date = '';
        $end_date = '';
        if (!empty($tanggal_filter)) {
            // Flatpickr range separator depends on locale, can be ' to ' or ' - '
            $separator = strpos($tanggal_filter, ' - ') !== false ? ' - ' : ' to ';
            $dates = explode($separator, $tanggal_filter);
            $start_date = trim($dates[0]);
            $end_date = isset($dates[1]) ? trim($dates[1]) : $start_date;
            
            $builder->where('tanggal >=', $start_date);
            $builder->where('tanggal <=', $end_date);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $logbooks = $builder->orderBy('tanggal', 'DESC')->paginate($perPage, 'logbooks');
        $pager = $this->logbookModel->pager;

        // Data untuk penanda warna pada kalender (mengambil semua riwayat logbook taruna)
        $allLogbooks = $this->logbookModel->where('user_id', $user_id)->findAll();
        $calendarData = [];
        foreach ($allLogbooks as $log) {
            $calendarData[$log['tanggal']] = $log['status'];
        }

        // Ambil tanggal mulai magang dari penugasan aktif
        $penugasanModel = new PenugasanMagangModel();
        $penugasan = $penugasanModel->getActivePenugasan($user_id);
        $tanggal_mulai = $penugasan ? $penugasan['tanggal_mulai'] : null;

        $data = [
            'title'          => 'Riwayat Logbook Harian',
            'logbooks'       => $logbooks,
            'tanggal_filter' => $tanggal_filter,
            'status'         => $status,
            'calendarData'   => json_encode($calendarData),
            'tanggal_mulai'  => $tanggal_mulai,
            'perPage'        => $perPage,
            'pager'          => $pager
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

        $rules = [
            'tanggal'     => 'required|valid_date',
            'kegiatan'    => 'required|min_length[10]',
            'dokumentasi' => 'uploaded[dokumentasi]|max_size[dokumentasi,5120]|ext_in[dokumentasi,jpg,jpeg,png,pdf]'
        ];

        $messages = [
            'dokumentasi' => [
                'uploaded' => 'File bukti dokumentasi wajib diunggah.',
                'max_size' => 'Ukuran file tidak boleh lebih dari 5MB.',
                'ext_in'   => 'Format file harus berupa JPG, JPEG, PNG, atau PDF.'
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

        // Handle upload file
        $file = $this->request->getFile('dokumentasi');
        $fileName = '';
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/logbook';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $fileName = $file->getRandomName();
            $file->move($uploadPath, $fileName);
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah file bukti dokumentasi.');
        }

        try {
            // Simpan data
            $this->logbookModel->save([
                'user_id'     => session()->get('id'),
                'penugasan_id'=> $penugasan ? $penugasan['id'] : null,
                'tanggal'     => $this->request->getPost('tanggal'),
                'kegiatan'    => $this->request->getPost('kegiatan'),
                'dokumentasi' => $fileName,
                'status'      => 'pending' // Default status menunggu validasi
            ]);

            return redirect()->to('/logbook')->with('success', 'Logbook harian berhasil ditambahkan! Menunggu validasi pembimbing.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Jika terjadi Duplicate Entry karena race condition
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Anda sudah mengisi laporan logbook pada tanggal tersebut (Terdeteksi pengiriman ganda).']);
            }
            // Error database lainnya
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada sistem saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        // Pastikan data ada, milik user yang login, dan status mengizinkan edit (hanya revisi/ditolak)
        if (!$logbook || $logbook['user_id'] != session()->get('id') || !in_array($logbook['status'], ['revisi', 'ditolak'])) {
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

        if (!$logbook || $logbook['user_id'] != session()->get('id') || !in_array($logbook['status'], ['revisi', 'ditolak'])) {
            return redirect()->to('/logbook')->with('error', 'Data tidak ditemukan atau sudah tidak dapat diedit (Terkunci).');
        }

        $rules = [
            'tanggal'     => 'required|valid_date',
            'kegiatan'    => 'required|min_length[10]',
        ];

        $file = $this->request->getFile('dokumentasi');
        $isFileUploaded = $file && $file->isValid() && !$file->hasMoved();
        
        if ($isFileUploaded) {
            $rules['dokumentasi'] = 'max_size[dokumentasi,5120]|ext_in[dokumentasi,jpg,jpeg,png,pdf]';
        }

        $messages = [
            'dokumentasi' => [
                'max_size' => 'Ukuran file tidak boleh lebih dari 5MB.',
                'ext_in'   => 'Format file harus berupa JPG, JPEG, PNG, atau PDF.'
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

            if (!$isFileUploaded && $logbook['tanggal'] == $inputTanggal && $logbook['kegiatan'] == $inputKegiatan) {
                return redirect()->back()->withInput()->with('error', 'Anda harus memperbaiki atau melakukan perubahan pada laporan (atau unggah bukti baru) sebelum mengirim ulang!');
            }
        }

        // Handle file upload if new file is provided
        $fileName = $logbook['dokumentasi'];
        if ($isFileUploaded) {
            $uploadPath = FCPATH . 'uploads/logbook';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $fileName = $newName;
            
            // Delete old file if it's not a URL
            if (!empty($logbook['dokumentasi']) && strpos($logbook['dokumentasi'], 'http') !== 0) {
                $oldFilePath = $uploadPath . '/' . $logbook['dokumentasi'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        try {
            $this->logbookModel->update($id, [
                'tanggal'     => $this->request->getPost('tanggal'),
                'kegiatan'    => $this->request->getPost('kegiatan'),
                'dokumentasi' => $fileName,
                'status'      => 'pending' // Reset status ke pending
            ]);

            $qs = $this->request->getUri()->getQuery();
            $redirectUrl = $qs ? '/logbook?' . $qs : '/logbook';
            
            return redirect()->to($redirectUrl)->with('success', 'Logbook harian berhasil diperbarui dan dikirim ulang untuk divalidasi.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('validation', ['tanggal' => 'Anda sudah memiliki laporan logbook lain pada tanggal tersebut (Terdeteksi pengiriman ganda).']);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada sistem saat memperbarui data.');
        }
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'taruna') {
            return redirect()->to('/dashboard');
        }

        $logbook = $this->logbookModel->find($id);

        // Taruna hanya bisa menghapus jika status revisi/ditolak
        if (!$logbook || $logbook['user_id'] != session()->get('id') || !in_array($logbook['status'], ['revisi', 'ditolak'])) {
            return redirect()->to('/logbook')->with('error', 'Data tidak ditemukan atau sudah tidak dapat dihapus (Terkunci).');
        }

        $this->logbookModel->delete($id);
        return redirect()->to('/logbook')->with('success', 'Logbook harian berhasil dihapus.');
    }
}
