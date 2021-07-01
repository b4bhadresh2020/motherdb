<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_clever_reach extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToCleverReachSubscriberList($getData,$convertkitListId){
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
            $accountData        = GetAllRecord(CLEVER_REACH_ACCOUNTS, $accountCondition, $is_single);        
            
             //LOG ENTRY
             $logPath    = FCPATH."log/clever_reach/";
             $fileName   = date("Ymd")."_log.txt"; 
             $logFile    = fopen($logPath.$fileName,"a");
             $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
             fwrite($logFile,$logData);
             fclose($logFile);


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

           //Check token expire or not
            $clientId = $accountData['client_id'];
            $clientSecret = $accountData['client_secret'];
            $token = $accountData['token'];

            if($accountData['token'] == null || ($accountData['expires_in'] != null && time() > $accountData['expires_in']) ){
                try{
                        // Get Token
                        $token_url = "https://rest.cleverreach.com/oauth/token.php";
                        //$data = array("grant_type" => "client_credentials");
                        $headers['grant_type'] = 'client_credentials';
                        $headers['client_id'] = $clientId;
                        $headers['client_secret'] = $clientSecret;
        
                        //send token generate request
                        $getTokenBody = $client->post($token_url, ['form_params' => $headers]); 
                        if($getTokenBody->getStatusCode() == 200){
                            $getTokenResponse = json_decode($getTokenBody->getBody(),true); 
                            $token = $getTokenResponse['access_token'];
                            $expiryTime = $getTokenResponse['expires_in'] + time();

                            $is_insert = FALSE;   
                            $condition = array(
                                'id' => $accountData['id']
                            );
                            $updateRecord = array(
                                'token' => $token,                
                                'expires_in' => $expiryTime                         
                            );
                            ManageData(CLEVER_REACH_ACCOUNTS,$condition,$updateRecord,$is_insert);
                        }

                }catch(\GuzzleHttp\Exception\ClientException $e){
                    $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                    return array("result" => "error","error" => array("msg" => isset($response['error'])? $response['error_description']:"Invalid Token"));            
                }
            }else{
                $token = $accountData['token'];
            }

            try{
                 //LIST ID 
                $list_id = $providerData['code'];      
                $newsubscriberUrl = "https://rest.cleverreach.com/v3/groups/".$list_id."/receivers";

                // Clever Retch data send api called
                $receiver = array(
                    "email"         => $getData['emailId'],
                    "registered"    => time(),
                    "activated"     =>  time(),
                    "deactivated"   => "0", 
                    "global_attributes" => array()
                );  

                if(!empty($getData['firstName'])){
                    $receiver['global_attributes']["firstname"] = $getData['firstName'];
                }

                if(!empty($getData['lastName'])){
                    $receiver['global_attributes']["lastname"]  = $getData['lastName'];
                }

                if(!empty($getData['phone'])){
                    $receiver['global_attributes']["phone"]     =   @$getData['phone'];
                }

                if(!empty($getData['gender'])){
                    $receiver['global_attributes']["gender"]    = $getData['gender'];
                }

                if(!empty($getData['address'])){
                    $receiver['global_attributes']["address"]   = $getData['address'];
                }

                if(!empty($getData['postCode'])){
                    $receiver['global_attributes']["postcode"]  = $getData['postCode'];
                }

                if(!empty($getData['city'])){
                    $receiver['global_attributes']["city"]      = $getData['city'];
                }

                if(!empty($getData['birthDate'])){
                    $receiver['global_attributes']["birthdate"]  = @$getData['birthDate'];
                }

                if(!empty($tagValue)){
                    $receiver['tags'][]  = $tagValue;
                }

                $data = json_encode($receiver);
                $body = $client->post($newsubscriberUrl, [
                    'body' => $data, 
                    'headers' => 
                        [
                            'Authorization' => 'Bearer ' . $token,    
                        ]
                ]);
                $responseCode = $body->getStatusCode();
                $subscriber = json_decode($body->getBody(),true);
                if ($responseCode == 200) {                
                    $subscriber_id = $subscriber['id'];    
                    return array("result" => "success","data" => array("id" => $subscriber_id));
                }else{
                    return array("result" => "error","error" => array("msg" => "Invalid Parameters"));
                }
            }catch(\GuzzleHttp\Exception\ClientException $e){
                $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                return array("result" => "error","error" => array("msg" => isset($response['error'])? $response['error']['message']:"Invalid Parameters"));            
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) { 
            $response = json_decode($e->getResponse()->getBody()->getContents(),true);
            return array("result" => "error","error" => array("msg" => isset($response['message'])?$response['message']:"Invalid Parameters"));            
        }
    } 

}