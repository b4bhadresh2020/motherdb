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

                $timeDiffInMinute = round((time() - strtotime($latestLiveDelivery['createdDate'])) / 60);
                if($timeDiffInMinute < 30){
                    $status = 1;
                }else if($timeDiffInMinute > 30 && $timeDiffInMinute < 720){
                    $status = 2;
                }

                ManageData(LIVE_DELIVERY,array("apikey" => $liveDelievery['apikey']),array("live_status" => $status),false);
            }
        }
    }
}
