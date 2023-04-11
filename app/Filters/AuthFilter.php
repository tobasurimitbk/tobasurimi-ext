<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty(session()->getTempdata("login"))) {
            return redirect()->to("/")->with('error', "Invalid Credential");
        } else {
            $now = date("Y-m-d H:i:s");
            $expired = session()->getTempdata("login")->expired;

            if ($now > $expired) {
                session()->destroy();
                return redirect()->to("/")->with('error', "Invalid Credential");
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
