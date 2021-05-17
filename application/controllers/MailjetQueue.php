<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MailjetQueue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_mailjet_queue');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();
        $providers = GetAllRecord(PROVIDERS,array("provider" => MAILJET),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_mailjet_queue->get_user_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $userData = $responseData['userData'];       

        $data = pagination_data('MailjetQueue/manage/', $dataCount, $start, 3, $perPage,$userData);
        $data['mailjetList'] = $providers;
        $data['headerTitle'] = "Mailjet Queue User Data";
        $data['load_page'] = 'mailjetQueue';
        $data["curTemplateName"] = "mailjetQueue/list";
        $this->load->view('commonTemplates/templateLayout', $data);
        
    }     
}