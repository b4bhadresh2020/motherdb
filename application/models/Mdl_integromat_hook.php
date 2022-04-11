<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_integromat_hook extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getAllHook($getData,$start,$perpage) {

        $totalCount = GetAllRecordCount(INTEGROMAT_HOOKS);

        $hooks = array();
        if ($totalCount > 0) {
            $this->db->limit($perpage,$start);
            $hooks =  GetAllRecord(INTEGROMAT_HOOKS);  
        }

        $response = array(
            'totalCount' => $totalCount,
            'hooks' => $hooks 
        );        
        return $response;        
    }
}