<?php

class Cron_clever_reach_delay_user extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_clever_reach');
    }

    public function index() {       
        
        $providers = GetAllRecord(PROVIDERS,array("provider" => CLEVER_REACH),false,array(),array(),array());
        foreach($providers as $provider){
            $providerData[$provider['id']] = $provider;
        }

        //get file data
        $condition = array(
            'deliveryTimestamp <=' => time(),
            'status' => 0
        );
        $is_single = FALSE;
        $userData = JoinData(CLEVER_REACH_DELAY_USER_DATA,$condition,LIVE_DELIVERY_DATA,"liveDeliveryDataId","liveDeliveryDataId","left",$is_single,array(),"","");

        $this->db->select('clever_reach_delay_user_data.id,live_delivery_data.liveDeliveryDataId,live_delivery_data.apikey,firstName,lastName,emailId,address,postCode,city,live_delivery_data.country,phone,gender,birthdateDay,birthdateMonth,birthdateYear,age,ip,tag,sucFailMsgIndex,providerId,live_delivery_data.groupName,live_delivery_data.keyword,isDuplicate');
        $this->db->from(CLEVER_REACH_DELAY_USER_DATA);
        $this->db->join(LIVE_DELIVERY_DATA,'clever_reach_delay_user_data.liveDeliveryDataId=live_delivery_data.liveDeliveryDataId');
        $this->db->join(LIVE_DELIVERY,'live_delivery_data.apikey=live_delivery.apikey');
        $this->db->where($condition);
        // $this->db->where('clever_reach_delay_user_data.liveDeliveryDataId', 719);
        $this->db->order_by('deliveryTimestamp');
        $this->db->limit(500);
        $query=$this->db->get();
        $userData= $query->result_array();
        // pre($userData);
        // die;
        
        foreach($userData as $user){   
            $emailAddressChunk = explode("@",$user['emailId']);
            $country = $user['country'];
            if($emailAddressChunk[1] == TELIA_DOMAIN || $emailAddressChunk[1] == LUUKKU_DOMAIN || (startsWith($emailAddressChunk[1],PP_DOMAIN_START) && endsWith($emailAddressChunk[1],PP_DOMAIN_END)) || (strpos($emailAddressChunk[1], YAHOO_DOMAIN) !== false && $country == 'SE') || (strpos($emailAddressChunk[1], ICLOUD_DOMAIN) !== false)){
                $responseField = $providerData[$user['providerId']]['response_field'];
                $response = array("result" => "error","error" => array("msg" => "MX Block"));
                // Update response in live delivery user data table
                $condition = array('liveDeliveryDataId' => $user['liveDeliveryDataId']);
                $is_insert = false;
                $updateArr = array($responseField => json_encode($response));
                ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);

                // Update response in clever reach queue user data table
                $queueCondition = array('id' => $user['id']);
                $is_insert = false;
                $queueUpdateArr = array(
                    "status" => 1,
                    "response" => json_encode($response),
                    "updatedDate" => date("Y-m-d H:i:s")
                );
                ManageData(CLEVER_REACH_DELAY_USER_DATA, $queueCondition, $queueUpdateArr, $is_insert);

                // Update response in clever reach history data.
                $historyData = array(       
                    'groupName' => $user['groupName'],
                    'keyword' => $user['keyword'],
                    'updateDate' => date("Y-m-d"),
                    'updateDateTime' => date("Y-m-d H:i:s"),
                    'response' => json_encode($response),
                    'status' => 2 // error - already subscribe + other error (mx block(telia, lukku, pp))
                );
                $historyCondition = array(
                    'liveDeliveryDataId' => $user['liveDeliveryDataId'],
                    'providerId' => $user['providerId']
                );
                $is_insert = false;
                ManageData(EMAIL_HISTORY_DATA, $historyCondition, $historyData, $is_insert);
            } else {
                if(isset($user['isDuplicate']) && !empty($user['isDuplicate'])){
                    $isDuplicate = json_decode($user['isDuplicate'],true);
                }else{
                    $isDuplicate = array();
                }
                if(!array_key_exists($user['providerId'],$isDuplicate) || (array_key_exists($user['providerId'],$isDuplicate) && $user['sucFailMsgIndex'] == 1)){
                    if (@$user['birthdateDay'] != '0' && @$user['birthdateMonth'] != '0' && @$user['birthdateYear'] != '0') {
                        $birthDate  = $user['birthdateYear'] . '-' . $user['birthdateMonth'] . '-' . $user['birthdateDay'];
                        $user['birthDate'] = date('Y-m-d', strtotime($birthDate));
                    }else{
                        $user['birthDate'] = "";
                    } 
                    $response = $this->mdl_clever_reach->AddEmailToCleverReachSubscriberList($user,$user['providerId']);
                }else{
                    $response = array("result" => "success","data" => "Duplicate condition not satisfied");
                }    
                $responseField = $providerData[$user['providerId']]['response_field'];
    
                // Update response in live delivery user data table
                $condition = array('liveDeliveryDataId' => $user['liveDeliveryDataId']);
                $is_insert = false;
                $updateArr = array($responseField => json_encode($response));
                ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);
    
                // Update response in clever reach queue user data table
                $queueCondition = array('id' => $user['id']);
                $is_insert = false;
                
                if($response['result'] == "success"){
                    $queueUpdateArr = array(
                        "status" => 1,
                        "response" => json_encode($response),
                        "updatedDate" => date("Y-m-d H:i:s")
                    );
                }else{
                    $queueUpdateArr = array(
                        "status" => 1,
                        "response" => json_encode($response),
                        "updatedDate" => date("Y-m-d H:i:s")
                    );
                }
                
                ManageData(CLEVER_REACH_DELAY_USER_DATA, $queueCondition, $queueUpdateArr, $is_insert);
    
                // Update response in clever reach history data.
                $historyData = array(       
                    'groupName' => $user['groupName'],
                    'keyword' => $user['keyword'],
                    'updateDate' => date("Y-m-d"),
                    'updateDateTime' => date("Y-m-d H:i:s"),
                    'response' => json_encode($response)
                );
                if($response != null){
                    if($response['result'] == "success"){
                        $historyData['status'] = 1; // success
                    }else{
                        $historyData['status'] = 0; // bad request
                    }
                }else{
                    $historyData['status'] = 0; // pending
                }
    
                $historyCondition = array(
                    'liveDeliveryDataId' => $user['liveDeliveryDataId'],
                    'providerId' => $user['providerId']
                );
                $is_insert = false;
                ManageData(EMAIL_HISTORY_DATA, $historyCondition, $historyData, $is_insert);
            }
        }        
    }        
}