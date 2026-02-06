<?php

defined('BASEPATH') or exit('No direct script access allowed');

class IVRCampaign extends CI_Controller
{

	private $path = 'page/ivr_campaign/';

	public function __construct()
	{
		parent::__construct();
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
				$update_last_activity                  = array();
				$update_last_activity['last_activity'] = date('Y-m-d H:i:s');

				$this->db->where('id', $this->session->userdata('id'));
				$this->db->update('user', $update_last_activity);
			}
		}
		$this->load->model('IvrCampaignModel', 'fm');
		$this->load->model('TitleModel', 'tm');
	}

	public function index()
	{
		$d = [
			'title'    => "IVR Campaign :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'campaign'
		];
		$this->load->view('page/_main', $d);
	}

	public function report_campaign_ivr()
	{

		$tgl1  = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2  = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$page  = $this->input->get('page') ? $this->input->get('page') : 1;
		$all   = $this->input->get('all') ? $this->input->get('all') : false;
		$limit = $all ? 1000000000000 : 250;

		$reportData     = '';
		$reportChart    = array();
		$reportChartPre = array();
		$totalPage      = 0;

		$listData  = $this->db->select('
		ivr.id_campaign,
		COUNT(IF(ivr.status=\'waiting\',1, NULL)) as status_waiting,
		COUNT(IF(ivr.status=\'done\',1, NULL)) as status_done,
		COUNT(IF(ivr.status=\'cancel\',1, NULL)) as status_cancel,
		COUNT(IF(!isnull(ivr.status),1, NULL)) as status_total,
		COUNT(IF(ivr.result=\'success\',1, NULL)) as result_success,
		COUNT(IF(ivr.result=\'no-answer\',1, NULL)) as result_no_answer,
		COUNT(IF(ivr.result=\'out-of-coverage\',1, NULL)) as result_out_of_coverage,
		COUNT(IF(ivr.result=\'busy\',1, NULL)) as result_busy,
		COUNT(IF(ivr.result=\'cancel\',1, NULL)) as result_cancel,
		COUNT(IF(!isnull(ivr.result),1, NULL)) as result_total,
		ic.title, ic.createDate	
		')
			->from('ivr_campaign_result ivr, ivr_campaign ic')
			->where('ivr.id_campaign=ic.id')
			->where('date(ivr.date)>=', $tgl1)
			->where('date(ivr.date)<=', $tgl2)
			->group_by('ivr.id_campaign')
			->order_by('ivr.date', 'asc')->get()->result();

		$totalPage = ceil(count($listData) / $limit);



		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td><a href="' . base_url('IVRCampaign/report_campaign_ivr_detail?id=' . $ld->id_campaign) . '" target="_blank">' . $ld->title . '</a></td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->createDate)) . '</td>';
					$reportData .= '<td>' . $ld->status_waiting . '</td>';
					$reportData .= '<td>' . $ld->status_done . '</td>';
					$reportData .= '<td>' . $ld->status_cancel . '</td>';
					$reportData .= '<td>' . $ld->status_total . '</td>';
					$reportData .= '<td>' . $ld->result_success . '</td>';
					$reportData .= '<td>' . $ld->result_no_answer . '</td>';
					$reportData .= '<td>' . $ld->result_out_of_coverage . '</td>';
					$reportData .= '<td>' . $ld->result_busy . '</td>';
					$reportData .= '<td>' . $ld->result_cancel . '</td>';
					$reportData .= '<td>' . $ld->result_total . '</td>';
					$reportData .= '</tr>';
				}

				!isset($reportChartPre['waiting']) ? $reportChartPre['waiting'] = 0 : '';
				!isset($reportChartPre['done']) ? $reportChartPre['done'] = 0 : '';
				!isset($reportChartPre['cancel']) ? $reportChartPre['cancel'] = 0 : '';

				$reportChartPre['waiting'] += $ld->status_waiting > 0 ? $ld->status_waiting : 0;
				$reportChartPre['done'] += $ld->status_done > 0 ? $ld->status_done : 0;
				$reportChartPre['cancel'] += $ld->status_cancel > 0 ? $ld->status_cancel : 0;
			}

			foreach ($reportChartPre as $kc => $rc) {
				$reportChart[] = array($kc, $rc);
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no ivr campaign performance in this period</td></tr>';
		}

		$d = [
			'title'    => "Report Performance IVR Campaign :: Telkomcel Helpdesk",
			'linkView'    => $this->path . 'report',
			'tgl1'        => $tgl1,
			'tgl2'        => $tgl2,
			'page'        => $page,
			'reportData'  => $reportData,
			'reportChart' => $reportChart,
			'totalPage'   => $totalPage,
			'all'         => $all
		];

		if ($all) {
			$this->load->view($this->path . 'report', $d);
		} else {
			$this->load->view('page/_main', $d);
		}
	}

	public function report_campaign_ivr_detail()
	{

		$id_campaign  = $this->input->get('id');
		$page  = $this->input->get('page') ? $this->input->get('page') : 1;
		$all   = $this->input->get('all') ? $this->input->get('all') : false;
		$limit = $all ? 1000000000000 : 250;

		$reportData     = '';
		$totalPage      = 0;

		$listData  = $this->db->select('ivr.*')
			->from('ivr_campaign_result ivr')
			->where('ivr.id_campaign=', $id_campaign)
			->order_by('ivr.date', 'asc')->get()->result();

		$totalPage = ceil(count($listData) / $limit);

		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td>' . $ld->msisdn . '</td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->date)) . '</td>';
					$reportData .= '<td>' . ucfirst($ld->status) . '</td>';
					$reportData .= '<td>' . ucfirst($ld->result) . '</td>';
					$reportData .= '</tr>';
				}
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no ivr campaign performance detail in this campaign</td></tr>';
		}

		$d = [
			'title'    => "Report Performance IVR Campaign Detail :: Telkomcel Helpdesk",
			'linkView'    => $this->path . 'report_detail',
			'id_campaign'        => $id_campaign,
			'page'        => $page,
			'reportData'  => $reportData,
			'totalPage'   => $totalPage,
			'all'         => $all
		];

		if ($all) {
			$this->load->view($this->path . 'report_detail', $d);
		} else {
			$this->load->view('page/_main', $d);
		}
	}

	public function dtshow()
	{
		echo $this->fm->dtshow();
	}

	public function getById()
	{
		$id = $this->input->get('id');

		echo json_encode($this->fm->get($id)->row());
	}

	public function delete()
	{
		$id = $this->input->get('id');
		if ($id != '') {
			$id = explode(',', $id);
			$this->fm->de($id);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->session->set_flashdata('delete', 'Success Delete Data');
		redirect($_SERVER['HTTP_REFERER']);
	}

	function submit()
	{
		$data = $this->input->post();

		$mp3File = '';
		$ext_mp3     = strtolower(pathinfo($_FILES['sound']['name'], PATHINFO_EXTENSION));

		if (isset($_FILES['sound']['tmp_name']) && ($ext_mp3 == 'mp3')) {

			$uniq = 'announcement' . $data['slot'];
			$mp3File = $uniq . '.' . $ext_mp3;
			$wavFile = $uniq . '.wav';
			$slnFile = $uniq . '.sln';

			if (!is_dir('ivr_campaign')) {
				mkdir('ivr_campaign', 0777);
			}

			if (!move_uploaded_file($_FILES['sound']['tmp_name'], 'ivr_campaign/' . $mp3File)) {
				$mp3File = '';
			} else {
				// shell_exec('/Applications/XAMPP/xamppfiles/htdocs/helpdesk/ivr_campaign/convert.sh 2>&1');
				// shell_exec('sudo /home/admin/web/helpdesk.telkomcel.tl/public_html/ivr_campaign/convert.sh 2>&1');
			}

			exec('sudo whoami');
			exec('sudo /home/admin/web/helpdesk.telkomcel.tl/public_html/ivr_campaign/convert.sh 2>&1');

			$data['mp3'] = $mp3File;
		}

		if ($data['id']) {
			$data['updateDate'] = date('Y-m-d H:i:s');
			$data['updateBy'] = $this->session->userdata('id');

			$this->fm->update($data, $data['id']);
		} else {
			$data['createDate'] = date('Y-m-d H:i:s');
			$data['createBy'] = $this->session->userdata('id');

			$data['id'] = $this->fm->save($data);
		}

		$ext_csv       = strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION));

		if (isset($_FILES['csv']['tmp_name']) && $ext_csv == 'csv') {
			$csv = file_get_contents($_FILES['csv']['tmp_name']);

			$xcsv = explode("\n", $csv);

			$id_campaign = $data['id'];

			if ($id_campaign) {
				$this->fm->delete_csv($id_campaign);
			}

			$rst = array();

			foreach ($xcsv as $num) {
				if ($num >= 67073000000 && $num <= 67074999999) {

					$rs = array();

					$rs['id_campaign'] = $id_campaign;
					$rs['msisdn'] = $num;
					$rs['status'] = 'waiting';

					$rst[] = $rs;
				}
			}

			$this->fm->save_csv($rst);
		}
	}
}
