<?php

defined('BASEPATH') or exit('No direct script access allowed');

class External extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	var $token_registered = 'f4c8425f-e751-4ecf-8283-f213fd3c617d';

	public function toLogin360()
	{
		$ret = array('status' => false, 'username' => '', 'password' => '');

		$username_plain = $this->session->userdata('username');
		$password_plain = $this->session->userdata('password');

		$this->load->model('AuthModel', 'auth');

		$username =  $this->auth->encrypt($username_plain, SECRET_KEY_SSO);
		$password = $this->auth->encrypt($password_plain, SECRET_KEY_SSO);

		$id_user       = $this->session->userdata('id');
		$status   = $this->db->select('status,status_call_type,status_call_start,status_call_msisdn,status_bts_info,status_tac_device,status_adc,status_call_center_number')->from('user')->where('id', $id_user)->get()->row();

		$ret['username'] = urlencode($username);
		$ret['password'] = urlencode($password);
		$ret['username_plain'] = urlencode($username_plain);
		$ret['password_plain'] = urlencode($password_plain);
		$ret['msisdn'] = $status->status_call_msisdn ? $status->status_call_msisdn : '';
		$ret['status'] = true;

		header('Content-Type:text/json');
		echo json_encode($ret);
	}

	public function login_crm_helpdesk()
	{
		$username_plain = urldecode($this->input->get('username'));
		$password_plain = urldecode($this->input->get('password'));

		try {

			$this->load->model('AuthModel', 'auth');

			$username = $this->auth->decrypt($username_plain, SECRET_KEY_SSO);
			$password = $this->auth->decrypt($password_plain, SECRET_KEY_SSO);
			// $password2 = External::encrypt('mLDdO3wRq2yP0CZe', SECRET_KEY_SSO);

			// echo $username;
			// echo '<br>';
			// echo $password;
			// echo '<br>';
			// echo $password2;
			// exit();
		} catch (Exception $e) {
			print_r($e);
		}

		if ($username != '' && $password != '') {
			// $usr = $this->db->select('id,fullName,username,privilegeId,photo')->from('user')->where('username', $username)->where('password', md5($password))->get();

			// if ($usr->num_rows() > 0) {
			// $usr = $usr->row();

			$this->load->model('AuthModel', 'auth');
			$aut = $this->auth->auth($username, $password);

			// print_r($aut);
			// exit();

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
			// } else {
			// 	header("HTTP/1.1 403 Forbidden");
			// }
		}
	}

	public function login360()
	{
		$headers = apache_request_headers();
		$token = $headers['token'];

		if ($token == $this->token_registered) {
			$ret = array('status' => false, 'data' => array(), 'msg' => '');

			header('content-type:application/json');

			$username = $this->input->post('username', TRUE);
			$password = $this->input->post('password', TRUE);

			if ($username != '' && $password != '') {
				$usr = $this->db->select('id,fullName,username,privilegeId,photo')->from('user')->where('username', $username)->where('password', md5($password))->get();

				if ($usr->num_rows() > 0) {
					$usr = $usr->row();

					if ($usr->photo) {
						$usr->photo = 'https://helpdesk.telkomcel.tl/upload/' . $usr->photo;
					}

					$ret['data'] = $usr;
					$ret['status'] = true;
					$ret['msg'] = 'Login success';
				} else {
					$ret['msg'] = 'Please provide valid login data';
				}
			} else {
				$ret['msg'] = 'Please provide valid login data';
			}

			echo json_encode($ret);
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
	}

	public function complaint_report($start_date, $end_date)
	{
		$headers = apache_request_headers();
		$token = $headers['token'];

		if ($token == $this->token_registered) {
			header('content-type:application/json');

			$ret = array('status' => false, 'data' => array(), 'msg' => '');

			if ($start_date && $end_date) {
				$daily_by_category       = $this->db->query("SELECT "
					. "count(ch.id) as total, ch.complainDate, ch.categoryId, category.categoryName "
					. "FROM complain ch "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "WHERE  ch.complainDate >= '$start_date' AND ch.complainDate <= '$end_date' "
					. "GROUP BY ch.categoryId, ch.complainDate")->result();

				$total_by_category       = $this->db->query("SELECT "
					. "count(ch.id) as total, ch.categoryId, category.categoryName "
					. "FROM complain ch "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "WHERE  ch.complainDate >= '$start_date' AND ch.complainDate <= '$end_date' "
					. "GROUP BY ch.categoryId")->result();

				if ($daily_by_category) {

					// foreach ($daily_by_category as $v) {
					// 	$v['total'] = (float)$v['total'];
					// }
					// foreach ($total_by_category as $v) {
					// 	$v['total'] = (float)$v['total'];
					// }

					$ret['status'] = true;
					$ret['data'] = array('daily' => $daily_by_category, 'total' => $total_by_category);
					$ret['msg'] = 'Data successfully fetch';
				} else {
					$ret['msg'] = 'Data not found';
				}
			} else {
				$ret['msg'] = 'Provide valid parameter';
			}

			echo json_encode($ret);
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
	}
}
