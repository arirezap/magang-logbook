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
        if (!in_array($role, ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $sort = $this->request->getGet('sort') ?? 'nama';
        $order = strtolower($this->request->getGet('order') ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
        
        $validSorts = [
            'nama' => 'users.nama',
            'notar' => 'users.nomor_induk',
            'tempat' => 'penugasan_magang.tempat_magang',
            'pembimbing' => 'p.nama'
        ];
        $sortColumn = $validSorts[$sort] ?? 'users.nama';

        $this->userModel->select('penugasan_magang.id as penugasan_id, penugasan_magang.tahun_ajaran, penugasan_magang.periode, penugasan_magang.tempat_magang, penugasan_magang.status_aktif, penugasan_magang.tanggal_mulai, penugasan_magang.tanggal_selesai, penugasan_magang.pembimbing_id, users.nama as nama_taruna, users.nomor_induk, p.nama as nama_pembimbing, users.id as taruna_id')
                ->join('penugasan_magang', 'penugasan_magang.taruna_id = users.id', 'left')
                ->join('users p', 'p.id = penugasan_magang.pembimbing_id', 'left')
                ->where('users.role', 'taruna')
                ->orderBy($sortColumn, $order);
                
        // Jika admin prodi, batasi hanya melihat taruna dari prodinya
        if ($role === 'admin_prodi' || $role === 'kaprodi') {
            $this->userModel->where('users.prodi_id', session()->get('prodi_id'));
        }

        // Filter Data
        $filterTahun = $this->request->getGet('tahun_ajaran');
        $filterPeriode = $this->request->getGet('periode');
        $filterNama = $this->request->getGet('nama');
        $filterProdi = $this->request->getGet('prodi_id');
        
        if (!empty($filterTahun)) {
            $this->userModel->where('penugasan_magang.tahun_ajaran', $filterTahun);
        }
        if (!empty($filterPeriode)) {
            $this->userModel->where('penugasan_magang.periode', $filterPeriode);
        }
        if (!empty($filterNama)) {
            $this->userModel->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }
        if (!empty($filterProdi) && in_array($role, ['direktur', 'wadir', 'kabag', 'superadmin'])) {
            $this->userModel->where('users.prodi_id', $filterProdi);
        }

        $perPage = $this->request->getGet('per_page') ?? 10;
        $penugasan = $this->userModel->paginate($perPage, 'penugasan');
        $pager = $this->userModel->pager;
        
        // Ambil tahun ajaran unik untuk filter
        $tahunList = $this->penugasanModel->select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'DESC')->findAll();

        // Data untuk dropdown form
        $builderTaruna = $this->userModel->where('role', 'taruna')->orderBy('nama', 'ASC');
        if ($role === 'admin_prodi' || $role === 'kaprodi') {
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
            'userRole'       => $role,
            'perPage'        => $perPage,
            'pager'          => $pager
        ];

        return view('admin/input_data_taruna/index', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])) {
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

        $taruna_id = $this->request->getPost('taruna_id');
        $tahun_ajaran = $this->request->getPost('tahun_ajaran');
        $periode = $this->request->getPost('periode');

        // Cek duplikasi penugasan
        $isDuplicate = $this->penugasanModel->where([
            'taruna_id' => $taruna_id,
            'tahun_ajaran' => $tahun_ajaran,
            'periode' => $periode
        ])->first();

        if ($isDuplicate) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Taruna tersebut sudah memiliki penugasan di periode yang sama.');
        }

        // Nonaktifkan penugasan sebelumnya untuk taruna ini
        $this->penugasanModel->where('taruna_id', $taruna_id)
                             ->set(['status_aktif' => false])
                             ->update();

        try {
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
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('error', 'Gagal: Taruna tersebut sudah memiliki penugasan di periode yang sama (Terdeteksi penugasan ganda).');
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada sistem saat menyimpan data.');
        }
    }

    public function update($id)
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'pembimbing_id' => 'required|is_natural_no_zero',
            'tempat_magang' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->penugasanModel->update($id, [
            'pembimbing_id' => $this->request->getPost('pembimbing_id'),
            'tempat_magang' => $this->request->getPost('tempat_magang')
        ]);

        return redirect()->back()->with('success', 'Data penugasan magang berhasil diperbarui.');
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
        if (!in_array($role, ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])) {
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
            $skippedUsers = [];

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
                    
                    // Fungsi anonim untuk membersihkan nama dari gelar dan tanda baca
                    $cleanName = function($str) {
                        $str = preg_replace('/[^a-z0-9\s]/', ' ', $str);
                        $words = explode(' ', $str);
                        $gelars = ['dr', 'prof', 'ir', 'st', 'mt', 'ssi', 'msi', 'msc', 'spd', 'mpd', 'mm', 'se', 'sh', 'mh', 'phd', 'amd', 'sst', 'atd', 'eng', 'ms', 'm', 's', 'dipl'];
                        $cleanWords = array_filter($words, function($w) use ($gelars) {
                            return strlen($w) > 2 && !in_array($w, $gelars);
                        });
                        return implode('', $cleanWords);
                    };
                    
                    $cleanInput = $cleanName($inputDosen);

                    foreach ($allPembimbing as $dsn) {
                        $dsnName = strtolower($dsn['nama']);
                        $dsnNip = strtolower($dsn['nomor_induk']);
                        $cleanDbName = $cleanName($dsnName);
                        
                        // Coba pencocokan NIP dulu (paling akurat)
                        if (strpos($dsnNip, $inputDosen) !== false || strpos($inputDosen, $dsnNip) !== false) {
                            $pembimbing_id = $dsn['id'];
                            break;
                        }
                        
                        // Coba pencocokan nama yang sudah dibersihkan dari gelar
                        if (!empty($cleanInput) && !empty($cleanDbName)) {
                            if (strpos($cleanDbName, $cleanInput) !== false || strpos($cleanInput, $cleanDbName) !== false) {
                                $pembimbing_id = $dsn['id'];
                                break;
                            }
                        }
                        
                        // Fallback ke pencocokan original jika tidak match
                        if (strpos($dsnName, $inputDosen) !== false || strpos($inputDosen, $dsnName) !== false) {
                            $pembimbing_id = $dsn['id'];
                            break;
                        }
                    }
                }

                // Cek akun taruna (sesuai aturan baru: skip jika sudah ada)
                $existingUser = $this->userModel->where('nomor_induk', $notar)->first();

                if ($existingUser) {
                    $skippedUsers[] = $notar;
                    continue; // Skip baris ini sepenuhnya
                }

                // Buat akun baru
                // Tentukan jenjang berdasarkan prodi_id
                $jenjang = 'D4'; // default fallback
                if (!empty($prodi_id)) {
                    foreach ($allProdi as $pd) {
                        if ($pd['id'] == $prodi_id) {
                            $pdNameLower = strtolower($pd['nama_prodi']);
                            if (strpos($pdNameLower, 'rekayasa') !== false || strpos($pdNameLower, 'rstj') !== false || strpos($pdNameLower, 'tro') !== false) {
                                $jenjang = 'D4';
                            } elseif (strpos($pdNameLower, 'otomotif') !== false || strpos($pdNameLower, 'to') !== false) {
                                $jenjang = 'D3';
                            }
                            break;
                        }
                    }
                }

                $this->userModel->insert([
                    'nomor_induk' => $notar,
                    'nama' => $nama,
                    'password' => password_hash($notar, PASSWORD_DEFAULT),
                    'role' => 'taruna',
                    'prodi_id' => $prodi_id,
                    'jenjang' => $jenjang,
                    'kelas' => $kelas
                ]);
                $taruna_id = $this->userModel->getInsertID();

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

            $msg = "$countSuccess data taruna baru berhasil di-import dan ditugaskan.";
            if (count($skippedUsers) > 0) {
                $msg .= " " . count($skippedUsers) . " data dilewati karena Notar sudah terdaftar (" . implode(", ", $skippedUsers) . ").";
                return redirect()->back()->with('success', $msg);
            }

            return redirect()->back()->with('success', $msg);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error membaca file: ' . $e->getMessage());
        }
    }

    public function batchMigrate()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $taruna_ids = $this->request->getPost('taruna_ids');
        if (empty($taruna_ids) || !is_array($taruna_ids)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu taruna untuk dimigrasi.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $countSuccess = 0;
        $skipped = [];

        foreach ($taruna_ids as $t_id) {
            // Cari penugasan aktif saat ini
            $currentPenugasan = $this->penugasanModel->where('taruna_id', $t_id)
                                                     ->where('status_aktif', true)
                                                     ->first();

            // Jika tidak ada penugasan atau bukan periode 1, skip
            if (!$currentPenugasan || $currentPenugasan['periode'] != 1) {
                $skipped[] = "Taruna ID $t_id tidak berada di Periode 1.";
                continue;
            }

            $tahun_ajaran = $currentPenugasan['tahun_ajaran'];
            
            // Cek apakah sudah ada Periode 2 di tahun ajaran yang sama
            $isDuplicate = $this->penugasanModel->where([
                'taruna_id' => $t_id,
                'tahun_ajaran' => $tahun_ajaran,
                'periode' => 2
            ])->first();

            if ($isDuplicate) {
                $skipped[] = "Taruna ID $t_id sudah memiliki Magang Periode 2.";
                continue;
            }

            // Nonaktifkan yang lama
            $this->penugasanModel->update($currentPenugasan['id'], ['status_aktif' => false]);

            // Buat yang baru untuk periode 2
            $this->penugasanModel->save([
                'taruna_id'       => $t_id,
                'pembimbing_id'   => $currentPenugasan['pembimbing_id'],
                'tahun_ajaran'    => $tahun_ajaran,
                'periode'         => 2,
                'tempat_magang'   => $currentPenugasan['tempat_magang'],
                'status_aktif'    => true
            ]);

            $countSuccess++;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses migrasi batch.');
        }

        $msg = "$countSuccess taruna berhasil dimigrasi ke Magang Periode 2.";
        if (count($skipped) > 0) {
            $msg .= " (" . count($skipped) . " taruna dilewati karena bukan di Periode 1 atau sudah ada di Periode 2).";
            // if we want it green despite some skips, keep it in success. Or use info.
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }
}
