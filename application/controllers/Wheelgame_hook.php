<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wheelgame_hook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        
    }
        
    function index(){

        $data = $this->input->post();

        $wheelgameLead = array(
            "name" => $data['name'],
            "emailId" => $data['email'],
            "phone" => $data['phone'],
        );
        $leadResponseId = ManageData(WHEELGAME_LEAD_DATA,[],$wheelgameLead,TRUE);

        // SEND DATA TO INTEGROMAT

        // Create a Guzzle client
        $client = new GuzzleHttp\Client();
        $subscriberUrl = "https://hook.integromat.com/76aaw9k2cibon6ny126b7yvsris6lhns";
        $body = $client->post($subscriberUrl, [
            'form_params' => $wheelgameLead, 
        ]); 
        $response =  $body->getBody();
        if("Accepted" == $response){
            ManageData(WHEELGAME_LEAD_DATA,['id' => $leadResponseId],['response' => $response],FALSE);
            echo json_encode(['status' => "Accepted"]);
        }else{
            ManageData(WHEELGAME_LEAD_DATA,['id' => $leadResponseId],['response' => 'Failed'],FALSE);
            echo json_encode(['status' => "success"]);
        }
    }      
    
}
