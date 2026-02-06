<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SubCategoryModel extends CI_Model
{

    public $tabel = 'subcategory';

    public function getSubCategoryName($id = '', $q = '', $obj = '')
    {
        if ($id != '') {
            $obj = ['id' => $id];
        }

        if ($obj != 0) {
            $q = $this->db->get_where($this->tabel, $obj);
        } else if ($q != '') {
            $q = $this->db->query($q);
        } else {
            $q = $this->db->get($this->tabel);
        }

        return $q;
    }

    public function escalation($status = '')
    {
        if ($status == '') {
            $status = $this->input->get('escalation');
        }

        if ($status == "N") {
            $q = 'NO';
        } elseif ($status == "Y") {
            $q = 'YES';
        }

        return $q;
    }

    public function statusActive($status = '')
    {
        if ($status == '') {
            $status = $this->input->get('statusActive');
        }

        if ($status == "N") {
            $q = 'No Active';
        } elseif ($status == "Y") {
            $q = 'Active';
        }

        return $q;
    }

    public function dtshowsubcategory()
    {
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'subCategoryId', 'subCategory', 'sla', 'if_sla', 'sub_category_type', 'escalation', 'statusActive');
        // Set searchable column fields
        $CI->dt->column_search = array('c.categoryName', 'c.categoryId', 'subCategoryId', 'subCategory', 'sla', 'if_sla', 'sub_category_type', 'escalation');
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*,c.categoryName,c.categoryId';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.subCategoryId' => 'DESC');

        $condition = [
            ['join', 'category c', $this->tabel . '.categoryId=c.categoryId', 'inner']
        ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->subCategoryId . ')" class="oke" type="checkbox" name="subCategoryId[]" value="' . $dt->subCategoryId . '">',
                $i,
                $dt->subCategory,
                $dt->sla,
                $dt->if_sla,
                $dt->sub_category_type,
                // $dt->escalation,
                $dt->categoryName,
                $this->statusActive($dt->statusActive),
            );
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->dt->countAll($condition),
            "recordsFiltered" => $this->dt->countFiltered($_POST, $condition),
            "data"            => $data,
        );

        // Output to JSON format
        return json_encode($output);
    }

    // funsgi delete user login

    public function de($subCategoryId)
    {
        $subCategoryId = implode(",", $subCategoryId);

        $p = $this->db->query("DELETE  FROM subcategory WHERE subCategoryId IN ($subCategoryId)");
        return true;
    }

    public function edit($categoryId)
    {

        $query = $this->db->where("categoryId", $categoryId)
            ->get("login");

        if ($query) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getPrivilege($id = '', $q = '', $obj = '')
    {
        if ($id != '') {
            $obj = ['id' => $id];
        }

        if ($obj != 0) {
            $q = $this->db->get_where($this->t, $obj);
        } else if ($q != '') {
            $q = $this->db->query($q);
        } else {
            $q = $this->db->get($this->t);
        }

        return $q;
    }

    public function inSubCategory($object = '')
    {

        if ($object == '') {

            $object = [
                'categoryId'        => $this->input->post('categoryId'),
                'subCategory'       => $this->input->post('subCategory'),
                'sla'               => $this->input->post('sla'),
                'if_sla'            => $this->input->post('if_sla'),
                'sub_category_type' => $this->input->post('sub_category_type'),
                'escalation'        => $this->input->post('escalation'),
                'statusActive'      => $this->input->post('statusActive'),
            ];
        }

        $q = $this->db->insert($this->tabel, $object);

        // Insert Log Actvitiy
        // $LM = &get_instance();
        // $LM->load->model('LogModel', 'lm');
        // $msgLog = "User : " . $this->session->userdata('username') . " -> Insert CDR";
        // $LM->lm->id_user = $this->session->userdata('id');
        // $LM->lm->inLogActivity($msgLog, json_encode($object));

        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public function upSubCategory($obj = '', $subCategoryId = '', $based_on = '')
    {
        $log = '';

        if ($subCategoryId != '') {
            $based_on = ['subCategoryId' => $subCategoryId];
        }

        $q = $this->db->update($this->tabel, $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Category ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function getSubCategory($subCategoryId = '')
    {
        if ($subCategoryId == '') {
            $subCategoryId = $this->input->get('subCategoryId');
        }

        $q = $this->db->get_where($this->tabel, ['subCategoryId' => $subCategoryId]);
        return $q;
    }

    public function getSubCategoryActive($categoryId)
    {
        $q = $this->db->get_where($this->tabel, ['categoryId' => $categoryId, 'statusActive' => 'Y']);

        return $q;
    }
}

/* End of file CategoryModel.php */
