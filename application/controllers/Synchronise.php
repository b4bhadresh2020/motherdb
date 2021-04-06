<?php

/**
 * 
 */
class Synchronise extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
    }

    public function manage($start = 0) {
        $data = array();
        $data['load_page'] = 'sync';
        $data["curTemplateName"] = "synchronise/list";
        $data['headerTitle'] = "Synchronise";
        $data['pageTitle'] = "Synchronise";
        $this->load->view('commonTemplates/templateLayout', $data);
    }


    public function changeSyncStatus(){
        $isSyncCronRunning = $this->input->post('changeVal');
        $condition = array("configKey" => 'isSyncCronRunning');
        $dataArr   = array("configVal" => $isSyncCronRunning);
        ManageData(SITECONFIG, $condition, $dataArr, FALSE);
    }

    
}