<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_active_campaign_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$activeCampaignListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $activeCampaignListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $activeCampaignAccountId     = $providerData['aweber_account']; 
            
            $activeCampaignAccountCondition   = array('id' => $activeCampaignAccountId);
            $is_single           = true;
            $activeCampaignAccountData   = GetAllRecord(ACTIVE_CAMPAIGN_ACCOUNTS, $activeCampaignAccountCondition, $is_single);        

            $apiUrl = $activeCampaignAccountData['api_url'];
            $apiKey = $activeCampaignAccountData['api_key']; 
            //LIST ID 
            $list_id = $providerData['code'];         
                    
            // //Get subscriber(exist or not) from email history table
            // $emailResponse = getSubscribeDetails($activeCampaignListId,$email);
            // $subscriptionId = $emailResponse['data']['id'];
            
            // check user is exist by list & email (live delivery)
            $responseField	= $providerData['response_field'];
            $liveDeliveryData = getLivedeliveryDetail($email, $responseField);
            
            if(!empty($liveDeliveryData)) {
                $emailresponse = json_decode($liveDeliveryData[$responseField],true);
                $subscriptionId = $emailresponse['data']['id'];
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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $activeCampaignListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }
            if(!empty($csvCronUserData)) {
                $subscriptionId = $csvEmailresponse['data']['id'];
            }

            // already unsubscribe user unsub using email
            if(empty($liveDeliveryData) && empty($csvCronUserData)) {
                // check user is exist by list & email (live delivery)
                $alreadyLiveDeliveryData = getAlreadySubscribeLivedeliveryDetail($email, $responseField);
                
                // check user is exist by list & email (user csv)
                if(!empty($getUserIdsStr)) {                   
                    $alreadyCsvCronUserData = getAlreadySubscribeCsvUserDetail($getUserIdsStr, $activeCampaignListId, $csvResponseField);
                }               
            }
           
            if((!empty($alreadyLiveDeliveryData) || !empty($alreadyCsvCronUserData)) || (empty($liveDeliveryData) && empty($csvCronUserData) && empty($alreadyLiveDeliveryData) && empty($alreadyCsvCronUserData))) {
                $getsubscriberUrl = $apiUrl . "/api/3/contacts/?email=" . $email;
                $body = $client->get($getsubscriberUrl,[
                    'headers' => [
                        'Api-Token' => $apiKey
                    ]
                ]);
                $getsubscriber = json_decode($body->getBody(),true);
                $getResponseCode = $body->getStatusCode();
                if($getResponseCode == 200 && !empty($getsubscriber['contacts'])) {
                    $subscriptionId = $getsubscriber['contacts'][0]['id'];
                }
            }
            
            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success') || ((!empty($alreadyLiveDeliveryData)) || (!empty($alreadyCsvCronUserData))) || ($getResponseCode == 200 && !empty($getsubscriber['contacts']))){
                $subscriberUrl = $apiUrl . "/api/3/contacts/" . $subscriptionId;
                $body = $client->get($subscriberUrl,[
                    'headers' => [
                        'Api-Token' => $apiKey
                    ]
                ]);
                $responseCode = $body->getStatusCode();
                $response = json_decode($body->getBody(),true);
                // UPDATE SUSBCRIBER STATUS (unsubscribe)
                if(!empty($responseCode) && $responseCode == 200 ){
                    
                    $unsubscriberUrl = $apiUrl . "/api/3/contactLists";
                    $updateDetail = [
                        "contactList" => [
                            "list" => $list_id,
                            "contact" => $subscriptionId,
                            "status" => 2
                        ]
                    ];
                    $updateData = json_encode($updateDetail);
                    $updateResponse = $client->post($unsubscriberUrl, [
                            'body' => $updateData, 
                            'headers' => [
                                'Api-Token' => $apiKey,
                                'Content-Type' => 'application/json'
                            ]
                        ]
                    );
                    
                    $name = $response['contact']['firstName'] . " " . $response['contact']['lastName'];
                    return array("result" => "success","data" => array("name" => $name,"updated_at" => date('Y-m-d H:i:s')));
                }else{
                    return array("result" => "error","msg" => "Subscriber not found");
                }
            } else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
    
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}