<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LiveDeliveryUndefinedApiKeyStat extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!is_logged())
            redirect(base_url());

        $this->load->model('mdl_live_undefined_api_key_delivery');
    }

    /*
     *  list code starts here
     */

    function manage($start = 0) {

        $data = array();

        if (@$this->input->get('reset')) {
            $_GET = array();
        }

        $perPage = 50;
        $returnArr = $this->mdl_live_undefined_api_key_delivery->getLiveDeliveryUndefinedApiKeyStatData($_GET,$start,$perPage);
        $dataCount = $returnArr['filteredDataCount'];

        $data = pagiationData('liveDeliveryUndefinedApiKeyStat/manage/', $dataCount, $start, 3, $perPage,TRUE);
        $data['rejectDetailCountsArr'] = $returnArr['rejectDetailCountsArr'];
        $data['totalRejectCount'] = $dataCount;

        $data['headerTitle'] = "Live Delivery Undefined Api Key Stat";
        $data['load_page'] = 'liveDeliveryUndefinedApiKeyStat';
        $data["curTemplateName"] = "liveDeliveryUndefinedApiKeyStat/list";
        $data['start'] = $start;
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    /*
     *  list code ends here
     */


     /*
     *  delete code starts here
     */

    function delete($liveDeliveryUndefinedApiKeyDataId = 0) {
        $this->db->where("liveDeliveryUndefinedApiKeyDataId", $liveDeliveryUndefinedApiKeyDataId);
        $this->db->delete(LIVE_DELIVERY_UNDEFINED_KEY_DATA);
        redirect("liveDeliveryUndefinedApiKeyStat/manage");
    }

}