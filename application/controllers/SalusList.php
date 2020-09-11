<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SalusList extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged())
            redirect(base_url());

        $this->load->model('salus_list_model');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 20;
        $responseData = $this->salus_list_model->getSalusData($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $salusData = $responseData['salusData'];

        $data = pagination_data('salusList/manage/', $dataCount, $start, 3, $perPage,$salusData);
        $data['load_page'] = 'saluslist';
        $data["curTemplateName"] = "salus/list";
        $data['headerTitle'] = "Salus List";
        $data['pageTitle'] = "Salus List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    
}