<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_batchstat extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getBatchStatData($getData,$start,$perpage) {

        $condition = array();
        $totalCount = GetAllRecordCount(BATCH,$condition);

        $batchStatData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $batchStatData = GetAllRecord(BATCH,$condition,FALSE,array(),array(),array(array('batchId' => 'DESC')));
        }
        
        $response = array(
            'totalCount' => $totalCount,
            'resultData' => $batchStatData 
        );

        return $response;
        
    }


}