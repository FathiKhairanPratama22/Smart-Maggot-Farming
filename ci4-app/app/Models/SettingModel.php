<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['suhu_min', 'suhu_max', 'kelembaban_min', 'kelembaban_max'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'suhu_min'       => 'required|numeric',
        'suhu_max'       => 'required|numeric',
        'kelembaban_min' => 'required|numeric',
        'kelembaban_max' => 'required|numeric'
    ];
}
