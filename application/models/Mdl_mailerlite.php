<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_Mailerlite extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToMailerliteSubscriberList($getData,$mailerLiteListId){
       
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $mailerLiteListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $mailerliteAccountId     = $providerData['aweber_account']; 
            
            $mailerliteCondition   = array('id' => $mailerliteAccountId);
            $is_single           = true;
            $mailerliteAccountData   = GetAllRecord(MAILERLITE_ACCOUNTS, $mailerliteCondition, $is_single);        
            $apiKey = $mailerliteAccountData['api_key'];

            $details = [
                'email' => $getData['emailId'],
                'name'  => $getData['firstName'] . ' ' . $getData['lastName'],
                'fields' => [
                    'firstname' => $getData['firstName'],
                    'lastname' => $getData['lastName'],
                    'phone' => $getData['phone'],
                    'gender' => $getData['gender'],
                    'city' => $getData['city'],
                    'zip' => $getData['postCode']
                ]
            ];
            $data = json_encode($details);
            
            //LIST ID 
            $list_id = $providerData['code'];      
            $newsubscriberUrl = "https://api.mailerlite.com/api/v2/groups/". $list_id ."/subscribers";
            $headers = [
                'x-mailerlite-apikey' => $apiKey,
                'Content-Type' => 'application/json',
            ];   
            $body = $client->post($newsubscriberUrl, [
                'body' => $data, 
                'headers' => $headers
            ]);  
              
            $responseCode = $body->getStatusCode();
            $subscriber = json_decode($body->getBody(),true);

            if ($responseCode == 200) {           
                $subscriber_id = $subscriber['id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));    
            } else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) { 
            $statusCode = $e->getResponse()->getStatusCode();         
            if($statusCode == "400"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }            
        }
    } 
}