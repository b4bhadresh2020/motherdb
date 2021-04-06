<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $condition = array();
        $dataCount = GetAllRecordCount(CAMPAIGN,$condition);

        $campaignData = array();
        if ($dataCount > 0) {
            $campaignData = GetAllRecord(CAMPAIGN, $condition, "",array(),array(),array(array('campaignId' => 'DESC')));    
        }
        
        $perPage = 15;
        $data = pagination_data('campaign/manage/', $dataCount, $start, 3, $perPage,$campaignData);
        $data['headerTitle'] = "Campaign";
        $data['load_page'] = 'campaign';
        $data["curTemplateName"] = "campaign/list";
        $data['start'] = $start;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */


    /*
     *  add/edit code starts here
     */

    function addEdit($campaignId = 0) {

        $this->form_validation->set_rules('campaignName','Campaign Name', 'callback_valid_campaign_name['.$campaignId.']'); 
        $this->form_validation->set_rules('country','Country', 'required'); 

        if ($this->form_validation->run() != FALSE) {

            $postVal = $_POST;
            $fieldArr = array('country','campaignName');
            $dataArr = array();
            foreach ($fieldArr as $value) {
                $dataArr[$value] = $postVal[$value];
            }

            if ($campaignId > 0) {
                $condition = array("campaignId" => $campaignId);
                $is_add = false;
                $createdId = ManageData(CAMPAIGN, $condition, $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("updateData"));
            } else {
                
                $is_add = true;
                $updatedResponse = ManageData(CAMPAIGN, array(), $dataArr, $is_add);
                SetMsg('loginSucMsg', loginRegSectionMsg("insertData"));
            }
            redirect("campaign/manage");
        }
        $data = array();
        if ($campaignId > 0) {
            $condition = array("campaignId" => $campaignId);
            $data = GetAllRecord(CAMPAIGN, $condition, true);
        }
        if ($campaignId > 0) {
            $data['addEditTitle'] = "Edit Campaign";
            $data['headerTitle']  = "Edit Campaign";
        }else{
            $data['addEditTitle'] = "Add Campaign";
            $data['headerTitle'] = "Add Campaign";

        }
        $data['load_page'] = 'campaign';
        $data["campaignId"]  = $campaignId;
        $data['error_msg'] = GetFormError();
        $data["curTemplateName"] = "campaign/addEdit";
        
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  add/edit code ends here
     */


    function valid_campaign_name($campaignName,$campaignId){
        
        if ($campaignName == '') {
            $this->form_validation->set_message('valid_campaign_name', 'The Campaign Name field is required.');
            return FALSE;
        }else{
            //check name validation
            $condition = array('campaignId !=' => $campaignId ,'campaignName' => $campaignName);
            $is_single = TRUE;
            $getCampaignName = GetAllRecord(CAMPAIGN,$condition,$is_single);
            
            if (count($getCampaignName) > 0) {
                $this->form_validation->set_message('valid_campaign_name', 'Duplicate Campaign Name.');
                return FALSE;  
            }else{
                return TRUE;
            }
        }
    }


    /*
     *  delete code starts here
     */

    function delete($campaignId = 0) {
        $this->db->where("campaignId", $campaignId);
        $this->db->delete(CAMPAIGN);
        redirect("campaign/manage");
    }

}