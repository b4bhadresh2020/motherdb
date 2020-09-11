<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class mdl_general_batch_stat extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getGeneralBatchStatData($getData,$start,$perpage) {

        $condition = array();
        $totalCount = GetAllRecordCount(GENERAL_BATCH,$condition);

        $generalBatchStatData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $generalBatchStatData = GetAllRecord(GENERAL_BATCH,$condition,FALSE,array(),array(),array(array('generalBatchId' => 'DESC')));
        }
        
        $response = array(
            'totalCount' => $totalCount,
            'resultData' => $generalBatchStatData 
        );

        return $response;
        
    }


}