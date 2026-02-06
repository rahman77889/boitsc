<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class UnitModel extends CI_Model {

    private $tabel = 'unitinfo';
    private $t     = 'unit';

    public function getTitle($id_title = '', $q = '', $obj = '') {
        if ($id_title != '') {
            $obj = ['id' => $id_title];
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

    public function dtshowtitle() {
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->t;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'unitName');
        // Set searchable column fields
        $CI->dt->column_search = array('unitName');
        // Set select column fields
        $CI->dt->select        = $this->t . '.*';
        // Set default order
        $CI->dt->order         = array($this->t . '.unitName' => 'ASC');

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
                ' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->unitName
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

    public function getUnit($id = '', $q = '', $obj = '') {
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

    public function cekStatus($status = '') {
        // Definisi
        if ($status == '') {
            $status = $this->input->get('status');
        }

        if ($status == 0) {
            $q = 'No Active';
        } elseif ($status == 1) {
            $q = 'Active';
        }
        return $q;
    }

    public function showdtunit() {
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'id', 'name', 'email', 'phone', 'unit_id', 'status', 'created_by', 'created_date');
        // Set searchable column fields
        $CI->dt->column_search = array($this->tabel . '.id', 'name', 'email', 'phone', 'unit_id', 'status', 'created_by', 'created_date', 'unitName');
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*,u.unitName';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.id' => 'DESC');

        $condition = [
            ['join', '`unit` u', $this->tabel . '.unit_id=u.id', 'inner']
        ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->name,
                $dt->email,
                $dt->phone,
                $dt->unitName,
                // $dt->status,
                $this->cekStatus($dt->status),
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

    public function de($id) {
        $id = implode(",", $id);

        $p = $this->db->query("DELETE  FROM unitinfo WHERE id IN ($id)");
        return true;
    }

    public function edit($id) {

        $query = $this->db->where("id", $id)
                ->get("unitinfo");

        if ($query) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function inUnitInformation($object = '') {

        if ($object == '') {

            $object = [
                'name'         => $this->input->post('name'),
                'email'        => $this->input->post('email'),
                'phone'        => $this->input->post('phone'),
                'unit_id'      => $this->input->post('unit'),
                'status'       => $this->input->post('status'),
                'created_date' => date('Y-m-d H:i:s'),
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

    public function upUnitInformation($obj = '', $id = '', $based_on = '') {
        $log = '';

        if ($id != '') {
            $based_on = ['id' => $id];
        }

        $q = $this->db->update($this->tabel, $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Unit Information ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function getUnitInformation($id = '') {
        if ($id == '') {
            $id = $this->input->get('id');
        }

        $q = $this->db->get_where($this->tabel, ['id' => $id]);
        return $q;
    }

    public function getUnitInfo($id = '') {
        if ($id == '') {
            $id = $this->input->get('id');
        }

        $q = $this->db->get_where($this->t, ['id' => $id]);
        return $q;
    }

    public function getUnitList() {
        $q = $this->db->get($this->t);
        return $q;
    }

    public function inTitle($object = '') {

        if ($object == '') {

            $object = [
                'unitName' => $this->input->post('unitName'),
            ];
        }

        $q = $this->db->insert($this->t, $object);

        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public function upTitle($obj = '', $id_title = '', $based_on = '') {
        $log = '';

        if ($id_title != '') {
            $based_on = ['id' => $id_title];
        }

        $q = $this->db->update($this->t, $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Unit Name ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function deUnit($id_title) {
        $id_title = implode(",", $id_title);

        $p = $this->db->query("DELETE  FROM unit WHERE id IN ($id_title)");
        return true;
    }

    public function showdtcounter() {
        // Definisi
        $condition = [];
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = 'queue';
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'id', 'nama_tempat', 'counter', 'nomor');
        // Set searchable column fields
        $CI->dt->column_search = array('id', 'counter', 'nomor');
        // Set select column fields
        $CI->dt->select        = '*, (select queue_setting.judul from queue_setting where queue_setting.id=queue.id_counter_setting) as nama_tempat';
        // Set default order
        $CI->dt->order         = array('counter' => 'ASC');

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->nama_tempat,
                $dt->counter,
                $dt->nomor
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

    public function deC($id) {
        $id = implode(",", $id);

        $p = $this->db->query("DELETE  FROM queue WHERE id IN ($id)");
        return true;
    }

    public function inCounter($object = '') {

        if ($object == '') {

            $object = [
                'id_counter_setting' => $this->input->post('id_counter_setting'),
                'counter'            => $this->input->post('counter'),
                'nomor'              => $this->input->post('nomor')
            ];
        }

        $q = $this->db->insert('queue', $object);

        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public function upCounter($obj = '', $id = '', $based_on = '') {
        $log = '';

        if ($id != '') {
            $based_on = ['id' => $id];
        }

        $q = $this->db->update('queue', $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Counter ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function getCounter($id = '') {
        if ($id == '') {
            $id = $this->input->get('id');
        }

        $q = $this->db->get_where('queue', ['id' => $id]);
        return $q;
    }

    public function showdtcounterPlace() {
        // Definisi
        $condition = [];
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = 'queue_setting';
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'id', 'judul', 'video');
        // Set searchable column fields
        $CI->dt->column_search = array('id', 'judul', 'video');
        // Set select column fields
        $CI->dt->select        = '*';
        // Set default order
        $CI->dt->order         = array('judul' => 'ASC');

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->judul,
                '<video width="320" height="240" controls><source src="' . base_url($dt->video) . '"></path></video≥'
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

    public function deCPlace($id) {
        $id = implode(",", $id);

        $p = $this->db->query("DELETE  FROM queue_setting WHERE id IN ($id)");
        return true;
    }

    public function inCounterPlace($object = '') {

        if ($object == '') {

            $video = '';
            if (isset($_FILES['video']) && $_FILES['video']['tmp_name']) {
                $config['upload_path']   = 'upload/';
                $config['allowed_types'] = 'mp4';
                $config['max_size']      = 100000; //set max size allowed in Kilobyte
                $config['file_name']     = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('video')) { //upload and validate
                    $data['inputerror'][]   = 'video';
                    $data['error_string'][] = 'Upload error: ' . $this->upload->display_errors('', ''); //show ajax error
                    $data['status']         = FALSE;
                    echo json_encode($data);
                    exit();
                }

                $video = $this->upload->data('file_name');
            }

            $object = [
                'judul' => $this->input->post('judul')
            ];

            if ($video) {
                $object['video'] = 'upload/' . $video;
            }
        }

        $q = $this->db->insert('queue_setting', $object);

        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public function upCounterPlace($obj = '', $id = '', $based_on = '') {
        $log = '';

        if ($id != '') {
            $based_on = ['id' => $id];
        }

        $q = $this->db->update('queue_setting', $obj, $based_on);

        $log = [
            'response' => $q,
            'request'  => $obj,
            'msg'      => 'Sukses ubah Counter ',
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function getCounterPlace($id = '') {
        if ($id == '') {
            $id = $this->input->get('id');
        }

        $q = $this->db->get_where('queue_setting', ['id' => $id]);
        return $q;
    }

}

/* End of file UnitModel.php */
