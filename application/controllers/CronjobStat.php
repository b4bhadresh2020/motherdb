<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CronjobStat extends CI_Controller {

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
        $dataCount = GetAllRecordCount(CRON_STATUS,$condition);

        $cronjonStatData = array();
        if($dataCount > 0){
            $cronjonStatData = GetAllRecord(CRON_STATUS, $condition, "",array(),array(),array(array('cronStatusId' => 'DESC')));    
        }
        
        $perPage = 15;
        $data = pagination_data('CronjobStat/manage/', $dataCount, $start, 3, $perPage,$cronjonStatData);
        $data['headerTitle'] = "Cronjob Status";
        $data['load_page'] = 'cronStat';
        $data["curTemplateName"] = "cronjobStat/list";
        $data['start'] = $start;

        //get cronjob stat
        $cronjobRunningStatus = 'No File is pending';
        $isCronRunning = getConfigVal('isCronRunning');

        if ($isCronRunning == 0) {
            $cronjobRunningStatus = 'Cronjob is not running';
        }else if($isCronRunning == 1){
            $cronjobRunningStatus = 'Cronjob is running';
        }

        $data['cronjobRunningStatus'] = $cronjobRunningStatus;

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */


}