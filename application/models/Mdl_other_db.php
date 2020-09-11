<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_other_db extends CI_Model {

    private $other_db;

	public function __construct() {
        parent::__construct();

        //Load another database
        $this->other_db = $this->load->database('another_db', TRUE);
    }


    function getUnsubscriberListByUniqueKey(){

        $unsubscribedUserList = $this->getAllUnsubscriberList();

        //return only unique key arr
        $uniqueKeyArr = array();
        foreach ($unsubscribedUserList as $usul) {
            $uniqueKeyArr[] = $usul['uniqueKey'];
        }
        return $uniqueKeyArr;
    }

    private function getAllUnsubscriberList(){

        $qry = "SELECT * FROM ". SMS_UNSUBSCRIBER_LIST;
        $unsubscribedUniqueKeyList = $this->GetDatabyqry($qry);

        return $unsubscribedUniqueKeyList;
    }

    private function GetDatabyqry($sql) {

        $res = $this->other_db->query($sql);
        return $res->result_array();
    }


    public function add_data_in_unique_key_link_table($insert_data){
        $this->other_db->insert(UNIQUEKEY_LINK,$insert_data);
    }

}