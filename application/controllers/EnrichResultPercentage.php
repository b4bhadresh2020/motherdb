<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EnrichResultPercentage extends CI_Controller
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

        $dataCount = GetAllRecordCount(ENRICHMENT_CRON_STATUS, array());

        $enrichResultPercentageData = array();
        if($dataCount > 0){
            $enrichResultPercentageData = GetAllRecord(ENRICHMENT_CRON_STATUS, array(), "");    
        }
        
        $perPage = 15;
        $data = pagination_data('enrichResultPercentage/manage/', $dataCount, $start, 3, $perPage, $enrichResultPercentageData);

        $data['load_page'] = 'enrichResultPercentage';
        $data["curTemplateName"] = "enrichResultPercentage/list";
        $data['headerTitle'] = "Enrichment Result Percentage";
        $data['pageTitle'] = "Enrichment Result Percentage";
        $data['start'] = $start;

        $this->load->view('commonTemplates/templateLayout', $data);
    }

}