<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('UnitModel', 'u');
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

    // Privileges
    public function getUnit() {
        $q = $this->u->getUnit();
        echo json_encode($q->result());
    }

    public function showdtunit() {
        echo $this->u->showdtunit();
    }

    public function deleteunit() {
        $id = $this->input->get('id');
        if ($id != '') {
            $id = explode(',', $id);
            $this->u->de($id);
        }
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function inUnitInformation() {
        $log = [];

        $in = $this->u->inUnitInformation();
        if ($in) {
            $log = [
                'msg' => 'Success Add Unit!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add Unit!',
            ];
        }

        echo json_encode($log);
    }

    public function upUnitInformation() {
        $id = $this->input->post('id');

        $object = [
            'name'         => $this->input->post('name'),
            'email'        => $this->input->post('email'),
            'phone'        => $this->input->post('phone'),
            'unit_id'      => $this->input->post('unit'),
            'status'       => $this->input->post('status'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        // // Insert Log Actvitiy
        // $msgLog = "User : " . $this->session->userdata('username') . " -> Update CDR";
        // $this->lm->id_user = $this->session->userdata('id');
        // $this->lm->inLogActivity($msgLog, json_encode($obj));

        echo json_encode($this->u->upUnitInformation($object, $id));
    }

    public function getUnitInformation() {
        echo json_encode($this->u->getUnitInformation()->row());
    }

    public function showdtqueue() {
        echo $this->u->showdtcounter();
    }

    public function deleteCounter() {
        $id = $this->input->get('id');
        if ($id != '') {
            $id = explode(',', $id);
            $this->u->deC($id);
        }
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function inCounter() {
        $log = [];

        $in = $this->u->inCounter();
        if ($in) {
            $log = [
                'msg' => 'Success Add Counter!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add Counter!',
            ];
        }

        echo json_encode($log);
    }

    public function upCounter() {
        $id = $this->input->post('id');

        $object = [
            'id_counter_setting' => $this->input->post('id_counter_setting'),
            'counter'            => $this->input->post('counter'),
            'nomor'              => $this->input->post('nomor')
        ];

        echo json_encode($this->u->upCounter($object, $id));
    }

    public function getCounter() {
        echo json_encode($this->u->getCounter()->row());
    }

    public function showdtqueuePlace() {
        echo $this->u->showdtcounterPlace();
    }

    public function deleteCounterPlace() {
        $id = $this->input->get('id');
        if ($id != '') {
            $id = explode(',', $id);
            $this->u->deCPlace($id);
        }
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function inCounterPlace() {
        $log = [];

        $in = $this->u->inCounterPlace();
        if ($in) {
            $log = [
                'msg' => 'Success Add Counter!',
            ];
        } else {
            $log = [
                'msg' => 'Failed Add Counter!',
            ];
        }

        echo json_encode($log);
    }

    public function upCounterPlace() {
        $id = $this->input->post('id');

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
            'judul' => $this->input->post('judul'),
        ];

        if ($video) {
            $object['video'] = $video;
        }

        echo json_encode($this->u->upCounterPlace($object, $id));
    }

    public function getCounterPlace() {
        echo json_encode($this->u->getCounterPlace()->row());
    }

}

/* End of file Unit.php */
