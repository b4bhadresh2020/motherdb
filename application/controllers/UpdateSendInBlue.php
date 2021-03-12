<?php

/**
 * 
 */
class UpdateSendInBlue extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('mdl_sendinblue_update');
    }

    public function index() {
        echo "process begin <br>";
        //get file data
        $condition = array(
            "sendinblueno != " => null
        );
        $is_single = FALSE;
        $liveDeliveryData = GetAllRecord(LIVE_DELIVERY_DATA,$condition,$is_single,array(),array(),array(),"emailId,gender,phone"); 
        foreach ($liveDeliveryData as $key => $data) {
            $response = $this->mdl_sendinblue_update->UpdateSendInBlueSubscriber($data);
        }       
        echo "process completed";
    }
}