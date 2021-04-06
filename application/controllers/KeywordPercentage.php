<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class KeywordPercentage extends CI_Controller
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


        // for keyword
        $keywordDataCount = GetAllRecordCount(KEYWORD_MASTER,array());

        $keywordData = array();
        if($keywordDataCount > 0){
            $keywordData = GetAllRecord(KEYWORD_MASTER, array(), "");
        }
        
        $perPage = 15;
        $data['keyword'] = pagination_data('keywordPercentage/manage/', $keywordDataCount, $start, 3, $perPage, $keywordData);


        // for batchstat
        /*$responseData = $this->mdl_batchstat->getBatchStatData($_GET,$start,$perPage); 

        $dataCount = $responseData['totalCount'];
        $batchStatData = $responseData['batchStatData'];

        $data['batchstat'] = pagination_data('keywordPercentage/manage/', $dataCount, $start, 3, $perPage,$batchStatData);*/


        $data['load_page'] = 'keywordPercentage';
        $data["curTemplateName"] = "keywordPercentage/list";
        $data['headerTitle'] = "Keyword Percentage";
        $data['pageTitle'] = "Keyword Percentage";
        $data['start'] = $start;

        $this->load->view('commonTemplates/templateLayout', $data);
    }

}