<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleKeduaToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'role_kedua' => [
                'type' => 'ENUM',
                'constraint' => ['superadmin', 'admin_prodi', 'kaprodi', 'pejabat', 'direktur', 'wadir', 'kabag'],
                'null' => true,
                'default' => null,
                'after' => 'role'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'role_kedua');
    }
}
