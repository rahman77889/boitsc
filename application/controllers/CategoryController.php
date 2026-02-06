<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CategoryController extends CI_Controller
{

    public $tabel = 'category';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
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
        $this->load->model('CategoryModel', 'cm');
    }

    public function download()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=category.csv');

        $output = fopen('php://output', 'w');

        ob_end_clean();

        $header_args = array('No', 'Category Name', 'Category Type', 'Group Inbox Tehnical CCO', 'Group Inbox Tehnical VAS', 'Status Active');

        fputcsv($output, $header_args);

        $no = 1;

        $result = $this->db->select('*')->from('category')->order_by('categoryName', 'asc')->get()->result();

        foreach ($result as $r) {
            $data_item = array(
                $no++
            );

            $data_item[] = $r->categoryName;
            $data_item[] = $r->categoryType;
            $data_item[] = $r->groupInboxTehnicalCco;
            $data_item[] = $r->groupInboxTehnicalVas;
            $data_item[] = $r->statusActive;

            fputcsv($output, $data_item);
        }
    }

    public function getCategoryName()
    {
        $q = $this->cm->getCategoryName();
        echo json_encode($q->result());
    }

    public function showdtcategory()
    {
        echo $this->cm->dtshowcategory();
    }

    //  delete
    public function deletecategory()
    {
        $categoryId = $this->input->get('categoryId');
        if ($categoryId != '') {
            // $categoryId = explode(',', $categoryId);
            $this->cm->de($categoryId);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
        // var_dump($categoryId);
        // echo $categoryId;
        // echo "hello";
        // $this->db->query("DELETE  FROM login WHERE categoryId IN ($categoryId)'");
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
        // $this->load->view('Settings/category');
    }

    #Delete Project List
    // public function deCategory()
    // {
    //     $categoryId = $_POST['categoryId'];
    //     if ($categoryId != '') {
    //         $this->cm->de($categoryId);
    //     } else {
    //     redirect($_SERVER['HTTP_REFERER']);
    //     }
    // }

    public function inCategory()
    {
        $log = [];

        $in = $this->cm->inCategory();
        if ($in) {
            $log = [
                'msg' => 'Success Add Category!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add Category!',
            ];
        }

        echo json_encode($log);
    }

    public function upCategory()
    {
        $categoryId = $this->input->post('categoryId');
        $object     = [
            'categoryName'          => $this->input->post('categoryName'),
            'categoryType'          => $this->input->post('categoryType'),
            'groupInboxTehnicalCco' => $this->input->post('groupInboxTehnicalCco'),
            'groupInboxTehnicalVas' => $this->input->post('groupInboxTehnicalVas'),
            'statusActive'          => $this->input->post('statusActive'),
        ];

        // var_dump($categoryId[0]);
        echo json_encode($this->cm->upCategory($object, $categoryId[0]));
    }

    public function getCategory()
    {
        echo json_encode($this->cm->getCategory()->row());
    }
}

/* End of file CategoryController.php */
