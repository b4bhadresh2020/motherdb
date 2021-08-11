<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_mailjet extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $this->load->model('mdl_mailjet');

    }

    public function index()
    {
        $mailProvider = $this->input->post('provider');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
        $mailjetAccountId     = $providerData['aweber_account']; 
        
        $mailjetCondition   = array('id' => $mailjetAccountId);
        $is_single           = true;
        $mailjetAccountData   = GetAllRecord(MAILJET_ACCOUNTS, $mailjetCondition, $is_single);

        $response = null;
        if($mailjetAccountData['status'] == 1) {
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
            
            $response = $this->mdl_mailjet->AddEmailToMailjetSubscriberList($data,$mailProvider);
        } else {
            $response = array("result" => "error","error" => array("msg" => "Account closed"));
        }
        echo json_encode($response);
    }
}
