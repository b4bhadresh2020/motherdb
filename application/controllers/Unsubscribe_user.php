<?php

use function GuzzleHttp\json_decode;

defined('BASEPATH') OR exit('No direct script access allowed');

class Unsubscribe_user extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();        
    }

    public function mailjet(){
        $subscribeDetail = [
            [
              "event"=> "unsub",
              "time"=> 1624012541,
              "MessageID"=> 57983847985302380,
              "Message_GUID"=> "30f7b5e5-72ad-4f36-99d5-8e995392ec59",
              "email"=> "solhoi@live.dk",
              "mj_campaign_id"=> 102954,
              "mj_contact_id"=> 92285981,
              "customcampaign"=> "mj.nl=40170",
              "mj_list_id"=> 29610,
              "ip"=> "109.58.188.200",
              "geo"=> "DK",
              "agent"=> "Mozilla/5.0 (iPad; CPU OS 12_5_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.2 Mobile/15E148 Safari/604.1",
              "CustomID"=> "",
              "Payload"=> ""
            ]
        ];
        $subscribeDetailJson = json_encode($subscribeDetail);
        $subscribeDetail = \json_decode($subscribeDetailJson,true);
        $email = $subscribeDetail[0]['email'];
        
        // FIND PROVIDER ID FROM LIST ID
        $listId =  $subscribeDetail[0]['mj_list_id'];
        $providerId = getProviderID($listId, MAILJET);
        
        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
        $unsubscribeData = [
            "provider_id" => $providerId,
            "esp"         => MAILJET,
            "email"       => $email,
            "name"        => NULL,
            "status"      => 1, // success
            "unsub_method"=> 1, // 1 - webhook(by main ESP)
            "response"    => date('Y-m-d H:i:s')
        ];
        // ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
        $this->unsubFromOtherEsp($email, MAILJET);        
    }
    
    public function marketingPlatform(){
        $subscribeDetail = [
            "listid"=> "80010",
            "event"=> "Unsubscribe",
            "profileid"=> 84259084,
            "emailaddress"=> "karnavi@gmail.com",
            "event_source"=> ""
        ];
        $subscribeDetailJson = json_encode($subscribeDetail);
        $subscribeDetail = \json_decode($subscribeDetailJson,true);
        $email = $subscribeDetail['emailaddress'];
        // $email = "tishu.codexive@gmail.com";
        
        // FIND PROVIDER ID FROM LIST ID
        $listId =  $subscribeDetail['listid'];
        $providerId = getProviderID($listId, MARKETING_PLATFORM);

        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
        $unsubscribeData = [
            "provider_id" => $providerId,
            "esp"         => MARKETING_PLATFORM,
            "email"       => $email,
            "name"        => NULL,
            "status"      => 1, // success
            "unsub_method"=> 1, // 1 - webhook(by main ESP)
            "response"    => date('Y-m-d H:i:s')
        ];
        // ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
        $this->unsubFromOtherEsp($email, MARKETING_PLATFORM);
    }  
    
    public function activeCampaign(){
        $email = $_POST['contact']['email'];

        // FIND PROVIDER ID FROM LIST ID
        $listId =  $_POST['list'][0]['id'];
        $providerId = getProviderID($listId, ACTIVE_CAMPAIGN);

        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
        $unsubscribeData = [
            "provider_id" => $providerId,
            "esp"         => ACTIVE_CAMPAIGN,
            "email"       => $email,
            "name"        => NULL,
            "status"      => 1, // success
            "unsub_method"=> 1, // 1 - webhook(by main ESP)
            "response"    => date('Y-m-d H:i:s')
        ];
        // ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
        $this->unsubFromOtherEsp($email, ACTIVE_CAMPAIGN);
        
    }
    
    public function ontraport(){
        $email = $this->input->post('email');

        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
        $unsubscribeData = [
            "provider_id" => NULL,
            "esp"         => ONTRAPORT,
            "email"       => $email,
            "name"        => NULL,
            "status"      => 1, // success
            "unsub_method"=> 1, // 1 - webhook(by main ESP)
            "response"    => date('Y-m-d H:i:s')
        ];
        // ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
        $this->unsubFromOtherEsp($email, ONTRAPORT);
    
    }

    public function unsubFromOtherEsp($email, $mainProvider) {
        $providerCondition   = array('main_provider' => $mainProvider);
        $is_single           = true;
        $settingsData        = GetAllRecord(WEBHOOK_UNSUBSCRIBE_SETTINGS, $providerCondition, $is_single);
      
        if(!empty($settingsData)) {
            $otherProviders = [
                '9' => 'mailjet',
                '11' => 'marketing_platform',
                '12' => 'ontraport',
                '13' => 'active_campaign',
            ];

            // GET UNSUBSCRIBER LIST USING EMAIL ID
            $condition       = array('email' => $email,'status' => 1);
            $is_single       = false;
            $existUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'provider_id');
          
            $providerID = array();
            foreach ($existUnsubscribeData as $unsubscriber) {
                if(!in_array($unsubscriber['provider_id'],$providerID)){
                    $providerID[] = $unsubscriber['provider_id'];
                }
            }

            // foreach($otherProviders as $provider => $other){
            //     if($settingsData[$other] == 1 ) { 
            //         if($provider == MAILJET) {
            //             $this->load->model('mdl_mailjet_unsubscribe');
            //             //LIST ID EMPTY GET COUNTRY WISE LIST  
            //             $listCondition  = array(
            //                 'provider' => $provider
            //             );
            //             $is_single             = false;
            //             $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

            //             $list = array_column($getListIDByCountry,'id');
                    
            //             foreach ($list as $listID) {   
            //                 // CHECK EMAIL ALREADY UNSUBSCRIBE
            //                 if(!in_array($listID,$providerID)){
            //                     // SEND DATA FOR UNSUBSCRIBE
            //                     $response = $this->mdl_mailjet_unsubscribe->makeUnsubscribe($email,$listID);

            //                     // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
            //                     if($response["result"] == "success"){
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => MAILJET,
            //                             "email"       => $email,
            //                             "name"        => $response["data"]["name"],
            //                             "status"      => 1, // success
            //                             "unsub_method"=> 2, // webhook (by other ESP)	
            //                             "response"    => $response["data"]["updated_at"]
            //                         ];
            //                     }else{
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => MAILJET,
            //                             "email"       => $email,
            //                             "name"        => NULL,
            //                             "status"      => 2, // error
            //                             "unsub_method"=> 2, // webhook (by other ESP)	
            //                             "response"    => $response["msg"]
            //                         ];
            //                     }
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }else{
            //                     $data = [
            //                         "provider_id" => $listID,
            //                         "esp"         => MAILJET,
            //                         "email"       => $email,
            //                         "name"        => NULL,
            //                         "status"      => 3, // already unsubscribed
            //                         "unsub_method"=> 2, // webhook (by other ESP)	
            //                         "response"    => "Already unsubscribed"
            //                     ];
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }
                                            
            //             } 
            //         } else if($provider == MARKETING_PLATFORM) { 
            //             $this->load->model('mdl_marketing_platform_unsubscribe');
            //             //LIST ID EMPTY GET COUNTRY WISE LIST  
            //             $listCondition  = array(
            //                 'provider' => $provider
            //             );
            //             $is_single             = false;
            //             $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

            //             $list = array_column($getListIDByCountry,'id');
        
            //             foreach ($list as $listID) {   
            //                 // CHECK EMAIL ALREADY UNSUBSCRIBE
            //                 if(!in_array($listID,$providerID)){
                                
            //                     // SEND DATA FOR UNSUBSCRIBE
            //                     $response = $this->mdl_marketing_platform_unsubscribe->makeUnsubscribe($email,$listID);
                            
            //                     // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
            //                     if($response["result"] == "success"){
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => MARKETING_PLATFORM,
            //                             "email"       => $email,
            //                             "name"        => $response["data"]["name"],
            //                             "status"      => 1, // success
            //                             "unsub_method"=> 2, // webhook (by other ESP)
            //                             "response"    => $response["data"]["updated_at"]
            //                         ];
            //                     }else{
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => MARKETING_PLATFORM,
            //                             "email"       => $email,
            //                             "name"        => NULL,
            //                             "status"      => 2, // error
            //                             "unsub_method"=> 2, // webhook (by other ESP)
            //                             "response"    => $response["msg"]
            //                         ];
            //                     }
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }else{
            //                     $data = [
            //                         "provider_id" => $listID,
            //                         "esp"         => MARKETING_PLATFORM,
            //                         "email"       => $email,
            //                         "name"        => NULL,
            //                         "status"      => 3, // already unsubscribed
            //                         "unsub_method"=> 2, // webhook (by other ESP)
            //                         "response"    => "Already unsubscribed"
            //                     ];
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }
            //             }                
            //         } else if($provider == ONTRAPORT) {
            //             $this->load->model('mdl_ontraport_unsubscribe');
            //             //LIST ID EMPTY GET COUNTRY WISE LIST  
            //             $listCondition  = array(
            //                 'provider' => $provider
            //             );
            //             $is_single             = false;
            //             $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

            //             $list = array_column($getListIDByCountry,'id');
            //             foreach ($list as $listID) {           
            //                 // CHECK EMAIL ALREADY UNSUBSCRIBE
            //                 if(!in_array($listID,$providerID)){
                               
            //                     // SEND DATA FOR UNSUBSCRIBE
            //                     $response = $this->mdl_ontraport_unsubscribe->makeUnsubscribe($email,$listID);
            //                     // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
            //                     if($response["result"] == "success"){
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => ONTRAPORT,
            //                             "email"       => $email,
            //                             "name"        => $response["data"]["name"],
            //                             "status"      => 1, // success
            //                             "unsub_method"=> 2, // webhook (by other ESP)	
            //                             "response"    => $response["data"]["updated_at"]
            //                         ];
            //                     }else{
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => ONTRAPORT,
            //                             "email"       => $email,
            //                             "name"        => NULL,
            //                             "status"      => 2, // error
            //                             "unsub_method"=> 2, // webhook (by other ESP)	
            //                             "response"    => $response["msg"]
            //                         ];
            //                     }
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }else{
            //                     $data = [
            //                         "provider_id" => $listID,
            //                         "esp"         => ONTRAPORT,
            //                         "email"       => $email,
            //                         "name"        => NULL,
            //                         "status"      => 3, // already unsubscribed
            //                         "unsub_method"=> 2, // webhook (by other ESP)	
            //                         "response"    => "Already unsubscribed"
            //                     ];
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }
            //             }               
            //         } else if($provider == ACTIVE_CAMPAIGN) {
            //             $this->load->model('mdl_active_campaign_unsubscribe');
            //             //LIST ID EMPTY GET COUNTRY WISE LIST  
            //             $listCondition  = array(
            //                 'provider' => $provider
            //             );
            //             $is_single             = false;
            //             $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

            //             $list = array_column($getListIDByCountry,'id');
        
            //             foreach ($list as $listID) {   
            //                 // CHECK EMAIL ALREADY UNSUBSCRIBE
            //                 if(!in_array($listID,$providerID)){
                                
            //                     // SEND DATA FOR UNSUBSCRIBE
            //                     $response = $this->mdl_active_campaign_unsubscribe->makeUnsubscribe($email,$listID);
                            
            //                     // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
            //                     if($response["result"] == "success"){
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => ACTIVE_CAMPAIGN,
            //                             "email"       => $email,
            //                             "name"        => $response["data"]["name"],
            //                             "status"      => 1, // success
            //                             "unsub_method"=> 2, // webhook (by other ESP)
            //                             "response"    => $response["data"]["updated_at"]
            //                         ];
            //                     }else{
            //                         $data = [
            //                             "provider_id" => $listID,
            //                             "esp"         => ACTIVE_CAMPAIGN,
            //                             "email"       => $email,
            //                             "name"        => NULL,
            //                             "status"      => 2, // error
            //                             "unsub_method"=> 2, // webhook (by other ESP)
            //                             "response"    => $response["msg"]
            //                         ];
            //                     }
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }else{
            //                     $data = [
            //                         "provider_id" => $listID,
            //                         "esp"         => ACTIVE_CAMPAIGN,
            //                         "email"       => $email,
            //                         "name"        => NULL,
            //                         "status"      => 3, // already unsubscribed
            //                         "unsub_method"=> 2, // webhook (by other ESP)
            //                         "response"    => "Already unsubscribed"
            //                     ];
            //                     // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
            //                     ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
            //                 }
            //             }               
            //         }
            //     }   
            // }
        }
    }
}