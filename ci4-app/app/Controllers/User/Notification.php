<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;
use App\Models\NotificationModel;

class Notification extends ResourceController
{
    protected $format = 'json';
    
    public function index()
    {
        $model = new NotificationModel();
        return $this->respond(['status' => 'success', 'data' => $model->orderBy('created_at', 'DESC')->findAll()]);
    }
}
