<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_convertkit_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
        require_once(FCPATH.'vendor/autoload.php');
    }

    
    function makeUnsubscribe($email,$convertkitListId){
        try{
            // Create a Guzzle client
            $client = new GuzzleHttp\Client();

            // fetch mail provider data from providers table
            $providerCondition   = array('id' => $convertkitListId);
            $is_single           = true;
            $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);


            $convertkitAccountId     = $providerData['aweber_account'];             
            $convertkitAccountCondition   = array('id' => $convertkitAccountId);
            $is_single           = true;
            $convertkitAccountData   = GetAllRecord(CONVERTKIT_ACCOUNTS, $convertkitAccountCondition, $is_single);     
           
            //API KEY  
            $api_key =  $convertkitAccountData['secret_key'];   
            
            //FIND subscriber
            $userData=array('emailId'=>$email,'providerId'=>$providerData['id'],'status'=>1);
            $getSingleUser = GetAllRecord(EMAIL_HISTORY_DATA,$userData,$is_single);
            if(!empty($getSingleUser)){
                // FIND SUBSCRIBER
                $responseData = json_decode($getSingleUser['response']);
                $subsciber_id = $responseData->data->id;
                $subscriberUrl = "https://api.convertkit.com/v3/subscribers/".$subsciber_id."?api_secret=".$api_key;
                $findProvider = $client->get($subscriberUrl);
                $findProviderData = (json_decode($findProvider->getBody(),true));
                // FIND SUBSCRIBER AND TAG REMOVE
                if(isset($findProviderData['subscriber'])){
                        //LIST ID
                        $list_id = $providerData['code'];      
                        $tagSubscriberUrl = CONVERTKIT_API_PATH.'tags/'.$list_id.'/unsubscribe'; 
                        $params = [
                            'api_secret' => $api_key,
                            'email' => $email
                        ];
                        $client->post($tagSubscriberUrl, [
                            'json' => $params, 
                            'headers' => ['charset' => "utf-8",
                                        'Content-Type' => "application/json"
                                        ]
                        ]);
                        return array("result" => "success","data" => array("name" => $findProviderData['subscriber']['first_name'],"updated_at" => date('Y-m-d H:i:s')));
                }else{
                    return array("result" => "error","msg" => "Subscriber not found");
                }
            }else{
                return array("result" => "error","msg" => "Subscriber not found");
            }
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}