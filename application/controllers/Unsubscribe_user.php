<?php

use function GuzzleHttp\json_decode;

defined('BASEPATH') OR exit('No direct script access allowed');

class Unsubscribe_user extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();  
        $this->is_main_webhook_called = 0;

    }

    public function mailjet($account){
        $subscribeDetailJson = file_get_contents('php://input');
        $subscribeDetail = \json_decode($subscribeDetailJson,true);
        $email = $subscribeDetail[0]['email'];

        // FIND PROVIDER ID FROM LIST ID
        $listId =  $subscribeDetail[0]['mj_list_id'];
        $providerId = getProviderID($account, $listId, MAILJET);

        if($this->is_main_webhook_called == 0) {  
            // GET ALREDY UNSUBSCRIBER LIST
            $condition       = array('email' => $email,'esp' => MAILJET,'provider_id' => $providerId,'status' => 1);
            $is_single       = false;
            $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');

            if(empty($getUnsubscribeData)) {
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
                ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                $this->unsubFromOtherEsp($email, MAILJET, $providerId);
            }
        }
    }
    
    public function marketingPlatform($account){
        $subscribeDetailJson = file_get_contents('php://input');
        $subscribeDetail = \json_decode($subscribeDetailJson,true);
        $email = $subscribeDetail['emailaddress'];

        // FIND PROVIDER ID FROM LIST ID
        $listId =  $subscribeDetail['listid'];
        $providerId = getProviderID($account, $listId, MARKETING_PLATFORM);

        if($this->is_main_webhook_called == 0) {
            // GET ALREDY UNSUBSCRIBER LIST
            $condition       = array('email' => $email,'esp' => MARKETING_PLATFORM,'provider_id' => $providerId,'status' => 1);
            $is_single       = false;
            $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');

            if(empty($getUnsubscribeData)) {
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
                ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                $this->unsubFromOtherEsp($email, MARKETING_PLATFORM, $providerId);
            }
        }
    }  
    
    public function activeCampaign($account){
        $email = $_POST['contact']['email'];

        // FIND PROVIDER ID FROM LIST ID
        $listId =  $_POST['list'][0]['id'];
        $providerId = getProviderID($account, $listId, ACTIVE_CAMPAIGN);

        if($this->is_main_webhook_called == 0) {
            // GET ALREDY UNSUBSCRIBER LIST
            $condition       = array('email' => $email,'esp' => ACTIVE_CAMPAIGN,'provider_id' => $providerId,'status' => 1);
            $is_single       = false;
            $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');

            if(empty($getUnsubscribeData)) {
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
                ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                $this->unsubFromOtherEsp($email, ACTIVE_CAMPAIGN, $providerId);
            }
        }
    }
    
    public function ontraport($account,$listId){
        $email = $this->input->post('email');
        // FIND PROVIDER ID FROM LIST ID
        $providerId = getProviderID($account, $listId, ONTRAPORT);

        if($this->is_main_webhook_called == 0) {
            // GET ALREDY UNSUBSCRIBER LIST
            $condition       = array('email' => $email,'esp' => ONTRAPORT,'provider_id' => $providerId,'status' => 1);
            $is_single       = false;
            $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');

            if(empty($getUnsubscribeData)) {                
                // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                $unsubscribeData = [
                    "provider_id" => $providerId,
                    "esp"         => ONTRAPORT,
                    "email"       => $email,
                    "name"        => NULL,
                    "status"      => 1, // success
                    "unsub_method"=> 1, // 1 - webhook(by main ESP)
                    "response"    => date('Y-m-d H:i:s')
                ];
                ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                $this->unsubFromOtherEsp($email, ONTRAPORT, $providerId);
            }
        }
    }

    public function unsubFromOtherEsp($email, $mainProvider, $mainProviderId) {
        $this->is_main_webhook_called = 1;
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
            // GET UNSUBSCRIBER LIST USING EMAIL ID THAT ALREADY HANDLE WHEN EMPLOYEE UNSUB USER (+ WEBHOOK (TO HANDLE DUPLICATE ENTRY WHEN EMPLOYEE UNSUB USER AND AT THAT MOMENT WEBHOOK EVENT IS ALSO CALLED))
            $condition       = array('email' => $email);
            $is_single       = false;
            $empExistUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'provider_id');
          
            $empProviderID = array();
            foreach ($empExistUnsubscribeData as $unsubscriber) {
                if(!in_array($unsubscriber['provider_id'],$empProviderID)){
                    $empProviderID[] = $unsubscriber['provider_id'];
                }
            }

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

            // // GET COUNTRY OF USER (LIVE DELIVERY)
            // $getProviderDetail = getProviderDetail($mainProviderId);
            // $responseField = $getProviderDetail['response_field'];
            // $liveDeliveryData = getLivedeliveryDetail($email, $responseField);
            // $userCountry = $liveDeliveryData['country'];

            // GET COUNTRY OF USER (LIVE DELIVERY)
            $country = '';
            $condition       = array('emailId' => $email);
            $is_single       = true;
            $liveDeliveryData    = GetAllRecord(LIVE_DELIVERY_DATA, $condition, $is_single,[],[],[],'country');
            if(!empty($liveDeliveryData)) {
                $country = $liveDeliveryData['country'];
            }

            // GET COUNTRY OF USER (USER(CSV))
            $condition       = array('emailId' => $email);
            $is_single       = true;
            $csvUserData    = GetAllRecord(USER, $condition, $is_single,[],[],[],'country');
            if(!empty($csvUserData)) {
                $country = $csvUserData['country'];
            }

            foreach($otherProviders as $provider => $other){
                if($settingsData[$other] == 1 ) { 
                    if($provider == MAILJET) {
                        $this->load->model('mdl_mailjet_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                            'mailjet_accounts.status' => 1
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        }                      
                        if($mainProvider == MAILJET) {
                            $listCondition['id !='] = $mainProviderId;
                        }

                        $is_single             = false;
                        // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                        $getListIDByCountry = JoinData(PROVIDERS,$listCondition,MAILJET_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");

                        $list = array_column($getListIDByCountry,'id');
                        foreach ($list as $listID) {   
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_mailjet_unsubscribe->makeUnsubscribe($email,$listID);

                                    // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                    if($response["result"] == "success"){
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => MAILJET,
                                            "email"       => $email,
                                            "name"        => $response["data"]["name"],
                                            "status"      => 1, // success
                                            "unsub_method"=> 2, // webhook (by other ESP)	
                                            "response"    => $response["data"]["updated_at"]                                            
                                        ];
                                        $fn = 'MAILJET IF IF';
                                    }else{
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => MAILJET,
                                            "email"       => $email,
                                            "name"        => NULL,
                                            "status"      => 2, // error
                                            "unsub_method"=> 2, // webhook (by other ESP)	
                                            "response"    => $response["msg"]                                           
                                        ];
                                        $fn = 'MAILJET IF ELSE';
                                    }
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => MAILJET,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)	
                                        "response"    => "Already unsubscribed"
                                    ];
                                    $fn = 'MAILJET ELSE';
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }
                            }              
                        } 
                    } else if($provider == MARKETING_PLATFORM) { 
                        $this->load->model('mdl_marketing_platform_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        } 
                        if($mainProvider == MARKETING_PLATFORM) {
                            $listCondition['id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

                        $list = array_column($getListIDByCountry,'id');
        
                        foreach ($list as $listID) {
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){   
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_marketing_platform_unsubscribe->makeUnsubscribe($email,$listID);
                                
                                    // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                    if($response["result"] == "success"){
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => MARKETING_PLATFORM,
                                            "email"       => $email,
                                            "name"        => $response["data"]["name"],
                                            "status"      => 1, // success
                                            "unsub_method"=> 2, // webhook (by other ESP)
                                            "response"    => $response["data"]["updated_at"]                                            
                                        ];
                                        $fn = 'MARKETING_PLATFORM IF IF';
                                    }else{
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => MARKETING_PLATFORM,
                                            "email"       => $email,
                                            "name"        => NULL,
                                            "status"      => 2, // error
                                            "unsub_method"=> 2, // webhook (by other ESP)
                                            "response"    => $response["msg"]                                           
                                        ];
                                        $fn = 'MARKETING_PLATFORM IF ELSE';
                                    }
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => MARKETING_PLATFORM,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];
                                    $fn = 'MARKETING_PLATFORM ELSE';
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }                
                    } else if($provider == ONTRAPORT) {
                        $this->load->model('mdl_ontraport_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        }
                        if($mainProvider == ONTRAPORT) {
                            $listCondition['id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

                        $list = array_column($getListIDByCountry,'id');
                        foreach ($list as $listID) {       
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){    
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_ontraport_unsubscribe->makeUnsubscribe($email,$listID);
                                    // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                    if($response["result"] == "success"){
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => ONTRAPORT,
                                            "email"       => $email,
                                            "name"        => $response["data"]["name"],
                                            "status"      => 1, // success
                                            "unsub_method"=> 2, // webhook (by other ESP)	
                                            "response"    => $response["data"]["updated_at"]
                                        ];
                                        $fn = 'ONTRAPORT IF IF';
                                    }else{
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => ONTRAPORT,
                                            "email"       => $email,
                                            "name"        => NULL,
                                            "status"      => 2, // error
                                            "unsub_method"=> 2, // webhook (by other ESP)	
                                            "response"    => $response["msg"]
                                        ];
                                        $fn = 'ONTRAPORT IF ELSE';
                                    }
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => ONTRAPORT,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)	
                                        "response"    => "Already unsubscribed"
                                    ];
                                    $fn = 'ONTRAPORT ELSE';
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }
                            }
                        }               
                    } else if($provider == ACTIVE_CAMPAIGN) {
                        $this->load->model('mdl_active_campaign_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        }
                        if($mainProvider == ACTIVE_CAMPAIGN) {
                            $listCondition['id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');

                        $list = array_column($getListIDByCountry,'id');
        
                        foreach ($list as $listID) {   
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_active_campaign_unsubscribe->makeUnsubscribe($email,$listID);
                                
                                    // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                    if($response["result"] == "success"){
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => ACTIVE_CAMPAIGN,
                                            "email"       => $email,
                                            "name"        => $response["data"]["name"],
                                            "status"      => 1, // success
                                            "unsub_method"=> 2, // webhook (by other ESP)
                                            "response"    => $response["data"]["updated_at"]
                                        ];
                                        $fn = 'ACTIVE_CAMPAIGN IF IF';
                                    }else{
                                        $data = [
                                            "provider_id" => $listID,
                                            "esp"         => ACTIVE_CAMPAIGN,
                                            "email"       => $email,
                                            "name"        => NULL,
                                            "status"      => 2, // error
                                            "unsub_method"=> 2, // webhook (by other ESP)
                                            "response"    => $response["msg"]
                                        ];
                                        $fn = 'ACTIVE_CAMPAIGN IF ELSE';
                                    }
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => ACTIVE_CAMPAIGN,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];
                                    $fn = 'ACTIVE_CAMPAIGN ELSE';
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }               
                    }
                }   
            }           
        }
    }
}   