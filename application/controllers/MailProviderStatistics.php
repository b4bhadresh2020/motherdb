<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MailProviderStatistics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged())
            redirect(base_url());
    }

    public function index()
    {

        $data['load_page'] = 'mailProviderStatistics';
        $data['headerTitle'] = "Mail Provider Statistics";
        $data["curTemplateName"] = "mailProviderStatistics/report";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getMailProviderData()
    {
        $data['load_page'] = 'mailProviderStatistics';
        $data['headerTitle'] = "Mail Provider Statistics";
        $data["curTemplateName"] = "mailProviderStatistics/report";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getProviderList(){
        $provider = $this->input->post("provider");
        $condition = array("provider" => $provider);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecord(PROVIDERS, $condition, $is_single, array(), array(), array(), 'id,listname');
        echo json_encode($liveDeliveries);
    }
}
