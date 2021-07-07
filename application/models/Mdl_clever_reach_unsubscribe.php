<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_clever_reach_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$cleverReachListId){
        
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $cleverReachListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $cleverReachAccountId     = $providerData['aweber_account']; 
            
            $cleverReachCondition   = array('id' => $cleverReachAccountId);
            $is_single           = true;
            $cleverReachAccountData   = GetAllRecord(CLEVER_REACH_ACCOUNTS, $cleverReachCondition, $is_single);        
                                
            //LIST ID 
            $list_id = $providerData['code'];

            //Check token expire or not
            $clientId = $cleverReachAccountData['client_id'];
            $clientSecret = $cleverReachAccountData['client_secret'];
            $token = $cleverReachAccountData['token'];

            if($cleverReachAccountData['token'] == null || ($cleverReachAccountData['expires_in'] != null && time() > $cleverReachAccountData['expires_in']) ){
                try{
                        // Get Token
                        $token_url = "https://rest.cleverreach.com/oauth/token.php";
                        
                        $headers['grant_type'] = 'client_credentials';
                        $headers['client_id'] = $clientId;
                        $headers['client_secret'] = $clientSecret;
        
                        //send token generate request
                        $getTokenBody = $client->post($token_url, ['form_params' => $headers]); 
                        if($getTokenBody->getStatusCode() == 200){
                            $getTokenResponse = json_decode($getTokenBody->getBody(),true); 
                            $token = $getTokenResponse['access_token'];
                            $expiryTime = $getTokenResponse['expires_in'] + time();

                            $is_insert = FALSE;   
                            $condition = array(
                                'id' => $cleverReachAccountData['id']
                            );
                            $updateRecord = array(
                                'token' => $token,                
                                'expires_in' => $expiryTime                         
                            );
                            ManageData(CLEVER_REACH_ACCOUNTS,$condition,$updateRecord,$is_insert);
                        }

                }catch(\GuzzleHttp\Exception\ClientException $e){
                    $response = json_decode($e->getResponse()->getBody()->getContents(),true);
                    return array("result" => "error","error" => array("msg" => isset($response['error'])? $response['error_description']:"Invalid Token"));            
                }
            }else{
                $token = $cleverReachAccountData['token'];
            }

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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $cleverReachListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }

            // when user not found in our daatbse but exist on esp
            if(empty($liveDeliveryData) && empty($csvCronUserData)) {
                //GET CONTACT USER
                $checkSubscriber = $this->checkSubscriber($token, $list_id, $email);
                $response = $checkSubscriber['response'];
                $getStatusCode = $checkSubscriber['getStatusCode'];
            }

            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success') || ($getStatusCode == 200)){
                // $subscriberUrl = "https://rest.cleverreach.com/v3/groups.json/".$list_id."/receivers/".$email;
                // $body = $client->get($subscriberUrl, [
                //             'headers' => [
                //                     'Authorization' => 'Bearer ' . $token,    
                //                 ]
                //         ]);
                // $response = json_decode($body->getbody(), true);
                // $getStatusCode = $body->getStatusCode();
                if(!isset($getStatusCode)) {
                    $checkSubscriber = $this->checkSubscriber($token, $list_id, $email);
                    $response = $checkSubscriber['response'];
                    $getStatusCode = $checkSubscriber['getStatusCode'];
                }
                // UPDATE SUSBCRIBER STATUS (unsubscribe)
                if(!empty($response) && $getStatusCode == 200){
                    $unsubscriberUrl = "https://rest.cleverreach.com/v3/groups.json/".$list_id."/receivers/".$email."/deactivate";
                    $updateResponse = $client->put($unsubscriberUrl, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token,    
                            ]
                    ]);
                    
                    $updateResponseBody = json_decode($updateResponse->getbody(), true);
                    $name = $response['global_attributes']['firstname'] . " " . $response['global_attributes']['lastname'];
                    return array("result" => "success","data" => array("name" => $name,"updated_at" => date('Y-m-d H:i:s')));
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

    public function checkSubscriber($token, $list_id, $email) {
        // Create a Guzzle client
        $client = new GuzzleHttp\Client();

        $subscriberUrl = "https://rest.cleverreach.com/v3/groups.json/".$list_id."/receivers/".$email;
        $body = $client->get($subscriberUrl, [
                'headers' => [
                        'Authorization' => 'Bearer ' . $token,    
                    ]
                ]);
        $data['response'] = json_decode($body->getbody(), true);
        $data['getStatusCode'] = $body->getStatusCode();
        return $data;
    }
}