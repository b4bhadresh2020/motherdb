<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendgrid extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToSendgridSubscriberList($getData,$sendGridListId){

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();        

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $sendGridListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);            
                    
            //LIST ID 
            $list_id = $providerData['code'];      
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
            $logPath    = FCPATH."log/sendgrid/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $data = array(
                "list_ids" => array($list_id),
                "contacts" => array(
                    array(
                        "email"         => $getData['emailId'],
                        "first_name"    => $getData['firstName'],
                        "last_name"     => $getData['lastName'],
                        "custom_fields" => array(
                            "w1_T" => $getData['gender'],
                            "w2_T" => $getData['phone'],
                            "w3_T" => $getData['birthDate'],
                            "w4_T" => $tagValue
                        )
                    )
                )
            );  

            $body = $client->put($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);
            
            $responseCode = $body->getStatusCode();  
            $subscriberUrl = $body->getHeader('Location')[0];
            $subscriberResponse = $client->get($subscriberUrl,
                ['headers' => ['Authorization' => 'Bearer ' . $accessToken]])->getBody();
            $subscriber = json_decode($subscriberResponse, true);

            if ($responseCode == 202) {                
                $subscriber_id = $subscriber['job_id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            }else if($responseCode == 400) {
                return array("result" => "error","error" => array("msg" => $subscriber['errors']['message']));
            }else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {            
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));            
        }
    } 
}