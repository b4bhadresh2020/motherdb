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
                $csvCronUserData = getCsvUserDetail($getUserIdsStr, $expertSenderListId, $csvResponseField);
                $csvEmailresponse = json_decode($csvCronUserData[$csvResponseField],true);
            }
            
            if((!empty($liveDeliveryData) && $emailresponse['result'] == 'success') || (!empty($csvCronUserData) && $csvEmailresponse['result'] == 'success')){
                $subscriberUrl = EXPERT_SENDER_API_PATH . 'Api/Subscribers?apiKey='.$api_key.'&email='.$email.'&option=Full';
                $ch = curl_init($subscriberUrl);
                curl_setopt($ch, CURLOPT_URL,$subscriberUrl);
                $headers = array(
                    "Content-Type: text/xml",
                    "Accept: text/xml",
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $getSubscriber = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);

                $xml = simplexml_load_string($getSubscriber);
                $subscriber = json_decode(json_encode($xml),true);
                if($statusCode == 200) {
                    $subscriberId = $subscriber['Data']['Id'];
                    $subscriberName = $subscriber['Data']['Firstname'] . " ". $subscriber['Data']['Lastname'];
                    $subscriberListId = $subscriber['Data']['StateOnLists']['StateOnList']['ListId'];
                    
                    // UPDATE SUSBCRIBER STATUS (unsubscribe)
                    if(isset($subscriberId) && $subscriber['Data']['Email'] == $email && $subscriberListId == $list_id){

                        $unsubscriberUrl = EXPERT_SENDER_API_PATH . 'Api/Subscribers/'.$subscriberId.'?apiKey='.$api_key.'&listId='.$list_id.'&channel=Email';
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $unsubscriberUrl);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        $result = curl_exec($curl);
                        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);             
                        
                        return array("result" => "success","data" => array("name" => $subscriberName,"updated_at" => date('Y-m-d H:i:s')));
                    }else{
                        return array("result" => "error","msg" => "Subscriber not found");
                    }
                } else{
                    return array("result" => "error","msg" => "Subscriber not found");
                }
            } else {
                return array("result" => "error","msg" => "Subscriber not found");
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}