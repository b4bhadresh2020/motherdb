<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_marketing_platform extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToMarketingPlatformSubscriberList($getData,$marketingPlatformListId){
      
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $marketingPlatformListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $marketingPlatformAccountId     = $providerData['aweber_account']; 
            
            $marketingPlatformCondition   = array('id' => $marketingPlatformAccountId);
            $is_single           = true;
            $marketingPlatformAccountData   = GetAllRecord(MARKETING_PLATFORM_ACCOUNTS, $marketingPlatformCondition, $is_single); 
            
            //LIST ID 
            $list_id = $providerData['code'];

            // Find tag value
            if(isset($getData["otherLable"]) && isset($getData["other"])){
                $otherLabel = json_decode($getData["otherLable"]);
                $otherData = json_decode($getData["other"]);

                $searchIndex = array_search("Tag",$otherLabel,true);
                if($searchIndex !== FALSE){
                    $tagValue = $otherData[$searchIndex];
                }else{
                    $tagValue = "";
                }  
            }else if(isset($getData["tag"])){
                $tagValue = $getData["tag"];
            }else{
                $tagValue = "";
            }

            // LOG ENTRY
            if (!file_exists(FCPATH."log/marketing_platform/")) {   
                mkdir(FCPATH."log/marketing_platform/", 0777, true);
            }
            $logPath    = FCPATH."log/marketing_platform/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $apiUsername = $marketingPlatformAccountData['api_username'];
            $apiToken = $marketingPlatformAccountData['api_token'];

            $details = [
                'Apiusername' => $apiUsername,
                'Apitoken'  => $apiToken,
                'listid' => $list_id,
                'emailaddress' => @$getData['emailId'],
                'status' => 'active',
                'mobile' => @$getData['phone'],
                'confirmed' => 'true',
                'contactFields' => [
                ]
            ];

            if(!empty($getData['firstName'])) {                
                $contactFields['fieldid'] = '2';
                $contactFields['value'] = @$getData['firstName'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['lastName'])) {                
                $contactFields['fieldid'] = '3';
                $contactFields['value'] = @$getData['lastName'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['phone'])) {                
                $contactFields['fieldid'] = '4';
                $contactFields['value'] = @$getData['phone'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['gender'])) {                
                $contactFields['fieldid'] = '12';
                $contactFields['value'] = @$getData['gender'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['address'])) {                
                $contactFields['fieldid'] = '167662';
                $contactFields['value'] = @$getData['address'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['postCode'])) {                
                $contactFields['fieldid'] = '10';
                $contactFields['value'] = @$getData['postCode'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['city'])) {                
                $contactFields['fieldid'] = '8';
                $contactFields['value'] = @$getData['city'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($getData['birthDate'])) {                
                $contactFields['fieldid'] = '7';
                $contactFields['value'] = @$getData['birthDate'];
                array_push($details['contactFields'], $contactFields);
            }
            if(!empty($tagValue)) {                
                $contactFields['fieldid'] = '167663';
                $contactFields['value'] = @$tagValue;
                array_push($details['contactFields'], $contactFields);
            }
            
            $data = json_encode($details);
                 
            $newsubscriberUrl = "https://api.mailmailmail.net/v1.1/Subscribers/AddSubscriberToList";
            $body = $client->post($newsubscriberUrl, [
                'body' => $data, 
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);  
              
            $responseCode = $body->getStatusCode();
            $subscriber_id = json_decode($body->getBody(),true);           

            if ($responseCode == 200) {           
                return array("result" => "success","data" => array("id" => $subscriber_id));    
            } else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {            
            $statusCode = $e->getResponse()->getStatusCode(); 
            if($statusCode == "400" || $statusCode == "401" || $statusCode == "403"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request - ". $e->getMessage()));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }            
        }
    } 
}