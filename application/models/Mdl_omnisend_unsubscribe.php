<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_omnisend_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$omnisendListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $omnisendListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $omnisendAccountId     = $providerData['aweber_account']; 
            
            $omnisendAccountCondition   = array('id' => $omnisendAccountId);
            $is_single           = true;
            $omnisendAccountData   = GetAllRecord(OMNISEND_ACCOUNTS, $omnisendAccountCondition, $is_single);        
            
            //LIST ID 
            $list_id = $providerData['code'];
            $api_key = $omnisendAccountData['api_key']; 

            // check user is exist by list & email (live delivery)
            $responseField	= $providerData['response_field'];
            $liveDeliveryData = getLivedeliveryDetail($email, $responseField);
            $emailresponse = json_decode($liveDeliveryData[$responseField],true);

            // check user is exist by list & email (user csv)
            $condition = array(
                'emailId' => $email
            );
            $is_single = FALSE;
            $getUserIds = GetAllRecord(USER, $condition, $is_single, array(), array(), array(), 'userId');
            $getUserIdsStr = implode(',',array_column($getUserIds, 'userId'));
            if(!empty($getUserIdsStr)) {
                $emailServiceProvider = $providerData['provider']; 
                $csvResponseField = getCsvUserResponseField($emailServiceProvider);
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $omnisendListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }

            // when user not found in our daatbse but exist on esp
            if(empty($liveDeliveryData) && empty($csvCronUserData)) {
                //GET CONTACT USER
                $checkSubscriber = $this->checkSubscriber($api_key, $email);
                $response = $checkSubscriber['response'];
                $getStatusCode = $checkSubscriber['getStatusCode'];
            }
           
            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success') || ($getStatusCode == 200 && !empty($response))){
                
                if(!isset($getStatusCode)) {
                    $checkSubscriber = $this->checkSubscriber($api_key, $email);
                    $response = $checkSubscriber['response'];
                    $getStatusCode = $checkSubscriber['getStatusCode'];
                }
                $contactID = @$response['contacts'][0]['contactID'];
                
                // UPDATE SUSBCRIBER STATUS (unsubscribe)
                if(!empty($response) && $getStatusCode == 200 && !empty($contactID)){
                    $todayDateTime = date("Y-m-d\TH:i:s\Z", strtotime(date('Y-m-d h:i:s')));
                    $details = [
                        'identifiers' => [
                            [
                                'type' => 'email',
                                'id' => $email,
                                'channels' => [
                                    'email' => [
                                        'status' => 'unsubscribed',
                                        'statusDate' => $todayDateTime
                                    ]
                                ]
                            ]
                        ]
                    ];
                    $data = json_encode($details);  
                    $unsubscriberUrl = "https://api.omnisend.com/v3/contacts/".$contactID;
                    $updateResponse = $client->patch($unsubscriberUrl, [
                        'body' => $data, 
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'x-api-key' => $api_key
                        ]
                    ]);
                    
                    if($updateResponse->getStatusCode() == 200) {
                        $name = @$response['contacts'][0]['firstName'] . " " . @$response['contacts'][0]['lastName'];
                        return array("result" => "success","data" => array("name" => $name,"updated_at" => date('Y-m-d H:i:s')));
                    } else {
                        return array("result" => "error","msg" => "Subscriber not unsubscribed");
                    }
                }else{
                    return array("result" => "error","msg" => "Subscriber not found");
                }
            } else {
                return array("result" => "error","msg" => "Subscriber not found");
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 

    public function checkSubscriber($api_key, $email) {
        
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();
        $getsubscriberUrl = "https://api.omnisend.com/v3/contacts?email=".$email;
        $body = $client->get($getsubscriberUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key
            ]
        ]); 
        $data['response'] = json_decode($body->getbody(), true);
        $data['getStatusCode'] = $body->getStatusCode();
        return $data;
    }
}