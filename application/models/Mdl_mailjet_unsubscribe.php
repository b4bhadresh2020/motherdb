<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'third_party/mailjet/vendor/autoload.php');
use \Mailjet\Resources;

class Mdl_mailjet_unsubscribe extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    
    function makeUnsubscribe($email,$mailjetListId){
        
        try{
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

            // check user is exist
            $condition = array('providerId' => $mailjetListId ,'emailId' => $email, 'status'=> '1');
            $is_single = TRUE;
            $getEmailDetail = GetAllRecord(EMAIL_HISTORY_DATA,$condition,$is_single,array(),array(),array(),'emailId,response');
            $emailresponse = json_decode($getEmailDetail['response'],true);

            if(!empty($getEmailDetail) && $emailresponse['result'] == 'success'){
                $mj = new \Mailjet\Client($api_key, $secret_key);
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
                
                $response = $mj->post(Resources::$ContactManagemanycontacts, ['body' => $body]);
                // get user contact details
                $contactResponse = $mj->get(Resources::$Contact, ['id' => $email]);
                $name = $contactResponse->getData()[0]['Name'];
                $updated_at = date('Y-m-d H:i:s');
              
                // UPDATE SUSBCRIBER STATUS
                return array("result" => "success","data" => array("name" => $name,"updated_at" => $updated_at));
            } else {
                return array("result" => "error","msg" => "Subscriber not found");
            }     
        }catch (\GuzzleHttp\Exception\ClientException $e) {
            return array("result" => "error","msg" => "Bad request");
        }
    } 
}