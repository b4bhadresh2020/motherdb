<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_blacklist extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }


    public function getblacklistUsers($getData,$start,$perpage){

        $global = @$getData['global'];
        $fileName = @$getData['fileName'];

        $condition = array();

        if(@$fileName){
            $condition['fileName'] = $fileName;
        }

        $like = array();
        if (@$global) {

            $search   = trim($global); 
            $fieldArr = array('firstName','lastName','phone','emailId','country','gender');
        
            foreach ($fieldArr as $value) {
                $like[$value] = $search;
            }
            
        }

        $is_single = FALSE;
        $blacklistCount = GetAllRecordCount(BLACKLIST,$condition,$is_single,array(),array($like),array());

        $blacklistData = array();
        if($blacklistCount > 0){

            $this->db->limit($perpage,$start);
            $blacklistData = GetAllRecord(BLACKLIST,$condition,$is_single,array(),array($like),array(array('blacklistId' => 'DESC')));
        }
        
        $response = array(
            'totalCount' => $blacklistCount,
            'blacklistData' => $blacklistData 
        );

        return $response;

    }

    
}