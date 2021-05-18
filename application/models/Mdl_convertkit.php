<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_convertkit extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToConvertkitSubscriberList($getData,$convertkitListId){

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();     
            
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $convertkitListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);    
            
            // fetch mail account data from convert accounts table
            $accountCondition   = array('id' => $providerData['aweber_account']);
            $is_single           = true;
            $accountData        = GetAllRecord(CONVERTKIT_ACCOUNTS, $accountCondition, $is_single);    
            
            
            //LIST ID 
            $list_id = $providerData['code'];      
            $newsubscriberUrl = "https://api.convertkit.com/v3/tags/".$list_id."/subscribe";
            $api_key = $accountData['api_key'];

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
            $logPath    = FCPATH."log/convertkit/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $data = array(
                "api_key" => $api_key,
                "email" => $getData['emailId'],
                "first_name" => $getData['firstName'],
                "fields" => array(
                        "last_name"     => $getData['lastName'],
                        "gender"        => strtolower($getData['gender']),
                        "phone"         => @$getData['phone'],
                        "birthdate"     => @$getData['birthDate'],
                        "custom_tag"    => @$tagValue
                )
            );  


            $body = $client->post($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['charset' => "utf-8",
                                  'Content-Type' => "application/json"
                                 ]
            ]);
            
            $responseCode = $body->getStatusCode();
            $subscriber = json_decode($body->getBody(),true);

            if ($responseCode == 200) {                
                $subscriber_id = $subscriber['subscription']['subscriber']['id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            }else if($responseCode == 400) {
                return array("result" => "error","error" => array("msg" => $subscriber['message']));
            }else{
                return array("result" => "error","error" => array("msg" => $subscriber['message']));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) { 
                $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                return array("result" => "error","error" => array("msg" => isset($response['message'])?$response['message']:"Invalid Parameters"));            
        }
    } 
}