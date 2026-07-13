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
        
        // Hanya Superadmin, Admin Prodi, Kaprodi, Direktur, Wadir, dan Kabag yang bisa mengakses
        if (!in_array($role, ['superadmin', 'admin_prodi', 'kaprodi', 'direktur', 'wadir', 'kabag'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $prodi_id = session()->get('prodi_id');
        
        $filterTanggal = $this->request->getGet('tanggal');
        $filterNama = $this->request->getGet('nama');
        $filterProdi = $this->request->getGet('prodi');
        $filterKelas = $this->request->getGet('kelas');
        $filterStatus = $this->request->getGet('status');

        $perPage = $this->request->getGet('per_page') ?? 10;
        
        // 1. Ambil semua logbook sesuai filter untuk menghitung statistik (tanpa paginasi)
        $allLogbooksForStats = $this->logbookModel->getAllLogbooksGlobal($role, $prodi_id, $filterTanggal, $filterNama, $filterProdi, $filterKelas, $filterStatus);

        // Rekapitulasi Statistik Sederhana
        $total = count($allLogbooksForStats);
        $disetujui = 0;
        $pending = 0;
        $revisi_ditolak = 0;

        foreach ($allLogbooksForStats as $log) {
            if ($log['status'] == 'disetujui') $disetujui++;
            elseif ($log['status'] == 'pending') $pending++;
            else $revisi_ditolak++;
        }

        // 2. Ambil logbook dengan paginasi untuk ditampilkan di tabel
        $logbooks = $this->logbookModel->getAllLogbooksGlobal($role, $prodi_id, $filterTanggal, $filterNama, $filterProdi, $filterKelas, $filterStatus, $perPage);
        $pager = $this->logbookModel->pager;
        
        // Ambil daftar prodi khusus untuk direktur/wadir/kabag/superadmin
        $prodiList = [];
        if (in_array($role, ['superadmin', 'direktur', 'wadir', 'kabag'])) {
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
            'userRole'       => $role,
            'perPage'        => $perPage,
            'pager'          => $pager,
            'kelasList'      => $kelasList
        ];

        return view('laporan/index', $data);
    }
}
