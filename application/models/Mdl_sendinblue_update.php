<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendinblue_update extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function UpdateSendInBlueSubscriber($getData){

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
                        
            //LIST ID             
            $newsubscriberUrl = "https://api.sendinblue.com/v3/contacts/".urlencode($getData['emailId']);
            $accessToken = "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";

            $data = array(                
                "updateEnabled" => false,
                "attributes" => array(
                        "SMS"  => @$getData['phone'],
                        //"GENDER"   => @$getData['gender'] 
                )
            );  


            $body = $client->put($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['api-key' => "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",
                                  'Accept' => "application/json",
                                  'Content-Type' => "application/json"
                                 ]
            ]);
            
            $responseCode = $body->getStatusCode();
            return array("result" => "success","data" => array("code" => $responseCode));
        }catch (\GuzzleHttp\Exception\ClientException $e) { 
                $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                return array("result" => "error","error" => array("msg" => isset($response['message'])?$response['message']:"Invalid Parameters"));            
        }
    } 
}