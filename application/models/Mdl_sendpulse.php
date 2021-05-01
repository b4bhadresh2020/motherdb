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
            $sendpulseAccountId     = $providerData['aweber_account']; 
            
            $sendpulseCondition   = array('id' => $sendpulseAccountId);
            $is_single           = true;
            $sendpulseAccountData   = GetAllRecord(SENDPULSE_ACCOUNTS, $sendpulseCondition, $is_single);        
            $accessToken = $sendpulseAccountData['accessToken'];
            
            //LIST ID 
            $list_id = $providerData['code'];      
            $newsubscriberUrl = "https://api.sendpulse.com/addressbooks/". $list_id ."/emails";
            $data = array(
                "id" => $list_id,
                "emails" => array(
                    $getData['emailId']
                )
            );  
           
            $body = $client->post($newsubscriberUrl, [
                'json' => $data, 
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);            
                
            $responseCode = $body->getStatusCode();
            
            if ($responseCode == 200) {                
                return array("result" => "success","data" => array("msg" => "Subscription Successfully"));
            } else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {   
            $statusCode = $e->getResponse()->getStatusCode();         
            if($statusCode == "400"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));
            }            
        }
    } 
}