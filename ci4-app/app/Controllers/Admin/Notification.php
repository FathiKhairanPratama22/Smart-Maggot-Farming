<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\NotificationModel;
use App\Models\SettingModel;

class Notification extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new NotificationModel();
        return $this->respond([
            'status' => 'success',
            'data'   => $model->orderBy('created_at', 'DESC')->findAll()
        ]);
    }

    public function settings()
    {
        $model = new SettingModel();
        return $this->respond([
            'status' => 'success',
            'data'   => $model->find(1)
        ]);
    }

    public function updateSettings()
    {
        $model = new SettingModel();
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        // Jika setting dengan ID 1 belum ada, buat baru
        if (!$model->find(1)) {
            $data['id'] = 1;
            $model->insert($data);
        } else {
            $model->update(1, $data);
        }

        if ($model->errors()) {
            return $this->failValidationErrors($model->errors());
        }

        return $this->respond(['status' => 'success', 'message' => 'Settings diperbarui']);
    }
}
