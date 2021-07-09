<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_webhook_unsubscribe extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }

    public function getUnsubscribeSettings() {
        $condition       = array(
            'main_provider !=' => 11
        );
        $is_single       = false;
        $UnsubscribeSettingsData    = GetAllRecord(WEBHOOK_UNSUBSCRIBE_SETTINGS, $condition, $is_single,[],[],[]);
        return $UnsubscribeSettingsData;        
    }
}