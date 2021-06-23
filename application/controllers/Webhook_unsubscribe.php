<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhook_unsubscribe extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }

        $this->load->model('mdl_webhook_unsubscribe');    
    }
    

    public function index()
    {
        $data['unsubscribeSettings'] = $this->mdl_webhook_unsubscribe->getUnsubscribeSettings();
        $data['load_page'] = 'webhookUnsubscribe';
        $data['headerTitle'] = "Webhook Unsubscribe Settings";
        $data["curTemplateName"] = "webhookUnsubscribe/addEdit";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    public function addEdit(){
        $otherProviders = $this->input->post();
        $response = 0;
        $otherProvidersDBColumnName = [
            '9' => 'mailjet',
            '11' => 'marketing_platform',
            '12' => 'ontraport',
            '13' => 'active_campaign',
        ];
        // set all column value as 0
        foreach($otherProvidersDBColumnName as $id => $providerCol) {
            for($i=0;$i<count($otherProvidersDBColumnName);$i++) {
                $uncheckedOptionData[$otherProvidersDBColumnName[$id]] = 0;
            }
            $is_insert = false;
            ManageData(WEBHOOK_UNSUBSCRIBE_SETTINGS, [],$uncheckedOptionData,$is_insert);
            $response++;
        }
        
        foreach($otherProviders as $mainProvider => $otherProvider){  
            $settingData = [];           
            foreach($otherProvider as $provider => $value){
                $settingData[$otherProvidersDBColumnName[$provider]] = 1;
            }         
            $settingData['updated_at'] = date('Y-m-d H:i:s');

            $is_insert = false;
            $condition = array(
                'main_provider' => $mainProvider
            );
            // INSERT DATA IN WEBHOOK_UNSUBSCRIBE_SETTINGS TABLE
            ManageData(WEBHOOK_UNSUBSCRIBE_SETTINGS, $condition,$settingData,$is_insert);           
            $response++;
        }

        if($response > 0) {
            $this->session->set_flashdata('success', 'Settings updated successfully!');
        } else {
            $this->session->set_flashdata('error', 'Error, Settings not updated!');
        }
        redirect("Webhook_unsubscribe");
    }
}
