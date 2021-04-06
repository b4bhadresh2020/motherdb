<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SiteConfig extends CI_Controller {


	public function __construct() {
		parent::__construct();
		if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }
	}

	public function index() {
		redirect("siteConfig/general");
	}

    /*
	manage
	for site  SiteConfig List
    */
	public function general() {
		$this->form_validation->set_rules('siteTitle', 'Website Title', 'required');
        
		
		$is_add = false;
        
        if ($this->input->post()) {
            $data = $_POST;
        }
        $files = $_FILES;
               
        if ($this->form_validation->run() != FALSE) {
            foreach ($data as $key => $value) {
                $condition = array("configKey" => $key);
                $dataArr   = array("configVal" => $value);
                $configId  = ManageData(SITECONFIG, $condition, $dataArr, $is_add);
                
            }
            SetMsg('loginSucMsg',"Data submit successfully");
        }
        $condition = array();
        $info = GetAllRecord(SITECONFIG, $condition, "");
        $data = array();
        if (count($info) > 0) {
            for ($i = 0;$i < count($info);$i++) {
                $data[$info[$i]["configKey"]] = $info[$i]["configVal"];
            }
        }
        $data["curTemplateName"] = "siteConfig/general";
        $data['error_msg']       = GetFormError();
        $data['suc_msg']         = GetMsg('loginSucMsg');
        $data['headerTitle']     = "General Setting";
        //pre($data);die;
        $this->load->view('commonTemplates/templateLayout', $data);
	} 

	
	
}

