<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Increase memory and execution time limit for massive seeding
        ini_set('memory_limit', '1G');
        ini_set('max_execution_time', 0);

        $db = \Config\Database::connect();
        
        $prodiNames = [
            'REKAYASA SISTEM TRANSPORTASI JALAN' => 'RSTJ',
            'TEKNOLOGI REKAYASA OTOMOTIF' => 'TRO',
            'TEKNOLOGI OTOMOTIF' => 'TO'
        ];
        
        $prodiIds = [];
        $dosenIds = [];
        $tarunaIds = [];
        $adminProdiIds = [];
        
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        echo "Menyiapkan Data Prodi...\n";
        foreach ($prodiNames as $nama => $singkatan) {
            $existingProdi = $db->table('prodi')->where('nama_prodi', $nama)->get()->getRowArray();
            if ($existingProdi) {
                $prodiIds[] = $existingProdi['id'];
                $prodiId = $existingProdi['id'];
            } else {
                $db->table('prodi')->insert([
                    'nama_prodi' => $nama,
                    'singkatan' => $singkatan,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $prodiId = $db->insertID();
                $prodiIds[] = $prodiId;
            }
            
            // Cek admin prodi
            $existingAdmin = $db->table('users')->where('role', 'admin_prodi')->where('prodi_id', $prodiId)->get()->getRowArray();
            if (!$existingAdmin) {
                $db->table('users')->insert([
                    'nomor_induk' => 'ADMIN-' . $singkatan,
                    'nama' => 'Admin ' . $singkatan,
                    'password' => $password,
                    'role' => 'admin_prodi',
                    'prodi_id' => $prodiId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        
        echo "Menyiapkan 30 Data Dosen (10 per Prodi)...\n";
        $faker = \Faker\Factory::create('id_ID');
        
        $currentDosenCount = $db->table('users')->where('role', 'pembimbing')->countAllResults();
        $dosenNeeded = max(0, 30 - $currentDosenCount);
        
        for ($i = 0; $i < $dosenNeeded; $i++) {
            $prodiId = $prodiIds[$i % count($prodiIds)];
            $db->table('users')->insert([
                'nomor_induk' => '19' . $faker->numerify('##############'),
                'nama' => $faker->name . ', S.T., M.T.',
                'password' => $password,
                'role' => 'pembimbing',
                'prodi_id' => $prodiId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        // Ambil semua ID Dosen (pastikan ada 30)
        $allDosen = $db->table('users')->where('role', 'pembimbing')->limit(30)->get()->getResultArray();
        
        echo "Menyiapkan 300 Data Taruna...\n";
        $currentTarunaCount = $db->table('users')->where('role', 'taruna')->countAllResults();
        $tarunaNeeded = max(0, 300 - $currentTarunaCount);
        
        $newTarunaIds = [];
        for ($i = 0; $i < $tarunaNeeded; $i++) {
            $prodiId = $prodiIds[$i % count($prodiIds)];
            $db->table('users')->insert([
                'nomor_induk' => '25' . str_pad($i + $currentTarunaCount, 6, '0', STR_PAD_LEFT),
                'nama' => $faker->name,
                'password' => $password,
                'role' => 'taruna',
                'prodi_id' => $prodiId,
                'jenjang' => ($prodiId == 1 || $prodiId == 2) ? 'D4' : 'D3',
                'kelas' => ($i % 2 == 0) ? 'A' : 'B',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $newTarunaIds[] = $db->insertID();
        }
        
        // Ambil 300 taruna
        $allTaruna = $db->table('users')->where('role', 'taruna')->limit(300)->get()->getResultArray();
        
        echo "Menyiapkan 300 Penugasan Magang untuk Tahun Ajaran 2026/2027...\n";
        $penugasanMap = []; // taruna_id => penugasan_id
        
        foreach ($allTaruna as $index => $taruna) {
            $dosen = $allDosen[$index % count($allDosen)];
            
            // Cek apakah sudah ada penugasan
            $existingPenugasan = $db->table('penugasan_magang')
                                    ->where('taruna_id', $taruna['id'])
                                    ->where('tahun_ajaran', '2026/2027')
                                    ->get()->getRowArray();
                                    
            if ($existingPenugasan) {
                $penugasanMap[$taruna['id']] = $existingPenugasan['id'];
            } else {
                $db->table('penugasan_magang')->insert([
                    'taruna_id' => $taruna['id'],
                    'pembimbing_id' => $dosen['id'],
                    'tahun_ajaran' => '2026/2027',
                    'periode' => '1',
                    'tempat_magang' => 'Dinas Perhubungan Kota ' . $faker->city,
                    'tanggal_mulai' => date('Y-m-d', strtotime('-1 month')),
                    'tanggal_selesai' => date('Y-m-d', strtotime('+5 months')),
                    'status_aktif' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $penugasanMap[$taruna['id']] = $db->insertID();
            }
        }
        
        echo "Menyiapkan Data Logbook 1 Bulan Terakhir (Ini akan memakan waktu sekitar 10-20 detik)...\n";
        
        $tarunaIds = array_column($allTaruna, 'id');
        if (!empty($tarunaIds)) {
            $db->table('logbooks')->whereIn('user_id', $tarunaIds)->delete();
        }
        
        $logbooksToInsert = [];
        $startDate = Time::now()->subMonths(1);
        $endDate = Time::now();
        
        $batchCount = 0;
        
        foreach ($allTaruna as $taruna) {
            if (!isset($penugasanMap[$taruna['id']])) continue;
            
            $penugasanId = $penugasanMap[$taruna['id']];
            
            // Looping dari start_date ke end_date
            $currentDate = clone $startDate;
            
            while ($currentDate->isBefore($endDate) || $currentDate->equals($endDate)) {
                // Skip sabtu minggu
                if ($currentDate->format('N') >= 6) {
                    $currentDate = $currentDate->addDays(1);
                    continue;
                }
                
                // Cek apakah logbook sudah ada di tanggal ini
                // Untuk optimasi di seeder, kita asumsikan belum ada, atau kita truncate saja kalau mau fresh.
                // Karena kita tidak truncate, kita akan cek.
                // Tapi cek 9000 kali itu lambat. Jadi kita pakai insert ignore atau kita bersihkan dulu logbooks 1 bulan terakhir.
                // Atau lebih amannya kita anggap taruna baru jadi pasti kosong.
                
                $status = $faker->randomElement(['pending', 'disetujui', 'disetujui', 'disetujui', 'revisi']); // Banyakin disetujui
                
                $logbooksToInsert[] = [
                    'user_id' => $taruna['id'],
                    'penugasan_id' => $penugasanId,
                    'tanggal' => $currentDate->toDateString(),
                    'kegiatan' => 'Melakukan pengujian kendaraan bermotor (KIR) untuk ' . rand(10, 30) . ' unit kendaraan ' . $faker->randomElement(['angkutan umum', 'bus', 'truk']),
                    'dokumentasi' => 'default.png', // Ganti null jika nullable
                    'status' => $status,
                    'catatan_pembimbing' => ($status == 'revisi') ? 'Tolong lengkapi dengan foto dokumentasi yang lebih jelas.' : (($status == 'disetujui') ? 'Bagus, terus tingkatkan.' : null),
                    'created_at' => $currentDate->toDateTimeString(),
                    'updated_at' => $currentDate->toDateTimeString()
                ];
                
                $currentDate = $currentDate->addDays(1);
                
                if (count($logbooksToInsert) >= 1000) {
                    $db->table('logbooks')->insertBatch($logbooksToInsert);
                    $batchCount += count($logbooksToInsert);
                    echo "\rTelah menyisipkan " . number_format($batchCount) . " logbook...";
                    $logbooksToInsert = [];
                }
            }
        }
        
        if (count($logbooksToInsert) > 0) {
            $db->table('logbooks')->insertBatch($logbooksToInsert);
            $batchCount += count($logbooksToInsert);
        }
        
        echo "\nData Demo Berhasil Digenerate! Total Logbook: " . number_format($batchCount) . "\n";
    }
}
