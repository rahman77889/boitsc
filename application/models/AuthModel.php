<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model
{

	var $secret_key = '92841921049702884808589896395213';

	function generateLinkSSO360()
	{
		$username_plain = $this->session->userdata('username');
		$password_plain = $this->session->userdata('password');

		$username = $this->encrypt($username_plain, SECRET_KEY_SSO);
		$password = $this->encrypt($password_plain, SECRET_KEY_SSO);

		$url = 'https://helpdesk360.telkomcel.tl/auth/sso-login/' . urlencode($username) . '/' . urlencode($password) . '/general-info';
		// $url = 'https://dashboard360.shiblysolution.id/auth/sso-login/' . urlencode($username) . '/'  . urlencode($password) . '/general-info';

		return $url;
	}

	function encrypt($plaintext, $password)
	{
		$key = hash('sha256', $password, true);
		$iv = openssl_random_pseudo_bytes(16);

		$ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext, $key, true);
		$combined = $iv . $hmac . $ciphertext;

		return base64_encode($combined);
	}

	function decrypt($encrypted_data, $password)
	{
		$key = hash('sha256', $password, true);
		$combined = base64_decode($encrypted_data);

		$iv = substr($combined, 0, 16);
		$hmac = substr($combined, 16, 32);
		$ciphertext = substr($combined, 48);

		$hmacCheck = hash_hmac('sha256', $ciphertext, $key, true);

		// PHP 5.3 doesn't have hash_equals, so we must create a timing-safe comparison function.
		if (strlen($hmac) != strlen($hmacCheck) || !$this->timingSafeCompare($hmac, $hmacCheck)) {
			throw new Exception("HMAC verification failed");
		}

		return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
	}

	function timingSafeCompare($a, $b)
	{
		$length = strlen($a);
		if ($length !== strlen($b)) {
			return false;
		}
		$result = 0;
		for ($i = 0; $i < $length; $i++) {
			$result |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $result === 0;
	}

	public function auth($username = '', $password = '')
	{
		$log = [];
		$status = 0;

		$m = &get_instance();
		$m->load->model('UsersModel', 'u');

		if ($username == '') {
			$username = $this->input->post('username', TRUE);
		}

		if ($password == '') {
			$password = $this->input->post('password', TRUE);
		}

		if ($username == '') {
			$msg = "Username is cannot empty";
		} else if ($password == '') {
			$msg = "Password is cannot empty'";
		} else {

			$usr = $m->u->getUsers('', '', ['username' => $username, 'password' => md5($password), 'active' => '1']);

			if ($usr->num_rows() > 0) {
				$usr = $usr->row();

				if ($usr->privilegeId == 6 || $usr->privilegeId == 7) {
					$this->session->set_flashdata('error', 'Sorry your privilege is not valid to login crm helpdesk');
					$msg = "Sorry your privilege is not valid to login crm helpdesk";
				} else {

					if (time() - strtotime($usr->last_activity) > 15 * 60) { // jika last activity 15 menit lalu maka boleh login lagi dan last session di clear
						$this->load->model('UsersModel', 'user');

						$detail_user_status = $this->db->select('*')->from('userStatus')->where('userId', $usr->id)->order_by('time_login desc')->limit(1)->get()->row();
						$time_login = $detail_user_status->time_login;

						$userStatus = [
							'time_logout' => $usr->last_activity,
							'duration' => strtotime($usr->last_activity) - strtotime($time_login)
						];

						$this->db->where('userId', $usr->id);
						$this->db->where('time_login', $time_login);
						$q = $this->db->update('userStatus', $userStatus);

						$statusLogin = array();
						$statusLogin['statusLogin'] = 0;
						$statusLogin['session_id'] = '';
						$statusLogin['last_activity'] = '';

						$this->db->where('id', $usr->id);
						$this->db->update('user', $statusLogin);

						$usr = $m->u->getUsers('', '', ['username' => $username, 'password' => md5($password), 'active' => '1']);
						$usr = $usr->row();
					}

					if ($usr->session_id == '' || $usr->id == 101 || time() - (10 * 60) > strtotime($usr->last_activity)) {

						$account = $m->u->getAccount($usr->id)->row();

						$user = array(
							'id' => $usr->id,
							'level' => $usr->level,
							'privilege' => $usr->privilegeId,
							'photo' => $usr->photo,
							'privilageName' => $account->privilegeName,
							'fullName' => $account->fullName,
							'username' => $account->username,
							'password' => $password,
							'extend_number' => $account->extend_number,
							'tipe' => $account->tipe,
							'area' => (string) $account->area,
							'id_counter' => $account->id_counter,
							'id_counter_setting' => $account->id_counter_setting,
							'time_login' => date('Y-m-d H:i:s')
						);

						$this->session->set_userdata($user);

						$msg = "Success login as " . $usr->fullName;
						$status = 1;

						$userStatus = [
							'userId' => $usr->id,
							'ipLocation' => $this->input->ip_address(),
							'time_login' => date('Y-m-d H:i:s')
						];

						$m->u->inUserStatus($userStatus);

						$statusLogin = array();
						$statusLogin['statusLogin'] = 1;
						$statusLogin['session_id'] = session_id();
						$statusLogin['last_activity'] = date('Y-m-d H:i:s');

						$this->db->where('id', $usr->id);
						$this->db->update('user', $statusLogin);
					} else {
						$this->session->set_flashdata('error', 'Sorry your account is still logged on another computer');
						$msg = "Sorry your account is still logged on another computer";
					}
				}
			} else {
				$this->session->set_flashdata('error', 'Sorry your password or username is not valid');
				$msg = "Sorry your password or username is not valid";
			}
		}


		$log = [
			'status' => $status,
			'msg' => $msg,
		];

		return $log;
	}
}

/* End of file AuthModel.php */
