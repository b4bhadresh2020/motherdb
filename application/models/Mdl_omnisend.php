<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_omnisend extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToOmnisendSubscriberList($getData,$omnisendListId){
      
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $omnisendListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $omnisendAccountId     = $providerData['aweber_account']; 
            
            $omnisendCondition   = array('id' => $omnisendAccountId);
            $is_single           = true;
            $omnisendAccountData   = GetAllRecord(OMNISEND_ACCOUNTS, $omnisendCondition, $is_single); 

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
            $logPath    = FCPATH."log/omnisend/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            //LIST ID 
            $list_id = $providerData['code'];
            $apiKey = $omnisendAccountData['api_key'];

            $gender = '';
            if(strtolower($getData['gender'] == 'male')) {
                $gender = 'm';
            } else if (strtolower($getData['gender'] == 'female')) {
                $gender = 'f';
            }
            $todayDateTime = date("Y-m-d\TH:i:s\Z", strtotime(date('Y-m-d h:i:s')));
            $details = [
                'segmentID' => $list_id,
                'identifiers' => [
                    [
                        'type' => 'email',
                        'id' => $getData['emailId'],
                        'channels' => [
                            'email' => [
                                'status' => 'subscribed',
                                'statusDate' => $todayDateTime
                            ]
                        ]
                    ]
                ]
            ];

            if(!empty($getData['firstName'])) {
                $details['firstName'] = $getData['firstName'];
            }
            if(!empty($getData['lastName'])) {
                $details['lastName'] = $getData['lastName'];
            }
            if(!empty($getData['city'])) {
                $details['city'] = $getData['city'];
            }
            if(!empty($getData['address'])) {
                $details['address'] = $getData['address'];
            }
            if(!empty($getData['postCode'])) {
                $details['postalCode'] = $getData['postCode'];
            }
            if(!empty($gender)) {
                $details['gender'] = $gender;
            }
            if(!empty($getData['birthDate'])) {
                $details['birthdate'] = $getData['birthDate'];
            }
            if(!empty($tagValue)) {
                $details['tags'][] = $tagValue;
            }
            if(!empty($getData['phone'])) {
                $phone = [
                    'type' => 'phone',
                    'id' => $getData['phone'],
                    'channels' => [
                        'sms' => [
                            'status' => 'nonSubscribed',
                            'statusDate' => $todayDateTime
                        ]
                    ]
                ];
                array_push($details['identifiers'], $phone);
                
            }
           
            $data = json_encode($details);           
                 
            $newsubscriberUrl = "https://api.omnisend.com/v3/contacts";
            $body = $client->post($newsubscriberUrl, [
                'body' => $data, 
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key' => $apiKey
                ]
            ]);  
              
            $responseCode = $body->getStatusCode();
            $response = json_decode($body->getBody(),true);           
            $subscriber_id = $response['contactID'];
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