<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_aweber_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$aweberListId){

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
            $subscriberUrl = AWEBER_API_PATH.'accounts/'.$account_id.'/lists/'.$list_id.'/subscribers';            
           
           // FIND SUBSCRIBER AND GET SELF LINK
            $params = [
                'ws.op' => 'find',
                'email' => $email
            ];
            
            $patchUrl = $subscriberUrl . "?" . http_build_query($params);
            $body = $client->get($patchUrl, [
                    'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);
            
            $response = json_decode($body->getbody(), true);
            
            // UPDATE SUSBCRIBER STATUS
            if(isset($response['entries'][0])){
                $selfUrl = $response['entries'][0]['self_link'];
                
                $updateData = [
                    "status" => "unsubscribed"    
                ];
                $updateResponse = $client->patch($selfUrl, ['json' => $updateData, 'headers' => ['Authorization' => 'Bearer ' . $accessToken]]);
                $updateResponseBody = json_decode($updateResponse->getbody(), true);
                
                return array("result" => "success","data" => array("name" => $updateResponseBody['name'],"updated_at" => $updateResponseBody['unsubscribed_at']));
            }else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
    
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}