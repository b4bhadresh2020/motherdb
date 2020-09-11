<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

// require the autoloader class if you haven't used composer to install the package
require_once(APPPATH.'third_party/Ctct/autoload.php');

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\CustomField;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class Mdl_constantcontact extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function AddEmailToContactSubscriberList($getData,$contactListId){
       
        // fetch mail provider data from providers table
        $providerCondition   = array('id' => $contactListId);
        $is_single           = true;
        $providerData        = GetAllRecord(PROVIDERS, $providerCondition, $is_single);

        $listId = $providerData['code'];

        try {
            $cc = new ConstantContact(CONSTANT_CONTACT_APIKEY);

            // check to see if a contact with the email addess already exists in the account
            $response = $cc->getContactByEmail(CONSTANT_CONTACT_ACCESS_TOKEN, $getData['emailId']);
    
            // create a new contact if one does not exist
            if (empty($response->results)) {
                
                $contact = new Contact();
                $customField = new CustomField();
                
                $contact->addEmail($getData['emailId']);
                $contact->addList($listId);
                $contact->first_name = $getData['firstName'];
                $contact->last_name = $getData['lastName'];
                $contact->cell_phone = $getData['phone'];
                $customField->name = 'custom_field_1';
                $customField->value = $getData['gender'];
                $contact->addCustomField($customField);
                $returnContact = $cc->addContact(CONSTANT_CONTACT_ACCESS_TOKEN, $contact, true);
                
            } else {
                // update the existing contact if address already existed    
                $contact = $response->results[0];
                $contact->addList($listId);
                $contact->first_name = $getData['firstName'];
                $contact->last_name = $getData['lastName'];
                $contact->cell_phone = $getData['phone'];
                $returnContact = $cc->updateContact(CONSTANT_CONTACT_ACCESS_TOKEN, $contact, true);
            }
            
            return array("result" => "success","data" => array("id" => $returnContact->id));
    
            // catch any exceptions thrown during the process and print the errors to screen
        } catch (CtctException $ex) {
            return array("result" => "error","error" => array("msg" => "Bad Request"));
        }
    }
}