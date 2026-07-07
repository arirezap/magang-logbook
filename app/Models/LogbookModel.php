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
    public function getLogbooksForPembimbing($pembimbing_id)
    {
        return $this->select('logbooks.*, users.nama as nama_taruna, users.nomor_induk as notar_taruna, prodi.nama_prodi, users.kelas')
                    ->join('users', 'users.id = logbooks.user_id')
                    ->join('prodi', 'prodi.id = users.prodi_id', 'left') // Untuk informasi kelas Taruna
                    ->where('users.pembimbing_id', $pembimbing_id)
                    ->orderBy('logbooks.created_at', 'DESC') // Tampilkan yang terbaru dibuat di atas
                    ->findAll();
    }

    // Fungsi custom untuk Laporan Global (Admin Prodi, Pejabat & Superadmin)
    public function getAllLogbooksGlobal($role, $prodi_id, $filterTanggal = null, $filterNama = null, $filterProdi = null, $filterKelas = null)
    {
        $builder = $this->select('logbooks.*, users.nama as nama_taruna, users.nomor_induk as notar_taruna, prodi.nama_prodi, users.kelas, p.nama as nama_pembimbing')
                    ->join('users', 'users.id = logbooks.user_id')
                    ->join('prodi', 'prodi.id = users.prodi_id', 'left')
                    ->join('users p', 'p.id = users.pembimbing_id', 'left');
                    
        // Jika admin prodi, filter berdasarkan prodi_id miliknya
        if ($role === 'admin_prodi' && !empty($prodi_id)) {
            $builder->where('users.prodi_id', $prodi_id);
        } else if (!empty($filterProdi)) {
            $builder->where('users.prodi_id', $filterProdi);
        }

        if (!empty($filterTanggal)) {
            $builder->where('logbooks.tanggal', $filterTanggal);
        }

        if (!empty($filterNama)) {
            $builder->like('users.nama', $filterNama);
        }
        
        if (!empty($filterKelas)) {
            $builder->where('users.kelas', $filterKelas);
        }

        // Pejabat dan Superadmin bisa melihat semua
        return $builder->orderBy('logbooks.tanggal', 'DESC')->findAll();
    }
}
