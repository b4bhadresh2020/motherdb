<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_aweber extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToAweberSubscriberList($getData,$language,$aweberListId){

        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $aweberListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $aweberAccountId     = $providerData['aweber_account']; 
            
            $aweberAccountCondition   = array('id' => $aweberAccountId);
            $is_single           = true;
            $aweberAccountData   = GetAllRecord(AWEBER_ACCOUNTS, $aweberAccountCondition, $is_single);        
            $accessToken = $aweberAccountData['accessToken'];
                    
            //LIST ID 
            $list_id = $providerData['code'];      
            $account_id = $providerData['account_id'];      
            $newsubscriberUrl = AWEBER_API_PATH.'accounts/'.$account_id.'/lists/'.$list_id.'/subscribers';

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
            // $logPath    = FCPATH."log/aweber/";
            // $fileName   = date("Ymd")."_log.txt"; 
            // $logFile    = fopen($logPath.$fileName,"a");
            // $logData    = $providerData['aweber_account']." ".$providerData['listname']." ".$getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            // fwrite($logFile,$logData);
            // fclose($logFile);
            
            $data = array(
                'ad_tracking' => generateRandomString(10),
                'email' => $getData['emailId'],
                'name' => $getData['firstName'].' '.$getData['lastName'],
                'ip_address' => '',
                'custom_fields' => array('Fname' => $getData['firstName'],'Lname' => $getData['lastName'],'phone no' => $getData['phone'],'gender' => strtolower($getData['gender']), 'birthdate' =>  $getData['birthDate'])
            );

            if(!empty($tagValue)){
                $data['tags'] = array($tagValue);
            }

            $body = $client->post($newsubscriberUrl, [
                    'json' => $data, 
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);
            
            $responseCode = $body->getStatusCode();                
            if ($responseCode == 201) {
                $subscriberUrl = $body->getHeader('Location')[0];
                $subscriberResponse = $client->get($subscriberUrl,
                    ['headers' => ['Authorization' => 'Bearer ' . $accessToken]])->getBody();
                $subscriber = json_decode($subscriberResponse, true);
                $subscriber_id = $subscriber['id'];    
                return array("result" => "success","data" => array("id" => $subscriber_id));
            } else {
                return array("result" => "error","error" => array("msg" => "Bad Request or duplicate email Id"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if($statusCode == "400"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));
            }
        }
    } 
}