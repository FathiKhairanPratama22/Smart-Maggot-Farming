<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        if (!$user['is_active']) {
            return $this->failUnauthorized('User is disabled');
        }

        if (!password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Invalid password');
        }

        $sessionData = [
            'id'         => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true
        ];

        session()->set($sessionData);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => [
                'name' => $user['name'],
                'role' => $user['role']
            ]
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login'); 
    }
}
