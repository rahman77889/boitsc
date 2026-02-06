<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UserMonitoring extends CI_Controller {

    private $path = 'page/user_monitoring/';

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

    public function Recordings() {
        $d = [
            'title'    => "User Monitoring - Recodings :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'recordings'
        ];
        $this->load->view('page/_main', $d);
    }

    public function call_monitoring() {
        $d = [
            'title'    => "User Monitoring - Call Monitoring :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'call_monitoring'
        ];
        $this->load->view('page/_main', $d);
    }

}
