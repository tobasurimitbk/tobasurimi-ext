<?php
// https://code.tutsplus.com/tutorials/how-to-use-curl-in-php--cms-36732
// https://roytuts.com/codeigniter-4-consume-external-rest-apis/

if (!function_exists('curl_request')) {
   function curl_request($method, $url, $token, $payload = [])
   {
      $api_url = getenv("apiURL") . $url;

      $headers = array(
         "Cache-Control: no-cache",
         "Pragma: no-cache",
         'Content-Type: application/json',
         "Authorization: Bearer " . $token
      );

      $ch = curl_init();

      switch ($method) {
         case "POST":
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($payload) {
               curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }
            break;

         case "PUT":
            curl_setopt($ch, CURLOPT_PUT, 1);
            break;

         case "PATCH":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($payload) {
               curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }
            break;

         case "DELETE":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            break;

         default:
            if ($payload) {
               $api_url = sprintf("%s?%s", $api_url, http_build_query($payload));
            }
      }

      curl_setopt($ch, CURLOPT_URL, $api_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 50000); //timeout in seconds

      $response = curl_exec($ch);

      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      $header = substr($response, 0, $header_size);
      $body = substr($response, $header_size);
      $res = ["code" => $httpcode, "header" => $header, "body" => $body];

      curl_close($ch);

      return $res;
   }
}

if (!function_exists('is_login')) {
   function is_login()
   {
      return session()->get("login");
   }
}

if (!function_exists('formatter')) {
   function formatter($value, $type)
   {
      switch ($type) {
         case "ARR_TO_INT":
            $return = [];
            if (is_array($value)) {
               foreach ($value as $val) {
                  $return[] = intval($val);
               }
            }
            return $return;
            break;
         case "STR_TO_INT":
            return !empty($value) ? intval($value) : "";
            break;
         case "STR_TO_BOOL":
            return !empty($value) ? ($value === "true" ? true : false)  : "";
            break;
         case "STR_TO_FLOAT":
            return !empty($value) ? floatval($value) : "";
            break;
         case "CURR_TO_INT":
            return intval(str_replace(",", "", $value));
            break;
         case "NUM_TO_CURR":
            return str_replace(",", ".", number_format($value));
            break;
         case "TO_YMD":
            return implode("/", array_reverse(explode("/", $value)));
            break;
         default:
            return $value;
      }
   }
}
