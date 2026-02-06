<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ManageSystem extends CI_Controller {

    private $path = 'page/manageSystem/';

    public function __construct() {
        parent::__construct();

        $this->load->model('UsersModel', 'u');
    }

    public function getUserAgent() {
        $query['data'] = $this->db->query("SELECT * FROM `user` WHERE privilegeId='3'")->result();
        // foreach ($query as $row) {
        //     echo $row->fullName;
        // }
        $this->load->view('page/dashboard/dashboard', $query);
    }

    public function getQueueList($id_counter_setting) {
        $json = $this->db->select('*')->from('queue')->where('id_counter_setting', $id_counter_setting)->get()->result();

        header('Content-Type:application/json');
        echo json_encode($json);
    }

    //getUserTableAll
    public function getUser() {
        $query = $this->u->getUsers();
        echo json_encode($query->result());
    }

    // Account Information

    public function index() {
        $kata = "saya ingin pulang ke pluto";
        $ex   = explode(' ', $kata);
        $data = "";

        foreach ($ex as $v) {
            $data .= strtoupper($v[0]);
        }

        echo $data;
    }

    public function accountInformation() {
        $cek_session   = $this->db->select('session_id, last_activity')->from('user')->where('id', $this->session->userdata('id'))->get()->row();
        $last_activity = strtotime($cek_session->last_activity);

        if ($cek_session->session_id != session_id() || time() - $last_activity > 15 * 60) {//force logout jika beda session id atau lebih dari 10 menit
            $this->session->sess_destroy();
            $this->session->unset_userdata('id');
            $this->session->unset_userdata('level');

            if ($this->input->post('mobile') == 'Y') {
                redirect('Login?mobile=true');
            } else {
                redirect('Login');
            }
        } else {
            $update_last_activity                  = array();
            $update_last_activity['last_activity'] = date('Y-m-d H:i:s');

            $this->db->where('id', $this->session->userdata('id'));
            $this->db->update('user', $update_last_activity);
        }

        $d = [
            'title'    => "Manage Sytem - Account Information :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'accountInformation'
        ];
        $this->load->view('page/_main', $d);
    }

    public function dtUser() {
        echo $this->u->dtUsers();
    }

    # Unit Information

    public function unitInformation() {
        $d = [
            'title'    => "Manage Sytem - Unit Information :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'unitInformation'
        ];
        $this->load->view('page/_main', $d);
    }

    // User Manager

    public function getUserManager() {
        $u = $this->u->getUserManager("id,username");
        echo json_encode($u);
    }

    public function getUserSupervisor() {
        $u = $this->u->getUserSupervisor("id,username");
        echo json_encode($u);
    }

    private function randomPassword($jml = 7) {
        $alphabet    = '123456789';
        $pass        = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $jml; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function addUser() {
        $cekUsername = $this->u->getUsers('', '', ['username' => $this->input->post('userid')]);
        if ($cekUsername->num_rows() > 0) {
            $log = [
                'msg'    => 'Username sudah digunakan, harap masukan username lain.',
                'status' => 0
            ];
        } else {
//            $pass = $this->randomPassword(6);
            $obj = [
                'extend_number'      => $this->input->post('extend_number'),
                'username'           => $this->input->post('userid'),
                'password'           => md5($this->input->post('password')), //md5($pass)
                'open_password'      => $this->input->post('password'),
                'fullName'           => $this->input->post('username'),
                'userManagerId'      => $this->input->post('usermanager'),
                'userSpvId'          => $this->input->post('userspv'),
                'privilegeId'        => $this->input->post('previleges'),
                'unitId'             => $this->input->post('unit'),
                'locationId'         => $this->input->post('location'),
                'tipe'               => $this->input->post('tipe'),
                'id_counter'         => $this->input->post('id_counter'),
                'id_counter_setting' => $this->input->post('id_counter_setting'),
                'active'             => 1,
                'level'              => 1,
                'statusLogin'        => 0,
                'created_date'       => date('Y-m-d H:i:s'),
                'created_by'         => $this->session->userdata('id')
            ];

            if (!empty($_FILES['photo']['name'])) {
                $upload       = $this->_do_upload();
                $obj['photo'] = $upload;
            }

            $q = $this->u->inUsers($obj);

            if ($q['response'] == true) {
                $log = [
                    'msg'    => 'Berhasil menambahkan user',
                    'status' => 1
                ];
            } else {
                $log = [
                    'msg'    => 'Gagal menambahkan user',
                    'status' => 0
                ];
            }
        }
        echo json_encode($log);
    }

    private function _do_upload() {
        $config['upload_path']   = 'upload/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']      = 200; //set max size allowed in Kilobyte
        $config['max_width']     = 2000; // set max width image allowed
        $config['max_height']    = 2000; // set max height allowed
        $config['file_name']     = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('photo')) { //upload and validate
            $data['inputerror'][]   = 'photo';
            $data['error_string'][] = 'Upload error: ' . $this->upload->display_errors('', ''); //show ajax error
            $data['status']         = FALSE;
            echo json_encode($data);
            exit();
        }
        return $this->upload->data('file_name');
    }

    public function deleteAccout() {
        $id = $this->input->get('id');
        if ($id != '') {
            $id = explode(',', $id);
            $this->u->deUsers($id[0]);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('delete', 'Success Delete Data');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function disableuser() {
        // $status = 1;
        $id = $this->input->get('id');

        if ($id != '') {
            $this->db->query("UPDATE user set active=0 WHERE id='$id'");
            $this->session->set_flashdata('disable', 'Success Disable User');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
        // echo $id;
        $this->session->set_flashdata('disable', 'Success Disable User');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function enableuser() {
        // $status = 1;
        $id = $this->input->get('id');

        if ($id != '') {
            $this->db->query("UPDATE user set active=1 WHERE id='$id'");
            $this->session->set_flashdata('enable', 'Success Enable User');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
        // echo $id;
        $this->session->set_flashdata('enable', 'Success Enable User');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function edit() {
        echo $this->session->userdata('photo');
    }

    public function getSubUser() {
        echo json_encode($this->u->getSubUser()->row());
    }

    public function upSubUser() {
        $id     = $this->input->post('id');
        $object = [
            'extend_number'      => $this->input->post('extend_number'),
            'username'           => $this->input->post('userid'),
            'open_password'      => $this->input->post('open_password'),
            'fullName'           => $this->input->post('username'),
            'userManagerId'      => $this->input->post('usermanager'),
            'userSpvId'          => $this->input->post('userspv'),
            'privilegeId'        => $this->input->post('previleges'),
            'unitId'             => $this->input->post('unit'),
            'locationId'         => $this->input->post('location'),
            'tipe'               => $this->input->post('tipe'),
            'id_counter'         => $this->input->post('id_counter'),
            'id_counter_setting' => $this->input->post('id_counter_setting')
        ];

        if ($this->input->post('password')) {
            $object['password'] = md5($this->input->post('password')); //md5($pass))
        }


        echo json_encode($this->u->upSubUser($object, $id));
    }

    public function updateProfil() {
        $id = $this->session->userdata('id');
        if (!empty($_FILES) && $_FILES['photo']['size'] > 0 && $_FILES['photo']['error'] == 0) {

            $upload = $this->_do_upload();

            $q         = $this->u->updatephoto($id, $upload);
            $user_data = array(
                'photo' => $upload
            );

            $this->session->set_userdata($user_data);
            if ($q) {
                $log = [
                    'msg'    => 'Berhasil menambahkan user',
                    'status' => 1
                ];
            } else {
                $log = [
                    'msg'    => 'Gagal menambahkan user',
                    'status' => 0
                ];
            }
        }
    }

    public function forceLogout($id_user) {
        $this->load->model('UsersModel', 'user');

        $detail_user_status = $this->db->select('*')->from('userStatus')->where('userId', $id_user)->order_by('time_login desc')->limit(1)->get()->row();
        $time_login         = $detail_user_status->time_login;

        $userStatus = [
            'time_logout' => date('Y-m-d H:i:s'),
            'duration'    => time() - strtotime($time_login)
        ];

        $this->db->where('userId', $id_user);
        $this->db->where('time_login', $time_login);
        $q = $this->db->update('userStatus', $userStatus);

        $statusLogin                  = array();
        $statusLogin['statusLogin']   = 0;
        $statusLogin['session_id']    = '';
        $statusLogin['last_activity'] = '';

        $this->db->where('id', $id_user);
        $this->db->update('user', $statusLogin);
    }

    public function cronForceLogout() {
        $listUser = $this->db->select('id')->from('user')->where('last_activity <= \'' . (date('Y-m-d H:i:s', time() - (10 * 60))) . '\'')->get()->result();

        foreach ($listUser as $lu) {
            $this->forceLogout($lu->id);
        }
    }

    public function counterQueuing() {
        $d = [
            'title'    => "Manage Sytem - Counter Queuing :: Telkomcel Helpdesk",
            'linkView' => $this->path . 'counterQueuing'
        ];
        $this->load->view('page/_main', $d);
    }

    public function settingQueuing() {
        $setting = $this->db->select('*')->from('queue_setting')->get()->row_array();

        $d = [
            'title'    => "Manage Sytem - Counter Setting:: Telkomcel Helpdesk",
            'linkView' => $this->path . 'settingQueuing',
            'setting'  => $setting
        ];
        $this->load->view('page/_main', $d);
    }

    public function settingsQueueSubmit() {
        if ($_FILES['video']['tmp_name']) {
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

        $data          = array();
        $data['judul'] = $this->input->post('judul');

        if ($video) {
            $data['video'] = 'upload/' . $video;
        }

        $res = $this->db->update('queue_setting', $data);

        if ($res) {
            $this->session->set_flashdata('disable', 'Success Disable User');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

}
