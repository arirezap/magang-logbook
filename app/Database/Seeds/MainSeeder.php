<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert Prodi
        $prodiData = [
            'nama_prodi' => 'Teknologi Otomotif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('prodi')->insert($prodiData);
        $prodi_id = $this->db->insertID();

        // 2. Insert Pembimbing Dummy
        $pembimbingData = [
            'nomor_induk'   => '198001012000031001', // Dummy NIP
            'nama'          => 'Budi Santoso, S.T., M.T.',
            'password'      => password_hash('password123', PASSWORD_DEFAULT), // Default password
            'role'          => 'pembimbing',
            'prodi_id'      => $prodi_id,
            'pembimbing_id' => null,
            'jenjang'       => null,
            'kelas'         => null,
            'tempat_magang' => null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($pembimbingData);
        $pembimbing_id = $this->db->insertID();

        // 3. Insert Taruna (IHZA ILHAM SUROSO)
        $tarunaData = [
            'nomor_induk'   => '25031013', // NOTAR
            'nama'          => 'IHZA ILHAM SUROSO',
            'password'      => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'taruna',
            'prodi_id'      => $prodi_id,
            'pembimbing_id' => $pembimbing_id,
            'jenjang'       => 'D3',
            'kelas'         => 'A',
            'tempat_magang' => 'DISHUB KOTA TEGAL',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($tarunaData);

        // 4. Insert Admin Prodi Dummy
        $adminData = [
            'nomor_induk'   => 'ADMIN-TO',
            'nama'          => 'Admin Prodi TO',
            'password'      => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'admin_prodi',
            'prodi_id'      => $prodi_id,
            'pembimbing_id' => null,
            'jenjang'       => null,
            'kelas'         => null,
            'tempat_magang' => null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($adminData);
        
        // 5. Insert Pejabat Dummy
        $pejabatData = [
            'nomor_induk'   => 'PEJABAT',
            'nama'          => 'Direktur PKTJ',
            'password'      => password_hash('password123', PASSWORD_DEFAULT),
            'role'          => 'pejabat',
            'prodi_id'      => null,
            'pembimbing_id' => null,
            'jenjang'       => null,
            'kelas'         => null,
            'tempat_magang' => null,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($pejabatData);
    }
}
