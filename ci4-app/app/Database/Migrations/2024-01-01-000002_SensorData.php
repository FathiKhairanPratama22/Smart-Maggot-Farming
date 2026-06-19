<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SensorData extends Migration
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
            'suhu' => [
                'type'       => 'FLOAT',
            ],
            'kelembaban' => [
                'type'       => 'FLOAT',
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
        $this->forge->createTable('sensor_data');
    }

    public function down()
    {
        $this->forge->dropTable('sensor_data');
    }
}
