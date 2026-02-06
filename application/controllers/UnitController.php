<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UnitController extends CI_Controller {

    public $tabel = 'unit';

    public function __construct() {
        parent::__construct();
        $this->load->model('UnitModel', 'um');
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
        $id_title = $this->input->get('id_title');
        $q        = $this->um->getTitle($id_title);
        echo json_encode($q->result());
    }

    public function showdttitle() {
        echo $this->um->dtshowtitle();
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
    public function deleteunit() {
        $id_unit = $this->input->get('id_unit');
        if ($id_unit != '') {
            $id_unit = explode(',', $id_unit);
            $this->um->deUnit($id_unit);
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

        $in = $this->um->inTitle();
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
            'unitName' => $this->input->post('unitName'),
        ];


        echo json_encode($this->um->upTitle($object, $id_title));
    }

    public function getSubCategory() {
        echo json_encode($this->scm->getSubCategory()->row());
    }

}

/* End of file CategoryController.php */
