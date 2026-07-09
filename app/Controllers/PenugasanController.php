<?php

namespace App\Controllers;

use App\Models\PenugasanMagangModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PenugasanController extends BaseController
{
    protected $penugasanModel;
    protected $userModel;

    public function __construct()
    {
        $this->penugasanModel = new PenugasanMagangModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Khusus admin prodi atau superadmin (pejabat)
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'pejabat', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $builder = $this->userModel->builder();
        $builder->select('penugasan_magang.id as penugasan_id, penugasan_magang.tahun_ajaran, penugasan_magang.periode, penugasan_magang.tempat_magang, penugasan_magang.status_aktif, penugasan_magang.tanggal_mulai, penugasan_magang.tanggal_selesai, users.nama as nama_taruna, users.nomor_induk, p.nama as nama_pembimbing, users.id as taruna_id')
                ->join('penugasan_magang', 'penugasan_magang.taruna_id = users.id', 'left')
                ->join('users p', 'p.id = penugasan_magang.pembimbing_id', 'left')
                ->where('users.role', 'taruna')
                ->orderBy('users.nama', 'ASC');
                
        // Jika admin prodi, batasi hanya melihat taruna dari prodinya
        if ($role === 'admin_prodi') {
            $builder->where('users.prodi_id', session()->get('prodi_id'));
        }

        // Filter Data
        $filterTahun = $this->request->getGet('tahun_ajaran');
        $filterPeriode = $this->request->getGet('periode');
        $filterNama = $this->request->getGet('nama');
        $filterProdi = $this->request->getGet('prodi_id');
        
        if (!empty($filterTahun)) {
            $builder->where('penugasan_magang.tahun_ajaran', $filterTahun);
        }
        if (!empty($filterPeriode)) {
            $builder->where('penugasan_magang.periode', $filterPeriode);
        }
        if (!empty($filterNama)) {
            $builder->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }
        if (!empty($filterProdi) && in_array($role, ['pejabat', 'superadmin'])) {
            $builder->where('users.prodi_id', $filterProdi);
        }

        $penugasan = $builder->get()->getResultArray();
        
        // Ambil tahun ajaran unik untuk filter
        $tahunList = $this->penugasanModel->select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'DESC')->findAll();

        // Data untuk dropdown form
        $builderTaruna = $this->userModel->where('role', 'taruna')->orderBy('nama', 'ASC');
        if ($role === 'admin_prodi') {
            $builderTaruna->where('prodi_id', session()->get('prodi_id'));
        }
        $tarunaList = $builderTaruna->findAll();

        $pembimbingList = $this->userModel->where('role', 'pembimbing')->orderBy('nama', 'ASC')->findAll();

        // Generate opsi tahun ajaran otomatis
        $currentYear = date('Y');
        $generatedTahunList = [];
        for ($i = -1; $i <= 2; $i++) {
            $startYear = $currentYear + $i;
            $endYear = $startYear + 1;
            $generatedTahunList[] = $startYear . '/' . $endYear;
        }

        // Ambil data prodi untuk filter
        $prodiModel = new \App\Models\ProdiModel();
        $prodiList = $prodiModel->findAll();

        $data = [
            'title'          => 'Input Data Taruna Magang',
            'penugasan'      => $penugasan,
            'tarunaList'     => $tarunaList,
            'pembimbingList' => $pembimbingList,
            'tahunList'      => $tahunList,
            'generatedTahunList' => $generatedTahunList,
            'prodiList'      => $prodiList,
            'filterTahun'    => $filterTahun,
            'filterPeriode'  => $filterPeriode,
            'filterNama'     => $filterNama,
            'filterProdi'    => $filterProdi,
            'userRole'       => $role
        ];

        return view('admin/input_data_taruna/index', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'pejabat', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'taruna_id'     => 'required|is_natural_no_zero',
            'pembimbing_id' => 'required|is_natural_no_zero',
            'tahun_ajaran'  => 'required',
            'periode'       => 'required|is_natural_no_zero',
            'tempat_magang' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Nonaktifkan penugasan sebelumnya untuk taruna ini
        $this->penugasanModel->where('taruna_id', $this->request->getPost('taruna_id'))
                             ->set(['status_aktif' => false])
                             ->update();

        // Buat penugasan baru
        $this->penugasanModel->save([
            'taruna_id'       => $this->request->getPost('taruna_id'),
            'pembimbing_id'   => $this->request->getPost('pembimbing_id'),
            'tahun_ajaran'    => $this->request->getPost('tahun_ajaran'),
            'periode'         => $this->request->getPost('periode'),
            'tempat_magang'   => $this->request->getPost('tempat_magang'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'status_aktif'    => true
        ]);

        return redirect()->back()->with('success', 'Penugasan magang berhasil ditambahkan.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Notar');
        $sheet->setCellValue('C1', 'Prodi (Nama/Singkatan)');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Tempat Magang');
        $sheet->setCellValue('F1', 'Dosen Pembimbing (Nama/NIP - Opsional)');
        
        $sheet->setCellValue('A2', 'Andi Taruna');
        $sheet->setCellValue('B2', '123456');
        $sheet->setCellValue('C2', 'TRO');
        $sheet->setCellValue('D2', 'A');
        $sheet->setCellValue('E2', 'PT KAI');
        $sheet->setCellValue('F2', 'Budi Santoso');
        
        // Format kolom Notar sebagai teks agar tidak menjadi scientific format
        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Taruna.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function importExcel()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'pejabat', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $tahun_ajaran = $this->request->getPost('tahun_ajaran');
        $periode = $this->request->getPost('periode');
        $file = $this->request->getFile('excel_file');

        if (!$tahun_ajaran || !$periode || !$file->isValid()) {
            return redirect()->back()->with('error', 'Tahun Ajaran, Periode, dan File Excel wajib diisi.');
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan xls, xlsx, atau csv.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Hapus header
            array_shift($rows);
            
            $db = \Config\Database::connect();
            $db->transStart();
            
            $countSuccess = 0;

            // Load data master untuk mapping
            $prodiModel = new \App\Models\ProdiModel();
            $allProdi = $prodiModel->findAll();
            $allPembimbing = $this->userModel->where('role', 'pembimbing')->findAll();

            foreach ($rows as $row) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip jika Nama/Notar kosong
                
                $nama = trim($row[0]);
                $notar = ltrim(trim($row[1]), "'"); // Hapus tanda kutip tunggal di awal jika ada
                $kelas = $row[3] ?? null;
                $tempat_magang = $row[4] ?? null;
                
                // Deteksi Prodi
                $prodi_id = session()->get('prodi_id'); // default
                if (!empty($row[2])) {
                    $inputProdi = strtolower(trim($row[2]));
                    foreach ($allProdi as $pd) {
                        $pdName = strtolower($pd['nama_prodi']);
                        
                        if (strpos($inputProdi, 'rstj') !== false || strpos($inputProdi, 'rekayasa sistem transportasi jalan') !== false) {
                            if (strpos($pdName, 'rekayasa sistem transportasi jalan') !== false || strpos($pdName, 'rstj') !== false) {
                                $prodi_id = $pd['id']; break;
                            }
                        } elseif (strpos($inputProdi, 'tro') !== false || strpos($inputProdi, 'teknologi rekayasa otomotif') !== false) {
                            if (strpos($pdName, 'teknologi rekayasa otomotif') !== false || strpos($pdName, 'tro') !== false) {
                                $prodi_id = $pd['id']; break;
                            }
                        } elseif (strpos($inputProdi, 'to') !== false || strpos($inputProdi, 'teknologi otomotif') !== false) {
                            if ((strpos($pdName, 'teknologi otomotif') !== false || strpos($pdName, 'to') !== false) && strpos($pdName, 'rekayasa') === false) {
                                $prodi_id = $pd['id']; break;
                            }
                        }
                        
                        // Fallback
                        if (strpos($pdName, $inputProdi) !== false || $pdName === $inputProdi) {
                            $prodi_id = $pd['id']; break;
                        }
                    }
                }

                // Deteksi Dosen Pembimbing
                $pembimbing_id = null;
                if (!empty($row[5])) {
                    $inputDosen = strtolower(trim($row[5]));
                    foreach ($allPembimbing as $dsn) {
                        $dsnName = strtolower($dsn['nama']);
                        $dsnNip = strtolower($dsn['nomor_induk']);
                        
                        if (strpos($dsnName, $inputDosen) !== false || strpos($dsnNip, $inputDosen) !== false || strpos($inputDosen, $dsnName) !== false || strpos($inputDosen, $dsnNip) !== false) {
                            $pembimbing_id = $dsn['id'];
                            break;
                        }
                    }
                }

                // Cek akun taruna
                $existingUser = $this->userModel->where('nomor_induk', $notar)->first();
                $taruna_id = null;

                if ($existingUser) {
                    $taruna_id = $existingUser['id'];
                    // Update kelas & prodi jika berubah
                    $this->userModel->update($taruna_id, [
                        'nama' => $nama,
                        'kelas' => $kelas,
                        'prodi_id' => $prodi_id
                    ]);
                } else {
                    // Buat akun baru
                    $this->userModel->insert([
                        'nomor_induk' => $notar,
                        'nama' => $nama,
                        'password' => password_hash($notar, PASSWORD_DEFAULT),
                        'role' => 'taruna',
                        'prodi_id' => $prodi_id,
                        'kelas' => $kelas
                    ]);
                    $taruna_id = $this->userModel->getInsertID();
                }

                // Nonaktifkan penugasan sebelumnya
                $this->penugasanModel->where('taruna_id', $taruna_id)
                                     ->set(['status_aktif' => false])
                                     ->update();
                
                // Buat penugasan baru
                $this->penugasanModel->save([
                    'taruna_id'       => $taruna_id,
                    'pembimbing_id'   => $pembimbing_id,
                    'tahun_ajaran'    => $tahun_ajaran,
                    'periode'         => $periode,
                    'tempat_magang'   => $tempat_magang,
                    'status_aktif'    => true
                ]);

                $countSuccess++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data. Import dibatalkan.');
            }

            return redirect()->back()->with('success', "$countSuccess taruna berhasil di-import dan ditugaskan.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error membaca file: ' . $e->getMessage());
        }
    }
}
