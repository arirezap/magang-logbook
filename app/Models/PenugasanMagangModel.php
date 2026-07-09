<?php

namespace App\Models;

use CodeIgniter\Model;

class PenugasanMagangModel extends Model
{
    protected $table            = 'penugasan_magang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'taruna_id',
        'pembimbing_id',
        'tahun_ajaran',
        'periode',
        'tempat_magang',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active assignment for a given taruna
     */
    public function getActivePenugasan($tarunaId)
    {
        return $this->where('taruna_id', $tarunaId)
                    ->where('status_aktif', true)
                    ->first();
    }
}
