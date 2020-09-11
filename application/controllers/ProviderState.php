<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProviderState extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_provider_state');    
    }

    /*
     *  list code starts here
     */

    function aweber($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,AWEBER);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'aweber';
        $data['formUrl'] = "providerState/aweber/0";
        $data['currentProviderName'] = "Aweber";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    } 
    
    function transmitvia($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,TRANSMITVIA);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'transmitvia';
        $data['formUrl'] = "providerState/transmitvia/0";
        $data['currentProviderName'] = "Transmitvia";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    } 

    function constantContact($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,CONSTANTCONTACT);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'constantcontact';
        $data['formUrl'] = "providerState/constantcontact/0";
        $data['currentProviderName'] = "Constant Contact";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }

    function ongage($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,ONGAGE);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'ongage';
        $data['formUrl'] = "providerState/ongage/0";
        $data['currentProviderName'] = "Ongage";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }
}