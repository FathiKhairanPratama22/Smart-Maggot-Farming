<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SensorModel;
use App\Services\NotificationService;

class Sensor extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        // Mendapatkan raw input JSON
        $json = $this->request->getJSON();

        if ($json) {
            $suhu = $json->suhu ?? null;
            $kelembaban = $json->kelembaban ?? null;
        } else {
            $suhu = $this->request->getPost('suhu');
            $kelembaban = $this->request->getPost('kelembaban');
        }

        if ($suhu === null || $kelembaban === null) {
            return $this->failValidationErrors('Suhu dan Kelembaban wajib diisi');
        }

        $sensorModel = new SensorModel();
        $sensorModel->insert([
            'suhu'       => $suhu,
            'kelembaban' => $kelembaban
        ]);

        // Panggil service notifikasi
        $notifService = new NotificationService();
        $notifService->checkSensorThresholds($suhu, $kelembaban);

        // Emit WebSocket Event via Node.js
        try {
            $client = \Config\Services::curlrequest();
            $client->post('http://localhost:3000/emit', [
                'json' => [
                    'event' => 'sensor_update',
                    'data'  => [
                        'suhu' => $suhu,
                        'kelembaban' => $kelembaban,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ],
                'timeout' => 2 // timeout cepat agar tidak memblokir API CI4 jika node mati
            ]);
        } catch (\Exception $e) {
            // Abaikan error jika nodejs mati
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Data sensor berhasil disimpan'
        ]);
    }
}
