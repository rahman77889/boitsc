<?php 


defined('BASEPATH') OR exit('No direct script access allowed');

class TitleModel extends CI_Model {

      public $tabel = 'title';

       public function getTitle($id_title = '', $q = '', $obj = '') {
        if ($id_title != '') {
            $obj = ['id_title' => $id_title];
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

    public function dtshowtitle() {
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'name_title');
        // Set searchable column fields
        $CI->dt->column_search = array('name_title');
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.id_title' => 'DESC');

        // $condition = [
        //     ['join', 'category c', $this->tabel . '.categoryId=c.categoryId', 'inner']
        // ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->id_title . ')" class="oke" type="checkbox" name="id_title[]" value="' . $dt->id_title . '">',
                $i,
                $dt->name_title,
                // $dt->sla,
                // $dt->if_sla,
                // $dt->sub_category_type,
                // $dt->escalation,
                // $dt->categoryName,
                // $this->statusActive($dt->statusActive),
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
   
    public function inTitle($object = '') {

        if ($object == '') {

            $object = [
                'name_title'       => $this->input->post('name_title'),
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



     public function upTitle($obj = '', $id_title = '', $based_on = '') {
        $log = '';

        if ($id_title != '') {
            $based_on = ['id_title' => $id_title];
        }

        $q = $this->db->update($this->tabel, $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Title ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function edit($id_title) {

        $query = $this->db->where("id_title", $id_title)
                ->get("title");

        if ($query) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function de($id_title) {
        $id_title = implode(",", $id_title);

        $p = $this->db->query("DELETE  FROM title WHERE id_title IN ($id_title)");
        return true;
    }
}

/* End of file TitleModel.php */
