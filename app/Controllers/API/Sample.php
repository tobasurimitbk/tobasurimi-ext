<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
// use \Firebase\JWT\JWT;
use App\Models\Admin\CategoryModel;

class Sample extends BaseController
{
    use ResponseTrait;
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function list()
    {
        $company_id = $this->request->getGet('company_id');
        $term = $this->request->getGet('term');

        if ($term) {
            $category = $this->categoryModel->where('company_idx', $company_id)->like('category_name', $term)->findAll();
        } else {
            $category = $this->categoryModel->where('company_idx', $company_id)->findAll();
        }

        if (!$category) {
            $response = [
                'status' => false,
                'message' => 'Data Not Found'
            ];
            return $this->respond($response, 404);
        }

        $response = [
            'status' => true,
            'data' => $category
        ];
        return $this->respond($response, 200);
    }

    public function create()
    {
        $rules = [
            'company_id' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Compant ID Required'
                ]
            ],
            'category_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Category Name Required'
                ]
            ]
        ]; //rules

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'company_idx'   => $this->request->getPost('company_id'),
            'category_name' => $this->request->getPost('category_name'),
            'created_at'    => date('Y-m-d H:i:s'),
        ];
        $this->categoryModel->save($data);

        $response = [
            'status' => true,
            'message' => 'Category Created Succesfully'
        ];

        return $this->respond($response, 201);   
    }

    public function update($id)
    {
        $rules = [
            'category_name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Category Name Required'
                ]
            ]
        ]; //rules

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => $this->validator->getErrors()
            ];
            return $this->respond($response, 400);
        }

        $data = [
            'category_name' => $this->request->getPost('category_name'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->categoryModel->update($id, $data);

        $response = [
            'status' => true,
            'message' => 'Category Updated Succesfully'
        ];

        return $this->respond($response, 201);   
    }
}
