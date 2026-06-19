<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ArticleModel;

class Article extends ResourceController
{
    protected $format = 'json';
    
    public function index()
    {
        $model = new ArticleModel();
        $query = $this->request->getVar('search');
        $kategori_id = $this->request->getVar('kategori_id');

        $builder = $model->select('artikel.*, kategori.nama_kategori')
                         ->join('kategori', 'kategori.id = artikel.kategori_id');

        if ($query) {
            $builder->groupStart()
                    ->like('judul', $query)
                    ->orLike('konten', $query)
                    ->groupEnd();
        }
        if ($kategori_id) {
            $builder->where('kategori_id', $kategori_id);
        }

        return $this->respond(['status' => 'success', 'data' => $builder->orderBy('created_at', 'DESC')->findAll()]);
    }
}
