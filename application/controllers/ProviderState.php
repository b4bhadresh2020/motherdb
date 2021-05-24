<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ProviderState extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

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

    function sendgrid($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,SENDGRID);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'sendgrid';
        $data['formUrl'] = "providerState/sendgrid/0";
        $data['currentProviderName'] = "Sendgrid";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    } 

    function sendinblue($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,SENDINBLUE);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'sendinblue';
        $data['formUrl'] = "providerState/sendinblue/0";
        $data['currentProviderName'] = "Sendinblue";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    } 

    function sendpulse($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,SENDPULSE);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'sendpulse';
        $data['formUrl'] = "providerState/sendpulse/0";
        $data['currentProviderName'] = "Sendpulse";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    } 

    function mailerlite($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,MAILERLITE);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'mailerlite';
        $data['formUrl'] = "providerState/mailerlite/0";
        $data['currentProviderName'] = "Mailerlite";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }

    function mailjet($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,MAILJET);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'mailjet';
        $data['formUrl'] = "providerState/mailjet/0";
        $data['currentProviderName'] = "Mailjet";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }

    function convertkit($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,CONVERTKIT);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'convertkit';
        $data['formUrl'] = "providerState/convertkit/0";
        $data['currentProviderName'] = "Convertkit";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }

    function marketingPlatform($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,MARKETING_PLATFORM);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'marketingPlatform';
        $data['formUrl'] = "providerState/marketingPlatform/0";
        $data['currentProviderName'] = "Marketing Platform";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }

    function ontraport($start = 0) {
        $data = array();
        $apiKeys = GetAllRecord(LIVE_DELIVERY,array(),false,array(),array(),array());
        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $response = $this->mdl_provider_state->get_state_data($_GET,ONTRAPORT);
        $data['apiKeys'] = $apiKeys;
        $data['providerStatusInfo'] = $response['providerStatusInfo'];
        $data['weekDays'] = $response['weekDays'];
        $data['liveDeliveryProvider'] = $response['liveDeliveryProvider'];
        $data['liveRepostProvider'] = $response['liveRepostProvider'];
        $data['headerTitle'] = "Email State Data";
        $data['load_page'] = 'ontraport';
        $data['formUrl'] = "providerState/ontraport/0";
        $data['currentProviderName'] = "Ontraport";
        $data["curTemplateName"] = "providerState/list";
        $this->load->view('commonTemplates/templateLayout', $data);        
    }
}