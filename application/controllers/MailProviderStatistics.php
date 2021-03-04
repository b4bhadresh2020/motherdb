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
        
        $provider = $this->input->post('provider');
        $list = $this->input->post('list');
        $deliveryDate = $this->input->post('deliveryDate');
        
        //get all apikey 
        $condition = array("isInActive" => 0);
        $is_single = FALSE;
        $liveDeliveries = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(array("mailProvider" => '"'.$list.'"')), array(), array(), 'apikey,mailProvider,delay');

        $liveDeliveryDelay = [];
        foreach ($liveDeliveries as $liveDelivery) {
            if(!empty($liveDelivery['delay'])){
                $delays = json_decode($liveDelivery['delay'],true);
                $liveDeliveryDelay[$liveDelivery['apikey']] = $delays[$list];
            }else{
                $liveDeliveryDelay[$liveDelivery['apikey']] = 0;
            }
        }
        pre($liveDeliveryDelay);
        die;

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
