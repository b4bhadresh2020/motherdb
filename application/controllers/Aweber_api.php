<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Aweber_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        $this->load->model('mdl_aweber_api');

    }

    public function index()
    {
        $mailProvider = $_GET['provider'];
        $customeField = [];
        if(isset($_GET['phone'])){
            $customeField['phone'] = $_GET['phone'];
        }

        if(@$_GET['day'] != '' && @$_GET['month'] != '' && @$_GET['year'] != '') {

            $birthDate  = $_GET['year'] . '-' . $_GET['month'] . '-' . $_GET['day'];
            $customeField['birthDate'] = date('Y-m-d', strtotime($birthDate));
        }
                
        $data = array(
            'name' => $_GET['name'],
            'email' => $_GET['email']
        );

        $this->mdl_aweber_api->AddEmailToAweberSubscriberList($data,$customeField,$mailProvider);
    }
}
