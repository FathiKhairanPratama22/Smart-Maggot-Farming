<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductionModel;

class Production extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new ProductionModel();
        return $this->respond([
            'status' => 'success',
            'data'   => $model->orderBy('tanggal', 'DESC')->findAll()
        ]);
    }

    public function create()
    {
        $model = new ProductionModel();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Data produksi ditambahkan']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function update($id = null)
    {
        $model = new ProductionModel();
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if ($model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Data produksi diperbarui']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function delete($id = null)
    {
        $model = new ProductionModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'Data dihapus']);
        }
        return $this->failNotFound();
    }
}
