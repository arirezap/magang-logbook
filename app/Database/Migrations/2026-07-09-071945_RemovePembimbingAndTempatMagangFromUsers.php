<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePembimbingAndTempatMagangFromUsers extends Migration
{
    public function up()
    {
        // 1. Drop foreign key untuk pembimbing_id jika ada
        try {
            $this->forge->dropForeignKey('users', 'users_pembimbing_id_foreign');
        } catch (\Exception $e) {
            // Abaikan jika error / tidak ada foreign key tersebut
        }

        // 2. Drop kolom
        $this->forge->dropColumn('users', ['tempat_magang', 'pembimbing_id']);
    }

    public function down()
    {
        $fields = [
            'pembimbing_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'tempat_magang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Tambahkan kembali foreign key (ini cukup opsional pada rollback)
        // $this->forge->addForeignKey('pembimbing_id', 'users', 'id', 'CASCADE', 'SET NULL');
    }
}
