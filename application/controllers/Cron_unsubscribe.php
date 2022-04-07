<?php

/**
 * 
 */
class Cron_unsubscribe extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $condition = array(
            'provider_unsubscriber.status' => 4
        );
        $unsubscriberPendingData = JoinData(PROVIDER_UNSUBSCRIBER,$condition,PROVIDERS,"provider_id","id",'',false,[["created_at" => "asc"]],"provider_unsubscriber.id,provider_id,email,provider",50);

        pre($unsubscriberPendingData);
        if(count($unsubscriberPendingData)){
            foreach($unsubscriberPendingData as $unsubscriber){
                
                $response = null;
                switch($unsubscriber['provider']){
                    case AWEBER:
                        $this->load->model('mdl_aweber_unsubscribe');
                        $response = $this->mdl_aweber_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);                        
                        break;
                    case MAILERLITE:
                        $this->load->model('mdl_mailerlite_unsubscribe');
                        $response = $this->mdl_mailerlite_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case MAILJET:
                        $this->load->model('mdl_mailjet_unsubscribe');
                        $response = $this->mdl_mailjet_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case CONVERTKIT:
                        $this->load->model('mdl_convertkit_unsubscribe');
                        $response = $this->mdl_convertkit_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case MARKETING_PLATFORM:
                        $this->load->model('mdl_marketing_platform_unsubscribe');
                        $response = $this->mdl_marketing_platform_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case ONTRAPORT:
                        $this->load->model('mdl_ontraport_unsubscribe');
                        $response = $this->mdl_ontraport_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case ACTIVE_CAMPAIGN:
                        $this->load->model('mdl_active_campaign_unsubscribe');
                        $response = $this->mdl_active_campaign_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case EXPERT_SENDER:
                        $this->load->model('mdl_expert_sender_unsubscribe');
                        $response = $this->mdl_expert_sender_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case CLEVER_REACH:
                        $this->load->model('mdl_clever_reach_unsubscribe');
                        $response = $this->mdl_clever_reach_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case OMNISEND:
                        $this->load->model('mdl_omnisend_unsubscribe');
                        $response = $this->mdl_omnisend_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    case SENDGRID:
                        $this->load->model('mdl_sendgrid_unsubscribe');
                        $response = $this->mdl_sendgrid_unsubscribe->makeUnsubscribe($unsubscriber['email'],$unsubscriber['provider_id']);
                        break;
                    default:
                        break;
                } 
                if(isset($response) && !is_null($response)){
                    if($response["result"] == "success"){
                        $data = [
                            "name"        => $response["data"]["name"],
                            "status"      => 1, // success
                            "response"    => $response["data"]["updated_at"]
                        ];
                    }else{
                        $data = [
                            "name"        => NULL,
                            "status"      => 2, // error
                            "response"    => $response["msg"]
                        ];
                    }
                    // UPDATE DATA IN PROVIDER UNSUBSCRIBER TABLE
                    ManageData(PROVIDER_UNSUBSCRIBER,['id' => $unsubscriber['id']],$data,false);
                }  
            }
        }
    }
}