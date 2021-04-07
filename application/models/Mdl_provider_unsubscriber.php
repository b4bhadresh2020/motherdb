<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_provider_unsubscriber extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getUnsubscriberData($getData,$start,$perpage) {

        $condition = array();
        isset($getData["list"]) && ($getData["list"] != 0) ? $condition["provider_id"] = $getData["list"] : '';
        ($getData["status"] != 0) ? $condition["provider_unsubscriber.status"] = $getData["status"] : '';
        !empty($getData["email"]) ? $condition["email"] = $getData["email"] : '';
        !empty($getData["deliveryDate"]) ? $condition["DATE(provider_unsubscriber.created_at)"] = $getData["deliveryDate"] : '';

        $is_single = false;
        
        $totalCount = GetAllRecordCount(PROVIDER_UNSUBSCRIBER,$condition);

        $unsubscriberData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $unsubscriberData = JoinData(PROVIDER_UNSUBSCRIBER,$condition,PROVIDERS,"provider_id","id",'',false,[],"email,provider,provider_unsubscriber.created_at,provider_unsubscriber.status,providers.country,providers.listname,providers.displayname,provider_unsubscriber.response");  
        }

        $response = array(
            'totalCount' => $totalCount,
            'unsubscriberData' => $unsubscriberData 
        );        
        return $response;        
    }
}