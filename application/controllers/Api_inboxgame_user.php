<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_inboxgame_user extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Accept: */*');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
    }

    public function index(){
        
    }
}