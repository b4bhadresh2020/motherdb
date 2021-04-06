<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CronjobProviderStat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_cronjob_provider_state');    
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {
        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 15;
        $responseData = $this->mdl_cronjob_provider_state->get_provider_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $cronjobProviderStatData = $responseData['cronjobProviderStatData'];

        $data = pagination_data('CronjobProviderStat/manage/', $dataCount, $start, 3, $perPage,$cronjobProviderStatData);
        $data['headerTitle'] = "Cronjob Provider Status";
        $data['load_page'] = 'cronProviderStat';
        $data["curTemplateName"] = "cronjobProviderStat/list";

        $this->load->view('commonTemplates/templateLayout', $data);
        
    }

    /*
     *  list code ends here
     */

     function updateStatus(){         
        //update status
        $condition = array('id' => $_POST["id"]);
        $is_insert = FALSE;
        $updateArr = array('status' => !$_POST["status"]);
        ManageData(CSV_FILE_PROVIDER_DATA,$condition,$updateArr,$is_insert);        
     }

     function delete(){      
         
        $condition = array('id' => $_POST["id"]);
        $is_insert = FALSE;
        $updateArr = array('status' => "3");
        ManageData(CSV_FILE_PROVIDER_DATA,$condition,$updateArr,$is_insert); 

        // //delete from csv file provider data
        // $this->db->where("id", $_POST["id"]);
        // $this->db->delete(CSV_FILE_PROVIDER_DATA);

        // //delete from actual cron user data for the provider
        // $this->db->where("providerId", $_POST["id"]);
        // $this->db->delete(CSV_CRON_USER_DATA);

        return true;
     }

     function getProviderHistoryData(){        
         $providerId = trim($this->input->post('providerId'));
        //get file data
        $condition = array(
            'providerId' => $providerId
        );
        $is_single = FALSE;
        $historyProviderData = GetAllRecord(CSV_FILE_PROVIDER_HISTORY,$condition,$is_single,array(),array(),array()); 
        $data['historyProviderData'] = $historyProviderData;
        $this->load->view('cronjobProviderStat/provider_detail_table_view', $data);
     }

     function getProviderData(){        
         $providerId = trim($this->input->post('providerId'));
        //get file data
        $condition = array(
            'id' => $providerId
        );
        $is_single = TRUE;
        $providerData = GetAllRecord(CSV_FILE_PROVIDER_DATA,$condition,$is_single,array(),array(),array()); 
        echo json_encode($providerData);
     }

     function updateProviderData(){         
        $is_insert = FALSE;   
        $condition = array(
            'id' => $_POST['id']
        );
        $updateRecord = array(
            'providerName' => $_POST['providerName'],                
            'providerList' => $_POST['providerList'],                
            'perDayRecord' => $_POST['perDayRecord'],                
            'fromDate' => $_POST['fromDate'],                
            'startTime' => $_POST['startTime'],                
            'endTime' => $_POST['endTime']                    
        );
        ManageData(CSV_FILE_PROVIDER_DATA,$condition,$updateRecord,$is_insert);
        redirect(site_url().'cronjobProviderStat/manage');
     }

     function historyData($start = 0){
         $id = $this->input->get('id');
        if($id != null){
            $data = array();

            if (@$this->input->get('reset')) {
                $_GET = array();
            }

            $perPage = 15;
            $responseData = $this->mdl_cronjob_provider_state->get_provider_history_data($_GET,$start,$perPage,$id);
            
            $dataCount = $responseData['totalCount'];
            $cronjobProviderHistoryData = $responseData['cronjobProviderHistoryData'];

            $data = pagination_data('CronjobProviderStat/historyData/', $dataCount, $start, 3, $perPage,$cronjobProviderHistoryData);
            $data['headerTitle'] = "Cronjob Provider History";
            $data['load_page'] = 'cronProviderHistory';
            $data["curTemplateName"] = "cronjobProviderStat/history_data";

            $this->load->view('commonTemplates/templateLayout', $data);
        }
     }
}