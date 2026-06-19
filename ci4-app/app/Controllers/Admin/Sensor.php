<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SensorModel;

class Sensor extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $sensorModel = new SensorModel();
        
        // Filter parameters
        $tanggal = $this->request->getVar('tanggal');
        $minSuhu = $this->request->getVar('min_suhu');
        $maxSuhu = $this->request->getVar('max_suhu');
        
        if ($tanggal) {
            $sensorModel->like('created_at', $tanggal, 'after');
        }
        if ($minSuhu !== null) {
            $sensorModel->where('suhu >=', $minSuhu);
        }
        if ($maxSuhu !== null) {
            $sensorModel->where('suhu <=', $maxSuhu);
        }

        // Pagination
        $data = $sensorModel->orderBy('created_at', 'DESC')->paginate(20);
        $pager = $sensorModel->pager;

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
            'pager'  => $pager->getDetails()
        ]);
    }

    public function delete($id = null)
    {
        $sensorModel = new SensorModel();
        if ($sensorModel->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'Data sensor dihapus']);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }
}
