<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_felinafinans_db extends CI_Model {

    private $felinafinans_db;

	public function __construct() {
        parent::__construct();
        //Load another database
        $this->felinafinans_db = $this->load->database('felinafinans_db', TRUE);
    }

    #insert update query with filter and flag

    function ManageData($table_name = '', $condition = array(), $udata = array(), $is_insert = false) {
        $resultArr = array();
        if ($condition && count($condition))
            $this->felinafinans_db->where($condition);
        if ($is_insert) {
            $this->felinafinans_db->insert($table_name, $udata);
            $insertid = $this->felinafinans_db->insert_id();
            return $insertid;
            #return 0;
        } else {
            if($this->felinafinans_db->update($table_name, $udata)){
                return 1;
            }else{
                return 0;
            }            
        }
    }    
}