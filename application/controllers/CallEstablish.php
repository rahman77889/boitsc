<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CallEstablish extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		if ($this->router->method != 'getBTSCode') {
			if ($this->session->userdata('privilege') == '') {
				redirect('Login');
			} else {
				$cek_session   = $this->db->select('session_id, last_activity')->from('user')->where('id', $this->session->userdata('id'))->get()->row();
				$last_activity = strtotime($cek_session->last_activity);

				if ($cek_session->session_id != session_id() || time() - $last_activity > 15 * 60) { //force logout jika beda session id atau lebih dari 10 menit
					$this->session->sess_destroy();
					$this->session->unset_userdata('id');
					$this->session->unset_userdata('level');

					redirect('Login');
				} else {
					//                    $update_last_activity                  = array();
					//                    $update_last_activity['last_activity'] = date('Y-m-d H:i:s');
					//
					//                    $this->db->where('id', $this->session->userdata('id'));
					//                    $this->db->update('user', $update_last_activity);
				}
			}
			$this->load->model('ModelCall', 'mc');
		}
	}

	public function get()
	{
		$id_user       = $this->session->userdata('id');
		$extend_number = $this->session->userdata('extend_number');

		$status = $this->db->select('status,status_call_type,status_call_start,status_call_language,status_call_msisdn,status_bts_info,status_adc,status_call_center_number')->from('user')->where('id', $id_user)->get()->row();

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer"      => false,
				"verify_peer_name" => false,
			),
		);

		//get info cug
		// $cug = file_get_contents('https://hakbesik.telkomcel.tl/check_cug?a=true&t=check&msisdn=' . $status->status_call_msisdn, false, stream_context_create($arrContextOptions));

		// $cug     = json_decode($cug, true);
		// $cugInfo = '';

		// if ($cug['success']) {
		// 	$cugInfo = $cug['cug'];
		// }

		$return           = array();
		$return['status'] = $status;
		$return['cug']    = '';

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	public function setStatusAdc($status)
	{
		$id_user       = $this->session->userdata('id');
		$extend_number = $this->session->userdata('extend_number');
		$ret           = array();

		$update               = array();
		$update['status_adc'] = $status;

		$this->db->where('extend_number', $extend_number);

		if ($this->db->update('user', $update)) {
			$ret['success'] = true;

			$cek = $this->db->select('time_login')->from('agentStatus')->where('duration', 0)->where('userId', $id_user)->order_by('time_login', 'desc')->limit(1)->get()->row();

			if (isset($cek->time_login) && $status == 'N') {
				$dataUp                = array();
				$dataUp['time_logout'] = date('Y-m-d H:i:s');
				$dataUp['duration']    = time() - strtotime($cek->time_login);

				$this->db->where('userId', $id_user);
				$this->db->where('duration', 0);
				$this->db->update('agentStatus', $dataUp);
			} elseif ($status == 'Y') {
				$dataIn                  = array();
				$dataIn['userId']        = $id_user;
				$dataIn['extend_number'] = $extend_number;
				$dataIn['time_login']    = date('Y-m-d H:i:s');

				$this->db->insert('agentStatus', $dataIn);

				if (isset($cek->time_login)) {
					$dataUp                = array();
					$dataUp['time_logout'] = date('Y-m-d H:i:s');
					$dataUp['duration']    = time() - strtotime($cek->time_login);

					$this->db->where('userId', $id_user);
					$this->db->where('duration', 0);
					$this->db->update('agentStatus', $dataUp);
				}
			}
		} else {
			$ret['success'] = false;
		}

		header('Content-Type:text/json');
		echo json_encode($ret, JSON_PRETTY_PRINT);
	}

	public function getDetail()
	{
		$id_user       = $this->session->userdata('id');
		$extend_number = $this->session->userdata('extend_number');

		$status   = $this->db->select('status,status_call_type,status_call_start,status_call_msisdn,status_bts_info,status_tac_device,status_adc,status_call_center_number')->from('user')->where('id', $id_user)->get()->row();
		$customer = null;
		$anc      = null;
		$bts      = null;
		$device   = null;

		if ($status->status == 'busy' && $status->status_call_msisdn) {
			$customer = $this->getInfo($status->status_call_msisdn);
			$anc      = $this->getInfoAnc($status->status_call_msisdn);
			if ($status->status_bts_info && $status->status_bts_info != '4G') {
				$bts    = $this->db->select('*')->from('bts')->where('site_id', $status->status_bts_info)->get()->row();
				$device = $this->db->select('*')->from('devices')->where('tac', $status->status_tac_device)->get()->row();
			}
		}

		$return                = array();
		$return['provider']    = $customer;
		$return['customer']    = $anc;
		$return['bts']         = $bts;
		$return['device']      = $device;
		$return['call_center'] = $status->status_call_center_number;

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	// private function token()
	// {
	// 	$data = file_get_contents('http://150.242.110.240:8280/token');
	// 	$json = json_decode($data, true);

	// 	return $json['access_token'];
	// }

	private function getInfo($msisdn)
	{
		$msisdn = str_replace(array('*', '+'), '', $msisdn);

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer"      => false,
				"verify_peer_name" => false,
			),
		);

		// $data = file_get_contents('http://150.242.110.240:8280/myTelkomcel/bta?type=30&msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&token=' . $this->token(), false, stream_context_create($arrContextOptions));
		$data = file_get_contents('http://172.17.12.126:9080/vas/account/accountpackages?msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&channel=300', false, stream_context_create($arrContextOptions));
		$json = json_decode($data, true);

		return $json;
	}

	public function getInfoSc($msisdn)
	{
		$ret = $this->getInfo($msisdn);

		$sc               = $ret['serviceClass'];
		$complaintTypeVal = '';

		if ($sc <= 1999) { //prepaid //$sc >= 1000 && 
			$complaintTypeVal = '2';
		} elseif ($sc >= 2000 && $sc <= 2999) { //hybrid
			$complaintTypeVal = '5';
		} elseif ($sc >= 3000 && $sc <= 39999) { //postpaid
			$complaintTypeVal = '1';
		}

		echo $complaintTypeVal;
	}

	public function getInfoAnc($msisdn, $print = false)
	{
		$msisdn = str_replace(array('*', '+'), '', $msisdn);

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer"      => false,
				"verify_peer_name" => false,
			),
		);

		// $data = file_get_contents('http://150.242.110.240:8280/myTelkomcel/bta?msisdn=' . $msisdn . '&type=103&token=' . $this->token() . '&trxid=' . mt_rand(100000, 999999), false, stream_context_create($arrContextOptions));
		$data = file_get_contents('http://172.17.12.126:9080/anc/' . $msisdn, false, stream_context_create($arrContextOptions));
		$json = json_decode($data, true);

		if (isset($json['customername'])) {
			if (!$print) {
				return $json;
			} else {
				echo $data;
			}
		} else {
			return null;
		}
	}

	public function getBTSCode($msisdn)
	{
		//+67073020425 => 3G
		$ret = '';
		if (strlen($msisdn) > 5 && (substr($msisdn, 4, 1) == 7 || substr($msisdn, 3, 1) == 7)) {
			$msisdn = str_replace(array('*', '+'), '', $msisdn);
			// $url    = 'http://10.70.2.189:8080/mobicents/gmlc/rest?msisdn=' . $msisdn;
			$url    = 'http://172.20.212.17:8080/location/info?msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&channel=400';
			$ctn    = @file_get_contents($url);
			$ctx    = explode(',', $ctn);

			// if ($ctx[0] == 'mcc=514') {
			$cellid = str_replace('cellid=', '', $ctx[3]);
			$lac = str_replace('lac=', '', $ctx[2]);

			// $retPre = $this->db->select('site_id')->from('sitemap')->or_where('ci2g', $cellid)->or_where('ci3g_850', $cellid)->or_where('ci3g_2100_1', $cellid)->or_where('ci3g_2100_2', $cellid)->or_where('ci3g_2100_3', $cellid)->limit(1)->get()->row();
			$retPre = $this->db->select('site_id')->from('sitemap')->where('cell_id', $cellid)->where('lac', $lac)->limit(1)->get()->row();

			if ($retPre != null && $retPre->site_id != null) {
				$tac_number = $ctx[8];
				$tac_number = str_replace('imei=', '', $tac_number);
				$ret        = $retPre->site_id . '_' . $tac_number . '_' . $ctn;
			} else {
				$tac_number = $ctx[8];
				$tac_number = str_replace('imei=', '', $tac_number);
				$ret        = $ret . '_' . $tac_number . '_' . $ctn;
			}
			// } elseif ($ctx[0] == 'mcc=-1') {
			// $ret = '4G';
			// }
		}
		echo $ret;
	}

	public function getBTSMap($btsCode)
	{
		$btsUrlMap = $this->db->select('urlmap')->from('bts')->where('site_id', $btsCode)->get()->row();

		if (isset($btsUrlMap->urlmap)) {
			echo $btsUrlMap->urlmap;
		}
	}

	public function getCallerDetail()
	{
		$msisdn = $this->input->post('msisdn');
		$detail = $this->db->select('call_center_number')->from('call_log_incoming')->where('msisdn', $msisdn)->where('status', 'waiting')->get()->row();

		$call_center   = '';
		$customer_name = '';

		if (isset($detail->call_center_number)) {
			$call_center = $detail->call_center_number;
		}

		$detail_customer = $this->getInfoAnc($msisdn);
		$customer_name   = $detail_customer['customername'];

		$return                  = array();
		$return['call_center']   = $call_center;
		$return['customer_name'] = $customer_name;

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	public function getListAgentActive()
	{
		$listUserExt = $this->db->select('fullName,extend_number')->from('user')->where('status_adc', 'Y')->where('status', 'stand_by')->where('extend_number > 0')->get()->result_array();

		$ret = '';
		foreach ($listUserExt as $lx) {
			$ret .= '<option value="' . $lx['extend_number'] . '">' . $lx['fullName'] . ' - ' . $lx['extend_number'] . '</option>';
		}

		$return          = array();
		$return['list']  = $ret;
		$return['count'] = count($listUserExt);

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	public function setTransferNow()
	{
		$update                              = array();
		$update['status']                    = 'stand_by';
		$update['status_call_type']          = null;
		$update['status_call_start']         = null;
		$update['status_call_msisdn']        = null;
		$update['status_call_language']      = null;
		$update['status_bts_info']           = null;
		$update['status_tac_device']         = null;
		$update['status_call_center_number'] = null;

		$this->db->where('extend_number', $this->session->userdata('extend_number'));
		$sukses = false;
		if ($this->db->update('user', $update)) {
			$sukses = true;
		}

		$return           = array();
		$return['sukses'] = $sukses;

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}
}
