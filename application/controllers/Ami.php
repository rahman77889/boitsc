<?php

use PAMI\Message\Event\DialEvent;

defined('BASEPATH') OR exit('No direct script access allowed');

class Ami extends CI_Controller {

    private $conn;

    public function __construct() {
        parent::__construct();

        error_reporting(E_ALL);
        require_once "vendor/autoload.php";
        error_reporting(E_ALL);

//        echo '<pre>';
    }

    public function test() {
        $options = array(
            'host'            => '10.140.90.7',
            'scheme'          => 'tcp://',
            'port'            => 5038,
            'username'        => 'amitest',
            'secret'          => '12345',
            'connect_timeout' => 10,
            'read_timeout'    => 10
        );
        $client  = new \PAMI\Client\Impl\ClientImpl($options);

        $client->registerEventListener(function ($event) {
            print_r($event);
        });

        $client->open();

        $client->process();

        $client->close();

// Register a specific method of an object for event listening
//        $client->registerEventListener(array($listener, 'handle'));
//
//// Register an IEventListener:
//        $client->registerEventListener($listener);
//
//        $client->registerEventListener(
//                array($listener, 'handleDialStart'), function ($event) {
//            return $event instanceof DialEvent && $event->getSubEvent() == 'Begin';
//        });
    }

    public function survei_value() {
//        $msisdn       = $this->input->get('msisdn');
//        $agent_no     = $this->input->get('agent_no');
        $survei_no    = $this->input->get('survei_no');
        $survei_value = $this->input->get('survei_value');

//        if (substr($msisdn, 0, 3) != '670') {
//            $msisdn = '670' . $msisdn;
//        }

        $last_queue = $this->db->select('id')->from('complain')->where('survei_status', 'O')->order_by('id', 'asc')->limit(1)->get()->row_array();

        $ret           = array();
        $ret['status'] = 'failed';

        if (isset($last_queue['id'])) {//found
            $update_survei                         = array();
            $update_survei['rating_' . $survei_no] = $survei_value;

            if ($survei_no == '3') {
                $update_survei['survei_status'] = 'Y';
            }

            $q = $this->db->where('id', $last_queue['id'])->update('complain', $update_survei);

            if ($q) {
                $ret['status'] = 'success';
            }
        }

        header('content-type:application/json');
        echo json_encode($ret);
        exit();
    }

    public function get_queue_last_call() {
        $last_queue = $this->db->select('survei,mdnProblem,id')->from('complain')->where('survei_status', 'W')->order_by('id', 'asc')->limit(1)->get()->row_array();

        $ret           = array();
        $ret['status'] = 'failed';
        $ret['data']   = array();

        if (isset($last_queue['mdnProblem'])) {

            $updateSurvey                  = array();
            $updateSurvey['survei_status'] = 'O';

            $this->db->where('id', $last_queue['id'])->update('complain', $updateSurvey);

            $updateSurvey                  = array();
            $updateSurvey['survei_status'] = 'Y';

            $this->db->where('survei_status', 'O')->where('id!=\'' . $last_queue['id'] . '\'')->update('complain', $updateSurvey);

            $ret['status'] = 'success';
            $ret['data']   = $last_queue;
        }

        header('content-type:application/json');
        echo json_encode($ret);
        exit();
    }

    public function get_last_call() {
//        $last_call = $this->db->select('extend,msisdn,id')->from('call_log_incoming')->where('!isnull(call_answer)')->where('status', 'hangup')->where('is_surveyed', 'N')->where('exists (select 1 from complain where mdnProblem=right(call_log_incoming.msisdn,11)  and survei!=\'\')')->order_by('id', 'desc')->limit(1)->get()->row_array();
//
//        $ret           = array();
//        $ret['status'] = 'failed';
//        $ret['data']   = array();
//
//        if (isset($last_call['id'])) {
//
//            $updateSurvey                = array();
//            $updateSurvey['is_surveyed'] = 'Y';
//
//            $this->db->where('id', $last_call['id'])->update('call_log_incoming', $updateSurvey);
//
//            $ret['status'] = 'success';
//            $ret['data']   = $last_call;
//        }
        $last_queue = $this->db->select('mdnProblem,id')->from('complain')->where('survei_status', 'O')->order_by('id', 'asc')->limit(1)->get()->row_array();

        $ret           = array();
        $ret['status'] = 'failed';
        $ret['data']   = array();

        if (isset($last_queue['mdnProblem'])) {

            $ret['status'] = 'success';
            $ret['data']   = $last_queue;
        }

        header('content-type:application/json');
        echo json_encode($ret);
        exit();
    }

}
