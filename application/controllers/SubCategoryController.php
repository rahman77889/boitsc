<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SubCategoryController extends CI_Controller
{

    public $tabel = 'subcategory';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('SubCategoryModel', 'scm');
        if ($this->session->userdata('privilege') == '') {
            redirect('Login');
        } else {
            $cek_session   = $this->db->select('session_id, last_activity')->from('user')->where('id', $this->session->userdata('id'))->get()->row();
            $last_activity = strtotime($cek_session->last_activity);

            if ($cek_session->session_id != session_id() || time() - $last_activity > 15 * 60) { //force logout jika beda session id atau lebih dari 10 menit
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

    public function download()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=sub_category.csv');

        $output = fopen('php://output', 'w');

        ob_end_clean();

        $header_args = array('No', 'Sub Category Name', 'SLA ( m" )', 'If Needed Visit ? Y : SLA ( m" )', 'Sub Category Type', 'Category', 'Status Active');

        fputcsv($output, $header_args);

        $no = 1;

        $result = $this->db->select('*,(select c.categoryName from category c where c.categoryId=subcategory.categoryId) as categoryName')->from('subcategory')->order_by('subCategory', 'asc')->get()->result();

        foreach ($result as $r) {
            $data_item = array(
                $no++
            );

            $data_item[] = $r->subCategory;
            $data_item[] = $r->sla;
            $data_item[] = $r->if_sla;
            $data_item[] = $r->sub_category_type;
            $data_item[] = $r->categoryName;
            $data_item[] = $r->statusActive;

            fputcsv($output, $data_item);
        }
    }

    public function getSubCategoryName()
    {
        $q = $this->scm->getSubCategoryName();
        echo json_encode($q->result());
    }

    public function showdtsubcategory()
    {
        echo $this->scm->dtshowsubcategory();
    }

    // link edit
    public function editcategory($categoryId)
    {

        $data = array(
            'title' => 'UPDATE DATA USER',
            'edit'  => $this->lm->edit($categoryId)
        );
        $this->load->view('login/editlogin', $data);
    }

    //proses update

    public function prosesudpate()
    {
        $categoryId['categoryId'] = $this->input->post('categoryId');
        $data                     = array(
            'categoryId' => $this->input->post('categoryId'),
            'username'   => $this->input->post('username'),
            'no_telepon' => $this->input->post('no_telepon'),
        );
        $this->db->update('login', $data, $categoryId);
        redirect('LoginController/list_login');
    }

    public function editcategorymulti()
    {
        $id_user = $this->input->get('categoryId');
        $data    = array(
            'title' => 'UPDATE DATA USER',
            'edit'  => $this->lm->edit($id_user)
        );

        $this->load->view('login/editlogin', $data);
    }

    //  delete
    public function deletesubcategory()
    {
        $subCategoryId = $this->input->get('subCategoryId');
        if ($subCategoryId != '') {
            $subCategoryId = explode(',', $subCategoryId);
            $this->scm->de($subCategoryId);
        }

        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function getPrivilege()
    {
        $p = $this->p->getPrivilege();
        echo json_encode($p->result());
    }

    public function inSubCategory()
    {
        $log = [];

        $in = $this->scm->inSubCategory();
        if ($in) {
            $log = [
                'msg' => 'Success Add SubCategory!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add SubCategory!',
            ];
        }

        echo json_encode($log);
    }

    public function upSubCategory()
    {
        $subCategoryId = $this->input->post('subCategoryId');
        $object        = [
            'categoryId'        => $this->input->post('categoryId'),
            'subCategory'       => $this->input->post('subCategory'),
            'sla'               => $this->input->post('sla'),
            'if_sla'            => $this->input->post('if_sla'),
            'sub_category_type' => $this->input->post('sub_category_type'),
            // 'escalation'        => $this->input->post('escalation'),
            'statusActive'      => $this->input->post('statusActive'),
        ];


        echo json_encode($this->scm->upSubCategory($object, $subCategoryId));
    }

    public function getSubCategory()
    {
        echo json_encode($this->scm->getSubCategory()->row());
    }
}

/* End of file CategoryController.php */
