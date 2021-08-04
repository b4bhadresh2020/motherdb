<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_clever_reach_esp extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }
    
    function GetCleverReachUnsubscriberList($getData, $currentTimestamp){
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();     

        //Check token expire or not
        $clientId = $getData['client_id'];
        $clientSecret = $getData['client_secret'];
        $token = $getData['token'];

        if($getData['token'] == null || ($getData['expires_in'] != null && time() > $getData['expires_in']) ){
            try{
                    // Get Token
                    $token_url = "https://rest.cleverreach.com/oauth/token.php";
                    
                    $headers['grant_type'] = 'client_credentials';
                    $headers['client_id'] = $clientId;
                    // $headers['client_id'] = '165451415';
                    $headers['client_secret'] = $clientSecret;
    
                    //send token generate request
                    $getTokenBody = $client->post($token_url, ['form_params' => $headers]); 
                    if($getTokenBody->getStatusCode() == 200){
                        $getTokenResponse = json_decode($getTokenBody->getBody(),true); 
                        $token = $getTokenResponse['access_token'];
                        $expiryTime = $getTokenResponse['expires_in'] + time();

                        $is_insert = FALSE;   
                        $condition = array(
                            'id' => $getData['id']
                        );
                        $updateRecord = array(
                            'token' => $token,                
                            'expires_in' => $expiryTime                         
                        );
                        ManageData(CLEVER_REACH_ACCOUNTS,$condition,$updateRecord,$is_insert);
                    }

            }catch(\GuzzleHttp\Exception\ClientException $e){
                $response = json_encode($e->getResponse()->getBody()->getContents(),true);
                //LOG ENTRY
                $logPath    = FCPATH."log/clever_reach_esp/";
                $fileName   = date("Ymd")."_log.txt"; 
                $logFile    = fopen($logPath.$fileName,"a");
                $logData    = $response." "."\n";
                fwrite($logFile,$logData);
                fclose($logFile);
                return array("result" => "error","msg" => "false");
            }
        }else{
            $token = $getData['token'];
        }
        
        $results = [];
        try{                      
            $getUnsubscriberUrl = "https://rest.cleverreach.com/v3/receivers/filter.json";
            // Get clever reach unsubscribe data send api called
            $timestampHourAgo = $currentTimestamp - (60*60) - 1;
            $detail = array(
                "groups"    => [$getData['code']],
                "operator"  => "AND",                   
                "rules"     => [
                    [
                        "field" => "deactivated",
                        "logic" => "bg",
                        "condition" => $timestampHourAgo
                    ],
                    [
                        "field" => "deactivated",
                        "logic" => "sm",
                        "condition" => $currentTimestamp
                    ]
                ]
            );  
            $detailJson = json_encode($detail);
            $body = $client->post($getUnsubscriberUrl, [
                'body' => $detailJson, 
                'headers' => 
                    [
                        'Authorization' => 'Bearer ' . $token,    
                    ]
            ]);
            $results = json_decode($body->getBody(),true);
            return array("result" => "success","msg" => $results);
        }catch(\GuzzleHttp\Exception\ClientException $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(),true);
            $errorLog = array("currentTimestamp" => $currentTimestamp, "timestampHourAgo" => $timestampHourAgo, "groupCode" => $getData['code'], "response" => json_encode($response));
            
            //LOG ENTRY
            $logPath    = FCPATH."log/clever_reach_esp/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = json_encode($errorLog)." "."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            return array("result" => "error","msg" => json_encode($response));
        }
    } 

}