<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendgrid_esp extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function GetSendgridUnsubscriberList($getData, $currentTimestamp, $timestampHourAgo){
        
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();     

        // API key
        $api_key = $getData['api_key'];  
        if (!file_exists(FCPATH."log/sendgrid_esp/")) {   
            mkdir(FCPATH."log/sendgrid_esp/", 0777, true);
        }
        try{   
            $getUnsubscriberUrl = "https://api.sendgrid.com/v3/suppression/unsubscribes?start_time=".$timestampHourAgo."&end_time=".$currentTimestamp;
            // Get clever reach unsubscribe data send api called
            $body = $client->get($getUnsubscriberUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$api_key,
                ]
            ]);
            $responseCode = $body->getStatusCode();
            $results = json_decode($body->getBody(),true);
            if ($responseCode == 200) { 
                return array("result" => "success","msg" => $results);
            } else {
                $errorLog = array("responseCode" => $responseCode, "currentTimestamp" => $currentTimestamp, "apiKey" => $getData['api_key'], "sendgridAccountId" => $getData['id'], "response" => json_encode($results));

                //LOG ENTRY
                $logPath    = FCPATH."log/sendgrid_esp/";
                $fileName   = date("Ymd")."_log.txt"; 
                $logFile    = fopen($logPath.$fileName,"a");
                $logData    = json_encode($errorLog)." "."\n";
                fwrite($logFile,$logData);
                fclose($logFile);

                return array("result" => "error","msg" => json_encode($results));
            }
        }catch(\GuzzleHttp\Exception\ClientException $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(),true);
            $errorLog = array("currentTimestamp" => $currentTimestamp, "apiKey" => $getData['api_key'], "sendgridAccountId" => $getData['id'], "response" => json_encode($response));
            
            //LOG ENTRY
            $logPath    = FCPATH."log/sendgrid_esp/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = json_encode($errorLog)." "."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            return array("result" => "error","msg" => json_encode($response));
        }
    }
}