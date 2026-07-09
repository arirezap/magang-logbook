<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProdiModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DosenController extends BaseController
{
    protected $userModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->prodiModel = new ProdiModel();
    }

    public function index()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'pejabat', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $builder = $this->userModel->builder();
        $builder->select('users.*, prodi.nama_prodi')
                ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                ->where('users.role', 'pembimbing')
                ->orderBy('users.nama', 'ASC');
                
        if ($role === 'admin_prodi') {
            $builder->where('users.prodi_id', session()->get('prodi_id'));
        }

        // Filter Data by Prodi & Nama (hanya untuk superadmin/pejabat)
        $filterProdi = $this->request->getGet('prodi_id');
        $filterNama = $this->request->getGet('nama');
        
        if (!empty($filterProdi) && in_array($role, ['pejabat', 'superadmin'])) {
            $builder->where('users.prodi_id', $filterProdi);
        }
        
        if (!empty($filterNama)) {
            $builder->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }

        $dosenList = $builder->get()->getResultArray();
        $prodiList = $this->prodiModel->findAll();

        $data = [
            'title'       => 'Input Data Dosen Pembimbing',
            'dosenList'   => $dosenList,
            'prodiList'   => $prodiList,
            'filterProdi' => $filterProdi,
            'filterNama'  => $filterNama,
            'userRole'    => $role
        ];

        return view('admin/input_data_dosen/index', $data);
    }

    public function store()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin_prodi', 'pejabat', 'superadmin'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama'        => 'required',
            'nomor_induk' => 'required|is_unique[users.nomor_induk]',
            'prodi_id'    => 'permit_empty|is_natural_no_zero'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data gagal ditambahkan. NIP/Nomor Induk mungkin sudah terdaftar.');
        }

        $prodi_id = $this->request->getPost('prodi_id') ?: session()->get('prodi_id');

        $this->userModel->save([
            'nama'        => $this->request->getPost('nama'),
            'nomor_induk' => $this->request->getPost('nomor_induk'),
            'password'    => password_hash($this->request->getPost('nomor_induk'), PASSWORD_DEFAULT),
            'role'        => 'pembimbing',
            'prodi_id'    => $prodi_id
        ]);

        return redirect()->back()->with('success', 'Data dosen pembimbing berhasil ditambahkan.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Nama Dosen');
        $sheet->setCellValue('B1', 'NIP / Nomor Induk');
        $sheet->setCellValue('C1', 'Prodi (Nama/Singkatan - Opsional)');
        
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', '198001012005011001');
        $sheet->setCellValue('C2', 'TRO');
        
        // Format kolom NIP sebagai teks agar tidak menjadi scientific format
        $sheet->getStyle('B')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Dosen.xlsx';
        
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

        $file = $this->request->getFile('excel_file');
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File Excel wajib diisi.');
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
            $allProdi = $this->prodiModel->findAll();

            foreach ($rows as $row) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip jika Nama/NIP kosong
                
                $nama = trim($row[0]);
                $nip = ltrim(trim($row[1]), "'"); // Hapus tanda kutip tunggal di awal jika ada
                
                // Cek apakah NIP sudah ada
                $existingUser = $this->userModel->where('nomor_induk', $nip)->first();
                if ($existingUser) {
                    continue; // Skip jika dosen sudah ada, atau bisa di-update
                }

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
                        
                        if (strpos($pdName, $inputProdi) !== false || $pdName === $inputProdi) {
                            $prodi_id = $pd['id']; break;
                        }
                    }
                }

                $this->userModel->save([
                    'nama'        => $nama,
                    'nomor_induk' => $nip,
                    'password'    => password_hash($nip, PASSWORD_DEFAULT),
                    'role'        => 'pembimbing',
                    'prodi_id'    => $prodi_id
                ]);
                $countSuccess++;
            }
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat proses import. Tidak ada data yang disimpan.');
            }

            return redirect()->back()->with('success', "$countSuccess data dosen pembimbing berhasil diimport.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}
