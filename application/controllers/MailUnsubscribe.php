<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MailUnsubscribe extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged())
            redirect(base_url());
    }

    public function index()
    {

        $data['load_page'] = 'mailUnsubscribe';
        $data['headerTitle'] = "Mail Unsubscribe";
        $data["curTemplateName"] = "mailUnsubscribe/list";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function mailUnsubscribe()
    {        
        $provider = $this->input->post('provider');
        $country = $this->input->post('country');
        $list = $this->input->post('list');
        $email = $this->input->post('email');        
                
        $data['load_page'] = 'mailUnsubscribe';
        $data['headerTitle'] = "Mail Unsubscribe";
        $data['curTemplateName'] = "mailUnsubscribe/list";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getProviderList(){
        $provider = $this->input->post("provider");
        $country  = $this->input->post("country");
        $condition = array("provider" => $provider);
        $is_in = array("country" => $country);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecordIn(PROVIDERS, $condition, $is_single, array(), array(), array(),$is_in,'id,listname');
        echo json_encode($liveDeliveries);
    }

    function unsubscribe(){
        $provider = $this->input->post('provider');
        $country = $this->input->post('country');
        $list = $this->input->post('list');
        $email = $this->input->post('email'); 
        echo "true";
    }
}
