<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_expert_sender extends CI_Model {
   
    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToExpertSenderSubscriberList($getData,$expertSenderListId){
      
        try{
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $expertSenderListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
            $expertSenderAccountId     = $providerData['aweber_account']; 
            
            $expertSenderCondition   = array('id' => $expertSenderAccountId);
            $is_single           = true;
            $expertSenderAccountData   = GetAllRecord(EXPERT_SENDER_ACCOUNTS, $expertSenderCondition, $is_single); 
            
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
            $logPath    = FCPATH."log/expert_sender/";
            $fileName   = date("Ymd")."_log.txt"; 
            $logFile    = fopen($logPath.$fileName,"a");
            $logData    = $getData['emailId']." ".$getData['firstName']." ".$getData['lastName']." ".time()."\n";
            fwrite($logFile,$logData);
            fclose($logFile);

            $api_key = $expertSenderAccountData['api_key'];       
            $subscribeUrl = EXPERT_SENDER_API_PATH . 'Api/Subscribers/';
     
            $details = '<?xml version="1.0" encoding="UTF-8"?><ApiRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema"><ApiKey>'.$api_key.'</ApiKey><ReturnData>true</ReturnData><VerboseErrors>true</VerboseErrors><Data xsi:type="Subscriber"><Mode>AddAndUpdate</Mode><Force>true</Force><ListId>'.$list_id.'</ListId><Email>'.@$getData['emailId'].'</Email><Firstname>'.@$getData['firstName'].'</Firstname><Lastname>'.@$getData['lastName'].'</Lastname><Properties><Property><Id>1</Id><Value xsi:type="xs:string">'.@$getData['phone'].'</Value></Property><Property><Id>2</Id><Value xsi:type="xs:string">'.@$getData['gender'].'</Value></Property><Property><Id>3</Id><Value xsi:type="xs:string">'.@$getData['address'].'</Value></Property><Property><Id>4</Id><Value xsi:type="xs:string">'.@$getData['postCode'].'</Value></Property><Property><Id>5</Id><Value xsi:type="xs:string">'.@$getData['city'].'</Value></Property><Property><Id>6</Id><Value xsi:type="xs:date">'.@$getData['birthDate'].'</Value></Property><Property><Id>7</Id><Value xsi:type="xs:string">'.@$tagValue.'</Value></Property></Properties></Data></ApiRequest>';

            $curl = curl_init($subscribeUrl);
            curl_setopt($curl, CURLOPT_URL, $subscribeUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Content-Type: text/xml",
                "Accept: text/xml",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $details);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            $xml = simplexml_load_string($response);
            $subscriber = json_decode(json_encode($xml),true);  

            if ($responseCode == 201) {    
                $subscriber_id = $subscriber['Data']['SubscriberData']['Id'];
                return array("result" => "success","data" => array("id" => $subscriber_id));    
            } else if($responseCode == 400) {
                $errorMsg = 'Unknown Error Response';
                if(isset($subscriber['ErrorMessage']['Messages']['Message'])) {
                    $errorMsg = $subscriber['ErrorMessage']['Messages']['Message'];
                } else {
                    $errorMsg = $subscriber['ErrorMessage']['Message'];
                }
                return array("result" => "error","error" => array("msg" => $responseCode." - ".$errorMsg));
            } else{
                return array("result" => "error","error" => array("msg" => "Unknown Error Response"));
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {              
            $statusCode = $e->getResponse()->getStatusCode(); 
            if($statusCode == "400" || $statusCode == "401" || $statusCode == "403"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request - ". $e->getMessage()));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }            
        }
    } 
}