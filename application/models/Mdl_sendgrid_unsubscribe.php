<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_sendgrid_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$sendgridListId){
        
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $sendgridListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $sendgridAccountId     = $providerData['aweber_account']; 
            
            $sendgridAccountCondition   = array('id' => $sendgridAccountId);
            $is_single           = true;
            $sendgridAccountData   = GetAllRecord(SENDGRID_ACCOUNTS, $sendgridAccountCondition, $is_single);        
            
            //LIST ID 
            $list_id = $providerData['code'];            
            $api_key = $sendgridAccountData['api_key']; 
           
            // check user is exist by list & email (live delivery)
            $responseField	= $providerData['response_field'];
            $liveDeliveryData = getLivedeliveryDetail($email, $responseField);
            if(!empty($liveDeliveryData)){
                $emailresponse = json_decode($liveDeliveryData[$responseField],true);
            }

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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $sendgridListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }

            // when user not found in our daatbse but exist on esp
            if(empty($liveDeliveryData) && empty($csvCronUserData)) {
                //GET CONTACT USER
                $checkSubscriber = $this->checkSubscriber($api_key, $list_id, $email);
                $response = $checkSubscriber['response'];
                $getStatusCode = $checkSubscriber['getStatusCode'];
            }
            
            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success') || ($getStatusCode == 200 && !empty($response))){
                
                if(!isset($getStatusCode)) {
                    $checkSubscriber = $this->checkSubscriber($api_key, $list_id, $email);
                    $response = $checkSubscriber['response'];
                    $getStatusCode = $checkSubscriber['getStatusCode'];
                }
                $contactID = @$response['result'][0]['id'];
                
                // UPDATE SUSBCRIBER STATUS (unsubscribe)
                if(!empty($response) && $getStatusCode == 200 && !empty($contactID)){
                    $details = [
                        'recipient_emails' => [$email]
                    ];
                    $apiData = json_encode($details);
                    $unsubscriberUrl = "https://api.sendgrid.com/v3/asm/suppressions/global";
                    $body = $client->post($unsubscriberUrl, [
                        'body' => $apiData, 
                        'headers' => [
                            'Authorization' => 'Bearer '.$api_key,
                            'Content-Type' => 'application/json'
                        ]
                    ]);
                    if($body->getStatusCode() == 201) {
                        $name = @$response['result'][0]['first_name'] . " " . @$response['result'][0]['last_name'];
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

    public function checkSubscriber($api_key, $list_id, $email) {
        
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();
        $getsubscriberUrl = "https://api.sendgrid.com/v3/marketing/contacts/search";

        $query = [
            "query" => "email LIKE '".$email."' AND CONTAINS(list_ids, '".$list_id."')"
        ];
        $searchData = json_encode($query);
        $body = $client->post($getsubscriberUrl, [
            'body' => $searchData,
            'headers' => [
                'Authorization' => 'Bearer '.$api_key,
                'Content-Type' => 'application/json'
            ]
        ]);
        
        $data['response'] = json_decode($body->getbody(), true);
        $data['getStatusCode'] = $body->getStatusCode();
        return $data;
    }
}