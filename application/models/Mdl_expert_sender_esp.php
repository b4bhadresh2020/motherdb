<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_expert_sender_esp extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function GetExpertSenderUnsubscriberList($getData, $currentTimestamp){
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();     

        // API key
        $api_key = $getData['api_key'];   
        $today = date('Y-m-d');    
        try{                      
            // Get clever reach unsubscribe data send api called
            $getUnsubscriberUrl = EXPERT_SENDER_API_PATH . 'Api/RemovedSubscribers?apiKey='.$api_key.'&startDate='.$today.'&endDate='.$today;
            $ch = curl_init($getUnsubscriberUrl);
            curl_setopt($ch, CURLOPT_URL,$getUnsubscriberUrl);
            $headers = array(
                "Content-Type: text/xml",
                "Accept: text/xml",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $getSubscriber = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);

            $xml = simplexml_load_string($getSubscriber);
            $results = json_decode(json_encode($xml),true);
            if($statusCode == 200) {
                return array("result" => "success","msg" => $results['Data']['RemovedSubscribers']);
            } else {
                $errorLog = array("responseCode" => $statusCode, "currentTimestamp" => $currentTimestamp, "providers_id" => $getData['providers_id'], "response" => json_encode($results));

                //LOG ENTRY
                $logPath    = FCPATH."log/expert_sender_esp/";
                $fileName   = date("Ymd")."_log.txt"; 
                $logFile    = fopen($logPath.$fileName,"a");
                $logData    = json_encode($errorLog)." "."\n";
                fwrite($logFile,$logData);
                fclose($logFile);

                return array("result" => "error","msg" => json_encode($results));
            }
        }catch(\GuzzleHttp\Exception\ClientException $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(),true);
            $errorLog = array("currentTimestamp" => $currentTimestamp, "providers_id" => $getData['providers_id'], "response" => json_encode($response));
            
            //LOG ENTRY
            $logPath    = FCPATH."log/expert_sender_esp/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = json_encode($errorLog)." "."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            return array("result" => "error","msg" => json_encode($response));
        }
    }
}