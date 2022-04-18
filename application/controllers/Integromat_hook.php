<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Integromat_hook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        
    }
        
    function index(){

        $logPath    = FCPATH."log/integromat/";
        if(!file_exists($logPath)){
                mkdir($logPath);
        }
        $fileName   = date("Ymd")."_log.txt"; 
        $logFile    = fopen($logPath.$fileName,"a");
        $logData    = json_encode($this->input->post())."\n";
        fwrite($logFile,$logData);
        fclose($logFile);
    }      
    
}
