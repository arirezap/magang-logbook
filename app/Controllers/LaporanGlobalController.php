<?php

namespace App\Controllers;

use App\Models\LogbookModel;

class LaporanGlobalController extends BaseController
{
    protected $logbookModel;

    public function __construct()
    {
        $this->logbookModel = new LogbookModel();
    }

    public function index()
    {
        $role = strtolower(session()->get('role'));
        
        // Hanya Superadmin, Admin Prodi dan Pejabat yang bisa mengakses
        if (!in_array($role, ['superadmin', 'admin_prodi', 'pejabat'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $prodi_id = session()->get('prodi_id');
        
        $filterTanggal = $this->request->getGet('tanggal');
        $filterNama = $this->request->getGet('nama');
        $filterProdi = $this->request->getGet('prodi');
        $filterKelas = $this->request->getGet('kelas');
        $filterStatus = $this->request->getGet('status');

        $logbooks = $this->logbookModel->getAllLogbooksGlobal($role, $prodi_id, $filterTanggal, $filterNama, $filterProdi, $filterKelas, $filterStatus);

        // Rekapitulasi Statistik Sederhana
        $total = count($logbooks);
        $disetujui = 0;
        $pending = 0;
        $revisi_ditolak = 0;

        foreach ($logbooks as $log) {
            if ($log['status'] == 'disetujui') $disetujui++;
            elseif ($log['status'] == 'pending') $pending++;
            else $revisi_ditolak++;
        }
        
        // Ambil daftar prodi khusus untuk pejabat/superadmin
        $prodiList = [];
        if (in_array($role, ['superadmin', 'pejabat'])) {
            $prodiModel = new \App\Models\ProdiModel();
            $prodiList = $prodiModel->getOrderedProdi();
        }

        // Ambil daftar kelas untuk semua role yang punya akses laporan
        $userModel = new \App\Models\UserModel();
        $kelasList = $userModel->select('kelas')->where('kelas !=', null)->where('kelas !=', '')->distinct()->findAll();

        $data = [
            'title'          => 'Laporan Global Magang',
            'logbooks'       => $logbooks,
            'userRole'       => $role,
            'total'          => $total,
            'disetujui'      => $disetujui,
            'pending'        => $pending,
            'revisi_ditolak' => $revisi_ditolak,
            'filterTanggal'  => $filterTanggal,
            'filterNama'     => $filterNama,
            'filterProdi'    => $filterProdi,
            'filterKelas'    => $filterKelas,
            'filterStatus'   => $filterStatus,
            'prodiList'      => $prodiList,
            'kelasList'      => $kelasList
        ];

        return view('laporan/index', $data);
    }
}
