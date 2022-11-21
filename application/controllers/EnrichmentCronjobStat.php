<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EnrichmentCronjobStat extends CI_Controller {

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

     public function exportCsv()
    {
        $start = 0;
        $perpage = 500000;
        $allEnrichDataArray = [];

        $condition = array(
            'enrichCronStatusId'  => $_GET['enrichCronStatusId']
        );

        $is_single = true;
        $enrichCronData = GetAllRecord(ENRICHMENT_CRON_STATUS, $condition, $is_single, array(), array(),array(),'header');

        $is_single = false;
        $enrichHistoryData = JoinData(ENRICHMENT_HISTORY_DATA, $condition, USER, "userId", "userId", "", $is_single, array(), "");        
       
        if (count($enrichHistoryData) > 0) {
         
            $csvHeader = json_decode($enrichCronData['header']);
            
            // Remove double quatation mark from the field name
            foreach ($csvHeader as $key => $value) {
                $csvHeader[$key] = trim($value,'"');
            }
            
            $csvExtraFieldHeader = [];
            if(!in_array("Full Name",$csvHeader)){
                $csvExtraFieldHeader[] = "firstName";
            }
            if(!in_array("Last Name",$csvHeader)){
                $csvExtraFieldHeader[] = "lastName";
            }
            if(!in_array("Email Id",$csvHeader)){
                $csvExtraFieldHeader[] = "emailId";
            } 
            if(!in_array("Phone",$csvHeader)){
                $csvExtraFieldHeader[] = "phone";
            } 
            if(!in_array("Address",$csvHeader)){
                $csvExtraFieldHeader[] = "address";
            } 
            if(!in_array("city",$csvHeader)){
                $csvExtraFieldHeader[] = "city";
            }            
            if(!in_array("Postcode",$csvHeader)){
                $csvExtraFieldHeader[] = "postCode";
            } 
            if(!in_array("Ip",$csvHeader)){
                $csvExtraFieldHeader[] = "ip";
            } 
            if(!in_array("campaignSource",$csvHeader)){
                $csvExtraFieldHeader[] = "campaignSource";
            } 
            if(!in_array("createdDate",$csvHeader)){
                $csvExtraFieldHeader[] = "createdDate";
            }
            if(!in_array("birthdateDay",$csvHeader)){
                $csvExtraFieldHeader[] = "birthdateDay";
            }
            if(!in_array("birthdateMonth",$csvHeader)){
                $csvExtraFieldHeader[] = "birthdateMonth";
            }
            if(!in_array("birthdateYear",$csvHeader)){
                $csvExtraFieldHeader[] = "birthdateYear";
            }

            foreach($enrichHistoryData as $enrichment){

                $enrichDataArray = json_decode($enrichment['enrichData']);
                if(isset($csvExtraFieldHeader) && count($csvExtraFieldHeader)){
                    foreach($csvExtraFieldHeader as $fieldName){
                        $enrichDataArray[] = $enrichment[$fieldName];
                    }
                }

                $allEnrichDataArray[] = $enrichDataArray;
            }

            $mergeCsvHeader = array_merge($csvHeader,$csvExtraFieldHeader);

            // file creation
            if ($start == 0) {
                                
                $filename = 'enrichment_' . date('Y-m-d H:i:s') . '_Entries.csv';
                
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");

                $file = fopen('php://output', 'w');
                fputcsv($file, $mergeCsvHeader);
                fclose($file);
            }

            $file = fopen('php://output', 'w');
            foreach ($allEnrichDataArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            exit;

        } else if (count($enrichHistoryData) == 0 && $start != 0) {
            exit;
        } else {

            $header             = array();
            $allEnrichDataArray  = array(array('', 'There is no data !'));
            $filename           = 'blank_excel_' . date('Y-m-d H:i:s') . ".csv";

            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Type: application/csv; ");

            // file creation
            $file = fopen('php://output', 'w');

            fputcsv($file, $header);
            foreach ($allEnrichDataArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            exit;
        }

    }

}