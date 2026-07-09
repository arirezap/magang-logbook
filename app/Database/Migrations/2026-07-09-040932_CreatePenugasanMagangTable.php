<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenugasanMagangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'taruna_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pembimbing_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'periode' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
            ],
            'tempat_magang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status_aktif' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('taruna_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembimbing_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('penugasan_magang');
    }

    public function down()
    {
        $this->forge->dropTable('penugasan_magang');
    }
}
