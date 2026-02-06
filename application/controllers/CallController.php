<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CallController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('privilege') == '') {
			redirect('Login');
		} else {
			$cek_session = $this->db->select('session_id, last_activity')->from('user')->where('id', $this->session->userdata('id'))->get()->row();
			$last_activity = strtotime($cek_session->last_activity);

			if ($cek_session->session_id != session_id() || time() - $last_activity > 15 * 60) { //force logout jika beda session id atau lebih dari 10 menit
				$this->session->sess_destroy();
				$this->session->unset_userdata('id');
				$this->session->unset_userdata('level');

				redirect('Login');
			} else {
				//                $update_last_activity                  = array();
				//                $update_last_activity['last_activity'] = date('Y-m-d H:i:s');
				//
				//                $this->db->where('id', $this->session->userdata('id'));
				//                $this->db->update('user', $update_last_activity);
			}
		}
		$this->load->model('ModelCall', 'mc');
	}

	public function createActivity()
	{
		$msisdn = $this->input->post('msisdn');
		$name = $this->input->post('name');
		$queue_no = $this->input->post('queue_no');
		$date_history = $this->input->post('date_history');
		$activity = $this->input->post('activity');

		$ret = array('status' => false, 'msg' => '');

		if ($queue_no && $date_history && $activity) {
			$data = array();
			$data['msisdn'] = $msisdn;
			$data['name'] = $name;
			$data['queue_no'] = $queue_no;
			$data['date_history'] = $date_history;
			$data['activity'] = $activity;
			$data['id_user'] = $this->session->userdata('id');
			$data['id_counter'] = $this->session->userdata('id_counter');
			$data['id_plaza'] = $this->session->userdata('id_counter_setting');

			$q = $this->db->insert('plaza_history', $data);

			if ($q) {
				$ret['status'] = true;
				$ret['msg'] = 'Data has been successfully saved';
			} else {
				$ret['msg'] = 'Data has been successfully NOT saved';
			}
		} else {
			$ret['msg'] = 'Please provide valid data';
		}

		echo json_encode($ret);
	}

	public function getDashboard($tgl1 = '', $tgl2 = '', $call_center_number = '')
	{
		if (!$tgl1) {
			$tgl1 = date('Y-m-d');
		}
		if (!$tgl2) {
			$tgl2 = date('Y-m-d');
		}

		$whereAgent = '';
		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 9) {
			//all
		} else {
			$whereAgent = ' and id_user=\'' . $this->session->userdata('id') . '\'';
		}

		if ($call_center_number == '147123888' || $call_center_number == '147123') {
			$call_center_number = '';
		}

		$whereCallCenter = '';
		if ($call_center_number) {

			if ($call_center_number == 'other') {
				$notIn = array('+123', '+147');
				$whereCallCenter = ' and call_center_number not in (\'+147\',\'+123\') ';
			} else {
				$whereCallCenter = ' and call_center_number=\'+' . $call_center_number . '\' ';
			}
		}

		if ($this->session->userdata('tipe') == '123') {
			$whereTipe = 'and tipe=\'123\'';
		} elseif ($this->session->userdata('tipe') == '147') {
			$whereTipe = 'and tipe=\'147\'';
		} elseif ($this->session->userdata('tipe') == '888') {
			$whereTipe = 'and tipe=\'888\'';
		} else {
			if ($call_center_number) {
				$whereTipe = 'and tipe=\'' . $call_center_number . '\'';
			} else {
				$whereTipe = '';
			}
		}

		$whereAgent .= $whereCallCenter;

		$return = array();
		$return['item']['agentLogin'] = $this->db->query("SELECT COUNT(id) as jumlah FROM user WHERE privilegeId in ('3') and active=1 AND statusLogin=1 " . $whereTipe)->row()->jumlah;
		$return['item']['agentReady'] = $this->db->query("SELECT COUNT(id) as jumlah FROM user WHERE privilegeId in ('3') and active=1 AND status_adc='Y' " . $whereTipe)->row()->jumlah;
		// $return['item']['totalCallToday'] = $this->db->query("SELECT COUNT(*) as jumlah FROM call_log_incoming WHERE date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['totalCallToday'] = 0;
		$return['item']['totalCallToday'] = $this->db->query("SELECT COUNT(*) as jumlah FROM call_log_incoming WHERE date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['asa'] = round($this->db->query("SELECT AVG(answer_time) as jumlah FROM call_log_incoming where answer_time>0 and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah, 2);
		$return['item']['handlingTime'] = round($this->db->query("SELECT AVG(duration) as jumlah FROM call_log_incoming where duration>0 and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah, 2);
		$return['item']['call5minutes'] = $this->db->query("SELECT COUNT(*) jumlah FROM call_log_incoming where duration > 300 and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['callWaiting'] = $this->db->query("SELECT COUNT(*) jumlah FROM call_log_incoming where status='waiting' and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['callMonitor'] = array();
		$return['topAgent'] = array();
		$return['totalCallInOut'] = array();
		$return['tableAgent'] = array();

		$totalanswercall = $this->db->query("SELECT SUM(totalanswer) as totalanswer from ((SELECT COUNT(id) as totalanswer FROM `call_log_incoming` WHERE status='hangup' and answer_time>0 and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent . ") union (SELECT COUNT(status) as totalanswer FROM `call_log_outgoing` WHERE status='hangup' and answer_time>0 and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent . ")) as t")->row();
		//        $totalabandonedcall = $this->db->query("SELECT SUM(totalhangup) as totalhangup from ((SELECT COUNT(status) as totalhangup FROM `call_log_incoming` WHERE status='hangup' and abandon='Y' and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent . ") union (SELECT COUNT(status) as totalhangup FROM `call_log_outgoing` WHERE status='hangup' and abandon='Y' and date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereAgent . ")) as t")->row();

		$resAbandon = $this->db->select('count(id) as jumlah')->from('call_log_abandon')->where('abandon', 'C')->where('date(tgl)>=', $tgl1)->where('date(tgl)<=', $tgl2);

		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 9) {
			//all
		} else {
			$resAbandon->where('id_user', $this->session->userdata('id'));
		}

		if ($call_center_number) {

			if ($call_center_number == 'other') {
				$notIn = array('+123', '+147');
				$resAbandon->where('call_center_number not in (\'+147\',\'+123\')');
			} else {
				$resAbandon->where('call_center_number', '+' . $call_center_number);
			}
		}

		$resAbandonFinal = $resAbandon->get()->row_array();

		$totalcall = $totalanswercall->totalanswer + ($resAbandonFinal['jumlah'] ? $resAbandonFinal['jumlah'] : 0);

		$return['callMonitor'] = [
			//            [
			//                'name' => 'Total Call',
			//                'y'    => (int) $totalcall
			//            ],
			[
				'name' => 'Total Answered Call',
				'y' => (int) $totalanswercall->totalanswer
			],
			[
				'name' => 'Total Abandoned Call ',
				'y' => (int) ($resAbandonFinal['jumlah'] ? $resAbandonFinal['jumlah'] : 0)
			]
		];

		$total_call_incoming = (int) $totalanswercall->totalanswer + (int) ($resAbandonFinal['jumlah'] ? $resAbandonFinal['jumlah'] : 0);

		$x = $this->mc->topagent($tgl1);

		foreach ($x as $row) {
			$return['topAgent'][] = array(
				'name' => $row['name'] . ' ' . round($row['jumlah'], 2) . '%',
				'y' => $row['jumlah'],
			);
		}

		$total_call_outgoing = (int) $this->db->query('select count(call_start) as total from call_log_outgoing where date(call_start)>=\'' . $tgl1 . '\' and date(call_start)<=\'' . $tgl2 . '\' ' . $whereAgent)->row()->total;

		$return['totalCallInOut'][] = array(
			'name' => 'Incoming',
			// 'y' => (int) $this->db->query('select count(call_start) as total from call_log_incoming where date(call_start)>=\'' . $tgl1 . '\' and date(call_start)<=\'' . $tgl2 . '\' ' . $whereAgent)->row()->total
			'y' => (int) $total_call_incoming
		);
		$return['totalCallInOut'][] = array(
			'name' => 'Outgoing',
			'y' => $total_call_outgoing
		);

		$return['item']['totalCallToday'] = $total_call_incoming + $total_call_outgoing;

		$dataAgent = $this->db->query("SELECT * FROM `user` WHERE privilegeId in ('3') and active=1 " . $whereTipe . " order by status_call_msisdn desc")->result();

		foreach ($dataAgent as $row) {

			$statusAgent = 'agentLogout';
			if ($row->statusLogin == 1) {
				$statusAgent = 'agentLogin';
			}
			if ($row->status == 'stand_by') {
				$statusAgent = 'agentReady';
			}
			if ($row->status == 'busy') {
				$statusAgent = 'agentBusy';
			}
			$return['tableAgent'][] = array('status' => $statusAgent, 'nama' => $row->fullName, 'pic' => $row->photo, 'ext' => $row->extend_number, 'call_type' => $row->status_call_type, 'call_msisdn' => $row->status_call_msisdn);
		}



		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	public function getDashboardCso($tgl1 = '', $tgl2 = '', $area = '')
	{

		error_reporting(E_ALL);

		if (!$tgl1) {
			$tgl1 = date('Y-m-d');
		}
		if (!$tgl2) {
			$tgl2 = date('Y-m-d');
		}

		$whereAgent = '';
		$whereAgentComplaint = '';
		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 9) {
			//all
		} else {
			$whereAgent = ' and id_user=\'' . $this->session->userdata('id') . '\'';
			$whereAgentComplaint = ' and userId=\'' . $this->session->userdata('id') . '\'';
		}

		$whereArea = '';
		$whereAreaPh = '';
		$whereAreaQueue = '';
		if ($area) {
			$whereArea = ' and area=\'' . $area . '\' ';
			$whereAreaPh = ' and ph.area=\'' . $area . '\' ';
			$whereAgentComplaint .= ' and userId in (select u.id from user u where u.area= \'' . $area . '\') ';
			$whereAreaQueue = ' and id_counter_setting in (select q.id from queue_setting q where q.area=\'' . $area . '\')';
		}

		$whereAgent .= $whereArea;

		$return = array();
		$return['item']['csoStanby'] = $this->db->query("SELECT COUNT(id) as jumlah FROM user WHERE privilegeId in ('4') and active=1 AND statusLogin=1 " . $whereArea)->row()->jumlah;
		$return['item']['totalCustomer'] = $this->db->query("SELECT COUNT(DISTINCT CASE WHEN msisdn IS NOT NULL THEN msisdn ELSE name END) as jumlah FROM plaza_history WHERE date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['averageHandling'] = round($this->db->query("SELECT AVG(TIMESTAMPDIFF(SECOND, date_history, finish_time)) as jumlah FROM plaza_history WHERE date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah, 2);
		$return['item']['hourlyService'] = round($this->db->query("SELECT AVG(tbl.total_data) as jumlah FROM (SELECT HOUR(date_history) as hr, COUNT(id) as total_data FROM plaza_history WHERE date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' " . $whereAgent . " GROUP BY HOUR(date_history)) as tbl")->row()->jumlah, 2);
		$return['item']['totalActivityToday'] = (int) $this->db->query("SELECT COUNT(id) as jumlah FROM plaza_history where date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['handlingTime5Min'] = (int) $this->db->query("SELECT count(id) as jumlah FROM plaza_history WHERE TIMESTAMPDIFF(MINUTE, date_history, finish_time) > 5 and date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' " . $whereAgent)->row()->jumlah;
		$return['item']['queueWaiting'] = $this->db->query("SELECT COUNT(id) jumlah FROM queue_list where status='N' and date(tgl)>='" . $tgl1 . "' and date(tgl)<='" . $tgl2 . "' ")->row()->jumlah;//. $whereAreaQueue
		$return['item']['averageWaiting'] = round($this->db->query("SELECT AVG(TIMESTAMPDIFF(SECOND, tgl, tgl_call)) as jumlah FROM queue_list where date(tgl)>='" . $tgl1 . "' and date(tgl)<='" . $tgl2 . "' ")->row()->jumlah, 2);//. $whereAreaQueue
		$return['plazaActivity'] = array();
		$return['topCso'] = array();
		$return['rating_cso'] = array();
		$return['trendDataPackage'] = array();
		$return['bestSales'] = array();
		$return['totalCustomer'] = array();

		$dataPlaza = $this->db->query("SELECT activity,count(id) as total FROM `plaza_history` WHERE date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "'  " . $whereArea . " group by activity")->result();
		$listActivity = array('information', 'penjualan', 'registration', 'complaint');

		$resPlaza = array();
		foreach ($dataPlaza as $p) {
			$resPlaza[$p->activity] = $p->total;
		}

		foreach ($listActivity as $a) {
			$return['plazaActivity'][] = array(
				'name' => ucfirst($a == 'penjualan' ? 'purchase' : $a),
				'y' => isset($resPlaza[$a]) ? (int) $resPlaza[$a] : 0
			);
		}

		$dataQueue = $this->db->query("SELECT status,count(id) as total FROM `queue_list` WHERE date(tgl)>='" . $tgl1 . "' and date(tgl)<='" . $tgl2 . "' " . $whereAreaQueue . "  group by status")->result();
		$listQueue = array('N' => 'waiting', 'Y' => 'done');

		$resQueue = array();
		foreach ($dataQueue as $p) {
			$resQueue[$p->status] = $p->total;
		}

		foreach ($listQueue as $a => $av) {
			$return['totalCustomer'][] = array(
				'name' => ucfirst($av),
				'y' => isset($resQueue[$a]) ? (int) $resQueue[$a] : 0
			);
		}

		$dataCso = $this->db->query("SELECT u.fullName,count(ph.id) as total FROM `plaza_history` ph  inner join user u on u.id=ph.id_user WHERE date(ph.date_history)>='" . $tgl1 . "' and date(ph.date_history)<='" . $tgl2 . "' " . $whereAreaPh . " group by u.id")->result();
		foreach ($dataCso as $p) {
			$return['topCso'][] = array(
				'name' => ucfirst($p->fullName),
				'y' => (int) $p->total
			);
		}

		$dataRating = $this->db->query("select
									avg(rating_1) as avg_rating_1,
									avg(rating_2) as avg_rating_2,
									avg(rating_3) as avg_rating_3,
									avg(rating_4) as avg_rating_4,
									avg(rating_5) as avg_rating_5,
									count(id) as total_customer
									from complain
									where
									complainDate>='$tgl1' and
									complainDate<='$tgl2' and
									rating_1>0 " . $whereAgentComplaint)->result();

		$return['total_voters'] = 0;

		if (count($dataRating) > 0) {
			$dar = $dataRating[0];

			$return['rating_cso'][] = array(
				'name' => 'Friendliness',
				'y' => (int) $dar->avg_rating_1
			);
			$return['rating_cso'][] = array(
				'name' => 'Solution',
				'y' => (int) $dar->avg_rating_2
			);
			$return['rating_cso'][] = array(
				'name' => 'Price & Quality',
				'y' => (int) $dar->avg_rating_3
			);
			$return['rating_cso'][] = array(
				'name' => 'Network',
				'y' => (int) $dar->avg_rating_4
			);
			$return['rating_cso'][] = array(
				'name' => 'Facilities',
				'y' => (int) $dar->avg_rating_5
			);

			$return['total_voters'] = $dar->total_customer;
		}

		$dataAgent = $this->db->query("SELECT * FROM `user` WHERE privilegeId in ('4') and active=1 " . $whereArea . " order by fullName")->result();

		foreach ($dataAgent as $row) {

			if ($row->statusLogin == 1) {
				$statusAgent = 'agentLogin';
			} else {
				$statusAgent = 'agentLogout';
			}
			$return['tableAgent'][] = array('status' => $statusAgent, 'nama' => $row->fullName, 'pic' => $row->photo);
		}

		$resAg = $this->db->query("select fullName as name, id, username,outlet_number
									from user
									where outlet_number is not null
									" . $whereArea . "
									order by fullName asc");
		$listAgent = $resAg->result();

		$list_number = [];
		$map_id_user = [];

		foreach ($listAgent as $la) {
			$list_number[] = '"670' . $la->outlet_number . '"';
			$map_id_user['670' . $la->outlet_number] = $la->name;
		}

		$response_cso = '';

		if (count($list_number) > 0) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.telkomcel.tl/tetum-dashboard/api/vas/transaction/list-by-cso',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => '{
										"page": 1,
										"limit": 1000000000000,
										"filter": [
											{
												"key": "vt.status",
												"value": "SUCCESS"
											},
											{
												"key": "start_date",
												"value": "' . $tgl1 . '"
											},
											{
												"key": "end_date",
												"value": "' . $tgl2 . '"
											},
											{
												"key": "ref_msisdn",
												"value": [' . implode(',', $list_number) . ']
											}
										]
									}',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: Basic MzYwQHRldHVtLnRsOkN1NXQzNm9AMjAyNQ=='
				),
			));

			$response_cso = curl_exec($curl);

			curl_close($curl);
		}

		$listData_cso = json_decode($response_cso, true);

		if ($listData_cso['code'] == 200) {
			// echo '<pre>';
			// print_r($listData_cso['data']);

			$res_per_sales = array();
			$res_per_product = array();

			foreach ($listData_cso['data'] as $d) {
				$res_per_sales[$d['customer_msisdn']] += 1;

				if ($d['product_type'] == 'Internet Package') {
					$res_per_product[$d['product_category'] . ' ' . $d['product_name']] += 1;
				}
			}

			foreach ($res_per_product as $a => $v) {
				$return['trendDataPackage'][] = array(
					'name' => ucfirst($a),
					'y' => $v
				);
			}
			foreach ($res_per_sales as $a => $v) {
				$return['bestSales'][] = array(
					'name' => ucfirst($map_id_user[$a]),
					'y' => $v
				);
			}
		}

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}

	public function getDashboardPlaza($tgl1 = '', $tgl2 = '')
	{
		if (!$tgl1) {
			$tgl1 = date('Y-m-d');
		}
		if (!$tgl2) {
			$tgl2 = date('Y-m-d');
		}

		$return = array();
		$return['plazaActivity'] = array();
		$return['topCso'] = array();
		$return['rating_cso'] = array();

		$id_plaza = $this->session->userdata('id_counter_setting');

		$dataPlaza = $this->db->query("SELECT activity,count(id) as total FROM `plaza_history` WHERE date(date_history)>='" . $tgl1 . "' and date(date_history)<='" . $tgl2 . "' and id_plaza='" . $id_plaza . "' group by activity")->result();

		$listActivity = array('information', 'penjualan', 'registration', 'complaint');

		$resPlaza = array();
		foreach ($dataPlaza as $p) {
			$resPlaza[$p->activity] = $p->total;
		}

		foreach ($listActivity as $a) {
			$return['plazaActivity'][] = array(
				'name' => ucfirst($a == 'penjualan' ? 'purchase' : $a),
				'y' => isset($resPlaza[$a]) ? (int) $resPlaza[$a] : 0
			);
		}

		$dataCso = $this->db->query("SELECT u.fullName,count(ph.id) as total FROM `plaza_history` ph inner join user u on u.id=ph.id_user WHERE date(ph.date_history)>='" . $tgl1 . "' and date(ph.date_history)<='" . $tgl2 . "' and ph.id_plaza='" . $id_plaza . "' group by u.id")->result();

		foreach ($dataCso as $p) {
			$return['topCso'][] = array(
				'name' => ucfirst($p->fullName),
				'y' => (int) $p->total
			);
		}

		$dataRating = $this->db->query("select
									avg(rating_1) as avg_rating_1,
									avg(rating_2) as avg_rating_2,
									avg(rating_3) as avg_rating_3,
									avg(rating_4) as avg_rating_4,
									avg(rating_5) as avg_rating_5,
									count(id) as total_customer
									from complain
									where
									complainDate>='$tgl1' and
									complainDate<='$tgl2' and
									rating_1>0 and
									id_counter_setting=$id_plaza")->result();

		$return['total_voters'] = 0;

		if (count($dataRating) > 0) {
			$dar = $dataRating[0];

			$return['rating_cso'][] = array(
				'name' => 'Friendliness',
				'y' => (int) $dar->avg_rating_1
			);
			$return['rating_cso'][] = array(
				'name' => 'Solution',
				'y' => (int) $dar->avg_rating_2
			);
			$return['rating_cso'][] = array(
				'name' => 'Price & Quality',
				'y' => (int) $dar->avg_rating_3
			);
			$return['rating_cso'][] = array(
				'name' => 'Network',
				'y' => (int) $dar->avg_rating_4
			);
			$return['rating_cso'][] = array(
				'name' => 'Facilities',
				'y' => (int) $dar->avg_rating_5
			);

			$return['total_voters'] = $dar->total_customer;
		}

		header('Content-Type:text/json');
		echo json_encode($return, JSON_PRETTY_PRINT);
	}
}

/* End of file CallController.php */
