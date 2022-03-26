<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendinblue extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToSendInBlueSubscriberList($getData,$sendInBlueListId){

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $sendInBlueListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);            
            
            //LIST ID 
            $list_id = $providerData['code'];      
            $newsubscriberUrl = "https://api.sendinblue.com/v3/contacts";
            $accessToken = "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";

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
            if (!file_exists(FCPATH."log/sendinblue/")) {   
                mkdir(FCPATH."log/sendinblue/", 0777, true);
            }
            $logPath    = FCPATH."log/sendinblue/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $data = array(
                "listIds" => array((int)$list_id),
                "email" => $getData['emailId'],
                "updateEnabled" => false,
                "attributes" => array(
                        "FIRSTNAME"    => $getData['firstName'],
                        "LASTNAME"     => $getData['lastName'],
                        "SMS"  => @$getData['phone'],
                        "ADDRESS"=> @$getData['address'],
                        "CITY"          => @$getData['city'],
                        "ZIPCODE"   => @$getData['postCode'],
                        "GENDER"   => strtolower($getData['gender']) 
                )
            );  


            $body = $client->post($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['api-key' => "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",
                                  'Accept' => "application/json",
                                  'Content-Type' => "application/json"
                                 ]
            ]);
            
            $responseCode = $body->getStatusCode();
            $subscriber = json_decode($body->getBody(),true);
            
            if ($responseCode == 201) {                
                $subscriber_id = $subscriber['id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            }else if($responseCode == 400) {
                return array("result" => "error","error" => array("msg" => $subscriber['message']));
            }else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) { 
                $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                return array("result" => "error","error" => array("msg" => isset($response['message'])?$response['message']:"Invalid Parameters"));            
        }
    } 
}