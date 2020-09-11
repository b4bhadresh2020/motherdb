<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_provider_state extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }


    public function get_state_data($getData,$providerID){ 
        
        $providerStatusInfo = array();        

        $apikey = @$getData['apikey'];
        $currentDate = date("Y-m-d");
        $deliveryDate = isset($getData['deliveryDate']) && $getData['deliveryDate'] != "" ? $getData['deliveryDate'] : date("Y-m-d", strtotime('monday this week'));
        $weekDates = array();
        
        for ($i=0; $i < 7; $i++) { 
            $weekDates[] = date("Y-m-d",strtotime("+$i day", strtotime($deliveryDate)));
        }        

        
        $condition = array(
            "provider" => $providerID
        );
        $providers = GetAllRecord(PROVIDERS,$condition,false,array(),array(),array(array("provider" => "asc")));
        $statusInfo = array();
        
        foreach($providers as $provider){
            // GET LIVE DELIVERY STATE
            for ($i=0; $i < 7; $i++) { 
                $newDeliveryDate = date("Y-m-d",strtotime("+$i day", strtotime($deliveryDate)));    
                $statusInfo[$provider['listname']][$newDeliveryDate]["livedelivery_success"] = array('total' => 0);
                
                if(strtotime($newDeliveryDate) <= strtotime($currentDate)){
                    $condition['providerId'] = $provider['id'];
                    $condition['status'] = 1;
                    $condition['updateDate'] = $newDeliveryDate;
    
                    if (@$apikey != "") {
                        $condition['apikey'] = $apikey;
                        $this->db->join(LIVE_DELIVERY_DATA,LIVE_DELIVERY_DATA.'.liveDeliveryDataId = '.EMAIL_HISTORY_DATA.'.liveDeliveryDataId');
                    }
    
                    $this->db->select('count(*) as total,email_history_data.keyword');
                    $this->db->from(EMAIL_HISTORY_DATA);
                    $this->db->where($condition);
                    $this->db->where('email_history_data.liveDeliveryDataId is NOT NULL', NULL, FALSE);
                    $this->db->group_by("email_history_data.keyword");
                    $query=$this->db->get();
                    $statusResponse=$query->result_array();

                    //pre($statusResponse);

                    foreach($statusResponse as $record){
                        $statusInfo[$provider['listname']][$newDeliveryDate]["livedelivery_success"][$record['keyword']] = $record['total'];                            
                        $statusInfo[$provider['listname']][$newDeliveryDate]["livedelivery_success"]['total'] += $record['total'];
                    }
                }                    
            }
            // GET LIVE REPOST STATE
            for ($i=0; $i < 7; $i++) { 
                $newDeliveryDate = date("Y-m-d",strtotime("+$i day", strtotime($deliveryDate)));    
                $statusInfo[$provider['listname']][$newDeliveryDate]["liverepost_success"] = array('total' => 0);
                
                if(strtotime($newDeliveryDate) <= strtotime($currentDate)){
                    $condition['providerId'] = $provider['id'];
                    $condition['status'] = 1;
                    $condition['updateDate'] = $newDeliveryDate;
    
                    if (@$apikey != "") {
                        $condition['apikey'] = $apikey;
                        $this->db->join(LIVE_DELIVERY_DATA,LIVE_DELIVERY_DATA.'.liveDeliveryDataId = '.EMAIL_HISTORY_DATA.'.liveDeliveryDataId');
                    }
    
                    $this->db->select('count(*) as total,email_history_data.keyword');
                    $this->db->from(EMAIL_HISTORY_DATA);
                    $this->db->where($condition);
                    $this->db->where('email_history_data.liveDeliveryDataId is NULL', NULL, FALSE);
                    $this->db->group_by("email_history_data.keyword");
                    $query=$this->db->get();
                    $statusResponse=$query->result_array();
    
                    //pre($statusResponse);

                    foreach($statusResponse as $record){
                        $statusInfo[$provider['listname']][$newDeliveryDate]["liverepost_success"][$record['keyword']] = $record['total'];
                        $statusInfo[$provider['listname']][$newDeliveryDate]["liverepost_success"]['total'] += $record['total'];                             
                    }
                }    
            }
        }
        $providerStatusInfo = $statusInfo;        

        $allProvidersData = GetAllRecord(PROVIDERS,array(),false,array(),array(),array(),"id,listname");
        foreach($allProvidersData as $providerData){
            $allProvider[$providerData['id']] = $providerData['listname'];
        }

        // LIVE DELIVERY PROVIDER
        $liveDeliveryProvidersData = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array(),"mailProvider,keyword,groupName");
        $liveDeliveryProvider = array();        
        foreach($liveDeliveryProvidersData as $providers){
            foreach(json_decode($providers['mailProvider']) as $provider){
                if($provider != "egoi"){
                    if(!in_array($allProvider[$provider],$liveDeliveryProvider)){
                        $liveDeliveryProvider[] = $allProvider[$provider]; 
                    }                    
                }
            }
        }

        // LIVE REPOST PROVIDER
        $liveRepostProvidersData = GetAllRecord(CSV_FILE_PROVIDER_DATA,array(),false,array(),array(),array(),"providerName,providerList");
        $liveRepostProvider = array();

        foreach($liveRepostProvidersData as $provider){            
            if($provider['providerName'] == AWEBER){
                $originalProviderID = getLiveRepostAweverProviderID($provider['providerList']);
                if(!in_array($allProvider[$originalProviderID],$liveRepostProvider)){
                    $liveRepostProvider[] = $allProvider[$originalProviderID]; 
                }
            }else if($provider['providerName'] == TRANSMITVIA){
                $originalProviderID = getLiveRepostTransmitviaProviderListID($provider['providerList']);
                if(!in_array($allProvider[$originalProviderID],$liveRepostProvider)){
                    $liveRepostProvider[] = $allProvider[$originalProviderID]; 
                }
            }else if($provider['providerName'] == ONGAGE){
                $originalProviderID = getLiveRepostOngageProviderID($provider['providerList']);
                if(!in_array($allProvider[$originalProviderID],$liveRepostProvider)){
                    $liveRepostProvider[] = $allProvider[$originalProviderID]; 
                }
            }            
        }

        return array(
            'weekDays' => $weekDates,
            'providerStatusInfo' => $providerStatusInfo,
            'liveDeliveryProvider' => $liveDeliveryProvider,            
            'liveRepostProvider' => $liveRepostProvider
        );            
    }
}