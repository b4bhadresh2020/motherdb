<?php

class Cron_aweber_delay_user extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_aweber_cron');
    }

    public function index() {       
        
        $providers = GetAllRecord(PROVIDERS,array("provider" => "1"),false,array(),array(),array());
        foreach($providers as $provider){
            $providerData[$provider['id']] = $provider;
        }

        //get file data
        $condition = array(
            'deliveryTimestamp <=' => time(),
            'status' => 0
        );

        $this->db->select('aweber_delay_user_data.id,live_delivery_data.liveDeliveryDataId,live_delivery_data.apikey,firstName,lastName,emailId,address,postCode,city,live_delivery_data.country,phone,gender,birthdateDay,birthdateMonth,birthdateYear,age,ip,tag,sucFailMsgIndex,providerId,live_delivery_data.groupName,live_delivery_data.keyword,isDuplicate');
        $this->db->from(AWEBER_DELAY_USER_DATA);
        $this->db->join(LIVE_DELIVERY_DATA,'aweber_delay_user_data.liveDeliveryDataId=live_delivery_data.liveDeliveryDataId');
        $this->db->join(LIVE_DELIVERY,'live_delivery_data.apikey=live_delivery.apikey');
        $this->db->where($condition);
        $this->db->order_by('deliveryTimestamp');
        $this->db->limit(140);
        $query=$this->db->get();
        $userData= $query->result_array();
        
        // pre($userData);
        // die;

        foreach($userData as $user){            
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
                $response = $this->mdl_aweber_cron->AddEmailToAweberSubscriberList($user,$user['country'],$user['providerId']);
            }else{
                $response = array("result" => "success","data" => "Duplicate condition not satisfied");
            }
            
            $responseField = $providerData[$user['providerId']]['response_field'];

            // Update response in live delivery user data table
            $condition = array('liveDeliveryDataId' => $user['liveDeliveryDataId']);
            $is_insert = false;
            $updateArr = array($responseField => json_encode($response));
            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);

            // Update response in aweber queue user data table
            $queueCondition = array('id' => $user['id']);
            $is_insert = false;
            
            if($response['result'] == "success"){
                    $queueUpdateArr = array(
                        "status" => 1,
                        "response" => json_encode($response),
                        "updatedDate" => date("Y-m-d H:i:s")
                    );
            }else{
                if(strpos($response["error"]["msg"], '401') !== false){
                    $queueUpdateArr = array(
                        "status" => 0,
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
            }
            
            ManageData(AWEBER_DELAY_USER_DATA, $queueCondition, $queueUpdateArr, $is_insert);

            // Update response in aweber history data.
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
                    if(strpos($response["error"]["msg"], '401') !== false){
                        $historyData['status'] = 0; // error - Bad Request (auth)
                    }else{
                        $historyData['status'] = 2; // error - already subscribe + other error
                    }
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