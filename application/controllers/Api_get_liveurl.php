<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_get_liveurl extends CI_Controller
{
    public function __construct() {
        parent::__construct();

        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }

    public function index() {
        $country = $_GET['country'];
        $dataSourceType = $_GET['dataSourceType'];
        
        $condition = array('country' => $country, 'dataSourceType' => $dataSourceType);
        $is_single = true;
        $getLiveDeliveryData = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array('liveDeliveryId' => 'asc')),'apikey');

        echo json_encode(array('apikey' => $getLiveDeliveryData['apikey']));
    }
}
?>