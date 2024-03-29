<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_expert_sender_queue extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }


    public function get_user_data($getData,$start,$perpage){

        $condition = array();
        $is_like = array();

        $email = @$getData['email'];
        $deliveryDate = @$getData['deliveryDate'];
        $providerId = @$getData['providerId'];
        $status = @$getData['status'];

        $providers = GetAllRecord(PROVIDERS,array("provider" => EXPERT_SENDER),false,array(),array(),array());
        foreach($providers as $provider){
            $providerData[$provider['id']] = $provider;
        }

        //$condition["status"] = 0;
        if (@$email) {
            $condition['emailId'] = $email;
        }

        if (@$deliveryDate) {
            $condition['deliveryDate'] = $deliveryDate;
        }

        if (@$providerId) {
            $condition['providerId'] = $providerId;
        }

        if (@$status != "-1") {
            $condition['status'] = $status;
        }
        
        $is_single = false;
        $userData = array();
        $totalUserData = JoinData(EXPERT_SENDER_DELAY_USER_DATA,$condition,LIVE_DELIVERY_DATA,"liveDeliveryDataId","liveDeliveryDataId","left",$is_single,array(),'expert_sender_delay_user_data.id');
        
        $this->db->limit($perpage,$start);        
        $userData = JoinData(EXPERT_SENDER_DELAY_USER_DATA,$condition,LIVE_DELIVERY_DATA,"liveDeliveryDataId","liveDeliveryDataId","left",$is_single,array(array("currentTimestamp" => "desc")));   
        
        foreach ($userData as $key => $user) {            
            $userData[$key]['providerListName'] = $providerData[$user['providerId']]['listname'];
        }

        $dataCount = counts($totalUserData);

        $response = array(
            'totalCount' => $dataCount,
            'userData' => $userData 
        );

        return $response;        
    }
}