<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_employee extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllEmployee($getData,$start,$perpage) {

        $condition = array("isDelete" => 0);
        $is_single = false;
        
        $totalCount = GetAllRecordCount(ADMINMASTER,$condition);

        $employees = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $employees =  GetAllRecord(ADMINMASTER,$condition);  
        }

        $response = array(
            'totalCount' => $totalCount,
            'employees' => $employees 
        );        
        return $response;        
    }
}