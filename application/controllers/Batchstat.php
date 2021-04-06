<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Batchstat extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_batchstat');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 25;
        $responseData = $this->mdl_batchstat->getBatchStatData($_GET,$start,$perPage);

        $dataCount = $responseData['totalCount'];
        $batchStatData = $responseData['resultData'];

        $data = pagination_data('batchstat/manage/', $dataCount, $start, 3, $perPage,$batchStatData);

        $data['load_page'] = 'batchstat';
        $data["curTemplateName"] = "batchstat/list";
        $data['headerTitle'] = "Group Clickers Stat (Batch)";
        $data['pageTitle'] = "Group Clickers Stat (Batch)";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

   
    
}