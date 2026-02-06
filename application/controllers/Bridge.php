<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bridge extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function addLogPurchaseSosmed($msisdn, $refil, $sourceChannel) {
        $data            = array();
        $data['msisdn']  = $msisdn;
        $data['refill']  = $refil;
        $data['channel'] = $sourceChannel;
        $data['tgl']     = date('Y-m-d H:i:s');

        $this->db->insert('activation_log_sosmed', $data);

        redirect('/');
    }

}
