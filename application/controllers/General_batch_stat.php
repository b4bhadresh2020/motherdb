<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General_batch_stat extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_general_batch_stat');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_general_batch_stat->getGeneralBatchStatData($_GET,$start,$perPage);

        $dataCount = $responseData['totalCount'];
        $resultData = $responseData['resultData'];

        $data = pagination_data('general_batch_stat/manage/', $dataCount, $start, 3, $perPage, $resultData);

        $data['load_page'] = 'general_batch_stat';
        $data["curTemplateName"] = "general_batch_stat/list";
        $data['headerTitle'] = "General Group Clickers Stat (General Batch)";
        $data['pageTitle'] = "general Group Clickers Stat (General Batch)";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

   
    
}