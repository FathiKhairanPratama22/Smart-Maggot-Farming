<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductionModel;

class Production extends ResourceController
{
    protected $format = 'json';
    
    public function index()
    {
        $model = new ProductionModel();
        return $this->respond(['status' => 'success', 'data' => $model->orderBy('tanggal', 'DESC')->findAll()]);
    }
}
