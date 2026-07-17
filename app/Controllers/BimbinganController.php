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
        $role = strtolower(session()->get('role'));
        $role_kedua = strtolower(session()->get('role_kedua') ?? '');
        
        // Fitur ini khusus untuk Dosen Pembimbing
        if ($role != 'pembimbing' && $role_kedua != 'pembimbing') {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Dosen Pembimbing.');
        }

        $pembimbing_id = session()->get('id');
        
        // Ambil daftar tempat magang unik untuk dropdown filter
        $tempatMagangList = $this->penugasanModel->builder()
            ->select('tempat_magang')
            ->where('pembimbing_id', $pembimbing_id)
            ->where('status_aktif', true)
            ->where('tempat_magang !=', '')
            ->where('tempat_magang IS NOT NULL', null, false)
            ->distinct()
            ->orderBy('tempat_magang', 'ASC')
            ->get()->getResultArray();

        // Total count for header
        $totalTaruna = $this->penugasanModel->builder()
            ->where('pembimbing_id', $pembimbing_id)
            ->where('status_aktif', true)
            ->countAllResults();

        $data = [
            'title'   => 'Daftar Taruna Bimbingan',
            'tempatMagangList' => array_column($tempatMagangList, 'tempat_magang'),
            'totalTaruna' => $totalTaruna
        ];

        return view('bimbingan/index', $data);
    }

    public function loadData()
    {
        $role = strtolower(session()->get('role'));
        $role_kedua = strtolower(session()->get('role_kedua') ?? '');
        
        if ($role != 'pembimbing' && $role_kedua != 'pembimbing') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $pembimbing_id = session()->get('id');
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search') ?? '';
        $tempat_magang = $this->request->getGet('tempat_magang') ?? '';
        
        $perPage = 12; // Jumlah data per load
        $offset = ($page - 1) * $perPage;

        $builder = $this->penugasanModel->builder();
        $builder->select('users.*, prodi.nama_prodi, penugasan_magang.tempat_magang as tempat_magang')
                ->join('users', 'users.id = penugasan_magang.taruna_id')
                ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                ->where('penugasan_magang.pembimbing_id', $pembimbing_id)
                ->where('penugasan_magang.status_aktif', true);

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('users.nama', $search)
                    ->orLike('users.nomor_induk', $search)
                    ->groupEnd();
        }

        if (!empty($tempat_magang)) {
            $builder->where('penugasan_magang.tempat_magang', $tempat_magang);
        }

        // Sorting: berdasarkan tempat magang lalu abjad nama
        $builder->orderBy('penugasan_magang.tempat_magang', 'ASC');
        $builder->orderBy('users.nama', 'ASC');

        $totalItems = $builder->countAllResults(false);
        $tarunas = $builder->limit($perPage, $offset)->get()->getResultArray();

        $hasMore = ($offset + $perPage) < $totalItems;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $tarunas,
            'hasMore' => $hasMore,
            'total' => $totalItems
        ]);
    }
}
