<?php

namespace App\Helpers;

use Exception;

class BeaCukaiApi
{

    protected $baseUrlProduction, $baseUrlDevelopment, $username, $password;

    public function __construct()
    {
        $this->baseUrlProduction = "https://apis-gw.beacukai.go.id";
        $this->baseUrlDevelopment = "https://apisdev-gw.beacukai.go.id";
        $this->username = "Tobasurimi";
        $this->password = "Surimitoba18";
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

        $endPoint = $this->baseUrlProduction . "/openapi/kurs/" . $kodeValuta;
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
                    'data' => "Server Ceisa Error : " . $httpCode,
                    'status' => false
                ];
            }
        }

        curl_close($ch);
    }


    public function getListGudangTPB($kodeKantor)
    {
        $token = $this->getTokenApi();
        $endPoint = $this->baseUrlProduction . "/openapi/gudangTPS/kodeKantor/" . $kodeKantor;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
        curl_close($ch);

        $responseData = json_decode($response);
        return $responseData->data;
    }

    public function getTokenApi()
    {
        try {
            $endPoint = $this->baseUrlProduction . "/nle-oauth/v1/user/login";
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

            curl_close($ch);

            $responseData = json_decode($response);

            return [
                'token' => $responseData->item->access_token,
                'status' => true
            ];
        } catch (Exception $e) {
            return [
                'message' => $e->getMessage(),
                'status' => false
            ];
        }
    }
}
