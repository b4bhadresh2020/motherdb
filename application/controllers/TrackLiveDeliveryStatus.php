<?php

/**
 *
 */
class TrackLiveDeliveryStatus extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $condition = array("isInActive" => 0);
        $liveDeliveryData = GetAllRecord(LIVE_DELIVERY,$condition,false,[],[],[],'apikey');

        foreach ($liveDeliveryData as $liveDelievery) {
            
            $status = 0;
            $latestLiveDelivery = GetAllRecord(LIVE_DELIVERY_DATA,array("apikey" => $liveDelievery['apikey']),true,[],[],array(array("createdDate" => "desc")),'apikey,createdDate');
            
            if(isset($latestLiveDelivery['createdDate'])){

                $todayDate = date("Y-m-d");
                $yesterdayDate = date('Y-m-d',strtotime("-1 days"));
                $lastDeliveryDate = date("Y-m-d",strtotime($latestLiveDelivery['createdDate']));

                if($lastDeliveryDate == $todayDate){
                    $status = 1;
                }else if($lastDeliveryDate == $yesterdayDate){
                    $status = 2;
                }

                ManageData(LIVE_DELIVERY,array("apikey" => $liveDelievery['apikey']),array("live_status" => $status),false);
            }
        }
    }
}
