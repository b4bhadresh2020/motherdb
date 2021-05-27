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
        echo json_encode($response);
    }
}
