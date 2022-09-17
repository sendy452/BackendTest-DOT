<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class Controller extends BaseController
{
    public function FetchApi()
    {
        $client = new \GuzzleHttp\Client();

        $response_province     = $client->get('https://api.rajaongkir.com/starter/province', [
            'headers' => [
                'key' => '0df6d5bf733214af6c6644eb8717c92c',
            ]
        ]);
        
        $array_province = $response_province->getBody()->getContents();
        $json_province = json_decode($array_province, true);
        $collection_province = collect($json_province);
        $data_province = collect($collection_province->get('rajaongkir'))->get('results');

        $response_city     = $client->get('https://api.rajaongkir.com/starter/city', [
            'headers' => [
                'key' => '0df6d5bf733214af6c6644eb8717c92c',
            ]
        ]);
        
        $array_city = $response_city->getBody()->getContents();
        $json_city = json_decode($array_city, true);
        $collection_city = collect($json_city);
        $data_city = collect($collection_city->get('rajaongkir'))->get('results');
        
        try {
            app('db')->table('province')->insert($data_province);
            app('db')->table('city')->insert($data_city);
            return json_encode([
                "status" => "success",
                "data" => "Success Fetching",
            ]);
        } catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1062'){
                return json_encode([
                    "status" => "success",
                    "data" => "Already Fetching",
                ]);
            }
        }
    }

    public function SearchProvince(Request $request)
    {
        $id = $request->get('id');

        if ($id == null) {
            $hasil = app('db')->table('province')->select('*')->get();

            if (count($hasil) == 0) {
                return json_encode([
                    "status" => "error",
                    "data" => "Province not found",
                ]);
            }else{
                return json_encode([
                    "status" => "success",
                    "data" => $hasil,
                ]);
            }

        }else {
            $hasil = app('db')->table('province')->select('*')->where('province_id', '=', $id)->get();
            
            if (count($hasil) == 0) {
                return json_encode([
                    "status" => "error",
                    "data" => "Province not found",
                ]);
            }else{
                return json_encode([
                    "status" => "success",
                    "data" => $hasil,
                ]);
            }
        }
    }

    public function SearchCity(Request $request)
    {
        $id = $request->get('id');

        if ($id == null) {
            $hasil = app('db')->table('city')->select('*')->get();

            if (count($hasil) == 0) {
                return json_encode([
                    "status" => "error",
                    "data" => "City not found",
                ]);
            }else{
                return json_encode([
                    "status" => "success",
                    "data" => $hasil,
                ]);
            }
            
        }else {
            $hasil = app('db')->table('city')->select('*')->where('city_id', '=', $id)->get();
            
            if (count($hasil) == 0) {
                return json_encode([
                    "status" => "error",
                    "data" => "City not found",
                ]);
            }else{
                return json_encode([
                    "status" => "success",
                    "data" => $hasil,
                ]);
            }
        }
    }

    public function SwapProvinces(Request $request)
    {

        $response = $this->SearchProvince($request);

        if (json_decode($response)->status == "error") {
            $client = new \GuzzleHttp\Client();

            $response_province     = $client->get('https://api.rajaongkir.com/starter/province', [
                'headers' => [
                    'key' => '0df6d5bf733214af6c6644eb8717c92c',
                ],
                'query' => [
                    'id' => $request->get('id'),
                ],
            ]);
            
            $array_province = $response_province->getBody()->getContents();
            $json_province = json_decode($array_province, true);
            $collection_province = collect($json_province);
            $data_province = collect($collection_province->get('rajaongkir'))->get('results');

            return $data_province;
        }else{
            return $response;
        }
    }

    public function SwapCities(Request $request)
    {

        $response = $this->SearchCity($request);

        if (json_decode($response)->status == "error") {
            $client = new \GuzzleHttp\Client();

            $response_city     = $client->get('https://api.rajaongkir.com/starter/city', [
                'headers' => [
                    'key' => '0df6d5bf733214af6c6644eb8717c92c',
                ],
                'query' => [
                    'id' => $request->get('id'),
                ],
            ]);
            
            $array_city = $response_city->getBody()->getContents();
            $json_city = json_decode($array_city, true);
            $collection_city = collect($json_city);
            $data_city = collect($collection_city->get('rajaongkir'))->get('results');

            return $data_city;
        }else{
            return $response;
        }
    }
}
