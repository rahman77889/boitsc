<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SmartCall extends CI_Controller {

    private $path = 'page/smartcall/';

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('privilege') == '') {
            redirect('Login');
        } else {
            $cek_session   = $this->db->select('session_id, last_activity')->from('user')->where('id', $this->session->userdata('id'))->get()->row();
            $last_activity = strtotime($cek_session->last_activity);

            if ($cek_session->session_id != session_id() || time() - $last_activity > 15 * 60) {//force logout jika beda session id atau lebih dari 10 menit
                $this->session->sess_destroy();
                $this->session->unset_userdata('id');
                $this->session->unset_userdata('level');

                redirect('Login');
            } else {
                $update_last_activity                  = array();
                $update_last_activity['last_activity'] = date('Y-m-d H:i:s');

                $this->db->where('id', $this->session->userdata('id'));
                $this->db->update('user', $update_last_activity);
            }
        }
    }

    public function call_number() {
        $listHistory = $this->db->select('msisdn, call_start as tgl')->from('call_log_outgoing')->where('id_user', $this->session->userdata('id'))->order_by('tgl', 'asc')->limit(30)->get()->result();

        $d = [
            'title'    => "User Monitoring - Recodings :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'call_number',
            'history'  => $listHistory
        ];
        $this->load->view('page/_main', $d);
    }

    public function auto_call() {
        $listHistory    = $this->db->select('msisdn, call_start as tgl')->from('call_log_outgoing')->where('id_user', $this->session->userdata('id'))->order_by('tgl', 'desc')->limit(30)->get()->result();
        $listHistoryIVR = $this->db->select('msisdn,lang,status')->from('autocall_ivr')->where('id_user', $this->session->userdata('id'))->order_by('id', 'desc')->limit(30)->get()->result();

        $d = [
            'title'      => "User Monitoring - Call Monitoring :: Telkomcel Helpdesk",
            'linkView'   => $this->path . 'auto_call',
            'history'    => $listHistory,
            'historyIVR' => $listHistoryIVR,
        ];
        $this->load->view('page/_main', $d);
    }

    public function auto_call_number() {
        $number = $this->input->post('number');
        $tipe   = $this->input->post('tipe');
        $number = explode("\n", $number);

        $unique_id = uniqid() . uniqid();

        foreach ($number as $num) {
            if ($num) {
                $data              = array();
                $data['id_user']   = $this->session->userdata('id');
                $data['msisdn']    = $num;
                $data['tgl']       = date('Y-m-d H:i:s');
                $data['lang']      = $this->input->post('lang');
                $data['status']    = 'waiting';
                $data['unique_id'] = $unique_id;
                $data['tipe']      = $tipe;
            }

            $this->db->insert('autocall_ivr', $data);
        }

        header('Content-Type:text/json');
        echo json_encode(array('success' => true, 'unique_id' => $unique_id));
    }

    public function result_ivr($id) {
        $list_result = $this->db->select('msisdn,lang,status,result')->from('autocall_ivr')->where('unique_id', $id)->get()->result_array();

        header('Content-Type:text/json');
        echo json_encode(array('success' => true, 'list' => $list_result, 'count' => count($list_result)));
    }

    public function cancel_ivr($id) {
        $ivr_update = array(
            'status' => 'cancel',
            'result' => 'cancel'
        );

        $this->db->where('unique_id', $id);
        $this->db->update('autocall_ivr', $ivr_update);

        header('Content-Type:text/json');
        echo json_encode(array('success' => true));
    }

}
