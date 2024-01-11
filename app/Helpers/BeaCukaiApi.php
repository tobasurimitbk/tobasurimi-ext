<?php

namespace App\Helpers;

use App\Models\MetadataModel;
use Exception;

class BeaCukaiApi
{

    protected $baseUrl, $username, $password;
    protected $metaDataModel;

    public function __construct()
    {
        $this->metaDataModel = new MetadataModel();
        $this->baseUrl = $this->metaDataModel->where('name', "Base Url BC")->first()['value'];
        $this->username = $this->metaDataModel->where('name', "Username BC")->first()['value'];
        $this->password = $this->metaDataModel->where('name', "Password BC")->first()['value'];
    }

    // API GET
    public function getNilaiValuta($kodeValuta)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/kurs/" . $kodeValuta;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                if (count($responseData->data) == 0) {
                    return [
                        'data' => 0,
                        'status' => true
                    ];
                } else {
                    return [
                        'data' => $responseData->data[0]->nilaiKurs,
                        'status' => true
                    ];
                }
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }

        curl_close($ch);
    }


    public function getListKodePelabuhan($kodeKantor)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/pelabuhan/kodeKantor/" . $kodeKantor;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                return [
                    'data' => $responseData->data,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }
    }

    public function getManifest($noHostBL, $tglHostBL, $kodeKantor, $namaImportir)
    {
        $token = $this->getTokenApi();

        if ($token['status'] === false) {
            return [
                'status' => false,
                'message' => $token['message']
            ];
        }

        $endPoint = $this->baseUrl . "/openapi/manifes-bc11?noHostBl=" . $noHostBL . "&tglHostBl=" . $tglHostBL . "&kodeKantor=" . $kodeKantor . "&nama=" . $namaImportir;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'message' => curl_error($ch),
                'status' => false
            ];
        } else {
            $responseData = json_decode($response);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                return [
                    'data' => $responseData->data,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }
    }

    public function getTokenApi()
    {
        try {
            $endPoint = $this->baseUrl . "/nle-oauth/v1/user/login";
            $headers = array(
                'Content-Type: application/json',
            );

            $postData = array(
                'username' => $this->username,
                'password' => $this->password,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endPoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return [
                    'message' => curl_error($ch),
                    'status' => false
                ];
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                $responseData = json_decode($response);
                return [
                    'token' => $responseData->item->access_token,
                    'status' => true
                ];
            } else {
                return [
                    'message' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }

            curl_close($ch);
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => false
            ];
        }
    }
}
