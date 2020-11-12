<?php

class Cron_sendinblue_delay_user extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_sendinblue');
    }

    public function index() {       
        
        $providers = GetAllRecord(PROVIDERS,array("provider" => SENDINBLUE),false,array(),array(),array());
        foreach($providers as $provider){
            $providerData[$provider['id']] = $provider;
        }

        //get file data
        $condition = array(
            'deliveryTimestamp <=' => time(),
            'status' => 0
        );
        $is_single = FALSE;
        $userData = JoinData(SENDINBLUE_DELAY_USER_DATA,$condition,LIVE_DELIVERY_DATA,"liveDeliveryDataId","liveDeliveryDataId","left",$is_single,array(),"","");

        foreach($userData as $user){   
            if (@$user['birthdateDay'] != '0' && @$user['birthdateMonth'] != '0' && @$user['birthdateYear'] != '0') {
                $birthDate  = $user['birthdateYear'] . '-' . $user['birthdateMonth'] . '-' . $user['birthdateDay'];
                $user['birthDate'] = date('Y-m-d', strtotime($birthDate));
            }else{
                $user['birthDate'] = "";
            } 

            $response = $this->mdl_sendinblue->AddEmailToSendInBlueSubscriberList($user,$user['providerId']);
            //$response = array("result" => "success","data" => array("id" => 0000));
            $responseField = $providerData[$user['providerId']]['response_field'];

            // Update response in live delivery user data table
            $condition = array('liveDeliveryDataId' => $user['liveDeliveryDataId']);
            $is_insert = false;
            $updateArr = array($responseField => json_encode($response));
            ManageData(LIVE_DELIVERY_DATA, $condition, $updateArr, $is_insert);

            // Update response in sendgrid queue user data table
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
            
            ManageData(SENDINBLUE_DELAY_USER_DATA, $queueCondition, $queueUpdateArr, $is_insert);

            // Update response in sendinblue history data.
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