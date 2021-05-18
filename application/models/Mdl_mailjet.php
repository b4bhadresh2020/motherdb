<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

// require the autoloader class if you haven't used composer to install the package
require_once(APPPATH.'third_party/mailjet/vendor/autoload.php');
use \Mailjet\Resources;

class Mdl_mailjet extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function AddEmailToMailjetSubscriberList($getData,$mailjetListId){
       
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

        // Find tag value
        if(isset($getData["otherLable"]) && isset($getData["other"])){
            $otherLabel = json_decode($getData["otherLable"]);
            $otherData = json_decode($getData["other"]);

            $searchIndex = array_search("Tag",$otherLabel,true);
            if($searchIndex !== FALSE){
                $tagValue = $otherData[$searchIndex];
            }else{
                $tagValue = "";
            }  
        }else if(isset($getData["tag"])){
            $tagValue = $getData["tag"];
        }else{
            $tagValue = "";
        }


        try {
            $mj = new \Mailjet\Client($api_key, $secret_key);
            $body = [
                'Contacts' => [
                    [
                        'Email' => @$getData['emailId'],
                        'IsExcludedFromCampaigns' => 'false',
                        'Name' => @$getData['firstName'] . ' ' . @$getData['lastName'],
                        'Properties' => [
                            'name' => @$getData['firstName'] . ' ' . @$getData['lastName'],
                            'firstname' => @$getData['firstName'],
                            'lastname' => @$getData['lastName'],
                            'phone' => @$getData['phone'],
                            'gender' => @$getData['gender'],
                            'address' => @$getData['address'],
                            'postcode' => @$getData['postCode'],
                            'city' => @$getData['city'],
                            'birthdate' => @$getData['birthDate']. ' 00:00:00',
                            'tag' => $tagValue
                        ]	
                    ]
                ],
                'ContactsLists' => [
                    [
                        'ListID' => $list_id,
                        'Action' => "addforce"
                    ]
                ]
            ];
            $response = $mj->post(Resources::$ContactManagemanycontacts, ['body' => $body]);
            $jobID = $response->getData()[0]['JobID'];

            return array("result" => "success","data" => array("id" => $jobID));
    
            // catch any exceptions thrown during the process and print the errors to screen
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();         
            if($statusCode == "400"){
                return array("result" => "error","error" => array("msg" => $statusCode." - Bad Request"));
            }else{
                return array("result" => "error","error" => array("msg" => $statusCode." - Subscriber already subscribed"));
            }
        }
    }
}