<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenugasanToLogbooks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('logbooks', [
            'penugasan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'user_id',
            ],
        ]);

        $this->forge->addForeignKey('penugasan_id', 'penugasan_magang', 'id', 'CASCADE', 'SET NULL');
        // Because addForeignKey via AddColumn is tricky in CI4 (it only works if you alter table manually sometimes),
        // we execute raw SQL for foreign key to be safe, or just use process constraint.
        // Wait, CI4 addForeignKey is for createTable. For alter table, we use $this->forge->processIndexes().
        $this->db->query('ALTER TABLE logbooks ADD CONSTRAINT logbooks_penugasan_id_foreign FOREIGN KEY (penugasan_id) REFERENCES penugasan_magang(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE logbooks DROP FOREIGN KEY logbooks_penugasan_id_foreign');
        $this->forge->dropColumn('logbooks', 'penugasan_id');
    }
}
