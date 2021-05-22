<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_marketing_platform_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$marketingPlatformListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $marketingPlatformListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $marketingPlatformAccountId     = $providerData['aweber_account']; 
            
            $marketingPlatformAccountCondition   = array('id' => $marketingPlatformAccountId);
            $is_single           = true;
            $marketingPlatformAccountData   = GetAllRecord(MARKETING_PLATFORM_ACCOUNTS, $marketingPlatformAccountCondition, $is_single);        

            $apiUsername = $marketingPlatformAccountData['api_username'];
            $apiToken = $marketingPlatformAccountData['api_token'];
            //LIST ID 
            $list_id = $providerData['code'];
                    
            //LIST ID 
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