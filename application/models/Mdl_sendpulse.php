<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendpulse extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToSendpulseSubscriberList($getData,$sendPulseListId){
       
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $sendPulseListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);            
            
            //LIST ID 
            $list_id = $providerData['code'];      
            $newsubscriberUrl = "https://api.sendpulse.com/addressbooks/". $list_id ."/emails";
            $accessToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY1Yzk0NTVlYTIxMmIzZTkxZThjZjMzZjQ4NjIzODE3YjE4ZjMzMjg5ZmJkNTBmNjkyZTUzN2FkODk3NjM4OWMyYzk1MzBlZDE5NGJhNDEyIn0.eyJhdWQiOiJmZTVkOGJmNmY2ODIyOWVhN2MxMDEzYTY0MGZkMWJjOCIsImp0aSI6ImY1Yzk0NTVlYTIxMmIzZTkxZThjZjMzZjQ4NjIzODE3YjE4ZjMzMjg5ZmJkNTBmNjkyZTUzN2FkODk3NjM4OWMyYzk1MzBlZDE5NGJhNDEyIiwiaWF0IjoxNjE4ODIzMzI5LCJuYmYiOjE2MTg4MjMzMjksImV4cCI6MTYxODgyNjkyOSwic3ViIjoiIiwic2NvcGVzIjpbXSwidXNlciI6eyJpZCI6NzY1ODc4NCwiZ3JvdXBfaWQiOm51bGwsInBhcmVudF9pZCI6bnVsbCwiYXJlYSI6InJlc3QifX0.QaAgENNfAJ26QIgMVAdSruEdyiGz9-JVnJKbalE_ZQoLB1cK1S4nA7WBN9EsyqTMVEMqRFAG-ob3YHbgtpjZTCmsEYUsmXPiBAz5ubmlaJ22xkf4I-IkrlQ3LoUHFYAu9ux25fgHEtppibJLiOzECErqsu5mcF5v0IQVPi7UVOjKaKO_itmcWCOj7x7dp0WlMHUHc1_G3qcK0RRZ8n3RhMoB9BMAnT4EP7WN6bzIXdUrmRg7mbkqcOoX4Ki0raB6hbyvw-k3Ps5OnHXbRb9P8_NISaPkNfsdHLlweKdc_hLC2neiNTpT_QDu67rhpSN1KIBYUdoqAvUMFOrLibf_ZQ";            

            $data = array(
                "id" => array($list_id),
                "emails" => array(
                    $getData['emailId']
                )
            );  
           
            $body = $client->post($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);            
                
            $responseCode = $body->getStatusCode();
            
            $subscriber = json_decode($body->getBody(),true);
            echo $responseCode;
            pre($subscriber);
            die;

            if ($responseCode == 200) {                
                $subscriber_id = $subscriber['job_id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            } else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {            
                return array("result" => "error","error" => array("msg" => "Bad Request"));            
        }
    } 
}