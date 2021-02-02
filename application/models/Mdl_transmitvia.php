<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_transmitvia extends CI_Model {

    public function __construct() {
        parent::__construct();

        // require the autoloader class if you haven't used composer to install the package
        require_once(APPPATH.'third_party/MailWizzApi/Autoloader.php');

        // register the autoloader if you haven't used composer to install the package
        MailWizzApi_Autoloader::register();
        
        // configuration object
        $config = new MailWizzApi_Config(array(
            'apiUrl'        => 'https://ema.transmitvia.com/api',
            'publicKey'     => '77765c14d9a61411887536d2347e692401cbc6a8',
            'privateKey'    => '2ca2a921656e29ff8a73744e4b1317177b5b9c71'
        ));

        // now inject the configuration and we are ready to make api calls
        MailWizzApi_Base::setConfig($config);

        // start UTC
        date_default_timezone_set('UTC');
    }

    
    function AddEmailToTransmitSubscriberList($getData,$transmitviaListId){
        $endpoint = new MailWizzApi_Endpoint_ListSubscribers();
        $response = $endpoint->create($transmitviaListId, array(
            'EMAIL'   => $getData['emailId'], // the confirmation email will be sent!!! Use valid email address
            'FNAME'   => $getData['firstName'],
            'LNAME'   => $getData['lastName'],
            'GENDER'   => strtolower($getData['gender'])
        ));

        // DISPLAY RESPONSE
        $responseData = $response->body;       
        
        if ($responseData["status"] == "success") {            
            $subscriber_id = $responseData["data"]["record"]["subscriber_uid"];            
            return array("result" => "success","data" => array("id" => $subscriber_id));
        } else {
            $message = isset($responseData["error"])?$responseData["error"]:"Bad Request or duplicate email Id";
            return array("result" => "error","error" => array("msg" => $message));
        }
    }
 
}