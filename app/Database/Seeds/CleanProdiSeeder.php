<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanProdiSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Cari semua prodi bernama 'Teknologi Otomotif'
        $prodis = $db->table('prodi')->where('nama_prodi', 'Teknologi Otomotif')->get()->getResultArray();

        if (count($prodis) > 1) {
            // Ambil ID yang pertama sebagai primary (yang dipertahankan)
            $primaryId = $prodis[0]['id'];
            
            // Loop untuk sisanya (yang duplikat)
            for ($i = 1; $i < count($prodis); $i++) {
                $duplicateId = $prodis[$i]['id'];
                
                // Pindahkan semua user (Admin/Dosen/Taruna) dari prodi duplikat ke prodi primary
                $db->table('users')->where('prodi_id', $duplicateId)->update(['prodi_id' => $primaryId]);
                
                // Hapus prodi duplikat tersebut
                $db->table('prodi')->where('id', $duplicateId)->delete();
                
                echo "Berhasil menghapus duplikat Prodi TO (ID: $duplicateId) dan memindahkan data ke Prodi TO utama (ID: $primaryId)\n";
            }
        } else {
            echo "Aman. Hanya ada 1 Prodi Teknologi Otomotif.\n";
        }
    }
}
