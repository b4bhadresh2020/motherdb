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

    public function expertSender() {
        //  Get all list of expert sender account
        $condition  = array(
            'provider' => EXPERT_SENDER,
            'expert_sender_accounts.status' => 1,
            // 'providers.id' => 149
        );
        $is_single = false;
        $getExpertSenderLists = JoinData(PROVIDERS,$condition,EXPERT_SENDER_ACCOUNTS,"aweber_account","id","left",$is_single,array(),'providers.*,expert_sender_accounts.*,providers.id AS providers_id');
        
        $currentTimestamp = time();
        $timestampHourAgo = $currentTimestamp - (60*60) - 1;
       
        $this->load->model('mdl_expert_sender_esp');
        foreach($getExpertSenderLists as $getExpertSenderList) {
            $getUnsubscriberData = $this->mdl_expert_sender_esp->GetExpertSenderUnsubscriberList($getExpertSenderList, $currentTimestamp); 
                                     
            if($getUnsubscriberData['result'] == 'success' && !empty($getUnsubscriberData['msg'])) {
                $unsubscribers = $getUnsubscriberData['msg'];
                foreach($unsubscribers as $unsubscriber) {
                    $unsubscribeTimestamp = strtotime($unsubscriber['UnsubscribedOn']);
                    if($unsubscribeTimestamp < $currentTimestamp && $unsubscribeTimestamp > $timestampHourAgo) {
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        $unsubscribeData = [
                            "provider_id" => $getExpertSenderList['providers_id'],
                            "esp"         => EXPERT_SENDER,
                            "email"       => $unsubscriber['Email'],
                            "status"      => 1, // success
                            "unsub_method"=> 1, // 1 - webhook(by main ESP)
                            "response"    => date('Y-m-d H:i:s', $unsubscribeTimestamp)
                        ];
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                        $this->unsubFromOtherEsp($unsubscriber['Email'], EXPERT_SENDER, $getExpertSenderList['providers_id']);
                    } 
                }
            }
        }
    }

    public function cleverReach() {
        //  Get all list of cleverreach account
        $condition  = array(
            'provider' => CLEVER_REACH,
            'clever_reach_accounts.status' => 1,
            // 'providers.id' => 160
        );
        $is_single = false;
        $getCleverReachLists = JoinData(PROVIDERS,$condition,CLEVER_REACH_ACCOUNTS,"aweber_account","id","left",$is_single,array(),'providers.*,clever_reach_accounts.*,providers.id AS providers_id');  

        $currentTimestamp = time();
        $this->load->model('mdl_clever_reach_esp');
        foreach($getCleverReachLists as $getCleverReachList) {
            
            $getUnsubscriberData = $this->mdl_clever_reach_esp->GetCleverReachUnsubscriberList($getCleverReachList, $currentTimestamp);           
            if($getUnsubscriberData['result'] == 'error' && $getUnsubscriberData['msg'] == 'false') {
                break;
            }
            if($getUnsubscriberData['result'] == 'success') {
                $unsubscribers = $getUnsubscriberData['msg'];
                foreach($unsubscribers as $unsubscriber) {
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    $unsubscribeData = [
                        "provider_id" => $getCleverReachList['providers_id'],
                        "esp"         => CLEVER_REACH,
                        "email"       => $unsubscriber['email'],
                        "name"        => $unsubscriber['global_attributes']['firstname'] . " " . $unsubscriber['global_attributes']['lastname'],
                        "status"      => 1, // success
                        "unsub_method"=> 1, // 1 - webhook(by main ESP)
                        "response"    => date('Y-m-d H:i:s', $unsubscriber['deactivated'])
                    ];
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                    $this->unsubFromOtherEsp($unsubscriber['email'], CLEVER_REACH, $getCleverReachList['providers_id']);
                }
            }
        }
    }

    public function omnisend() {
        //  Get all list of omnisend account
        $condition  = array(
            'provider' => OMNISEND,
            'omnisend_accounts.status' => 1,
        );
        $is_single = false;
        $getOmnisendLists = JoinData(PROVIDERS,$condition,OMNISEND_ACCOUNTS,"aweber_account","id","left",$is_single,array(),'providers.*,omnisend_accounts.*,providers.id AS providers_id');
        
        $currentTimestamp = time();
        $timestampHourAgo = $currentTimestamp - (60*60) - 1;
       
        $this->load->model('mdl_omnisend_esp');
        foreach($getOmnisendLists as $getOmnisendList) {
            $getUnsubscriberData = $this->mdl_omnisend_esp->GetOmnisendUnsubscriberList($getOmnisendList, $currentTimestamp);                 
            if($getUnsubscriberData['result'] == 'success') {
                $unsubscribers = @$getUnsubscriberData['msg']['contacts'];
                foreach($unsubscribers as $unsubscriber) {
                    // pre($unsubscriber);
                    $key = array_search('unsubscribed', array_column($unsubscriber['statuses'], 'status'));
                    $unsubscribeTimestamp = strtotime($unsubscriber['statuses'][$key]['date']);

                    if($unsubscribeTimestamp < $currentTimestamp && $unsubscribeTimestamp > $timestampHourAgo) {
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        $unsubscribeData = [
                            "provider_id" => $getOmnisendList['providers_id'],
                            "esp"         => OMNISEND,
                            "email"       => $unsubscriber['email'],
                            "name"        => $unsubscriber['firstName'] . " " . $unsubscriber['lastName'],
                            "status"      => 1, // success
                            "unsub_method"=> 1, // 1 - webhook(by main ESP)
                            "response"    => date('Y-m-d H:i:s', $unsubscribeTimestamp)
                        ];
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                        $this->unsubFromOtherEsp($unsubscriber['email'], OMNISEND, $getOmnisendList['providers_id']);
                    }
                }
            }
        }
    }

    public function sendgrid() {
        //  Get all list of sendgrid account
        $condition  = array(
            'provider' => SENDGRID,
            'sendgrid_accounts.status' => 1,
        );
        $is_single = false;
        $getSendgridLists = JoinData(PROVIDERS,$condition,SENDGRID_ACCOUNTS,"aweber_account","id","left",$is_single,array(),'providers.*,sendgrid_accounts.*,providers.id AS providers_id');
        
        $currentTimestamp = time();
        $timestampHourAgo = $currentTimestamp - (60*60) - 1;
        
        $this->load->model('mdl_sendgrid_esp');
        foreach($getSendgridLists as $getSendgridList) {
            $getUnsubscriberData = $this->mdl_sendgrid_esp->GetSendgridUnsubscriberList($getSendgridList, $currentTimestamp, $timestampHourAgo);
            if($getUnsubscriberData['result'] == 'success') {
                $unsubscribers = @$getUnsubscriberData['msg'];
                foreach($unsubscribers as $unsubscriber) {
                    // pre($unsubscriber);
                    $unsubscribeTimestamp = $unsubscriber['created'];
                   
                    if($unsubscribeTimestamp < $currentTimestamp && $unsubscribeTimestamp > $timestampHourAgo) {
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        $unsubscribeData = [
                            "provider_id" => $getSendgridList['providers_id'],
                            "esp"         => SENDGRID,
                            "email"       => $unsubscriber['email'],
                            "name"        => NULL,
                            "status"      => 1, // success
                            "unsub_method"=> 1, // 1 - webhook(by main ESP)
                            "response"    => date('Y-m-d H:i:s', $unsubscribeTimestamp)
                        ];
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$unsubscribeData,true);
                        $this->unsubFromOtherEsp($unsubscriber['email'], SENDGRID, $getSendgridList['providers_id']);
                        return;
                    }
                }
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
                '15' => 'clever_reach',
                '16' => 'omnisend',
                '14' => 'expert_sender',
                '5' => 'sendgrid'
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
                            $listCondition['providers.id !='] = $mainProviderId;
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

                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    
                                    if(empty($getUnsubscribeData)) {
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
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
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
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }
                            }              
                        } 
                    } else if($provider == MARKETING_PLATFORM) { 
                        // $this->load->model('mdl_marketing_platform_unsubscribe');
                        // //LIST ID EMPTY GET COUNTRY WISE LIST  
                        // $listCondition  = array(
                        //     'provider' => $provider,
                        //     'marketing_platform_accounts.status' => 1
                        // );  
                        // if(!empty($country)) {
                        //     $listCondition['country'] = $country;
                        // } 
                        // if($mainProvider == MARKETING_PLATFORM) {
                        //     $listCondition['providers.id !='] = $mainProviderId;
                        // }
                        // $is_single             = false;
                        // // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                        // $getListIDByCountry = JoinData(PROVIDERS,$listCondition,MARKETING_PLATFORM_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");

                        // $list = array_column($getListIDByCountry,'id');
        
                        // foreach ($list as $listID) {
                        //     // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                        //     if(!in_array($listID,$empProviderID)){   
                        //         // CHECK EMAIL ALREADY UNSUBSCRIBE
                        //         if(!in_array($listID,$providerID)){
                                    
                        //             // SEND DATA FOR UNSUBSCRIBE
                        //             $response = $this->mdl_marketing_platform_unsubscribe->makeUnsubscribe($email,$listID);
                                
                        //              // GET ALREDY UNSUBSCRIBER LIST
                        //             $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                        //             $is_single       = true;
                        //             $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');

                        //             if(empty($getUnsubscribeData)) {
                        //                 // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        //                 if($response["result"] == "success"){
                        //                     $data = [
                        //                         "provider_id" => $listID,
                        //                         "esp"         => MARKETING_PLATFORM,
                        //                         "email"       => $email,
                        //                         "name"        => $response["data"]["name"],
                        //                         "status"      => 1, // success
                        //                         "unsub_method"=> 2, // webhook (by other ESP)
                        //                         "response"    => $response["data"]["updated_at"]                                            
                        //                     ];                        
                        //                 }else{
                        //                     $data = [
                        //                         "provider_id" => $listID,
                        //                         "esp"         => MARKETING_PLATFORM,
                        //                         "email"       => $email,
                        //                         "name"        => NULL,
                        //                         "status"      => 2, // error
                        //                         "unsub_method"=> 2, // webhook (by other ESP)
                        //                         "response"    => $response["msg"]                                           
                        //                     ];                        
                        //                 }
                        //                 // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        //                 ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                        //             }
                        //         }else{
                        //             $data = [
                        //                 "provider_id" => $listID,
                        //                 "esp"         => MARKETING_PLATFORM,
                        //                 "email"       => $email,
                        //                 "name"        => NULL,
                        //                 "status"      => 3, // already unsubscribed
                        //                 "unsub_method"=> 2, // webhook (by other ESP)
                        //                 "response"    => "Already unsubscribed"                                        
                        //             ];                        
                        //             // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        //             ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                        //         }                                
                        //     }
                        // }                
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
                            $listCondition['providers.id !='] = $mainProviderId;
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

                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
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
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
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
                            $listCondition['providers.id !='] = $mainProviderId;
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

                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
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
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
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
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }               
                    } else if($provider == EXPERT_SENDER) {
                        $this->load->model('mdl_expert_sender_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        }
                        if($mainProvider == EXPERT_SENDER) {
                            $listCondition['providers.id !='] = $mainProviderId;
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
                                    $response = $this->mdl_expert_sender_unsubscribe->makeUnsubscribe($email,$listID);

                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
                                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                        if($response["result"] == "success"){
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => EXPERT_SENDER,
                                                "email"       => $email,
                                                "name"        => $response["data"]["name"],
                                                "status"      => 1, // success
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["data"]["updated_at"]
                                            ];                                            
                                        }else{
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => EXPERT_SENDER,
                                                "email"       => $email,
                                                "name"        => NULL,
                                                "status"      => 2, // error
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["msg"]
                                            ];                                           
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => EXPERT_SENDER,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];                                   
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }               
                    } else if($provider == CLEVER_REACH) { 
                        $this->load->model('mdl_clever_reach_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                            'clever_reach_accounts.status' => 1
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        } 
                        if($mainProvider == CLEVER_REACH) {
                            $listCondition['providers.id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                        $getListIDByCountry = JoinData(PROVIDERS,$listCondition,CLEVER_REACH_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");

                        $list = array_column($getListIDByCountry,'id');
        
                        foreach ($list as $listID) {
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){   
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_clever_reach_unsubscribe->makeUnsubscribe($email,$listID);
                                
                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
                                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                        if($response["result"] == "success"){
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => CLEVER_REACH,
                                                "email"       => $email,
                                                "name"        => $response["data"]["name"],
                                                "status"      => 1, // success
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["data"]["updated_at"]                                            
                                            ];                                            
                                        }else{
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => CLEVER_REACH,
                                                "email"       => $email,
                                                "name"        => NULL,
                                                "status"      => 2, // error
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["msg"]                                           
                                            ];                                            
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => CLEVER_REACH,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];                                    
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }                
                    } else if($provider == OMNISEND) { 
                        $this->load->model('mdl_omnisend_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                            'omnisend_accounts.status' => 1
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        } 
                        if($mainProvider == OMNISEND) {
                            $listCondition['providers.id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                        $getListIDByCountry = JoinData(PROVIDERS,$listCondition,OMNISEND_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");

                        $list = array_column($getListIDByCountry,'id');
        
                        foreach ($list as $listID) {
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){   
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_omnisend_unsubscribe->makeUnsubscribe($email,$listID);
                                
                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
                                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                        if($response["result"] == "success"){
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => OMNISEND,
                                                "email"       => $email,
                                                "name"        => $response["data"]["name"],
                                                "status"      => 1, // success
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["data"]["updated_at"]                                            
                                            ];                                            
                                        }else{
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => OMNISEND,
                                                "email"       => $email,
                                                "name"        => NULL,
                                                "status"      => 2, // error
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["msg"]                                           
                                            ];                                            
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => OMNISEND,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];                                   
                                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                }                                
                            }
                        }                
                    } else if($provider == SENDGRID) { 
                        // unsub contact globally means from all the list (unsub)
                        $this->load->model('mdl_sendgrid_unsubscribe');
                        //LIST ID EMPTY GET COUNTRY WISE LIST  
                        $listCondition  = array(
                            'provider' => $provider,
                            'sendgrid_accounts.status' => 1
                        );  
                        if(!empty($country)) {
                            $listCondition['country'] = $country;
                        } 
                        if($mainProvider == SENDGRID) {
                            $listCondition['providers.id !='] = $mainProviderId;
                        }
                        $is_single             = false;
                        // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                        $getListIDByCountry = JoinData(PROVIDERS,$listCondition,SENDGRID_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");

                        $list = array_column($getListIDByCountry,'id');
        
                        foreach ($list as $listID) {
                            // CHECK EMAIL ALREADY UNSUBSCRIBE USING EMPLOYEE 
                            if(!in_array($listID,$empProviderID)){   
                                // CHECK EMAIL ALREADY UNSUBSCRIBE
                                if(!in_array($listID,$providerID)){
                                    
                                    // SEND DATA FOR UNSUBSCRIBE
                                    $response = $this->mdl_sendgrid_unsubscribe->makeUnsubscribe($email,$listID);
                                
                                    // GET ALREDY UNSUBSCRIBER LIST
                                    $condition       = array('email' => $email,'provider_id' => $listID,'status' => 1);
                                    $is_single       = true;
                                    $getUnsubscribeData    = GetAllRecord(PROVIDER_UNSUBSCRIBER, $condition, $is_single,[],[],[],'id');
                                    if(empty($getUnsubscribeData)) {
                                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                                        if($response["result"] == "success"){
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => SENDGRID,
                                                "email"       => $email,
                                                "name"        => $response["data"]["name"],
                                                "status"      => 1, // success
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["data"]["updated_at"]                                            
                                            ];                                            
                                        }else{
                                            $data = [
                                                "provider_id" => $listID,
                                                "esp"         => SENDGRID,
                                                "email"       => $email,
                                                "name"        => NULL,
                                                "status"      => 2, // error
                                                "unsub_method"=> 2, // webhook (by other ESP)
                                                "response"    => $response["msg"]                                           
                                            ];                                            
                                        }
                                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                                    }
                                }else{
                                    $data = [
                                        "provider_id" => $listID,
                                        "esp"         => SENDGRID,
                                        "email"       => $email,
                                        "name"        => NULL,
                                        "status"      => 3, // already unsubscribed
                                        "unsub_method"=> 2, // webhook (by other ESP)
                                        "response"    => "Already unsubscribed"                                        
                                    ];                                   
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