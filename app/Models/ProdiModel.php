<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdiModel extends Model
{
    protected $table            = 'prodi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_prodi'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Mengambil Program Studi dengan urutan prioritas: RSTJ, TRO, TO
    public function getOrderedProdi()
    {
        $list = $this->findAll();
        
        usort($list, function($a, $b) {
            $order = [
                'rekayasa sistem transportasi jalan' => 1,
                'teknologi rekayasa otomotif' => 2,
                'teknologi otomotif' => 3
            ];
            
            $nameA = strtolower(trim($a['nama_prodi']));
            $nameB = strtolower(trim($b['nama_prodi']));
            
            $posA = isset($order[$nameA]) ? $order[$nameA] : 999;
            $posB = isset($order[$nameB]) ? $order[$nameB] : 999;
            
            if ($posA === $posB) {
                return strcmp($nameA, $nameB);
            }
            
            return $posA - $posB;
        });
        
        return $list;
    }
}
