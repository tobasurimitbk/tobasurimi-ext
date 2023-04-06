<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    protected $user;

    public function __construct()
    {
        $this->user = new UserModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function getUser()
    {
        $data = [
            'user' => $this->user->getUsers()
        ];
        return view('auth/user', $data);
    }

    public function getUserDetail($id)
    {
        $data = [
            'user' => $this->user->getUsers($id)
        ];

        return view('auth/userDetail', $data);
    }
}
