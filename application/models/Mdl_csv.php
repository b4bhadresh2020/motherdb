<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_csv extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function insertGroupName($groupName){

        //Insert group in group table
        $condition = array('groupName' => $groupName);
        $is_single = TRUE;
        $countGroupData = GetAllRecordCount(GROUP_MASTER,$condition,$is_single);

        if ($countGroupData == 0) {
            //insert group 
            $condition = array();
            $groupDataArr = array('groupName' => $groupName);
            $is_insert = TRUE;
            ManageData(GROUP_MASTER,$condition,$groupDataArr,$is_insert);
        }
    }   


    public function insertKeyword($keyword){
        
        //Insert keyword in keyword table
        $condition = array('keyword' => $keyword);
        $is_single = TRUE;
        $countKeywordData = GetAllRecordCount(KEYWORD_MASTER,$condition,$is_single);

        if ($countKeywordData == 0) {
            //insert keyword 
            $condition = array();
            $keywordDataArr = array('keyword' => $keyword);
            $is_insert = TRUE;
            ManageData(KEYWORD_MASTER,$condition,$keywordDataArr,$is_insert);
        } 
    }
}