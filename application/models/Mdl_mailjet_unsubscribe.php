<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
// require_once(APPPATH.'third_party/mailjet/vendor/autoload.php');
// use \Mailjet\Resources;

class Mdl_mailjet_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$mailjetListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $mailjetListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $mailjetAccountId     = $providerData['aweber_account']; 
            
            $mailjetCondition   = array('id' => $mailjetAccountId);
            $is_single           = true;
            $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);        
            $api_key = $mailjetAccountData['api_key'];
            $secret_key = $mailjetAccountData['secret_key'];
                    
            //LIST ID 
            $list_id = $providerData['code'];
            $emailresponse = null;
            $csvEmailresponse = null;

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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $mailjetListId, $csvResponseField);
                if(!empty($csvCronUserData)){
                    $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
                }
            }

            //if(empty($emailresponse) && empty($csvCronUserData)) {
                 // get user contact details
                $checkContactUrl = "https://api.mailjet.com/v3/REST/contact/". $email;
                $getContactBody = $client->get($checkContactUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'auth' => [
                        $api_key, $secret_key
                    ]
                ]);
                $getSubscriber = json_decode($getContactBody->getBody(),true);
                $getStatusCode = $getContactBody->getStatusCode();
            //}

            // check user is exist
            // $condition = array('providerId' => $mailjetListId ,'emailId' => $email, 'status'=> '1');
            // $is_single = TRUE;
            // $getEmailDetail = GetAllRecord(EMAIL_HISTORY_DATA,$condition,$is_single,array(),array(),array(),'emailId,response');
            // $emailresponse = json_decode($getEmailDetail['response'],true);            

           
            // if(!empty($getSubscriber) && $getStatusCode == 200){
            if((!empty($liveDeliveryData) && ($emailresponse != null && $emailresponse['result'] == 'success') && $getStatusCode == 200 && !empty($getSubscriber['Data'])) || (!empty($csvCronUserData) && ($csvEmailresponse != null && $csvEmailresponse['result'] == 'success') && $getStatusCode == 200 && !empty($getSubscriber['Data']))){
                $unsubscriberUrl = "https://api.mailjet.com/v3/REST/contact/managemanycontacts";
                $body = [
                    'Contacts' => [
                        [
                            'Email' => $email,
                            'IsExcludedFromCampaigns' => 'false'
                        ]
                    ],
                    'ContactsLists' => [
                        [
                            'ListID' => $list_id,
                            'Action' => "unsub"
                        ]
                    ]
                ];
                $bodyData = json_encode($body);

                try {
                    $body = $client->post($unsubscriberUrl, [
                        'body' => $bodyData, 
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ],
                        'auth' => [
                            $api_key, $secret_key
                        ]
                    ]);                   
                    
                    // get user contact details
                    $getContactUrl = "https://api.mailjet.com/v3/REST/contact/". $email;
                    $contactBody = $client->get($getContactUrl, [
                        'headers' => [
                            'Content-Type' => 'application/json'
                        ],
                        'auth' => [
                            $api_key, $secret_key
                        ]
                    ]);
                    $subscriber = json_decode($contactBody->getBody(),true);
                    $name = $subscriber['Data'][0]['Name'];
                    $updated_at = date('Y-m-d H:i:s');
                  
                    // UPDATE SUSBCRIBER STATUS
                    return array("result" => "success","data" => array("name" => $name,"updated_at" => $updated_at));
                } catch(Exception $e) {
                    $statusCode = $e->getCode();
                    if($statusCode == 404) {
                        return array("result" => "error","msg" => "Subscriber not found");
                    } else if($statusCode == 401) {
                        return array("result" => "error","msg" => "Unauthorized");
                    } else {
                        $errorMsg = $e->getMessage();
                        if(strpos($errorMsg, "OpenSSL SSL_connect") !== false){
                            return array("result" => "error","msg" => "Account is temporary closed by ESP");
                        } else{
                            return array("result" => "error","msg" => $errorMsg);
                        }
                    }
                }
            } else {
                return array("result" => "error","msg" => "Subscriber not found");
            }     
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request ".$e->getMessage());
        }
    } 
}