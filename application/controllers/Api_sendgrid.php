<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_sendgrid extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $this->load->model('mdl_sendgrid');

    }

    public function index()
    {
        $mailProvider = $this->input->post('provider');

        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $mailProvider);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);   
        $sendgridAccountId     = $providerData['aweber_account']; 
        
        $sendgridCondition   = array('id' => $sendgridAccountId);
        $is_single           = true;
        $sendgridAccountData   = GetAllRecord(SENDGRID_ACCOUNTS, $sendgridCondition, $is_single);

        $response = null;
        if($sendgridAccountData['status'] == 1) {
            $emailId = $this->input->post('emailId');
            $phone = $this->input->post('phone');
            $firstName = $this->input->post('firstName');
            $lastName = $this->input->post('lastName');
            $birthDate = $this->input->post('birthDate');
            $tag = $this->input->post('tag');
                    
            $data = array(
                'emailId' => $emailId,
                'phone' => $phone,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'birthDate' => $birthDate,
                'tag'       =>  $tag
            );
            
            $response = $this->mdl_sendgrid->AddEmailToSendgridSubscriberList($data,$mailProvider);
        } else {
            $response = array("result" => "error","error" => array("msg" => "Account closed"));
        }
        echo json_encode($response);
    }
}
