<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

    private $path = 'page/faq/';

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
        $this->load->model('FaqModel', 'fm');
        $this->load->model('TitleModel', 'tm');
    }

    public function index() {
        $d = [
            'title'    => "Faq :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'faq'
        ];
        $this->load->view('page/_main', $d);
    }

    public function create_faq() {
        $d = [
            'title'    => "Create Faq :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'create'
        ];
        $this->load->view('page/_main', $d);
    }

    public function dtshowcreatefaq() {
        echo $this->fm->dtshowcreatefaq();
    }

    public function dtshowfaq() {
        echo $this->fm->dtshowfaq();
    }

    public function deletefaq() {
        $faqId = $this->input->get('faqId');
        if ($faqId != '') {
            $faqId = explode(',', $faqId);
            $this->fm->de($faqId);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    function do_upload() {
        $faqFile = '';
        $ext     = strtolower(pathinfo($_FILES['faqFile']['name'], PATHINFO_EXTENSION));

        if (isset($_FILES['faqFile']['tmp_name']) && ($ext == 'pdf' || $ext == 'jpg')) {
            $faqFile = uniqid() . '.'.$ext;
            if (!move_uploaded_file($_FILES['faqFile']['tmp_name'], 'pdf/' . $faqFile)) {
                $faqFile = '';
            }
        }

        $config['upload_path']   = "./video";
        $config['allowed_types'] = 'mp4';
        $config['encrypt_name']  = TRUE;

        $videoFile = '';
        $ext       = strtolower(pathinfo($_FILES['videoFile']['name'], PATHINFO_EXTENSION));

        if (isset($_FILES['videoFile']['tmp_name']) && $ext == 'mp4') {
            $videoFile = uniqid() . '.mp4';
            if (!move_uploaded_file($_FILES['videoFile']['tmp_name'], 'video/' . $videoFile)) {
                $videoFile = '';

                echo 'error on : ' . $_FILES["videoFile"]["error"];
                exit();
            }
        }

        $categoryId    = $this->input->post('categoryId');
        $subCategoryId = $this->input->post('subCategoryId');
        $id_title      = $this->input->post('id_title');
        $videoUrl      = $this->input->post('videoUrl');
        $status        = $this->input->post('status');


        $result = $this->fm->simpan_faq($categoryId, $subCategoryId, $id_title, $faqFile, $status, $videoFile, $videoUrl);
        echo json_encode($result);
    }

    public function getTitle() {
        $q = $this->tm->getTitle();
        echo json_encode($q->result());
    }

}
