<?php

namespace App\Filters;

use App\Models\MetadataModel;
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

        $metaDataModel = new MetadataModel();

        // EXCEPT ENDPOINT
        $except = json_decode($metaDataModel->where('name', 'Route Hak Akses Except')->first()['value']);

        // VALIDASI HAK AKSES SESSION
        $uri = service('uri');
        $hakAkses = session()->get('login')->this_access;
        $segment1 = $uri->getSegment(1);

        $allowedAccess = false;

        $uri = service('uri');
        $hakAkses = session()->get('login')->this_access;
        $segment1 = $uri->getSegment(1);

        if (\in_array($segment1, $except)) {
            $allowedAccess = true;
        } else

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
