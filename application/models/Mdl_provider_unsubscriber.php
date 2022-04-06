<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_provider_unsubscriber extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getUnsubscriberData($getData,$start,$perpage) {

        $condition = array();
        ($getData["provider"] != 0) ? $condition["providers.provider"] = $getData["provider"] : '';
        !empty($getData["country"]) ? $condition["providers.country"] = $getData["country"] : '';
        isset($getData["list"]) && ($getData["list"] != 0) ? $condition["provider_id"] = $getData["list"] : '';
        !empty($getData["deliveryDate"]) ? $condition["DATE(provider_unsubscriber.created_at)"] = $getData["deliveryDate"] : '';
        ($getData["status"] != 0) ? $condition["provider_unsubscriber.status"] = $getData["status"] : '';
        !empty($getData["email"]) ? $condition["email"] = $getData["email"] : '';

        $is_single = false;
        
        $totalCount = JoinDataCount(PROVIDER_UNSUBSCRIBER,$condition,PROVIDERS,"provider_id","id");
        $unsubscriberData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $unsubscriberData = JoinData(PROVIDER_UNSUBSCRIBER,$condition,PROVIDERS,"provider_id","id",'',false,[["created_at" => "desc"]],"email,provider,provider_unsubscriber.created_at,provider_unsubscriber.status,providers.country,providers.listname,providers.displayname,provider_unsubscriber.response");  
        }
        $response = array(
            'totalCount' => $totalCount,
            'unsubscriberData' => $unsubscriberData 
        );        
        return $response;        
    }
}