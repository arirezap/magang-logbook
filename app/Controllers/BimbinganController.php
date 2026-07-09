<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PenugasanMagangModel;

class BimbinganController extends BaseController
{
    protected $userModel;
    protected $penugasanModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->penugasanModel = new PenugasanMagangModel();
    }

    public function index()
    {
        // Fitur ini khusus untuk Dosen Pembimbing
        if (session()->get('role') != 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Dosen Pembimbing.');
        }

        $pembimbing_id = session()->get('id');
        
        // Ambil data taruna yang dibimbing oleh dosen ini melalui tabel penugasan_magang (aktif)
        $builder = $this->penugasanModel->builder();
        $tarunas = $builder->select('users.*, prodi.nama_prodi, penugasan_magang.tempat_magang as tempat_magang, penugasan_magang.tahun_ajaran, penugasan_magang.periode')
                           ->join('users', 'users.id = penugasan_magang.taruna_id')
                           ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                           ->where('penugasan_magang.pembimbing_id', $pembimbing_id)
                           ->where('penugasan_magang.status_aktif', true)
                           ->orderBy('users.nama', 'ASC')
                           ->get()->getResultArray();

        $data = [
            'title'   => 'Daftar Taruna Bimbingan',
            'tarunas' => $tarunas
        ];

        return view('bimbingan/index', $data);
    }
}
