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
}
