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
        $logbooks = $this->logbookModel->getAllLogbooksGlobal($role, $prodi_id);

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

        $data = [
            'title'          => 'Laporan Global Magang',
            'logbooks'       => $logbooks,
            'userRole'       => $role,
            'total'          => $total,
            'disetujui'      => $disetujui,
            'pending'        => $pending,
            'revisi_ditolak' => $revisi_ditolak
        ];

        return view('laporan/index', $data);
    }
}
