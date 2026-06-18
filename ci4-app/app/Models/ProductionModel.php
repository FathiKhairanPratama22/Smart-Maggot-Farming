<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionModel extends Model
{
    protected $table            = 'log_harian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['berat_maggot', 'berat_pakan', 'tanggal', 'jumlah_pupuk', 'catatan'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
}
