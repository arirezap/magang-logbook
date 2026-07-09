<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_induk', 
        'nama', 
        'password', 
        'role', 
        'prodi_id', 
        'jenjang',
        'kelas'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Mengambil data pengguna beserta nama prodi dan nama pembimbingnya
    public function getUsersWithDetails($user_role = null, $prodi_id = null, $filterNama = null, $filterProdi = null, $filterRole = null)
    {
        $builder = $this->select('users.*, prodi.nama_prodi')
                        ->join('prodi', 'prodi.id = users.prodi_id', 'left');

        if ($user_role === 'superadmin') {
            // Superadmin melihat semua role kecuali dirinya sendiri
            $builder->where('users.role !=', 'superadmin');
        } elseif ($user_role === 'admin_prodi') {
            // Admin Prodi hanya mengelola Dosen dan Taruna di prodinya sendiri
            $builder->whereIn('users.role', ['taruna', 'pembimbing'])
                    ->where('users.prodi_id', $prodi_id);
        } else {
            // Pejabat melihat Dosen dan Taruna dari seluruh prodi
            $builder->whereIn('users.role', ['taruna', 'pembimbing']);
        }

        // Terapkan Filter
        if (!empty($filterNama)) {
            $builder->groupStart()
                    ->like('users.nama', $filterNama)
                    ->orLike('users.nomor_induk', $filterNama)
                    ->groupEnd();
        }
        if (!empty($filterProdi)) {
            $builder->where('users.prodi_id', $filterProdi);
        }
        if (!empty($filterRole)) {
            $builder->where('users.role', $filterRole);
        }

        return $builder->orderBy('users.role', 'ASC')->orderBy('users.nama', 'ASC')->findAll();
    }
}
