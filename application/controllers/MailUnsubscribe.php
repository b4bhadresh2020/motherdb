<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MailUnsubscribe extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }

        $this->load->model('mdl_provider_unsubscriber');    
    }
    

    public function index()
    {
        $data['load_page'] = 'mailUnsubscribe';
        $data['headerTitle'] = "Mail Unsubscribe";
        $data["curTemplateName"] = "mailUnsubscribe/list";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getUnsubscriberData($start = 0){

        $perPage = 25;

        $responseData = $this->mdl_provider_unsubscriber->getUnsubscriberData($_GET,$start,$perPage); 

        $dataCount = $responseData['totalCount'];
        $unsubscriberData = $responseData['unsubscriberData'];

        $data = pagination_data('mailUnsubscribe/getUnsubscriberData/', $dataCount, $start, 3, $perPage,$unsubscriberData);

        $data["provider"] = $this->input->get('provider');
        $data["country"] = $this->input->get('country');
        $data["list"] = $this->input->get('list');
        $data["deliveryDate"] = $this->input->get('deliveryDate');
        $data["status"] = $this->input->get('status');
        $data["email"] =  $this->input->get('email');
        $data['load_page'] = 'mailUnsubscribe';
        $data['headerTitle'] = "Mail Unsubscribe";
        $data["curTemplateName"] = "mailUnsubscribe/list";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function mailUnsubscribe()
    {        
        $provider = $this->input->post('provider');
        $country = $this->input->post('country');
        $list = $this->input->post('list');
        $email = $this->input->post('email');        
                
        $data['load_page'] = 'mailUnsubscribe';
        $data['headerTitle'] = "Mail Unsubscribe";
        $data['curTemplateName'] = "mailUnsubscribe/list";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getProviderList(){
        $provider = $this->input->post("provider");
        $country  = $this->input->post("country");
        $condition = array("provider" => $provider);
        $is_in = array("country" => $country);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecordIn(PROVIDERS, $condition, $is_single, array(), array(), array(),$is_in,'id,listname,displayname');
        echo json_encode($liveDeliveries);
    }

    function unsubscribe(){
        $provider = $this->input->post('provider');
        $country = $this->input->post('country');
        $email = $this->input->post('email'); 
        $successUnsubscribe = [];
        $failUnsubscribe = [];

        if($provider == 0) {
            $providers = array("9","11","12","13","14");  
        } else {
            $providers = array($provider);
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
        foreach($providers as $provider){
            $list = '';
            if($provider == AWEBER){
                $this->load->model('mdl_aweber_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_aweber_unsubscribe->makeUnsubscribe($email,$listID);

                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                                    
                }                
            } else if($provider == MAILERLITE) {
                $this->load->model('mdl_mailerlite_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_mailerlite_unsubscribe->makeUnsubscribe($email,$listID);
                    
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                }               
            } else if($provider == MAILJET){
                $this->load->model('mdl_mailjet_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_mailjet_unsubscribe->makeUnsubscribe($email,$listID);

                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                                    
                }               
            } else if($provider == CONVERTKIT){
                $this->load->model('mdl_convertkit_unsubscribe');

                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_convertkit_unsubscribe->makeUnsubscribe($email,$listID);
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                                    
                }                
            } else if($provider == MARKETING_PLATFORM) {
                $this->load->model('mdl_marketing_platform_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_marketing_platform_unsubscribe->makeUnsubscribe($email,$listID);
                    
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                }                
            } else if($provider == ONTRAPORT) {
                $this->load->model('mdl_ontraport_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_ontraport_unsubscribe->makeUnsubscribe($email,$listID);
                    
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                }               
            } else if($provider == ACTIVE_CAMPAIGN) {
                $this->load->model('mdl_active_campaign_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_active_campaign_unsubscribe->makeUnsubscribe($email,$listID);
                    
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                }               
            } else if($provider == EXPERT_SENDER) {
                $this->load->model('mdl_expert_sender_unsubscribe');
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                                    'country' =>  $country,
                                    'provider' => $provider
                                );
                    $is_single             = false;
                    $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        
                        // SEND DATA FOR UNSUBSCRIBE
                        $response = $this->mdl_expert_sender_unsubscribe->makeUnsubscribe($email,$listID);
                    
                        // ADD RECORD IN DATABASE FOR UNSUBSCRIBER LIST.
                        if($response["result"] == "success"){
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => $response["data"]["name"],
                                "status"      => 1, // success
                                "response"    => $response["data"]["updated_at"]
                            ];
                            $successUnsubscribe[] = $providerData['listname'];
                        }else{
                            $data = [
                                "provider_id" => $listID,
                                "email"       => $email,
                                "name"        => NULL,
                                "status"      => 2, // error
                                "response"    => $response["msg"]
                            ];
                            $failUnsubscribe[] = $providerData['listname'];
                        }
                        // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                        ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                    }else{
                        $successUnsubscribe[] = $providerData['listname'];
                    }
                }               
            }
        }
        $successUnsubscribeList = implode(", ",$successUnsubscribe);
        $failUnsubscribeList = implode(", ",$failUnsubscribe);
        echo json_encode(array("successList" => $successUnsubscribeList, "failList" => $failUnsubscribeList));
    }
}
