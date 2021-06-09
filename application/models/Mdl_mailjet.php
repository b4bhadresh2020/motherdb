<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_mailjet extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }
    

    function AddEmailToMailjetSubscriberList($getData,$mailjetListId){
       
        // Create a Guzzle client
        $client = new GuzzleHttp\Client(); 
        
        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailjetListId);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
        $mailjetAccountId     = $providerData['aweber_account']; 
        
        $mailjetCondition   = array('id' => $mailjetAccountId);
        $is_single           = true;
        $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);        
        $api_key = $mailjetAccountData['api_key'];
        $secret_key = $mailjetAccountData['secret_key'];

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
        $logPath    = FCPATH."log/mailjet/";
        $fileName   = date("Ymd")."_log.txt"; 
        $logFile    = fopen($logPath.$fileName,"a");
        $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
        fwrite($logFile,$logData);
        fclose($logFile);

        try {            
            
            $body = [
                'Contacts' => [
                    [
                        'Email' => @$getData['emailId'],
                        'IsExcludedFromCampaigns' => 'false',
                        'Name' => @$getData['firstName'] . ' ' . @$getData['lastName'],
                        'Properties' => [
                            'name' => @$getData['firstName'] . ' ' . @$getData['lastName'],
                            'firstname' => @$getData['firstName'],
                            'lastname' => @$getData['lastName'],
                            'phone' => @$getData['phone'],
                            'gender' => @$getData['gender'],
                            'address' => @$getData['address'],
                            'postcode' => @$getData['postCode'],
                            'city' => @$getData['city'],
                            'birthdate' => @$getData['birthDate']. ' 00:00:00',
                            'tag' => $tagValue
                        ]	
                    ]
                ],
                'ContactsLists' => [
                    [
                        'ListID' => $list_id,
                        'Action' => "addforce"
                    ]
                ]
            ];
            $bodyData = json_encode($body); 
            
            try {

                $newsubscriberUrl = "https://api.mailjet.com/v3/REST/contact/managemanycontacts";
                $body = $client->post($newsubscriberUrl, [
                    'body' => $bodyData, 
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'auth' => [
                        $api_key, $secret_key
                    ]
                ]);  
                $responseCode = $body->getStatusCode();
                $subscriber = json_decode($body->getBody(),true);
            
                if($responseCode == "201") {
                    $jobID = $subscriber['Data'][0]['JobID'];
                    return array("result" => "success","data" => array("id" => $jobID));
                } else if ($responseCode == "401") {
                    return array("result" => "error","error" => array("msg" => "Unauthorized"));
                } else {
                    return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
                }

            } catch(Exception $e) {
                return array("result" => "error","error" => array("msg" => $e->getMessage()));
            }

            // catch any exceptions thrown during the process and print the errors to screen
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            
            $statusCode = $e->getResponse()->getStatusCode();      
          
            if($statusCode == "400"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }
        }
    }
}