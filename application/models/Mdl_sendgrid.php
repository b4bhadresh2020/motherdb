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
            $sendgridAccountId   = $providerData['aweber_account'];           
            
            $sendgridCondition   = array('id' => $sendgridAccountId);
            $is_single           = true;
            $sendgridAccountData   = GetAllRecord(SENDGRID_ACCOUNTS, $sendgridCondition, $is_single);

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
            if (!file_exists(FCPATH."log/sendgrid/")) {   
                mkdir(FCPATH."log/sendgrid/", 0777, true);
            }
            $logPath    = FCPATH."log/sendgrid/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            //LIST ID 
            $list_id = $providerData['code'];      
            $accessToken = $sendgridAccountData['api_key'];
            $newsubscriberUrl = "https://api.sendgrid.com/v3/marketing/contacts";

            $data = array(
                "list_ids" => array($list_id),
                "contacts" => array(
                    array(
                        "email"  => $getData['emailId'],
                    )
                )
            );
            
            if(!empty($getData['firstName'])) {
                $data['contacts'][0]['first_name'] = $getData['firstName'];
            }
            if(!empty($getData['lastName'])) {
                $data['contacts'][0]['last_name'] = $getData['lastName'];
            }
            if(!empty($getData['phone'])) {
                $data['contacts'][0]['phone_number'] = $getData['phone'];
            }
            if(!empty($getData['address'])) {
                $data['contacts'][0]['address_line_1'] = $getData['address'];
            }
            if(!empty($getData['city'])) {
                $data['contacts'][0]['city'] = $getData['city'];
            }
            if(!empty($getData['postCode'])) {
                $data['contacts'][0]['postal_code'] = $getData['postCode'];
            }   
            if(!empty($getData['gender'])){
                $data['contacts'][0]['custom_fields']['w1_T'] = strtolower($getData['gender']);
            }
            if(!empty($getData['birthDate'])){
                $data['contacts'][0]['custom_fields']['w2_T'] = $getData['birthDate'];
            }
            if(!empty($tagValue)){
                $data['contacts'][0]['custom_fields']['w3_T'] = $tagValue;
            }

            $body = $client->put($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);            

            $responseCode = $body->getStatusCode();
            $subscriber = json_decode($body->getBody(),true);

            if ($responseCode == 202) {                
                $subscriber_id = $subscriber['job_id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            }else if($responseCode == 400) {
                return array("result" => "error","error" => array("msg" => $subscriber['errors']['message']));
            }else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {        
            $statusCode =  $e->getResponse()->getStatusCode();   
            return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request - ". $e->getMessage()));      
        }
    } 
}