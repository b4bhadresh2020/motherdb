<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_active_campaign extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToActiveCampaignSubscriberList($getData,$activeCampaignListId){
       
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $activeCampaignListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $activeCampaignAccountId     = $providerData['aweber_account']; 
            
            $activeCampaignCondition   = array('id' => $activeCampaignAccountId);
            $is_single           = true;
            $activeCampaignAccountData   = GetAllRecord(ACTIVE_CAMPAIGN_ACCOUNTS, $activeCampaignCondition, $is_single); 
            
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
            $logPath    = FCPATH."log/active_campaign/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $apiUrl = $activeCampaignAccountData['api_url'];
            $apiKey = $activeCampaignAccountData['api_key'];

            $details = [
                'contact' => [
                    'email' => @$getData['emailId'],
                    'firstName' => @$getData['firstName'],
                    'lastName' => @$getData['lastName'],
                    'phone' => @$getData['phone'],
                    'status' => 1,
                    'fieldValues' => [
                        [
                            'field' => '1',
                            'value' => @$getData['gender']
                        ],
                        [
                            'field' => '2',
                            'value' => @$getData['address']
                        ],
                        [
                            'field' => '3',
                            'value' => @$getData['postCode']
                        ],
                        [
                            'field' => '4',
                            'value' => @$getData['city']
                        ],                  
                        [
                            'field' => '5',
                            'value' => @$getData['birthDate']
                        ],
                        [
                            'field' => '6',
                            'value' => @$tagValue
                        ]
                    ]
                ]
            ];
            $data = json_encode($details);
                 
            $newsubscriberUrl = $apiUrl . "/api/3/contacts";
            $body = $client->post($newsubscriberUrl, [
                'body' => $data, 
                'headers' => [
                    'Api-Token' => $apiKey,
                    'Content-Type' => 'application/json'
                ]
            ]);  
              
            $responseCode = $body->getStatusCode();
            $response = json_decode($body->getBody(),true); 
           
            if ($responseCode == 201) {  
                $subscriber_id = $response['contact']['id'];
                //  Add subscriber to list 
                $contactListArr = [
                    "contactList" => [
                        "list" => $list_id,
                        "contact" => $subscriber_id,
                        "status" => 1
                    ]
                ];
                $contactList = json_encode($contactListArr);

                $subscriberListUrl = $apiUrl . "/api/3/contactLists";
                $contactListbody = $client->post($subscriberListUrl, [
                    'body' => $contactList, 
                    'headers' => [
                        'Api-Token' => $apiKey,
                        'Content-Type' => 'application/json'
                    ]
                ]);  
                $listResponseCode = $contactListbody->getStatusCode();
                        
                if($listResponseCode == 201) {
                    return array("result" => "success","data" => array("id" => $subscriber_id));    
                } else {
                    return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
                }
            } else {
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