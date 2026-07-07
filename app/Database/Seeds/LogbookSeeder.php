<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class LogbookSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $faker = Factory::create('id_ID');

        // Ambil semua taruna
        $tarunas = $db->table('users')->where('role', 'taruna')->get()->getResultArray();

        if (empty($tarunas)) {
            echo "Error: Tidak ada data taruna. Silakan jalankan MassiveSeeder terlebih dahulu.\n";
            return;
        }

        $logbooksData = [];
        $statuses = ['pending', 'disetujui', 'revisi', 'ditolak'];

        foreach ($tarunas as $taruna) {
            // Buat 5 logbook untuk setiap taruna (misal 5 hari terakhir)
            for ($i = 0; $i < 5; $i++) {
                $status = $statuses[array_rand($statuses)];
                
                // Jika status disetujui, revisi, atau ditolak, beri catatan pembimbing
                $catatan = null;
                if ($status !== 'pending') {
                    $catatan = $faker->sentence(6);
                }

                // Tanggal mundur (hari ini - $i hari)
                $tanggal = date('Y-m-d', strtotime("-$i days"));

                $logbooksData[] = [
                    'user_id'            => $taruna['id'],
                    'tanggal'            => $tanggal,
                    'kegiatan'           => "Melakukan kegiatan magang harian: " . rtrim($faker->sentence(8), '.') . " di tempat magang.",
                    'dokumentasi'        => 'https://drive.google.com/file/d/dummy-link-' . rand(1000, 9999) . '/view',
                    'status'             => $status,
                    'catatan_pembimbing' => $catatan,
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Insert batch ke tabel logbooks
        $db->table('logbooks')->insertBatch($logbooksData);
        echo "Berhasil mengenerate " . count($logbooksData) . " data logbook dummy untuk taruna!\n";
    }
}
