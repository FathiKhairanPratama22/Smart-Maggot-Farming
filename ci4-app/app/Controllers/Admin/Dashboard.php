<?php

namespace App\Controllers\Admin;

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

        // Ambil data sensor terbaru
        $recentSensors = $sensorModel->orderBy('created_at', 'DESC')->limit(50)->find();

        // Hitung total produksi
        $totalProduksi = $productionModel->selectSum('berat_maggot')->first();
        
        // Produksi hari ini
        $hariIni = date('Y-m-d');
        $produksiHariIni = $productionModel->where('tanggal', $hariIni)->selectSum('berat_maggot')->first();

        // Produksi bulan ini
        $bulanIni = date('Y-m');
        $produksiBulanIni = $productionModel->like('tanggal', $bulanIni, 'after')->selectSum('berat_maggot')->first();

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'recent_sensors'      => $recentSensors,
                'summary_production'  => [
                    'total'      => $totalProduksi['berat_maggot'] ?? 0,
                    'hari_ini'   => $produksiHariIni['berat_maggot'] ?? 0,
                    'bulan_ini'  => $produksiBulanIni['berat_maggot'] ?? 0
                ]
            ]
        ]);
    }
}
