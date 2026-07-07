<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyUsersRoleEnum extends Migration
{
    public function up()
    {
        // Ubah struktur kolom role untuk mendukung superadmin
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['taruna', 'pembimbing', 'admin_prodi', 'pejabat', 'superadmin'],
                'default'    => 'taruna',
            ],
        ];
        
        $this->forge->modifyColumn('users', $fields);

        // Perbaiki data superadmin yang sudah terlanjur salah input (kosong) karena penolakan ENUM sebelumnya
        $db = \Config\Database::connect();
        $db->table('users')->where('nomor_induk', 'SUPERADMIN')->update(['role' => 'superadmin']);
    }

    public function down()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['taruna', 'pembimbing', 'admin_prodi', 'pejabat'],
                'default'    => 'taruna',
            ],
        ];
        
        $this->forge->modifyColumn('users', $fields);
    }
}
