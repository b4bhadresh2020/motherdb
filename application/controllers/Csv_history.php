<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Csv_history extends CI_Controller
{
	
	public function __construct() {
        parent::__construct();

        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_csv_history');
    }

    public function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }
        
        $perPage = 25;
        $responseData = $this->mdl_csv_history->get_csv_history_data($_GET,$start,$perPage);
        
        $dataCount = $responseData['totalCount'];
        $export_files_data = $responseData['export_files_data'];

        $data = pagination_data('csv_history/manage/', $dataCount, $start, 3, $perPage, $export_files_data);

        $data['load_page'] = 'csv_history';
        $data["curTemplateName"] = "csv_history/list";
        $data['headerTitle'] = "CSV History List";
        $data['pageTitle'] = "CSV History List";

        $this->load->view('commonTemplates/templateLayout', $data);
    }

    

    public function export_csv($fileNameId){
        
        //get data of file detail from export_files
        $condition = array('fileNameId' => $fileNameId);
        $is_single = TRUE;
        $this->db->limit(1);
        $export_files_data = GetAllRecord(EXPORT_FILES,$condition,$is_single);
        
        $csvType = $export_files_data['csvType'];

        if ($csvType == 1) {
            $this->csvWithMergeTag($export_files_data);
        }else{
            $this->csvWithOutMergeTag($export_files_data);
        }
        
    }

    public function csvWithMergeTag($postData, $start = 0, $perPage = 25){

        $postData['table_name'] = WITH_MERGE_TAG;
        $modelResponse = $this->mdl_csv_history->get_csv_data($postData, $start, $perPage);

        $totalCount = $modelResponse['totalCount'];
        $userData   = $modelResponse['csv_data'];

        if ($totalCount > 0) {

            //with mergetags
            $reArrangeArray = $this->mdl_csv_history->makeArrForExcelMergeTag($postData, $userData);

            // file creation
            if ($start == 0) {

                $fileNameId = $postData['fileNameId'];
                $this->mdl_csv_history->changeFileDownloadFlag($fileNameId);

                $header   = array('Number', 'Url', 'Unsubscribe Url');
                $fileName = $postData['fileName'];

                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Content-Type: application/csv; ");

                //open file to write
                $file = fopen('php://output', 'w');
                fputcsv($file, $header);
                fclose($file);

            }

            //open file to write
            $file = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);

            //check if there is more data or not
            if ($perPage == count($userData)) {

                $start = $start + $perPage;
                $this->csvWithMergeTag($postData, $start);

            } else {

                //client wants extra lines at the end of the csv file
                $msg            = $postData['msg'];
                $reArrangeArray = $this->mdl_csv_history->getExcelLastLinesWithMergeTag($msg);

                $file = fopen('php://output', 'w');
                foreach ($reArrangeArray as $key => $line) {
                    fputcsv($file, $line);
                }
                fclose($file);
                exit;
            }

        } else if (count($userData) == 0 && $start != 0) {

            //client wants extra lines at the end of the csv file
            $msg            = $postData['msg'];
            $reArrangeArray = $this->mdl_csv_history->getExcelLastLinesWithMergeTag($msg);

            $file           = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            exit;

        } else {
            $this->mdl_csv_history->makeBlankCsv();
        }

    }



    public function csvWithOutMergeTag($postData, $start = 0, $perPage = 25)
    {

        
        $postData['table_name'] = WITHOUT_MERGE_TAG;
        $modelResponse = $this->mdl_csv_history->get_csv_data($postData, $start, $perPage);

        $totalCount = $modelResponse['totalCount'];
        $userData   = $modelResponse['csv_data'];

        if ($totalCount > 0) {

            //without mergetags
            $reArrangeArray = $this->mdl_csv_history->makeArrForExcelWithoutMergeTag($postData, $userData);

            // file creation
            if ($start == 0) {

                $fileNameId = $postData['fileNameId'];
                $this->mdl_csv_history->changeFileDownloadFlag($fileNameId);

                $header   = array('Number', 'Message');
                $fileName = $postData['fileName'];

                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$fileName");
                header("Content-Type: application/csv; ");

                //open file to write
                $file = fopen('php://output', 'w');
                fputcsv($file, $header);
                fclose($file);

            }

            //open file to write
            $file = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);

            //check if there is more data or not
            if ($perPage == count($userData)) {

                $start = $start + $perPage;
                $this->csvWithOutMergeTag($postData, $start);

            } else {
                exit;
            }
        } else if (count($userData) == 0 && $start != 0) {
            exit;
        } else {
            $this->mdl_csv_history->makeBlankCsv();
        }

    }
    
}