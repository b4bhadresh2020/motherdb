<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_aweber_api extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function AddEmailToAweberSubscriberList($getData,$customeField,$aweberListId){

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
            
            $data = array(
                'ad_tracking' => generateRandomString(10),
                'email' => $getData['email'],
                'name' => $getData['name'],
                'custom_fields' => $customeField
            );            

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