<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ArticleModel;
use App\Models\CategoryModel;

class Article extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new ArticleModel();
        $articles = $model->select('artikel.*, kategori.nama_kategori')
                          ->join('kategori', 'kategori.id = artikel.kategori_id')
                          ->orderBy('created_at', 'DESC')
                          ->findAll();

        return $this->respond(['status' => 'success', 'data' => $articles]);
    }

    public function create()
    {
        $model = new ArticleModel();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if ($model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'Artikel ditambahkan']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function update($id = null)
    {
        $model = new ArticleModel();
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if ($model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'Artikel diperbarui']);
        }
        return $this->failValidationErrors($model->errors());
    }

    public function delete($id = null)
    {
        $model = new ArticleModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'Artikel dihapus']);
        }
        return $this->failNotFound();
    }

    // Kategori endpoints (bisa di-pisah tapi ditaruh disini sementara untuk efisiensi)
    public function categories()
    {
        $model = new CategoryModel();
        return $this->respond(['status' => 'success', 'data' => $model->findAll()]);
    }

    public function createCategory()
    {
        $model = new CategoryModel();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if ($model->insert($data)) return $this->respondCreated(['status' => 'success', 'message' => 'Kategori dibuat']);
        return $this->failValidationErrors($model->errors());
    }
}
