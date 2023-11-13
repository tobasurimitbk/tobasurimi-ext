<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // LOGIN CHECK 
        if (empty(session()->getTempdata("login"))) {
            return redirect()->to("/")->with('error', "Invalid Credential");
        }

        // VALIDASI HAK AKSES SESSION
        $uri = service('uri');
        $hakAkses = session()->get('login')->this_access;
        $segment1 = $uri->getSegment(1);

        $allowedAccess = false;

        $uri = service('uri');
        $hakAkses = session()->get('login')->this_access;
        $segment1 = $uri->getSegment(1);

        if ($segment1 == "dashboard") {
            $allowedAccess = true;
        } else {
            foreach ($hakAkses as $h) {
                if (isset($h->child)) {
                    foreach ($h->child as $c) {
                        if ($c->url == '/' . $segment1) {
                            $allowedAccess = true;
                        }
                    }
                }
            }
        }

        if (!$allowedAccess) {
            return redirect()->to("403")->with('error', "");
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
