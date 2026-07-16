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
        'role_kedua',
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
    public function getUsersWithDetails($user_role = null, $prodi_id = null, $filterNama = null, $filterProdi = null, $filterRole = null, $perPage = 0, $sort = 'nama', $order = 'ASC')
    {
        $builder = $this->select('users.*, prodi.nama_prodi')
                        ->join('prodi', 'prodi.id = users.prodi_id', 'left');

        if ($user_role === 'superadmin') {
            // Superadmin melihat semua role termasuk superadmin lainnya
        } elseif (in_array($user_role, ['admin_prodi', 'kaprodi'])) {
            // Admin Prodi dan Kaprodi hanya mengelola Dosen dan Taruna di prodinya sendiri
            // DAN Dosen (pembimbing) dari prodi lain yang membimbing taruna dari prodinya
            $builder->whereIn('users.role', ['taruna', 'pembimbing', 'admin_prodi', 'kaprodi'])
                    ->groupStart()
                        ->where('users.prodi_id', $prodi_id)
                        ->orWhere("users.id IN (
                            SELECT pm.pembimbing_id 
                            FROM penugasan_magang pm 
                            JOIN users u2 ON u2.id = pm.taruna_id 
                            WHERE u2.prodi_id = " . $this->db->escape($prodi_id) . "
                        )", null, false)
                    ->groupEnd();
        } else {
            // Direktur / Wadir / Kabag melihat Dosen dan Taruna dari seluruh prodi
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

        $validSorts = [
            'nama' => 'users.nama',
            'role' => 'users.role',
            'prodi' => 'prodi.nama_prodi',
            'pembimbing' => 'users.role' // Pembimbing display is tricky since it requires another join, we default to role or nama
        ];
        $sortColumn = $validSorts[$sort] ?? 'users.nama';

        if ($perPage > 0) {
            return $builder->orderBy($sortColumn, $order)
                           ->paginate($perPage, 'users');
        }

        return $builder->orderBy($sortColumn, $order)
                       ->findAll();
    }
}
