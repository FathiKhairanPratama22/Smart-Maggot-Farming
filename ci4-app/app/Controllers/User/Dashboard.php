<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SensorModel;
use App\Models\ProductionModel;

class Dashboard extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $sensorModel = new SensorModel();
        $productionModel = new ProductionModel();

        $recentSensors = $sensorModel->orderBy('created_at', 'DESC')->limit(20)->find();
        $totalProduksi = $productionModel->selectSum('berat_maggot')->first();

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'recent_sensors' => $recentSensors,
                'total_produksi' => $totalProduksi['berat_maggot'] ?? 0
            ]
        ]);
    }
}
