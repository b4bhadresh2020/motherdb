<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LiveDeliveryStat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());

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

        $data['headerTitle'] = "Live Delivery Stat";
        $data['load_page'] = 'liveDeliveryStat';
        $data["curTemplateName"] = "liveDeliveryStat/list";
        $data['start'] = $start;
        //pre($data);die;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */


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

}