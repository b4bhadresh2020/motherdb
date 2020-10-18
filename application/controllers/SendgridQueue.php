<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SendgridQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_sendgrid_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => SENDGRID),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_sendgrid_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('SendgridQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['sendgridList'] = $providers;
        $data['headerTitle'] = "Sendgrid Queue User Data";
        $data['load_page'] = 'sendgridQueue';
        $data["curTemplateName"] = "sendgridQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}