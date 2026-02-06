<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Systems extends CI_Controller {

    private $path = 'page/system/';

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

    public function change_password() {
        $id = $this->input->get('id');

        $query = $this->db->query("SELECT * FROM user WHERE id='$id'")->row();

        $d = [
            'title'    => "System - Change Password :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'change_password',
            'query'    => $query,
        ];
        $this->load->view('page/_main', $d);
    }

    public function upPassword() {

        $id            = $this->session->userdata('id');
        $password      = $this->input->post('password');
        $password_dua  = $this->input->post('password_dua');
        $open_password = $this->input->post('open_password');
        $query_dua     = $this->db->query("SELECT * FROM user WHERE password='" . md5($password) . "' AND id='$id'");

        if ($query_dua->num_rows() > 0) {

            if ($open_password == $password_dua) {
                $data = [
                    'password'      => md5($this->input->post('password_dua')),
                    'open_password' => $open_password,
                ];
                $this->db->update('user', $data, ['id' => $id]);
                echo "Success Update Password!";
            }
        } else {
            echo "Password not valid";
        }
    }

    public function manually_report() {
        $d = [
            'title'    => "System - Manualy Report :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'manually_report'
        ];
        $this->load->view('page/_main', $d);
    }

    public function broadcast_msg() {
        $d = [
            'title'    => "System - Broadcast Messages :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'broadcast_msg'
        ];
        $this->load->view('page/_main', $d);
    }

}
