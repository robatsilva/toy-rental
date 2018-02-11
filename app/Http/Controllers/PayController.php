<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Employe;

use Auth;

class PayController extends Controller
{
    public function getSession(){
        return $this->createRequest("https://ws.sandbox.pagseguro.uol.com.br/v2/sessions", null);
    }

    function preApprovals(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        
        $body = array("plan"=> "EA2264ABDDDD8A1334AEEF808C263F50",
            "reference"=> "",
            "sender"=> [
              "name"=> $user->name,
              "email"=> $user->email,
              "hash"=> $request->input("hash"),
              "phone"=> [
                "areaCode"=> $user->area_code,
                "number"=> $user->phone
              ],
              "documents"=> [
                [
                    "type"=> "CNPJ",
                    "value"=> $request->input("cnpj"),
                ]
              ],
              "address"=> [
                "street"=> $request->input("address"),
                "number"=> $request->input("address_number"),
                "complement"=> "",
                "district"=> $request->input("address_district"),
                "city"=> $request->input("address_city"),
                "state"=> $request->input("address_state"),
                "country"=> "BRA",
                "postalCode"=> $request->input("postalcode"),
              ]
            ],
            "paymentMethod"=> [
              "type"=> "CREDITCARD",
              "creditCard"=> [
                "token"=> $request->input("card_token"),
                "holder"=> [
                  "name"=>  $request->input("card_name"),
                  "birthDate"=> date("d/m/Y", strtotime($request->input("card_birthdate"))),
                  "documents"=> [
                    [
                        "type"=> $request->input("doc_type"),
                        "value"=> $request->input("card_cnpj"),
                    ]
                  ],
                  "phone"=> [
                    "areaCode"=> $request->input("card_area_code"),
                    "number"=> $request->input("card_phone")
                  ]
                ]
              ]
            ]);
            // return response()->json($user);
          return $this->createRequest("https://ws.sandbox.pagseguro.uol.com.br/pre-approvals", $body);
    }

    function createRequest($url, $body){
        //Definindo as credenciais
        $email = "authentic.desenvolvimento@gmail.com";
        $token = "4FB6DEADD9E242F79C43CD82E88F016E";

        //URL da chamada para o PagSeguro
        $url = $url . "?email=" .$email ."&token=".$token;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        if($body){
          $body = json_encode($body);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
          curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
              'Content-Type: application/json',
              'Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1'));
        }
        $output = curl_exec($ch);

        curl_close($ch);
        if($body){
          $xml = $output;
        } else {
          $xml = simplexml_load_string($output);
        }
        $json = json_encode($xml);
        $array =  json_decode($json,TRUE);
        return response()->json($array);
    }
}
