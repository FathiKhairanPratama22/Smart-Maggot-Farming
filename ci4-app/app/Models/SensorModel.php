<?php

namespace App\Models;

use CodeIgniter\Model;

class SensorModel extends Model
{
    protected $table            = 'log_sensor';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['suhu', 'kelembaban', 'waktu'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
}
