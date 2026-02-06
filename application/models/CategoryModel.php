<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryModel extends CI_Model {

    public $tabel = 'category';

    public function getCategoryName($id = '', $q = '', $obj = '') {
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

    public function groupInboxTehnicalCco($status = '') {
        if ($status == '') {
            $status = $this->input->get('groupInboxTehnicalCco');
        }

        if ($status == "N") {
            $q = 'NO';
        } elseif ($status == "Y") {
            $q = 'YES';
        }

        return $q;
    }

    public function groupInboxTehnicalVas($status = '') {
        if ($status == '') {
            $status = $this->input->get('groupInboxTehnicalVas');
        }

        if ($status == "N") {
            $q = 'NO';
        } elseif ($status == "Y") {
            $q = 'YES';
        }

        return $q;
    }

    public function statusActive($status = '') {
        if ($status == '') {
            $status = $this->input->get('statusActive');
        }

        if ($status == "N") {
            $q = 'No Active';
        } elseif ($status == "Y") {
            $q = "Active";
        }

        return $q;
    }

    public function dtshowcategory() {
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'categoryId', 'categoryName', 'categoryType', 'groupInboxTehnicalCco', 'groupInboxTehnicalVas', 'statusActive');
        // Set searchable column fields
        $CI->dt->column_search = array('categoryId', 'categoryName', 'categoryType', 'groupInboxTehnicalCco', 'groupInboxTehnicalVas', 'statusActive');
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.categoryId' => 'DESC');

        // $condition =
        //     [
        //         ['where', $this->tabel . '.aktif', '1']
        //     ];
        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->categoryId . ')" class="oke" type="checkbox" name="categoryId[]" value="' . $dt->categoryId . '">',
                // '<a href="' . site_url("Project_List/charter?id_pc=" . $dt->id) . '" > ' . $dt->task . '</a>',
                $dt->categoryName,
                $dt->categoryType,
                $this->groupInboxTehnicalCco($dt->groupInboxTehnicalCco),
                $this->groupInboxTehnicalVas($dt->groupInboxTehnicalVas),
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

    public function de($categoryId ='') {
        // $categoryId = implode(",", $categoryId);

        $p = $this->db->query("DELETE  FROM category WHERE categoryId='$categoryId'");
        return true;
    }

    public function inCategory($object = '') {

        if ($object == '') {

            $object = [
                'categoryName'          => $this->input->post('categoryName'),
                'categoryType'          => $this->input->post('categoryType'),
                'groupInboxTehnicalCco' => $this->input->post('groupInboxTehnicalCco'),
                'groupInboxTehnicalVas' => $this->input->post('groupInboxTehnicalVas'),
                'statusActive'          => $this->input->post('statusActive'),
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

    public function upCategory($obj = '', $categoryId = '', $based_on = '') {
        $log = '';

        if ($categoryId != '') {
            $based_on = ['categoryId' => $categoryId];
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

    public function getCategory($categoryId = '') {
        if ($categoryId == '') {
            $categoryId = $this->input->get('categoryId');
        }

        $q = $this->db->get_where($this->tabel, ['categoryId' => $categoryId]);
        return $q;
    }

    public function getCategoryActive() {
        $q = $this->db->get_where($this->tabel, ['statusActive' => 'Y']);
        return $q;
    }

}

/* End of file CategoryModel.php */
