<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BlacklistCronjobStat extends CI_Controller {

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
        $dataCount = GetAllRecordCount(BLACKLIST_CRON_STATUS, $condition);

        $blacklistCronStatusData = array();
        if($dataCount > 0){
            $blacklistCronStatusData = GetAllRecord(BLACKLIST_CRON_STATUS, $condition, "",array(),array(),array(array('blacklistCronStatusId' => 'DESC')));
        } 
        
        $perPage = 15;
        $data = pagination_data('blacklistCronjobStat/manage/', $dataCount, $start, 3, $perPage, $blacklistCronStatusData);
        $data['headerTitle'] = "Blacklist Cronjob Status";
        $data['load_page'] = 'blacklistCronStat';
        $data["curTemplateName"] = "blacklistCronJobStat/list";
        $data['start'] = $start;

        //get cronjob stat
        $cronjobRunningStatus = 'No File is pending';
        $isCronRunning = getConfigVal('isBlacklistCronRunning');

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