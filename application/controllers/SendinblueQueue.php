<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SendinblueQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_sendinblue_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => SENDINBLUE),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_sendinblue_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('SendinblueQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['sendInBlueList'] = $providers;
        $data['headerTitle'] = "Sendinblue Queue User Data";
        $data['load_page'] = 'sendinblueQueue';
        $data["curTemplateName"] = "sendinblueQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}