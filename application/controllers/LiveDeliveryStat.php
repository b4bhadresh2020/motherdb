<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LiveDeliveryStat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged()){
            redirect(base_url());
        }else if(is_logged() && !is_admin()){
            redirect("mailUnsubscribe");
        }

        $this->load->model('mdl_live_delivery');
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 20;
        $returnArr = $this->mdl_live_delivery->getLiveDeliveryStatData($_GET,$start,$perPage);

        $data['countsArr'] = $returnArr['countsArr'];
        $data['rejectDetailCountsArr'] = $returnArr['rejectDetailCountsArr'];
        $data['countGenderArr'] = $returnArr['countGenderArr'];
        $data['countAgeArr'] = $returnArr['countAgeArr'];
        $data['countCityArr'] = $returnArr['countCityArr'];
        $data['countTotalArr'] = $returnArr['countTotalArr'];
        $data['countRejectionArr'] = $returnArr['countRejectionArr'];

        $data['headerTitle'] = "Live Delivery Stat";
        $data['load_page'] = 'liveDeliveryStat';
        $data["curTemplateName"] = "liveDeliveryStat/list";
        $data['start'] = $start;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */

    public function exportCsv()
    {
        $start = 0;
        $perpage = 500000;

        $getData = array(
            'apikey'            => $_GET['apikey'],
            'chooseFilter'      => $_GET['chooseFilter'],
            'chooseSucFailRes'  => $_GET['chooseSucFailRes'],            
            'globleSearch' => urldecode($_GET['globleSearch']),
            'startDate' => $_GET['startDate'],
            'endDate'   => $_GET['endDate'],
        );
        
        $userDataResponse = $this->mdl_live_delivery->getLiveDeliveryStatData($getData,$start,$perpage,TRUE);
        $userdata         = $userDataResponse['filteredData'];

        
       
        $userdataCount    = count($userdata);
        if ($userdataCount > 0) {
         
            $condition = array('apikey' => $_GET['apikey']);
            $is_single = true;
            $getLiveDelivery = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(),array(array('liveDeliveryId' => 'asc')),'apikey,dataSourceType');
            $dataSourceType = $getLiveDelivery['dataSourceType'];
            
            $reArrangeArray = array();
            if($dataSourceType == 1 || $dataSourceType == 2) {
                $keyArr = array('emailId', 'createdDate');
            } else {
                $keyArr         = array('firstName', 'lastName', 'emailId', 'address', 'postCode', 'city', 'phone', 'gender', 'birthdateDay', 'birthdateMonth', 'birthdateYear', 'ip', 'createdDate');
            }

            for ($i = 0; $i < $userdataCount; $i++) {

                foreach ($keyArr as $value) {
                    $reArrangeArray[$i][$value] = "=\"" .$userdata[$i][$value]. "\"";
                }

            }
            
            // file creation
            if ($start == 0) {
                if($dataSourceType == 1 || $dataSourceType == 2) {
                    $header   = array('Email Id', 'Created on');
                } else {
                    $header   = array('Full Name', 'Last Name', 'Email Id', 'Address', 'Postcode', 'City', 'Phone', 'Gender', 'Birthdate Day', 'Birthdate Month', 'Birthdate Year', 'Ip', 'Created on');
                }
                
                $filename = 'liveuserdata_' . date('Y-m-d H:i:s') . '_Total_' . $userdataCount . '_Entries.csv';
                
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");

                $file = fopen('php://output', 'w');
                fputcsv($file, $header);
                fclose($file);
            }

            $file = fopen('php://output', 'w');
            foreach ($reArrangeArray as $key => $line) {
                fputcsv($file, $line);
            }
            fclose($file);
            exit;

        } else if (count($userdata) == 0 && $start != 0) {
            exit;
        } else {

            $header         = array();
            $reArrangeArray = array(array('', 'There is no data !'));
            $filename       = 'blank_excel_' . date('Y-m-d H:i:s') . ".csv";

            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
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

    }


     /*
     *  delete code starts here
     */

    function delete($liveDeliveryDataId = 0) {
        $this->db->where("liveDeliveryDataId", $liveDeliveryDataId);
        $this->db->delete(LIVE_DELIVERY_DATA);
        redirect("liveDeliveryStat/manage");
    }



    //ajax call
    function get_live_delivery_stat_data($start = 0){

        $getData = $this->input->post('getData');
        $perPage = 20;
        $wantToDataRecords = TRUE;
        $liveDeliveryData = $this->mdl_live_delivery->getLiveDeliveryStatData($getData,$start,$perPage,$wantToDataRecords);
        $data = array();
        $data['listArr'] = $liveDeliveryData['filteredData'];
        $data['start'] = $start;

        $this->load->view('liveDeliveryStat/live_delivery_partial_table', $data);

    }

    function getDataSourceType(){
        $apikey = $this->input->post('apikey');
        if(!empty($apikey)) {
            $condition = array('apikey' => $apikey);
            $is_single = true;
            $getLiveDelivery = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array('liveDeliveryId' => 'asc')),'apikey,dataSourceType');

            echo json_encode($getLiveDelivery);
        }
        
    }

}