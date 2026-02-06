<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AccoutInformationModel extends CI_Model {

    public $tabel = 'user';

    public function getUser() {
        $query = $this->db->query("SELECT * FROM user")->result();
        return $query;
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
        $CI->dt->column_order  = array(null, 'id', 'fullName', 'userManagerId', 'userSpvId', 'unitId', 'locationId', 'username', 'password', 'statusLogin', 'ip_address', 'active', 'level', 'privilegeId', 'created_date', 'created_by', 'open_password');
        // Set searchable column fields
        $CI->dt->column_search = array('id', 'fullName', 'userManagerId', 'userSpvId', 'unitId', 'locationId', 'username', 'password', 'statusLogin', 'ip_address', 'active', 'level', 'privilegeId', 'created_date', 'created_by', 'open_password');
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
                ' <input onclick="hapus(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->fullName,
                $dt->username,
                $dt->categoryName,
                $dt->categoryType,
                $dt->privilegeId,
                $dt->userSpvId,
                $dt->userManagerId,
                $dt->locationId,
                $dt->statusLogin
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

    public function de($categoryId) {
        $categoryId = implode(",", $categoryId);

        $p = $this->db->query("DELETE  FROM category WHERE categoryId IN ($categoryId)");
        return true;
    }

    public function edit($categoryId) {

        $query = $this->db->where("categoryId", $categoryId)
                ->get("category");

        if ($query) {
            return $query->row();
        } else {
            return false;
        }
    }

}

/* End of file CategoryModel.php */
