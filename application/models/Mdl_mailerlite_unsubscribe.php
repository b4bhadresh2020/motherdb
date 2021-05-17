<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_mailerlite_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$mailerliteListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $mailerliteListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $mailerliteAccountId     = $providerData['aweber_account']; 
            
            $mailerliteAccountCondition   = array('id' => $mailerliteAccountId);
            $is_single           = true;
            $mailerliteAccountData   = GetAllRecord(MAILERLITE_ACCOUNTS, $mailerliteAccountCondition, $is_single);        
            $apiKey = $mailerliteAccountData['api_key'];
                    
            //LIST ID 
            $subscriberUrl = "https://api.mailerlite.com/api/v2/subscribers/". $email;
            $body = $client->get($subscriberUrl, [
                'headers' => ['x-mailerlite-apikey' => $apiKey]
            ]);
            $response = json_decode($body->getbody(), true);
            
            // UPDATE SUSBCRIBER STATUS
            if(isset($response['id']) && $response['email'] = $email){
                $unsubscriberUrl = "https://api.mailerlite.com/api/v2/subscribers/". $response['id'];
                $headers = [
                    'x-mailerlite-apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ]; 

                $updateData ='{"type": "unsubscribed"}';
                $updateResponse = $client->put($unsubscriberUrl, [
                        'body' => $updateData, 
                        'headers' => $headers
                    ]
                );
                $updateResponseBody = json_decode($updateResponse->getbody(), true);
                return array("result" => "success","data" => array("name" => $updateResponseBody['name'],"updated_at" => $updateResponseBody['date_unsubscribe']));
            }else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
    
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}