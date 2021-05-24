<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_ontraport extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToOntraportSubscriberList($getData,$ontraportListId){
          // LOG ENTRY
          $logPath    = FCPATH."log/ontraport/";
          $fileName   = date("Ymd")."_log.txt"; 
          $logFile    = fopen($logPath.$fileName,"a");
          $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
          fwrite($logFile,$logData);
          fclose($logFile);

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $ontraportListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $ontraportAccountId     = $providerData['aweber_account']; 
            
            $ontraportCondition   = array('id' => $ontraportAccountId);
            $is_single           = true;
            $ontraportAccountData   = GetAllRecord(ONTRAPORT_ACCOUNTS, $ontraportCondition, $is_single);   
            $appApiKey = $ontraportAccountData['app_id'];     
            $apiKey = $ontraportAccountData['api_key'];
            $list_id = $providerData['code'];
            $details = [
                'firstname' => $getData['firstName'],
                'lastname' => $getData['lastName'],
                'email' => $getData['emailId'],
                'office_phone' => $getData['phone'],
                'fb_gender' => $getData['gender'],
                'city' => $getData['city'],
                'zip' => $getData['postCode'],
                'contact_cat' => $list_id
            ];
            $data = json_encode($details);
            
            //LIST ID 
            $subscriberUrl = "https://api.ontraport.com/1/Contacts";
            $headers = [
                'Api-Key' => $apiKey,
                'Api-Appid' => $appApiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];   
            $body = $client->post($subscriberUrl, [
                'body' => $data, 
                'headers' => $headers
            ]);  
              
            $responseCode = $body->getStatusCode();
            $subscriber = json_decode($body->getBody(),true);
            
            if ($responseCode == 200) {           
                $subscriber_id = $subscriber['data']['id']; 
                 // LOG ENTRY
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