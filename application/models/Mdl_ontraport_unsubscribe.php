<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_ontraport_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$ontraportListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $ontraportListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);
            $ontraportAccountId     = $providerData['aweber_account']; 
            
            $ontraportAccountCondition   = array('id' => $ontraportAccountId);
            $is_single           = true;
            $ontraportAccountData   = GetAllRecord(ONTRAPORT_ACCOUNTS, $ontraportAccountCondition, $is_single);        

            $appApiKey = $ontraportAccountData['app_id'];     
            $apiKey = $ontraportAccountData['api_key'];
            
            //LIST ID 
            $list_id = $providerData['code'];
                
            // //FIND subscriber
            // $userData=array('emailId'=>$email,'providerId'=>$providerData['id'],'status'=>1);
            // $getSingleUser = GetAllRecord(EMAIL_HISTORY_DATA,$userData,$is_single);
            // check user is exist by list & email (live delivery)
            $responseField	= $providerData['response_field'];
            $liveDeliveryData = getLivedeliveryDetail($email, $responseField);
            $emailresponse = json_decode($liveDeliveryData[$responseField],true); 
            if(!empty($liveDeliveryData)) {
                $subsciber_id = $emailresponse['data']['id'];
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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $ontraportListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }
            if(!empty($csvCronUserData)) {
                $subsciber_id = $csvEmailresponse['data']['id'];
            }

            // when user not found in our daatbse but exist on esp
            if(empty($liveDeliveryData) && empty($csvCronUserData)) {
                //GET CONTACT USER
                $checkContactUrl = "https://api.ontraport.com/1/Contacts?search=".$email;
                $headers = [
                            'Api-Key' => $apiKey,
                            'Api-Appid' => $appApiKey,
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ]; 
                $contactResponse = $client->get($checkContactUrl,[
                    'headers' => $headers
                ]);
                $getResponse = (json_decode($contactResponse->getBody(),true));  
                $getResponseCode = $contactResponse->getStatusCode();
                if($getResponseCode == 200 && !empty($getResponse['data'])) {
                    $subsciber_id = $getResponse['data'][0]['id'];
                } 
            }

            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success') || ($getResponseCode == 200 && !empty($getResponse['data']))){
                // FIND SUBSCRIBER
                // $responseData = json_decode($getSingleUser['response']);
                // $subsciber_id = $responseData->data->id;
                $details = [
                    'objectID' => 0,
                    'remove_list' => [$list_id],
                    'ids'       => [$subsciber_id]
                ];
                $data = json_encode($details);
                $subscriberUrl = "https://api.ontraport.com/1/objects/tag";
                $headers = [
                    'Api-Key' => $apiKey,
                    'Api-Appid' => $appApiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ];   
                $client->delete($subscriberUrl, [
                    'body' => $data, 
                    'headers' => $headers
                ]);  
                
                //GET CONTACT USER
                $getContactUrl = "https://api.ontraport.com/1/Contact";
                $getContactDataArr = [
                    'id' => $subsciber_id
                ];
                $getContact = json_encode($getContactDataArr);
                $getContactResponse = $client->get($getContactUrl,[
                    'body'=> $getContact,
                    'headers' => $headers
                ]);
                $response = (json_decode($getContactResponse->getBody(),true));
                return array("result" => "success","data" => array("name" => $response['data']['firstname']." ".$response['data']['lastname'],"updated_at" => date('Y-m-d H:i:s')));
            }else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}