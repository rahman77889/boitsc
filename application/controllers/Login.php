<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

	private $path = 'page/login/';

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function sso360()
	{
		$this->load->model('AuthModel', 'auth');
		$url = $this->auth->generateLinkSSO360();

		header('location:' . $url);
	}

	public function index()
	{
		$d = [
			'title' => "Login :: Helpdesk",
		];
		$this->load->view('page/login/login', $d);
	}

	public function prosesLogin()
	{
		$this->load->model('AuthModel', 'auth');
		$aut = $this->auth->auth();
		if ($aut['status'] == 0) {
			$this->session->set_flashdata('error', $aut['msg']);
			if ($this->input->post('mobile') == 'Y') {
				redirect('Login?mobile=true');
			} else {
				redirect('Login');
			}
		} else {
			$this->session->set_flashdata('success', $aut['msg']);

			if ($this->input->post('mobile') == 'Y') {
				$this->session->set_userdata('mobile', 'Y');
				redirect('ManageSystem/accountInformation');
			} else {
				$this->session->set_userdata('mobile', 'N');
				redirect('Dashboard');
			}
		}
	}

	public function logout()
	{
		$isMobile = false;
		if ($this->session->userdata('mobile') == 'Y') {
			$isMobile = true;
		}

		$this->load->model('UsersModel', 'user');
		$time_login = $this->session->userdata('time_login');

		$userStatus = [
			'userId'      => $this->session->userdata('id'),
			'ipLocation'  => $this->input->ip_address(),
			'time_logout' => date('Y-m-d H:i:s'),
			'duration'    => time() - strtotime($time_login)
		];

		$this->db->where('userId', $this->session->userdata('id'));
		$this->db->where('time_login', $time_login);
		$q = $this->db->update('userStatus', $userStatus);

		$statusLogin                  = array();
		$statusLogin['statusLogin']   = 0;
		$statusLogin['session_id']    = '';
		$statusLogin['last_activity'] = '';

		$this->db->where('id', $this->session->userdata('id'));
		$this->db->update('user', $statusLogin);

		if ($q) {
			$this->session->sess_destroy();
			$this->session->unset_userdata('id');
			$this->session->unset_userdata('level');
		}

		if ($isMobile) {
			redirect('Login?mobile=true');
		} else {
			redirect('Login');
		}
	}
}
