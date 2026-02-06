<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

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

    private $path = 'page/settings/';

    public function category() {
        $d = [
            'title'    => "Settings - Category :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'category'
        ];
        $this->load->view('page/_main', $d);
    }

    public function subcategory() {
        $d = [
            'title'    => "Settings - Sub Category :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'subcategory'
        ];
        $this->load->view('page/_main', $d);
    }

    public function titlesetting() {
        $d = [
            'title'    => "Settings - Title Setting :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'titlesetting'
        ];
        $this->load->view('page/_main', $d);
    }

    public function unitsetting() {
        $d = [
            'title'    => "Settings - Unit Setting :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'unitsetting'
        ];
        $this->load->view('page/_main', $d);
    }

}
