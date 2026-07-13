<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        // Mengambil seluruh data user utuh beserta nama prodi-nya
        $user = $userModel->select('users.*, prodi.nama_prodi')
                          ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                          ->find(session()->get('id'));

        // Logika untuk menyingkat nama prodi
        $singkatanProdi = '';
        if (!empty($user['nama_prodi'])) {
            $namaProdi = strtoupper(trim($user['nama_prodi']));
            if ($namaProdi == 'REKAYASA SISTEM TRANSPORTASI JALAN') {
                $singkatanProdi = 'RSTJ';
            } elseif ($namaProdi == 'TEKNOLOGI REKAYASA OTOMOTIF') {
                $singkatanProdi = 'TRO';
            } elseif ($namaProdi == 'TEKNOLOGI OTOMOTIF') {
                $singkatanProdi = 'TO';
            } else {
                // Fallback dinamis: Ambil huruf pertama setiap kata
                $words = explode(' ', $namaProdi);
                foreach ($words as $w) {
                    $singkatanProdi .= substr($w, 0, 1);
                }
            }
        }
        
        // Menggabungkan prodi dan kelas
        $user['kelas_lengkap'] = trim($singkatanProdi . ' ' . ($user['kelas'] ?? ''));

        // Mengambil statistik berdasarkan role
        $stats = [];
        $logbookModel = new \App\Models\LogbookModel();
        $role = strtolower($user['role']);
        
        if ($role == 'taruna') {
            $penugasanModel = new \App\Models\PenugasanMagangModel();
            $penugasan = $penugasanModel->where('taruna_id', $user['id'])
                                        ->where('status_aktif', true)
                                        ->first();
            if ($penugasan) {
                $user['tempat_magang'] = $penugasan['tempat_magang'];
            }

            $stats['total'] = $logbookModel->where('user_id', $user['id'])->countAllResults();
            $stats['disetujui'] = $logbookModel->where('user_id', $user['id'])->where('status', 'disetujui')->countAllResults();
            $stats['pending'] = $logbookModel->where('user_id', $user['id'])->where('status', 'pending')->countAllResults();
            $stats['revisi'] = $logbookModel->where('user_id', $user['id'])->where('status', 'revisi')->countAllResults();
        } elseif ($role == 'pembimbing') {
            $penugasanModel = new \App\Models\PenugasanMagangModel();
            $stats['total_taruna'] = $penugasanModel->where('pembimbing_id', $user['id'])->where('status_aktif', true)->countAllResults();
            $stats['pending_validasi'] = $logbookModel->join('penugasan_magang pm', 'pm.id = logbooks.penugasan_id')
                                                      ->where('pm.pembimbing_id', $user['id'])
                                                      ->where('logbooks.status', 'pending')
                                                      ->countAllResults();
        } elseif (in_array($role, ['admin_prodi', 'kaprodi'])) {
            $stats['total_taruna'] = $userModel->where('role', 'taruna')->where('prodi_id', $user['prodi_id'])->countAllResults();
            $stats['total_pembimbing'] = $userModel->where('role', 'pembimbing')->where('prodi_id', $user['prodi_id'])->countAllResults();
            $stats['logbook_hari_ini'] = $logbookModel->join('users', 'users.id = logbooks.user_id')
                                                      ->where('users.prodi_id', $user['prodi_id'])
                                                      ->where('logbooks.tanggal', date('Y-m-d'))
                                                      ->countAllResults();
            $stats['pending_logbook'] = $logbookModel->join('users', 'users.id = logbooks.user_id')
                                                     ->where('users.prodi_id', $user['prodi_id'])
                                                     ->where('logbooks.status', 'pending')
                                                     ->countAllResults();
        } elseif (in_array($role, ['direktur', 'wadir', 'kabag', 'superadmin'])) {
            $stats['total_taruna'] = $userModel->where('role', 'taruna')->countAllResults();
            $stats['total_pembimbing'] = $userModel->where('role', 'pembimbing')->countAllResults();
            $stats['logbook_hari_ini'] = $logbookModel->where('tanggal', date('Y-m-d'))->countAllResults();
            $stats['pending_logbook'] = $logbookModel->where('status', 'pending')->countAllResults();
        }

        $data = [
            'title' => 'Dashboard Utama',
            'user' => $user,
            'stats' => $stats
        ];
        
        return view('dashboard/index', $data);
    }

    public function profile()
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->select('users.*, prodi.nama_prodi')
                          ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                          ->find(session()->get('id'));

        if ($user && $user['role'] == 'taruna') {
            $penugasanModel = new \App\Models\PenugasanMagangModel();
            $penugasan = $penugasanModel->select('penugasan_magang.tempat_magang, p.nama as nama_pembimbing')
                                        ->join('users p', 'p.id = penugasan_magang.pembimbing_id', 'left')
                                        ->where('taruna_id', $user['id'])
                                        ->where('status_aktif', true)
                                        ->first();
            if ($penugasan) {
                $user['tempat_magang'] = $penugasan['tempat_magang'];
                $user['nama_pembimbing'] = $penugasan['nama_pembimbing'];
            }
        }

        $data = [
            'title' => 'Profil Saya',
            'user'  => $user
        ];

        return view('profile/index', $data);
    }

    public function updatePassword()
    {
        $userModel = new \App\Models\UserModel();
        $userId = session()->get('id');
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/profile')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Validasi input
        $rules = [
            'old_password'     => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        $messages = [
            'new_password' => [
                'min_length' => 'Password baru minimal harus 6 karakter.'
            ],
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password baru.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->with('validation', $this->validator->getErrors());
        }

        // Verifikasi password lama
        $oldPassword = $this->request->getPost('old_password');
        if (!password_verify((string)$oldPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }

        // Hash password baru dan simpan
        $newPassword = $this->request->getPost('new_password');
        $hashedPassword = password_hash((string)$newPassword, PASSWORD_DEFAULT);

        $userModel->update($userId, [
            'password' => $hashedPassword
        ]);

        return redirect()->to('/profile')->with('success', 'Password berhasil diubah.');
    }
}
