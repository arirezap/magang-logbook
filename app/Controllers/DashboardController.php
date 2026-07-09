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
            $stats['total'] = $logbookModel->where('user_id', $user['id'])->countAllResults();
            $stats['disetujui'] = $logbookModel->where('user_id', $user['id'])->where('status', 'disetujui')->countAllResults();
            $stats['pending'] = $logbookModel->where('user_id', $user['id'])->where('status', 'pending')->countAllResults();
            $stats['revisi'] = $logbookModel->where('user_id', $user['id'])->where('status', 'revisi')->countAllResults();
        } elseif ($role == 'pembimbing') {
            $stats['total_taruna'] = $userModel->where('pembimbing_id', $user['id'])->where('role', 'taruna')->countAllResults();
            $stats['pending_validasi'] = $logbookModel->join('users', 'users.id = logbooks.user_id')
                                                      ->where('users.pembimbing_id', $user['id'])
                                                      ->where('logbooks.status', 'pending')
                                                      ->countAllResults();
        } elseif ($role == 'admin_prodi') {
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
        } elseif (in_array($role, ['pejabat', 'superadmin'])) {
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
        $user = $userModel->select('users.*, prodi.nama_prodi, p.nama as nama_pembimbing')
                          ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                          ->join('users p', 'p.id = users.pembimbing_id', 'left')
                          ->find(session()->get('id'));

        $data = [
            'title' => 'Profil Saya',
            'user'  => $user
        ];

        return view('profile/index', $data);
    }
}
