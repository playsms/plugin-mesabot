<?php

class Mesabot{

    protected $token;
    protected $baseUrl;
    protected $response;

    public function __construct(){
        if(defined("MESABOT_TOKEN")){
            $this->token = MESABOT_TOKEN;
            $this->baseUrl = "https://api.mesabot.com/api/v1/";
        }else{
            throw new Exception("Token not defined, Create a constan variable named MESABOT_TOKEN", 1);
        }

    }

    public function response(){
        return $this->response;
    }

    public function sms($data){
        $endpoint = 'messages';
        $method = 'POST';
        $response = $this->api($endpoint,$method,$data);
    }

    private function api($endpoint,$method,$data = null){

    $url = $this->baseUrl.$endpoint;

    $curlInit = curl_init($url);

    // check apabila $data tidak sama dengan null encode menjadi json
    $data != null? $json_data = json_encode($data) : $json_data = null;

    // mengirim dan meminta data ke API Back-end
    curl_setopt( $curlInit, CURLOPT_CUSTOMREQUEST, $method ); //set method request

    if($data != null && ($method == 'POST' || $method == 'PUT')){
        curl_setopt( $curlInit, CURLOPT_POSTFIELDS, $json_data ); //set json_data pada method post
    }
    curl_setopt( $curlInit, CURLOPT_TIMEOUT,10);
    curl_setopt( $curlInit, CURLOPT_RETURNTRANSFER, true );

    curl_setopt( $curlInit, CURLOPT_HTTPHEADER, array (
        'Content-Type: application/json',
        'Authorization: Bearer '.$this->token,
        'Content-Length: ' . strlen( $json_data ) )
      );

      //hasil dari endpoint masuk ke $result
      $result = curl_exec( $curlInit );

      if(curl_error($curlInit))
        {
            $response = new stdClass();
            $response->messages = 'error:' . curl_error($curlInit);
            $this->response = $response;
            curl_close($curlInit);
            return $this;
        }else{
            //data diubah ke array
            $data_array = json_decode( $result,true);

            //meminta informasi status ke endpoint api back-end
            $status = curl_getinfo( $curlInit, CURLINFO_HTTP_CODE );

            $response = new stdClass();
            $response->status_code = $status;
            $response->messages = $data_array;

            $this->response = $response;
            curl_close($curlInit);
            return $this;
        }
    }


}
