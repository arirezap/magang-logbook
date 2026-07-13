<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueConstraintsToPreventRaceConditions extends Migration
{
    public function up()
    {
        // Add unique constraint to penugasan_magang table
        $this->db->query('ALTER TABLE penugasan_magang ADD CONSTRAINT unique_taruna_periode UNIQUE (taruna_id, tahun_ajaran, periode)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE logbooks DROP INDEX unique_user_tanggal');
        $this->db->query('ALTER TABLE penugasan_magang DROP INDEX unique_taruna_periode');
    }
}
