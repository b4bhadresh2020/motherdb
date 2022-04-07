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
        $espAccountTable = getAccountTableName($provider);
        $country  = $this->input->post("country");

        // $condition = array("provider" => $provider);
        // $is_in = array("country" => $country);
        // $is_single = FALSE;
        // $liveDeliveries = GetAllRecordIn(PROVIDERS, $condition, $is_single, array(), array(), array(),$is_in,'id,listname,displayname');

        $this->db->select('providers.id,providers.listname,providers.displayname');
        $this->db->from(PROVIDERS);
        if(!empty($espAccountTable)) {
            $this->db->join($espAccountTable,'providers.aweber_account='.$espAccountTable.'.id','left');
            $this->db->where($espAccountTable.'.status', 1);
        }
        $this->db->where('providers.provider', $provider);
        if(!empty($country)) {
            $this->db->where_in('providers.country', $country);
        }
        $liveDeliveries = $this->db->get()->result_array();

        echo json_encode($liveDeliveries);
    }

    function unsubscribe(){
        $provider = $this->input->post('provider');
        $country = $this->input->post('country');
        $email = $this->input->post('email'); 
        $successUnsubscribe = [];
        $failUnsubscribe = [];
        $alreadyUnsubscribe = [];

        if($provider == 0) {
            // $providers = array("9","11","12","13","14","15");  
            $providers = array("5","9","12","13","14","15","16");  
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

        // GET COUNTRY OF USER (LIVE DELIVERY)
        $condition       = array('emailId' => $email);
        $is_single       = true;
        $liveDeliveryData    = GetAllRecord(LIVE_DELIVERY_DATA, $condition, $is_single,[],[],[],'country');
        if(empty($country) && !empty($liveDeliveryData)) {
            $country = $liveDeliveryData['country'];
        }

        // GET COUNTRY OF USER (USER(CSV))
        $condition       = array('emailId' => $email);
        $is_single       = true;
        $csvUserData    = GetAllRecord(USER, $condition, $is_single,[],[],[],'country');
        if(empty($country) && !empty($csvUserData)) {
            $country = $csvUserData['country'];
        }
       
        foreach($providers as $provider){
            $list = '';
            if($provider == AWEBER){                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Aweber)'; 
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Aweber)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }                
            } else if($provider == MAILERLITE) {               
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Mailerlite)';
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Mailerlite)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                }               
            } else if($provider == MAILJET){                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider,
                        'mailjet_accounts.status' => 1
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
                    $is_single             = false;
                    // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $getListIDByCountry = JoinData(PROVIDERS,$listCondition,MAILJET_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");
                    $list = array_column($getListIDByCountry,'id');
                }
                
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Mailjet)';                        
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Mailjet)';                        
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }               
            } else if($provider == CONVERTKIT){            
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Convertkit)';                         
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Convertkit)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }                
            } else if($provider == MARKETING_PLATFORM) {                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider,
                        'marketing_platform_accounts.status' => 1
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
                    $is_single             = false;
                    // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $getListIDByCountry = JoinData(PROVIDERS,$listCondition,MARKETING_PLATFORM_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");
                    $list = array_column($getListIDByCountry,'id');
                }

                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){                        
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Marketing Platform)';                        
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Marketing Platform)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                }                
            } else if($provider == ONTRAPORT) {                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Ontraport)';                        
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Ontraport)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                }               
            } else if($provider == ACTIVE_CAMPAIGN) {                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Active Campaign)';                         
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Active Campaign)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                }               
            } else if($provider == EXPERT_SENDER) {                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
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
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Expert Sender)';                        
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Expert Sender)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);
                }               
            } else if($provider == CLEVER_REACH){                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider,
                        'clever_reach_accounts.status' => 1
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
                    $is_single             = false;
                    // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $getListIDByCountry = JoinData(PROVIDERS,$listCondition,CLEVER_REACH_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");
                    $list = array_column($getListIDByCountry,'id');
                }
                
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Clever Reach)';                        
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Clever Reach)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }               
            } else if($provider == OMNISEND){                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider,
                        'omnisend_accounts.status' => 1
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
                    $is_single             = false;
                    // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $getListIDByCountry = JoinData(PROVIDERS,$listCondition,OMNISEND_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");
                    $list = array_column($getListIDByCountry,'id');
                }
                
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Omnisend)';                         
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Omnisend)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }               
            } else if($provider == SENDGRID){                
                //LIST ID EMPTY GET COUNTRY WISE LIST
                $list = $this->input->post('list');
                if(empty($list)){
                    $listCondition  = array(
                        'provider' => $provider,
                        'sendgrid_accounts.status' => 1
                    );
                    if(!empty($country)) {
                        $listCondition['country'] = $country;
                    }
                    $is_single             = false;
                    // $getListIDByCountry    = GetAllRecord(PROVIDERS, $listCondition, $is_single,[],[],[],'id');
                    $getListIDByCountry = JoinData(PROVIDERS,$listCondition,SENDGRID_ACCOUNTS,"aweber_account","id","left",$is_single,array(),"providers.id","");
                    $list = array_column($getListIDByCountry,'id');
                }
                
                foreach ($list as $listID) {   
                    
                    // fetch mail provider data from providers table
                    $providerCondition   = array('id' => $listID);
                    $is_single           = true;
                    $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

                    // CHECK EMAIL ALREADY UNSUBSCRIBE
                    if(!in_array($listID,$providerID)){
                        // SEND DATA IN QUEUE
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 4, // Pending
                            "response"    => "Pending"
                        ];
                        $queueUnsubscribe[] = $providerData['listname'].'(Sendgrid)'; 
                    }else{
                        $data = [
                            "provider_id" => $listID,
                            "email"       => $email,
                            "name"        => NULL,
                            "status"      => 3, // already unsubscribed
                            "response"    => "Already unsubscribed"
                        ];
                        $alreadyUnsubscribe[] = $providerData['listname'].'(Sendgrid)';
                    }
                    // INSERT DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,[],$data,true);                                    
                }               
            }
        }
        $queueUnsubscribeList = implode(", ",$queueUnsubscribe);
        $alreadyUnsubscribeList = implode(", ",$alreadyUnsubscribe);
        echo json_encode(array("queueList" => $queueUnsubscribeList, "alreadyUnsubscribeList" => $alreadyUnsubscribeList));
    }
}
