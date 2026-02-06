<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TitleController extends CI_Controller {

    public $tabel = 'subcategory';

    public function __construct() {
        parent::__construct();
        $this->load->model('TitleModel', 'tm');
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

    public function getTitle() {
        $q = $this->tm->getTitle();
        echo json_encode($q->result());
    }

    public function showdttitle() {
        echo $this->tm->dtshowtitle();
    }

    // link edit
    public function editcategory($categoryId) {

        $data = array(
            'title' => 'UPDATE DATA USER',
            'edit'  => $this->lm->edit($categoryId)
        );
        $this->load->view('login/editlogin', $data);
    }

    //proses update

    public function prosesudpate() {
        $categoryId['categoryId'] = $this->input->post('categoryId');
        $data                     = array(
            'categoryId' => $this->input->post('categoryId'),
            'username'   => $this->input->post('username'),
            'no_telepon' => $this->input->post('no_telepon'),
        );
        $this->db->update('login', $data, $categoryId);
        redirect('LoginController/list_login');
    }

    public function editTitle() {
        $id_title = $this->input->get('id_title');
        $data     = array(
            'title' => 'UPDATE DATA Title',
            'edit'  => $this->lm->edit($id_title)
        );

        $this->load->view('login/editlogin', $data);
    }

    //  delete
    public function deletetitle() {
        $id_title = $this->input->get('id_title');
        if ($id_title != '') {
            $id_title = explode(',', $id_title);
            $this->tm->de($id_title);
        }

        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function getPrivilege() {
        $p = $this->p->getPrivilege();
        echo json_encode($p->result());
    }

    public function inTitle() {
        $log = [];

        $in = $this->tm->inTitle();
        if ($in) {
            $log = [
                'msg' => 'Success Add Title!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add Title!',
            ];
        }

        echo json_encode($log);
    }

    public function upTitle() {
        $id_title = $this->input->post('id_title');
        $object   = [
            'name_title' => $this->input->post('name_title'),
        ];


        echo json_encode($this->tm->upTitle($object, $id_title));
    }

    public function getSubCategory() {
        echo json_encode($this->scm->getSubCategory()->row());
    }

}

/* End of file CategoryController.php */
