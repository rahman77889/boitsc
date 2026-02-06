<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking extends CI_Controller {

    private $path = 'page/tracking/';

    public function __construct() {
        parent::__construct();
        $this->load->model('ComplainHistoryModel', 'chm');
        $this->load->model('CategoryModel', 'cm');
        $this->load->model('UnitModel', 'um');
        $this->load->model('UsersModel', 'usm');

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

    public function complain_history_sosmed() {
        $channel   = $this->input->get('channel');
        $tgl1      = $this->input->get('tgl1');
        $tgl2      = $this->input->get('tgl2');
        $ticket_no = $this->input->get('ticket_no');

        $channel_list              = array();
        $channel_list['facebook']  = 'Facebook';
        $channel_list['instagram'] = 'Instagram';
        $channel_list['whatsapp']  = 'Whatsapp';
        $channel_list['twitter']   = 'Twitter';
        $channel_list['telegram']  = 'Telegram';
        $channel_list['email']     = 'Email';
        $channel_list['sms']       = 'SMS';
        $channel_list['webchat']   = 'Webchat';

        foreach ($channel_list as $ct => $cv) {
            $listChannel[] = '<option value="' . $ct . '">' . $cv . '</option>';
        }
        $listChannel = '<option value="">--All Channel--</option>' . implode('', $listChannel);

        $d = [
            'title'       => "Tracking - Complain History Social Media :: Telkomcel",
            'linkView'    => $this->path . 'complain_history_sosmed',
            'listChannel' => $listChannel,
            'tgl1'        => $tgl1,
            'tgl2'        => $tgl2,
            'ticket_no'   => $ticket_no,
        ];
        $this->load->view('page/_main', $d);
    }

    public function complain_history_sosmed_detail() {
        $d = [
            'title'    => "Tracking - Detail Complain History Social Media :: Telkomcel",
            'linkView' => $this->path . 'complain_history_sosmed_detail'
        ];
        $this->load->view('page/_main', $d);
    }

    public function complain_history() {
        $resCategory = $this->cm->getCategoryActive()->result_array();

        $listCategory = array();
        foreach ($resCategory as $ct) {
            $listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
        }

        $listCategory = '<option value="">--Choose All Cetegory--</option>' . implode('', $listCategory);

        $restUnit = $this->um->getUnitList()->result_array();

        $listUnit = array();
        foreach ($restUnit as $ct) {
            $listUnit[] = '<option value="' . $ct['id'] . '">' . $ct['unitName'] . '</option>';
        }

        $listUnit = '<option value="">--Choose All Unit--</option>' . implode('', $listUnit);

        $restUser = $this->usm->getUsers()->result_array();

        $listUser = array();
        foreach ($restUser as $ct) {
            $listUser[] = '<option value="' . $ct['id'] . '">' . $ct['fullName'] . '</option>';
        }

        $listUser = '<option value="">--Choose All User--</option>' . implode('', $listUser);

        $restComplaintType = $this->db->select('*')->from('complaintype')->get()->result_array();

        $listComplainType = array();
        foreach ($restComplaintType as $ct) {
            $listComplainType[] = '<option value="' . $ct['id'] . '">' . $ct['name'] . '</option>';
        }

        $listComplainType = '<option value="">--All Complaint Type--</option>' . implode('', $listComplainType);

        $d = [
            'title'            => "Tracking - Complain History :: Telkomcel",
            'linkView'         => $this->path . 'complain_history',
            'listCategory'     => $listCategory,
            'listUnit'         => $listUnit,
            'listUser'         => $listUser,
            'listComplainType' => $listComplainType
        ];
        $this->load->view('page/_main', $d);
    }

    public function showdtcomplainthistory() {
        echo $this->chm->showdtcomplainthistory();
    }

}
