<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_history extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function addInHistoryTable($fileName,$fileModuleType,$isImported,$jsonValue,$totalCount = ''){

        $condition = array();
        $is_insert = TRUE;
        $dataArr = array(
            'fileName' => $fileName,
            'fileModuleType' => $fileModuleType,
            'totalCount' => $totalCount,
            'isImported' => $isImported,
            'value' => $jsonValue
        );

        ManageData(HISTORY,$condition,$dataArr,$is_insert);
    }   

    public function getHistoryData($getData,$start,$perpage) {

        $isImported = @$getData['isImported'];
        $fileModuleType = @$getData['fileModuleType'];

        $condition = array();
        $is_single = false;

        if (isset($isImported) && $isImported != '') {
            $condition['isImported'] = $isImported;
        }

        if (@$fileModuleType) {
            $condition['fileModuleType'] = $fileModuleType;   
        }

        $totalCount = GetAllRecordCount(HISTORY,$condition);

        $historyData = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $historyData = GetAllRecord(HISTORY,$condition,$is_single,array(),array(),array(array('createdDate' => 'DESC')));    
        }

        $response = array(
            'totalCount' => $totalCount,
            'historyData' => $historyData 
        );
        
        return $response;
        
    }

}