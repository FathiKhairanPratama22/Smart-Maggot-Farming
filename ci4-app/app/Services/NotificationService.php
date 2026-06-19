<?php

namespace App\Services;

use App\Models\SettingModel;
use App\Models\NotificationModel;

class NotificationService
{
    public function checkSensorThresholds($suhu, $kelembaban)
    {
        $settingModel = new SettingModel();
        // Assuming settings are on ID 1
        $settings = $settingModel->find(1);

        if (!$settings) {
            return; // No thresholds set
        }

        $notificationModel = new NotificationModel();
        $pesan = [];

        if ($suhu > $settings['suhu_max']) {
            $pesan[] = "Suhu terlalu TINGGI ({$suhu}°C)";
        } elseif ($suhu < $settings['suhu_min']) {
            $pesan[] = "Suhu terlalu RENDAH ({$suhu}°C)";
        }

        if ($kelembaban > $settings['kelembaban_max']) {
            $pesan[] = "Kelembaban terlalu TINGGI ({$kelembaban}%)";
        } elseif ($kelembaban < $settings['kelembaban_min']) {
            $pesan[] = "Kelembaban terlalu RENDAH ({$kelembaban}%)";
        }

        foreach ($pesan as $msg) {
            $notificationModel->insert([
                'pesan' => $msg,
                'jenis' => 'warning',
                'is_read' => 0
            ]);

            // Emit WebSocket Alert
            try {
                $client = \Config\Services::curlrequest();
                $client->post('http://localhost:3000/emit', [
                    'json' => [
                        'event' => 'notification_alert',
                        'data'  => [
                            'pesan' => $msg,
                            'jenis' => 'warning',
                            'created_at' => date('Y-m-d H:i:s')
                        ]
                    ],
                    'timeout' => 2
                ]);
            } catch (\Exception $e) {}
        }
    }
}
