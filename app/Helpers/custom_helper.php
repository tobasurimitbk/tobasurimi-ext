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

function romanMonthNumber(int $number): string
{
   $map = [
      'M'   => 1000,
      'CM'  => 900,
      'D'   => 500,
      'CD'  => 400,
      'C'   => 100,
      'XC'  => 90,
      'L'   => 50,
      'XL'  => 40,
      'X'   => 10,
      'IX'  => 9,
      'V'   => 5,
      'IV'  => 4,
      'I'   => 1
   ];
   $returnValue = '';

   while ($number > 0) {
      foreach ($map as $roman => $int) {
         if ($number >= $int) {
            $number -= $int;
            $returnValue .= $roman;
            break;
         }
      }
   }

   return $returnValue;
}

function penyebut(int $nilai): string {
   $huruf = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
   $temp = "";

   if ($nilai < 12) {
       $temp = " ". $huruf[floor($nilai)];
   } elseif ($nilai <20) {
       $temp = penyebut($nilai - 10). " belas";
   } elseif ($nilai < 100) {
       $temp = penyebut($nilai/10)." puluh". penyebut(floor($nilai) % 10);
   } elseif ($nilai < 200) {
       $temp = " seratus" . penyebut($nilai - 100);
   } else if ($nilai < 1000) {
       $temp = penyebut($nilai/100) . " ratus" . penyebut(floor($nilai) % 100);
   } else if ($nilai < 2000) {
       $temp = " seribu" . penyebut($nilai - 1000);
   } else if ($nilai < 1000000) {
       $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
   } else if ($nilai < 1000000000) {
      $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
   } else if ($nilai < 1000000000000) {
      $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
   } else if ($nilai < 1000000000000000) {
      $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
   }

   return $temp;
}

function terbilang($x)
{
   $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

   if ($x < 12)
      return " " . $angka[$x];
   elseif ($x < 20)
      return terbilang($x - 10) . " Belas";
   elseif ($x < 100)
      return terbilang($x / 10) . " Puluh" . terbilang($x % 10);
   elseif ($x < 200)
      return "Seratus" . terbilang($x - 100);
   elseif ($x < 1000)
      return terbilang($x / 100) . " Ratus" . terbilang($x % 100);
   elseif ($x < 2000)
      return "Seribu" . terbilang($x - 1000);
   elseif ($x < 1000000)
      return terbilang($x / 1000) . " Ribu" . terbilang($x % 1000);
   elseif ($x < 1000000000)
      return terbilang($x / 1000000) . " Juta" . terbilang($x % 1000000);
   elseif ($x < 1000000000000)
      return terbilang($x / 1000000000) . " Miliar" . " " . terbilang($x % 1000000000);
   elseif ($x < 1000000000000000)
      return terbilang($x / 1000000000000) . " Triliun" . " " . terbilang($x % 1000000000000);

}
