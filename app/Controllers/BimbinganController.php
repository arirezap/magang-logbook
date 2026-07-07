<?php

namespace App\Controllers;

use App\Models\UserModel;

class BimbinganController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Fitur ini khusus untuk Dosen Pembimbing
        if (session()->get('role') != 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Dosen Pembimbing.');
        }

        $pembimbing_id = session()->get('id');
        
        // Ambil data taruna yang dibimbing oleh dosen ini
        $tarunas = $this->userModel->select('users.*, prodi.nama_prodi')
                                   ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                                   ->where('users.role', 'taruna')
                                   ->where('users.pembimbing_id', $pembimbing_id)
                                   ->orderBy('users.nama', 'ASC')
                                   ->findAll();

        $data = [
            'title'   => 'Daftar Taruna Bimbingan',
            'tarunas' => $tarunas
        ];

        return view('bimbingan/index', $data);
    }
}
