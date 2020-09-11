<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_inboxgame_db extends CI_Model {

    private $inboxgame_db;

	public function __construct() {
        parent::__construct();
        //Load another database
        $this->inboxgame_db = $this->load->database('inboxgame_db', TRUE);
    }

    #insert update query with filter and flag

    function ManageData($table_name = '', $condition = array(), $udata = array(), $is_insert = false) {
        $resultArr = array();
        if ($condition && count($condition))
            $this->inboxgame_db->where($condition);
        if ($is_insert) {
            $this->inboxgame_db->insert($table_name, $udata);
            $insertid = $this->inboxgame_db->insert_id();
            return $insertid;
            #return 0;
        } else {
            if($this->inboxgame_db->update($table_name, $udata)){
                return 1;
            }else{
                return 0;
            }            
        }
    }    
}