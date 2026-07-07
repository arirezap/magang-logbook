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

        $data = [
            'title' => 'Dashboard Utama',
            'user' => $user
        ];
        
        return view('dashboard/index', $data);
    }
}
