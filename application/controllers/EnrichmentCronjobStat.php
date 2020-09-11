<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EnrichmentCronjobStat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {

        $condition = array();
        $dataCount = GetAllRecordCount(ENRICHMENT_CRON_STATUS, $condition);

        $enrichmentCronjobStatData = array();
        if($dataCount > 0){
            $enrichmentCronjobStatData = GetAllRecord(ENRICHMENT_CRON_STATUS, $condition, "",array(),array(),array(array('enrichCronStatusId' => 'DESC')));    
        }
        
        $perPage = 15;
        $data = pagination_data('enrichmentCronjobStat/manage/', $dataCount, $start, 3, $perPage, $enrichmentCronjobStatData);
        $data['headerTitle'] = "Enrichment Cronjob Status";
        $data['load_page'] = 'enrichCronStat';
        $data["curTemplateName"] = "enrichCronjobStat/list";
        $data['start'] = $start;

        //get cronjob stat
        $cronjobRunningStatus = 'No File is pending';
        $isCronRunning = getConfigVal('isEnrichCronRunning');

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