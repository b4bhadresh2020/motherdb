<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_esp_account extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function get_account_data($table, $start, $perpage){
        $dataCount = GetAllRecordCount($table);
        $accounts = array();

        if ($dataCount > 0) {
            $this->db->limit($perpage,$start);
            $accounts =  GetAllRecord($table);    
        }
        $response = array(
            'totalCount' => $dataCount,
            'accounts' => $accounts 
        );
        return $response;
    }

    public function get_account_status_log($getData, $esp, $accountId, $start, $perPage) {
        $created_at = @$getData['created_at'];
        $condition = array(
            'account_status_log.esp' => $esp,
            'account_status_log.account_id' => $accountId
        );
        if(!empty($created_at)) {
            $condition['DATE(created_at)'] = $created_at;
        }
        
        $is_single = false;
        $dataCount = GetAllRecordCount(ACCOUNT_STATUS_LOG, $condition, $is_single);       
        $result = array();

        if($dataCount > 0) {
            $table = getAccountTable($esp);
            $this->db->limit($perPage,$start);
            $result = JoinData(ACCOUNT_STATUS_LOG,$condition,$table,"account_id","id","left",$is_single,array(),"account_status_log.*,email_id",""); 
        }
        $response = array(
            'totalCount' => $dataCount,
            'statusLog' => $result
        );
        return $response;       
    }
}