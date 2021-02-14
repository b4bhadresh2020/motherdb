<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ProviderStatistics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged())
            redirect(base_url());
    }

    public function index()
    {
        $data = array();

        //get all apikey 
        $condition = array();
        $is_single = FALSE;
        $liveDeliveries = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(), 'apikey,groupName,keyword,mailProvider');

        $data['apikeys'] = $liveDeliveries;
        $data['load_page'] = 'providerStatistics';
        $data['headerTitle'] = "Provider Statistics";
        $data["curTemplateName"] = "providerStatistics/report";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getMailProviderData()
    {
        $response_messages = [
            "1" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "403 -","blacklisted" => "blacklisted", "host" => "resolve host"],
            "2" => [ "success" => "success", "subscriber_exist" => "subscriber already", "auth_fail" => "Service Unavailable", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "resolve host"],
            "3" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "Wrong provider"],
            "4" => [ "success" => "success", "subscriber_exist" => "412 " ,  "auth_fail" => "401 -", "bad_fail" => "429 ", "blacklisted" => "blacklisted", "host" => "resolve host"],
            "5" => [ "success" => "success", "subscriber_exist" => "400 -", "auth_fail" => "401 -", "bad_fail" => "Bad Request", "blacklisted" => "blacklisted", "host" => "resolve host"],
            "6" => [ "success" => "success", "subscriber_exist" => "Contact already exist", "auth_fail" => "401 -", "bad_fail" => "Invalid phone number", "blacklisted" => "blacklisted", "host" => "Request already received"],
        ];
        $apikey = $this->input->post('apikey');
        $deliveryDate = $this->input->post('deliveryDate');
        
        $data = array();
        $liveDeliveryStastic = array();

        // fetch mail provider data from providers table
        //$providerCondition   = array('isInActive' => 0);
        $providerCondition   = array('apikey' => '45754a36795461653447446c485a5152587177342f413d3d');
        $is_single           = false;
        $liveDeliveries      = GetAllRecord(LIVE_DELIVERY, $providerCondition, $is_single, [], [], [], 'apikey,mailProvider,delay,isDuplicate,groupName,keyword');

        foreach ($liveDeliveries as $liveDelivery) {

            $apikey = $liveDelivery['apikey'];

            // decode provider data into array
            $mailProviders  = json_decode($liveDelivery['mailProvider']);

            // decode provider delay data into array
            $mailProvidersDelay = json_decode($liveDelivery['delay'],true);

            // decode provider isduplicate data into array
            $mailProvidersDuplicateFlag = [];
            if(!empty($liveDelivery['isDuplicate'])){
                $mailProvidersDuplicateFlag = json_decode($liveDelivery['isDuplicate'],true);
            }
            
            // remove if egoi provider set
            if (($key = array_search("egoi", $mailProviders)) !== false) {
                unset($mailProviders[$key]);
            }

            // get provider listname from provider table
            
            if(count($mailProviders) > 0) {
                $is_in = array("id" => $mailProviders);                
                $providerDetail = GetAllRecordIn(PROVIDERS,[],false,[],[],[],$is_in,'id,listname,response_field,provider');
            }

            if(isset($providerDetail) && count($providerDetail)){
                foreach ($providerDetail as $key => $provider) {
                    $providerDetail[$key]['delay'] = 0;
                    if($provider['provider'] == AWEBER){
                        $providerDetail[$key]['delay'] = $mailProvidersDelay[$provider['id']];
                    }
    
                    // check isduplicate is enable/disable
                    if(array_key_exists($provider['id'],$mailProvidersDuplicateFlag)){
                        $providerDetail[$key]['isDuplicate'] = 1;
                    }else{
                        $providerDetail[$key]['isDuplicate'] = 0;
                    }
                }
    
                // get live deivery data as per status wise.
                $this->db->select("isFail,sucFailMsgIndex,count(*) as total");
                $this->db->from(LIVE_DELIVERY_DATA);
                $this->db->where("apikey",$apikey);
                $this->db->where("DATE(createdDate)",$deliveryDate);
                $this->db->group_by("sucFailMsgIndex");
                $liveDeliveryStatusCounter = $this->db->get()->result_array();
    
                $liveDeliveryStastic[$apikey]['success'] = 0;
                $liveDeliveryStastic[$apikey]['duplicate'] = 0;
                $liveDeliveryStastic[$apikey]['failed'] = 0;
                $liveDeliveryStastic[$apikey]['total'] = 0;
    
                foreach ($liveDeliveryStatusCounter as $counter) {
                    if($counter['sucFailMsgIndex'] == 0){
                        // add total number of success records
                        $liveDeliveryStastic[$apikey]['success'] = $counter['total'];                      
                    }else if($counter['sucFailMsgIndex'] == 1){
                        // add total number of duplicate records
                        $liveDeliveryStastic[$apikey]['duplicate'] = $counter['total'];                    
                    }else{
                        // add total number of failed records                    
                        $liveDeliveryStastic[$apikey]['failed'] = $counter['total'];                    
                    }            
                    // add all the status counter in total
                    $liveDeliveryStastic[$apikey]['total'] += $counter['total'];
                }  
                
                // Get total number of records per provider wise from live delivery table those are instant send.
                foreach ($providerDetail as $key => $provider) {
                    $condition = array();
                    $condition['apikey'] = $apikey;
                    $condition['DATE(createdDate)'] = $deliveryDate;
                    if($provider['isDuplicate']){
                        $is_in = array('sucFailMsgIndex' => [1]);
                    }else{
                        $is_in = array('sucFailMsgIndex' => [0,1]);
                    }
                    
                    $providerDetail[$key]['success'] = 0;
                    $providerDetail[$key]['subscriber_exist'] = 0;
                    $providerDetail[$key]['auth_fail'] = 0;
                    $providerDetail[$key]['bad_fail'] = 0;
    
                    if($provider['delay'] == 0){
                        // get data from live delivery table  
    
                        $is_like = array(array($provider['response_field'] => $response_messages[$provider['provider']]['success']));
                        $providerDetail[$key]['success'] = GetAllRecordCountIn(LIVE_DELIVERY_DATA,$condition,true,$is_like,[],[],$is_in);
    
                        $is_like = array(array($provider['response_field'] => $response_messages[$provider['provider']]['subscriber_exist']));
                        $providerDetail[$key]['subscriber_exist'] = GetAllRecordCountIn(LIVE_DELIVERY_DATA,$condition,true,$is_like,[],[],$is_in);
                        
                        $is_like = array(array($provider['response_field'] => $response_messages[$provider['provider']]['auth_fail']));
                        $providerDetail[$key]['auth_fail'] = GetAllRecordCountIn(LIVE_DELIVERY_DATA,$condition,true,$is_like,[],[],$is_in);
                        
                        $is_like = array(array($provider['response_field'] => $response_messages[$provider['provider']]['bad_fail']));
                        $providerDetail[$key]['bad_fail'] = GetAllRecordCountIn(LIVE_DELIVERY_DATA,$condition,true,$is_like,[],[],$is_in);
    
                    }else{
                        // get data from delay table.
                        $delayTableName = "";
                        switch($provider['provider']){
                            case 1:
                                $delayTableName = AWEBER_DELAY_USER_DATA;
                                break;
                            case 2:
                                $delayTableName = TRANSMITVIA_DELAY_USER_DATA;
                                break;
                            case 3:
                                $delayTableName = CONTACT_DELAY_USER_DATA;
                                break;
                            case 4:
                                $delayTableName = ONGAGE_DELAY_USER_DATA;
                                break;
                            case 5:
                                $delayTableName = SENDGRID_DELAY_USER_DATA;
                                break;
                            case 6:
                                $delayTableName = SENDINBLUE_DELAY_USER_DATA;
                                break; 
    
                        }
                        
                        $liveDeliveriesInfo = GetAllRecordIn(LIVE_DELIVERY_DATA, $condition, $is_single, [], [], [],$is_in, 'liveDeliveryDataId');
                        
                        $liveDeliveriesID = [];
                        foreach ($liveDeliveriesInfo as $value) {
                            array_push($liveDeliveriesID,$value['liveDeliveryDataId']);
                        }
    
                        if(count($liveDeliveriesID)){
                            $is_in      = array("liveDeliveryDataId" => $liveDeliveriesID);
                            $delay_condition = array("providerId" => $provider['id']);
    
                            $is_like    = array(array("response" => $response_messages[$provider['provider']]['success']));
                            $providerDetail[$key]['success'] = GetAllRecordCountIn($delayTableName,$delay_condition,true,$is_like,[],[],$is_in); 
    
                            $is_like    = array(array("response" => $response_messages[$provider['provider']]['subscriber_exist']));
                            $providerDetail[$key]['subscriber_exist'] = GetAllRecordCountIn($delayTableName,$delay_condition,true,$is_like,[],[],$is_in);                            
    
                            $is_like    = array(array("response" => $response_messages[$provider['provider']]['auth_fail']));
                            $providerDetail[$key]['auth_fail'] = GetAllRecordCountIn($delayTableName,$delay_condition,true,$is_like,[],[],$is_in); 
    
                            $is_like    = array(array("response" => $response_messages[$provider['provider']]['bad_fail']));
                            $providerDetail[$key]['bad_fail'] = GetAllRecordCountIn($delayTableName,$delay_condition,true,$is_like,[],[],$is_in); 
                        }
                    }    
                    $liveDeliveryStastic[$apikey]['providerDetail'] = $providerDetail;
                }
            }

        }  
        pre($liveDeliveryStastic);
        die;
    }
}
