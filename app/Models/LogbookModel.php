<?php

namespace App\Models;

use CodeIgniter\Model;

class LogbookModel extends Model
{
    protected $table            = 'logbooks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penugasan_id',
        'user_id', 
        'tanggal', 
        'kegiatan', 
        'dokumentasi', 
        'status', 
        'catatan_pembimbing'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Fungsi custom untuk Dosen Pembimbing
    public function getLogbooksForPembimbing($pembimbing_id, $filterTanggal = null, $filterNama = null, $filterStatus = null, $perPage = 10)
    {
        $builder = $this->select('logbooks.*, users.nama as nama_taruna, users.nomor_induk as notar_taruna, prodi.nama_prodi, users.kelas, pm.tempat_magang as tempat_magang_logbook, pm.tahun_ajaran, pm.periode')
                    ->join('users', 'users.id = logbooks.user_id')
                    ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                    ->join('penugasan_magang pm', 'pm.id = logbooks.penugasan_id', 'left')
                    ->where('pm.pembimbing_id', $pembimbing_id);
                    
        if (!empty($filterTanggal)) {
            $separator = ' to ';
            if (strpos($filterTanggal, ' - ') !== false) {
                $separator = ' - ';
            } elseif (strpos($filterTanggal, ' sampai ') !== false) {
                $separator = ' sampai ';
            }
            
            $dates = explode($separator, $filterTanggal);
            $start_date = trim($dates[0]);
            $end_date = isset($dates[1]) ? trim($dates[1]) : $start_date;
            
            $builder->where('logbooks.tanggal >=', $start_date);
            $builder->where('logbooks.tanggal <=', $end_date);
        }

        if (!empty($filterNama)) {
            $builder->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }

        if (!empty($filterStatus)) {
            $builder->where('logbooks.status', $filterStatus);
        }

        $builder->orderBy("FIELD(logbooks.status, 'pending', 'revisi', 'ditolak', 'disetujui')", 'ASC', false);
        return $builder->orderBy('logbooks.tanggal', 'DESC')->paginate($perPage, 'logbooks');
    }


    // Fungsi custom untuk Laporan Global (Admin Prodi, Kaprodi, Direktur, Wadir, Kabag & Superadmin)
    public function getAllLogbooksGlobal($role, $prodi_id = null, $filterTanggal = null, $filterNama = null, $filterProdi = null, $filterKelas = null, $filterStatus = null, $perPage = 0)
    {
        $builder = $this->select('logbooks.*, users.nama as nama_taruna, users.nomor_induk as notar_taruna, prodi.nama_prodi, users.kelas, pm.tempat_magang as tempat_magang_logbook, pm.tahun_ajaran, pm.periode, pp.nama as nama_pembimbing')
                    ->join('users', 'users.id = logbooks.user_id')
                    ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                    ->join('penugasan_magang pm', 'pm.id = logbooks.penugasan_id', 'left')
                    ->join('users pp', 'pp.id = pm.pembimbing_id', 'left'); // new
                    
                    
        // Jika admin prodi / kaprodi, filter berdasarkan prodi_id miliknya
        if (in_array($role, ['admin_prodi', 'kaprodi']) && !empty($prodi_id)) {
            $builder->where('users.prodi_id', $prodi_id);
        } else if (!empty($filterProdi)) {
            $builder->where('users.prodi_id', $filterProdi);
        }

        if (!empty($filterTanggal)) {
            $separator = ' to ';
            if (strpos($filterTanggal, ' - ') !== false) {
                $separator = ' - ';
            } elseif (strpos($filterTanggal, ' sampai ') !== false) {
                $separator = ' sampai ';
            }
            
            $dates = explode($separator, $filterTanggal);
            $start_date = trim($dates[0]);
            $end_date = isset($dates[1]) ? trim($dates[1]) : $start_date;
            
            $builder->where('logbooks.tanggal >=', $start_date);
            $builder->where('logbooks.tanggal <=', $end_date);
        }

        if (!empty($filterNama)) {
            $builder->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }
        
        if (!empty($filterKelas)) {
            $builder->where('users.kelas', $filterKelas);
        }

        if (!empty($filterStatus)) {
            $builder->where('logbooks.status', $filterStatus);
        }

        // Jika $perPage > 0, gunakan pagination
        if ($perPage > 0) {
            return $builder->orderBy('logbooks.tanggal', 'DESC')->paginate($perPage, 'logbooks');
        }

        // Pejabat dan Superadmin bisa melihat semua (untuk statistik)
        return $builder->orderBy('logbooks.tanggal', 'DESC')->findAll();
    }
}
