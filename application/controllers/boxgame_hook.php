<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Boxgame_hook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        
    }
        
    function index(){

        $data = $this->input->post();

        $boxgameLead = array(
            "name" => $data['name'],
            "emailId" => $data['email'],
            "phone" => $data['phone'],
        );
        $leadResponseId = ManageData(BOXGAME_LEAD_DATA,[],$boxgameLead,TRUE);

        // SEND DATA TO INTEGROMAT

        // Create a Guzzle client
        $client = new GuzzleHttp\Client();
        $subscriberUrl = "https://hook.integromat.com/jsp233c59lpscx22vr4jclf77rayk7s3";
        $body = $client->post($subscriberUrl, [
            'form_params' => $boxgameLead, 
        ]); 
        $response =  $body->getBody();
        if("Accepted" == $response){
            ManageData(BOXGAME_LEAD_DATA,['id' => $leadResponseId],['response' => $response],FALSE);
            echo json_encode(['status' => $response]);
        }else{
            ManageData(BOXGAME_LEAD_DATA,['id' => $leadResponseId],['response' => 'Failed'],FALSE);
            echo json_encode(['status' => "success"]);
        }
    }      
    
}
