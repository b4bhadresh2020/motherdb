<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_expert_sender extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $this->load->model('mdl_expert_sender');

    }

    public function index()
    {
        $mailProvider = $this->input->post('provider');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
        $expertSenderAccountId     = $providerData['aweber_account']; 
        
        $expertSenderCondition   = array('id' => $expertSenderAccountId);
        $is_single           = true;
        $expertSenderAccountData   = GetAllRecord(EXPERT_SENDER_ACCOUNTS, $expertSenderCondition, $is_single);

        $response = null;
        if($expertSenderAccountData['status'] == 1) {
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
            
            $response = $this->mdl_expert_sender->AddEmailToExpertSenderSubscriberList($data,$mailProvider);
        } else {
            $response = array("result" => "error","error" => array("msg" => "Account closed"));
        }
        echo json_encode($response);
    }
}
