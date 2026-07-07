<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class MassiveSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $faker = Factory::create('id_ID');

        // 1. Ambil data Prodi
        $prodis = $db->table('prodi')->get()->getResultArray();
        
        if (empty($prodis)) {
            echo "Error: Tabel Prodi kosong! Silakan jalankan seeder prodi terlebih dahulu.\n";
            return;
        }

        $prodiMap = [];
        foreach ($prodis as $p) {
            $prodiMap[$p['nama_prodi']] = $p['id'];
        }

        $prodiIds = array_column($prodis, 'id');
        $password = password_hash('password123', PASSWORD_DEFAULT);

        // 2. Generate 4 Pejabat
        $pejabatData = [
            ['nomor_induk' => 'DIR001', 'nama' => 'I Made Dwi Jendra, S.Si., MT.', 'role' => 'pejabat'],
            ['nomor_induk' => 'WADIR001', 'nama' => 'Wadir 1 Akademik', 'role' => 'pejabat'],
            ['nomor_induk' => 'WADIR002', 'nama' => 'Wadir 2 Umum', 'role' => 'pejabat'],
            ['nomor_induk' => 'WADIR003', 'nama' => 'Wadir 3 Kemahasiswaan', 'role' => 'pejabat'],
        ];

        foreach ($pejabatData as $p) {
            // Cek existensi agar aman dijalankan ulang
            if ($db->table('users')->where('nomor_induk', $p['nomor_induk'])->countAllResults() == 0) {
                $db->table('users')->insert([
                    'nomor_induk' => $p['nomor_induk'],
                    'nama' => $p['nama'],
                    'password' => $password,
                    'role' => $p['role'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 3. Generate 10 Dosen
        // Kita sebar 10 dosen ini ke 3 prodi (4, 3, 3)
        $dosenList = [];
        $dosenCount = 1;
        
        foreach ($prodiIds as $index => $prodiId) {
            // Prodi pertama dapat 4 dosen, lainnya 3 dosen
            $quota = ($index == 0) ? 4 : 3;
            
            for ($i = 0; $i < $quota; $i++) {
                $nip = 'DSN0' . str_pad($dosenCount, 2, '0', STR_PAD_LEFT);
                $namaDosen = $faker->name . ', M.T.';
                
                if ($db->table('users')->where('nomor_induk', $nip)->countAllResults() == 0) {
                    $db->table('users')->insert([
                        'nomor_induk' => $nip,
                        'nama' => $namaDosen,
                        'password' => $password,
                        'role' => 'pembimbing',
                        'prodi_id' => $prodiId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $dosenId = $db->insertID();
                } else {
                    $dsnRow = $db->table('users')->where('nomor_induk', $nip)->get()->getRowArray();
                    $dosenId = $dsnRow['id'];
                }

                $dosenList[$prodiId][] = $dosenId;
                $dosenCount++;
            }
        }

        // 4. Generate 50 Taruna
        // Kita sebar 50 taruna ke 3 prodi (misal: 17, 17, 16)
        $tarunaCount = 1;
        $jenjangMap = [
            'Rekayasa Sistem Transportasi Jalan' => 'D4',
            'Teknologi Rekayasa Otomotif' => 'D4',
            'Teknologi Otomotif' => 'D3'
        ];

        foreach ($prodis as $index => $prodi) {
            $quota = ($index == 2) ? 16 : 17;
            $prodiId = $prodi['id'];
            $jenjang = $jenjangMap[$prodi['nama_prodi']] ?? 'D4';
            $dosensInThisProdi = $dosenList[$prodiId];
            
            for ($i = 0; $i < $quota; $i++) {
                $notar = 'TAR' . str_pad($tarunaCount, 3, '0', STR_PAD_LEFT);
                $namaTaruna = $faker->name;
                
                // Pilih dosen secara round-robin atau random agar merata
                $dosenId = $dosensInThisProdi[$i % count($dosensInThisProdi)];
                $kelas = ($i % 2 == 0) ? 'A' : 'B'; // Bagi kelas A dan B
                
                if ($db->table('users')->where('nomor_induk', $notar)->countAllResults() == 0) {
                    $db->table('users')->insert([
                        'nomor_induk' => $notar,
                        'nama' => $namaTaruna,
                        'password' => $password,
                        'role' => 'taruna',
                        'prodi_id' => $prodiId,
                        'pembimbing_id' => $dosenId,
                        'jenjang' => $jenjang,
                        'kelas' => $kelas,
                        'tempat_magang' => 'Dinas Perhubungan ' . $faker->city,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                $tarunaCount++;
            }
        }
        
        echo "Data Pejabat, Dosen, dan Taruna berhasil digenerate!\n";
    }
}
