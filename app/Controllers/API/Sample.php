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
    }
}
