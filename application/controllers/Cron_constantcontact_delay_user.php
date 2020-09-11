<?php

class Cron_constantcontact_delay_user extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mdl_constantcontact');                                
    }

    public function index() {       

        
        $providers = GetAllRecord(PROVIDERS,array("provider" => CONSTANTCONTACT),false,array(),array(),array());
        foreach($providers as $provider){
            $providerData[$provider['id']] = $provider;
        }

        //get file data
        $condition = array(
            'deliveryTimestamp <=' => time(),
            'status' => 0
        );
        $is_single = FALSE;
        $userData = JoinData(CONTACT_DELAY_USER_DATA,$condition,LIVE_DELIVERY_DATA,"liveDeliveryDataId","liveDeliveryDataId","left",$is_single,array());

        foreach($userData as $user){
            $response = $this->mdl_constantcontact->AddEmailToContactSubscriberList($user,$user['providerId']);   
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
            ManageData(CONTACT_DELAY_USER_DATA, $queueCondition, $queueUpdateArr, $is_insert);

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