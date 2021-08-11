<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_active_campaign extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $this->load->model('mdl_active_campaign');

    }

    public function index()
    {
        $mailProvider = $this->input->post('provider');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
        $activeCampaignAccountId     = $providerData['aweber_account']; 
        
        $activeCampaignCondition   = array('id' => $activeCampaignAccountId);
        $is_single           = true;
        $activeCampaignAccountData   = GetAllRecord(ACTIVE_CAMPAIGN_ACCOUNTS, $activeCampaignCondition, $is_single);

        $response = null;
        if($activeCampaignAccountData['status'] == 1) {
            $emailId = $this->input->post('emailId');
            $phone = $this->input->post('phone');
            $firstName = $this->input->post('firstName');
            $lastName = $this->input->post('lastName');
            $birthDate = $this->input->post('birthDate');
                    
            $data = array(
                'emailId' => $emailId,
                'phone' => $phone,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'birthDate' => $birthDate
            );
            
            $response = $this->mdl_active_campaign->AddEmailToActiveCampaignSubscriberList($data,$mailProvider);
        } else {
            $response = array("result" => "error","error" => array("msg" => "Account closed"));
        }
        echo json_encode($response);
    }
}
