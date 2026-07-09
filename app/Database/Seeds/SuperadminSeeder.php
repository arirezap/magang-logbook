<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert 2 Prodi Tambahan (Karena TO sudah ada di MainSeeder)
        $prodiData = [
            [
                'nama_prodi' => 'Rekayasa Sistem Transportasi Jalan',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_prodi' => 'Teknologi Rekayasa Otomotif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        
        $this->db->table('prodi')->insertBatch($prodiData);

        // 2. Insert Superadmin
        $superadminData = [
            'nomor_induk'   => 'SUPERADMIN',
            'nama'          => 'Administrator Sistem PKTJ',
            'password'      => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'superadmin',
            'prodi_id'      => null,
            'jenjang'       => null,
            'kelas'         => null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('users')->insert($superadminData);
    }
}
