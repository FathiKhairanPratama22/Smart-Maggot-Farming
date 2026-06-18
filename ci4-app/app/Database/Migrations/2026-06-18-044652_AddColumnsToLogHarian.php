<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToLogHarian extends Migration
{
    public function up()
    {
        $this->forge->addColumn('log_harian', [
            'jumlah_pupuk' => [
                'type' => 'FLOAT',
                'null' => true,
                'default' => 0
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('log_harian', ['jumlah_pupuk', 'catatan']);
    }
}
