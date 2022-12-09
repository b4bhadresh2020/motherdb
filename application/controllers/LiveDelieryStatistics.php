<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LiveDelieryStatistics extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!is_logged()) {
            redirect(base_url());
        } else if (is_logged() && !is_admin()) {
            redirect("mailUnsubscribe");
        }
    }

    public function chart()
    {
        $data = array();

        //get all apikey 
        $condition = array();
        $is_single = FALSE;
        $getLiveDeliveryAllApiKeys = GetAllRecord(LIVE_DELIVERY, $condition, $is_single, array(), array(), array(array('liveDeliveryId' => 'desc')), 'liveDeliveryId ,apikey,groupName,keyword,mailProvider');

        $data['apikeys'] = $getLiveDeliveryAllApiKeys;
        $data['load_page'] = 'liveDeliveryStatistics';
        $data['headerTitle'] = "Live Delivery Statistics";
        $data["curTemplateName"] = "LiveDelieryStatistics/chart";
        $this->load->view('commonTemplates/templateLayout', $data);
    }

    function getMailProviderData()
    {
        $apikey = $this->input->post('apikey');
        if (!empty($this->input->post('deliveryDate'))) {
            $deliveryDate = $this->input->post('deliveryDate');
        } else {
            $deliveryDate = date('Y-m-d');
        }

        $data = array();
        // fetch mail provider data from providers table
        $providerCondition   = array('apikey' => $apikey);
        $is_single           = true;
        $providerData        = GetAllRecord(LIVE_DELIVERY, $providerCondition, $is_single, [], [], [], 'mailProvider,delay');

        // get provider name
        $mailProviders = json_decode($providerData['mailProvider']);
        if (($key = array_search("egoi", $mailProviders)) !== false) {
            unset($mailProviders[$key]);
        }
        $providerIds = implode(',', $mailProviders);
        if (!empty($providerIds)) {
            $data['providerDetail'] = $this->db->select('id,listname')->where('id IN(' . $providerIds . ')')->get('providers')->result_array();
        }
        // get live delivery data
        $userDelays = json_decode($providerData['delay'], true);

        $live_delivery = [];
        if (!empty($userDelays)) {
            foreach ($userDelays as $kud => $userDelay) {
                if ($userDelay == 0) {
                    $total_send = $this->db->select('count(l.liveDeliveryDataId) AS total')
                        ->from('live_delivery_data As l')
                        ->where('DATE_FORMAT(l.createdDate,"%Y-%m-%d")', $deliveryDate)
                        ->where('l.apikey', $apikey)
                        ->get()->row_array();

                    $live_delivery[$kud]['queue_record'] = $total_send['total'];
                    $live_delivery[$kud]['send_record'] = $total_send['total'];
                } else {
                    $aweberList = getProviderList(AWEBER);
                    $aweberListIds = array_column($aweberList, 'id');

                    $transmitviaList = getProviderList(TRANSMITVIA);
                    $transmitviaListIds = array_column($transmitviaList, 'id');

                    $constantContactList = getProviderList(CONSTANTCONTACT);
                    $constantContactListIds = array_column($constantContactList, 'id');

                    $ongageList = getProviderList(ONGAGE);
                    $ongageListIds = array_column($ongageList, 'id');

                    $sendgridList = getProviderList(SENDGRID);
                    $sendgridListIds = array_column($sendgridList, 'id');

                    $sendInBlueList = getProviderList(SENDINBLUE);
                    $sendInBlueListIds = array_column($sendInBlueList, 'id');

                    // Total queue data 
                    $this->db->select('count(l.liveDeliveryDataId) AS total');
                    $this->db->from('live_delivery_data As l');
                    if (in_array($kud, $aweberListIds)) {
                        $this->db->join('aweber_delay_user_data AS ad', 'ad.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $transmitviaListIds)) {
                        $this->db->join('transmitvia_delay_user_data AS td', 'td.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $constantContactListIds)) {
                        $this->db->join('contact_delay_user_data AS cd', 'cd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $ongageListIds)) {
                        $this->db->join('ongage_delay_user_data AS od', 'od.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $sendgridListIds)) {
                        $this->db->join('sendgrid_delay_user_data AS sgd', 'sgd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $sendInBlueListIds)) {
                        $this->db->join('sendinblue_delay_user_data AS sbd', 'sbd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    }

                    $this->db->where('l.apikey', $apikey);
                    $this->db->where('DATE_FORMAT(l.createdDate,"%Y-%m-%d")', $deliveryDate);
                    if (in_array($kud, $aweberListIds)) {
                        $this->db->where('ad.providerId', $kud);
                    } else if (in_array($kud, $transmitviaListIds)) {
                        $this->db->where('td.providerId', $kud);
                    } else if (in_array($kud, $constantContactListIds)) {
                        $this->db->where('cd.providerId', $kud);
                    } else if (in_array($kud, $ongageListIds)) {
                        $this->db->where('od.providerId', $kud);
                    } else if (in_array($kud, $sendgridListIds)) {
                        $this->db->where('sgd.providerId', $kud);
                    } else if (in_array($kud, $sendInBlueListIds)) {
                        $this->db->where('sbd.providerId', $kud);
                    }
                    $total_queue = $this->db->get()->row_array();

                    // Total send data 
                    $this->db->select('count(l.liveDeliveryDataId) AS total');
                    $this->db->from('live_delivery_data As l');
                    if (in_array($kud, $aweberListIds)) {
                        $this->db->join('aweber_delay_user_data AS ad', 'ad.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $transmitviaListIds)) {
                        $this->db->join('transmitvia_delay_user_data AS td', 'td.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $constantContactListIds)) {
                        $this->db->join('contact_delay_user_data AS cd', 'cd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $ongageListIds)) {
                        $this->db->join('ongage_delay_user_data AS od', 'od.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $sendgridListIds)) {
                        $this->db->join('sendgrid_delay_user_data AS sgd', 'sgd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    } else if (in_array($kud, $sendInBlueListIds)) {
                        $this->db->join('sendinblue_delay_user_data AS sbd', 'sbd.liveDeliveryDataId = l.liveDeliveryDataId', 'left');
                    }

                    $this->db->where('l.apikey', $apikey);
                    $this->db->where('DATE_FORMAT(l.createdDate,"%Y-%m-%d")', $deliveryDate);
                    if (in_array($kud, $aweberListIds)) {
                        $this->db->where('ad.providerId', $kud);
                        $this->db->where('ad.status', 1);
                    } else if (in_array($kud, $transmitviaListIds)) {
                        $this->db->where('td.providerId', $kud);
                        $this->db->where('td.status', 1);
                    } else if (in_array($kud, $constantContactListIds)) {
                        $this->db->where('cd.providerId', $kud);
                        $this->db->where('cd.status', 1);
                    } else if (in_array($kud, $ongageListIds)) {
                        $this->db->where('od.providerId', $kud);
                        $this->db->where('od.status', 1);
                    } else if (in_array($kud, $sendgridListIds)) {
                        $this->db->where('sgd.providerId', $kud);
                        $this->db->where('sgd.status', 1);
                    } else if (in_array($kud, $sendInBlueListIds)) {
                        $this->db->where('sbd.providerId', $kud);
                        $this->db->where('sbd.status', 1);
                    }
                    $total_send = $this->db->get()->row_array();

                    $live_delivery[$kud]['queue_record'] = $total_queue['total'];
                    $live_delivery[$kud]['send_record'] = $total_send['total'];
                }
            }
        }
        $data['live_delivery'] = $live_delivery;
        echo json_encode($data);
    }
}
