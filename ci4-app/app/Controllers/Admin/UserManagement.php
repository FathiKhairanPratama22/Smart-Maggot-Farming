<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class UserManagement extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new UserModel();
        // Hide password hash
        $users = $model->select('id, name, email, role, is_active, created_at, updated_at')->findAll();
        return $this->respond(['status' => 'success', 'data' => $users]);
    }

    public function create()
    {
        $model = new UserModel();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'User dibuat']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function update($id = null)
    {
        $model = new UserModel();
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (isset($data['password'])) {
            if (empty($data['password'])) unset($data['password']);
            else $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'User diperbarui']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function delete($id = null)
    {
        $model = new UserModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'User dihapus']);
        }
        return $this->failNotFound();
    }
}
