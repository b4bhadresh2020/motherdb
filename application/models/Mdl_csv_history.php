<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_csv_history extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }


    public function get_csv_history_data($getData,$start,$perpage){

        $condition = array();

        $is_single = FALSE;
        $count = GetAllRecordCount(EXPORT_FILES,$condition);

        $table_data = array();
        if ($count > 0) {
            $this->db->limit($perpage,$start);
            $table_data = GetAllRecord(EXPORT_FILES,$condition,$is_single,array(),array(),array(array('fileNameId' => 'DESC')));
        }

        $response = array(
            'totalCount' => $count,
            'export_files_data' => $table_data 
        );

        return $response;
        
    }


    public function get_csv_data($postData, $start, $perPage){
        
        $table_name = $postData['table_name'];
        $fileNameId = $postData['fileNameId'];

        $condition = array('fileNameId' => $fileNameId);
        $totalCount = GetAllRecordCount($table_name,$condition); 

        $csv_data = array();
        if ($totalCount > 0) {
            //get data
            $condition = array('fileNameId' => $fileNameId);
            $is_single = FALSE;
            $this->db->limit($perPage,$start);
            $csv_data = GetAllRecord($table_name,$condition);
        }

        $response = array(
            'totalCount' => $totalCount,
            'csv_data' => $csv_data
        );

        return $response;
    }


    public function makeArrForExcelMergeTag($postData,$userData){

        $msg = $postData['msg'];
        $redirectUrl = $postData['redirectUrl'];
        $excelDataArr = array();
        
        $i = 0;
        foreach ($userData as $value) {

            $phone = $value['phone'];
            $url = $value['url'];
            $unsubscribe_url = $value['unsubscribe_url'];

            
            $excelDataArr[$i]['phone'] = $phone;
            $excelDataArr[$i]['url'] = $url;
            $excelDataArr[$i]['unsubscribe_url'] = $unsubscribe_url;

            $i++;
        }
        return $excelDataArr;
    }

    
    public function getExcelLastLinesWithMergeTag($msg){

        /*
         *  now skip 4 lines in excel and then add two lines
         *  1) SMS hos provideren kunne se sådan ud:
         *  2) message
         */   
         
        $spaceArray = array();    
        for ($i = 0; $i < 7 ; $i++) { 

            $spaceArray[$i]['number'] = '';

            if ($i == 5) {
                $spaceArray[$i]['number'] = 'SMS hos provideren kunne se sådan ud:';
            }else if($i == 6){
                $spaceArray[$i]['number'] = $msg;
            }

        }
        return $spaceArray;
    }



    public function makeArrForExcelWithoutMergeTag($postData,$userData){

        $excelDataArr = array();
       
        $i = 0;
        foreach ($userData as $value) {

            $phone = $value['phone'];
            $message = $value['message'];
            //make array for excel
            $excelDataArr[$i]['phone'] = $phone;
            $excelDataArr[$i]['msg'] = $message;

            $i++;
        }

        return $excelDataArr;
        
    }



    public function makeBlankCsv()
    {

        //no data found in excel
        $header         = array();
        $reArrangeArray = array(array('', 'There is no data !'));
        $fileName       = 'blank_csv_' . date('Y-m-d H:i:s') . ".csv";

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/csv; ");

        // file creation
        $file = fopen('php://output', 'w');

        fputcsv($file, $header);
        foreach ($reArrangeArray as $key => $line) {
            fputcsv($file, $line);
        }
        fclose($file);
        exit;
    }

    public function changeFileDownloadFlag($fileNameId){

        $condition = array('fileNameId' => $fileNameId);
        $is_insert = FALSE;
        $updateArr = array('isUsed' => 1);

        ManageData(EXPORT_FILES,$condition,$updateArr,$is_insert);
    }
}