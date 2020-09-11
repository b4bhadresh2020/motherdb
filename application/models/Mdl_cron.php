<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_cron extends CI_Model {

	public function __construct() {
        parent::__construct();
    }

    public function updateAllStatusInCronStatus($filePath,$recordCount,$notInstertedRecords,$emptyRecords,$isFileRunning){

        //get record from CRON_STATUS table
        $condition = array('filePath' => $filePath);
        $is_single = TRUE;
        $getCronStatus = GetAllRecord(CRON_STATUS,$condition,$is_single,array(),array(),array(array('cronStatusId' => 'DESC')));  //dont change order by here (Hint : same file name,same record on both tables (cron_status,csv_file_data))

        if (count($getCronStatus) > 0) {
            
            $totalInsertedRecords = (int)$getCronStatus['totalInsertedRecords'] + $recordCount;
            $notInstertedRecords = (int)$getCronStatus['notInstertedRecords'] + $notInstertedRecords;
            $emptyRecords = (int)$getCronStatus['emptyRecords'] + $emptyRecords;

            //update record
            $condition = array('cronStatusId' => $getCronStatus['cronStatusId']);
            $updateArrr = array(
                'totalInsertedRecords' => $totalInsertedRecords,
                'notInstertedRecords' => $notInstertedRecords,
                'emptyRecords' => $emptyRecords,
                'isFileRunning' => $isFileRunning

            );
            if ($getCronStatus['isFileRunning'] != 2 ) {
                $updateArrr['isFileRunning'] = $isFileRunning;
            }
            ManageData(CRON_STATUS, $condition, $updateArrr, FALSE);
        }

    }


    public function updateAllStatusInEnrichCronStatus($filePath,$recordCount,$notUpdatedRecords,$emptyRecords,$isFileRunning){

        //get record from enrichment_cron_status table
        $condition = array('filePath' => $filePath);
        $is_single = TRUE;
        $getCronStatus = GetAllRecord(ENRICHMENT_CRON_STATUS,$condition,$is_single,array(),array(),array(array('enrichCronStatusId' => 'DESC')));  //dont change order by here (Hint : same file name,same record on both tables (enrichment_cron_status,enrich_csv_file))

        if (count($getCronStatus) > 0) {
            
            $totalUpdatedRecords = (int)$getCronStatus['totalUpdatedRecords'] + $recordCount;
            $notUpdatedRecords = (int)$getCronStatus['notUpdatedRecords'] + $notUpdatedRecords;
            $emptyRecords = (int)$getCronStatus['emptyRecords'] + $emptyRecords;

            //update record
            $condition = array('enrichCronStatusId' => $getCronStatus['enrichCronStatusId']);
            $updateArrr = array(
                'totalUpdatedRecords' => $totalUpdatedRecords,
                'notUpdatedRecords' => $notUpdatedRecords,
                'emptyRecords' => $emptyRecords
            );

            if ($getCronStatus['isFileRunning'] != 2 ) {
                $updateArrr['isFileRunning'] = $isFileRunning;
            }
            ManageData(ENRICHMENT_CRON_STATUS, $condition, $updateArrr, FALSE);
        }
    }



    public function updateAllStatusInBlackListCronStatus($filePath,$recordCount,$notDeletedRecords,$emptyRecords,$isFileRunning){

        //get record from enrichment_cron_status table
        $condition = array('filePath' => $filePath);
        $is_single = TRUE;
        $getCronStatus = GetAllRecord(BLACKLIST_CRON_STATUS,$condition,$is_single,array(),array(),array(array('blacklistCronStatusId' => 'DESC')));  //dont change order by here (Hint : same file name,same record on both tables (blacklist_cron_status,blacklist_csv_file))

        if (count($getCronStatus) > 0) {
            
            $totalDeletedRecords = (int)$getCronStatus['totalDeletedRecords'] + $recordCount;
            $notDeletedRecords = (int)$getCronStatus['notDeletedRecords'] + $notDeletedRecords;
            $emptyRecords = (int)$getCronStatus['emptyRecords'] + $emptyRecords;

            //update record
            $condition = array('blacklistCronStatusId' => $getCronStatus['blacklistCronStatusId']);
            $updateArrr = array(
                'totalDeletedRecords' => $totalDeletedRecords,
                'notDeletedRecords' => $notDeletedRecords,
                'emptyRecords' => $emptyRecords
            );

            if ($getCronStatus['isFileRunning'] != 2 ) {
                $updateArrr['isFileRunning'] = $isFileRunning;
            }
            ManageData(BLACKLIST_CRON_STATUS, $condition, $updateArrr, FALSE);
        }
    }

}