<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_expert_sender_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$expertSenderListId){
        try{
            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $expertSenderListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $expertSenderAccountId     = $providerData['aweber_account']; 
            
            $expertSenderAccountCondition   = array('id' => $expertSenderAccountId);
            $is_single           = true;
            $expertSenderAccountData   = GetAllRecord(EXPERT_SENDER_ACCOUNTS, $expertSenderAccountCondition, $is_single);        
            
            //LIST ID 
            $list_id = $providerData['code'];
            $api_key = $expertSenderAccountData['api_key']; 
            echo /***need to implement from here.. */
            die;
            $subscriberUrl = "https://api.mailmailmail.net/v1.1/Subscribers/GetSubscriberDetails?Apiusername=".$apiUsername."&Apitoken=".$apiToken."&listid=".$list_id."&emailaddress=".$email."";
            
            $body = $client->get($subscriberUrl);
            $response = json_decode($body->getbody(), true);
            
            // UPDATE SUSBCRIBER STATUS (unsubscribe)
            if(isset($response['subscriberid']) && $response['emailaddress'] = $email){
                $unsubscriberUrl = "https://api.mailmailmail.net/v1.1/Subscribers/UnsubscribeSubscriberEmail";
                $updateDetail = [
                    'Apiusername' => $apiUsername,
                    'Apitoken'  => $apiToken,
                    'listid' => $list_id,
                    'emailaddress' => $email
                ];
                $updateData = json_encode($updateDetail);
                $updateResponse = $client->post($unsubscriberUrl, [
                        'body' => $updateData, 
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ]
                    ]
                );
                
                $updateResponseBody = json_decode($updateResponse->getbody(), true);
                $name = $response['contact_fields'][2]['fieldvalue'] . " " . $response['contact_fields'][3]['fieldvalue'];
                return array("result" => "success","data" => array("name" => $name,"updated_at" => date('Y-m-d H:i:s')));
            }else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
    
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}