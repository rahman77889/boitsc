<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ComplainHandling extends CI_Controller
{

	public $tabel = 'complain';
	private $path = 'page/complain/';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');

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
		$this->load->model('ComplainHandlingModel', 'chm');
		$this->load->model('CategoryModel', 'cm');
		$this->load->model('SubCategoryModel', 'scm');
		$this->load->model('UnitModel', 'um');
		$this->load->model('UsersModel', 'usm');
	}

	public function detail_complain($id = '', $idh = '')
	{
		$this->create_complain($id, $idh);
	}

	public function create_complain($id = '', $idh = '')
	{

		$msisdn           = '';
		$customerName     = '';
		$bts_sitename     = '';
		$btsUrlMap        = '';
		$complaintTypeVal = '';
		$call_center      = '';
		$channel          = '';

		if ($this->input->get('msisdn')) {
			$msisdn       = $this->input->get('msisdn');
			$customerName = $this->input->get('name');
			$channel      = $this->input->get('channel');
			$queue_no     = $this->input->get('queue_no') ? $this->input->get('queue_no') : 0;
			$bts_info     = $this->getBTSCode($msisdn);
			if ($bts_info && $bts_info != '4G') {
				$bts_sitename = $bts_info;
				$mapRes       = $this->db->select('urlmap')->from('bts')->where('site_id', $bts_info)->get()->row();
				if (isset($mapRes->urlmap)) {
					$btsUrlMap = $mapRes->urlmap;
				}
			}
			$sc          = (int) $this->input->get('sc');
			$call_center = str_replace('+', '', $this->input->get('call_center'));

			if ($sc >= 1000 && $sc <= 1999) { //prepaid
				$complaintTypeVal = '2';
			} elseif ($sc >= 2000 && $sc <= 2999) { //hybrid
				$complaintTypeVal = '5';
			} elseif ($sc >= 3000 && $sc <= 39999) { //postpaid
				$complaintTypeVal = '1';
			}
		}

		if ($this->input->get('name')) {
			$customerName = $this->input->get('name');
			$channel      = $this->input->get('channel');
		}

		$restFaq = $this->chm->getListFAQ();

		$listFaq = array();
		foreach ($restFaq as $faq) {
			$listFaq[] = '<option data-pdf="' . (is_file('pdf/' . $faq['faqFile']) ? base_url('pdf/' . $faq['faqFile']) : '') . '" data-video="' . (is_file('video/' . $faq['videoFile']) ? base_url('video/' . $faq['videoFile']) : '') . '" data-embed="' . $faq['videoUrl'] . '" value="' . $faq['faqId'] . '">' . $faq['categoryTitle'] . ' - ' . $faq['faqTitle'] . '</option>';
		}

		$listFaq = implode('', $listFaq);

		$restComplaintType = $this->chm->getListComplaintType();

		$listComplaintType = array();
		foreach ($restComplaintType as $ct) {
			$listComplaintType[] = '<option value="' . $ct['id'] . '">' . $ct['name'] . '</option>';
		}

		$listComplaintType = '<option value="">--Choose--</option>' . implode('', $listComplaintType);

		$resCategory = $this->cm->getCategoryActive()->result_array();
		// var_dump($resCategory);

		$listCategory = array();
		foreach ($resCategory as $ct) {
			$listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
		}

		$listCategory = '<option value="">--Choose--</option>' . implode('', $listCategory);

		$restComplainStatus = $this->chm->getListComplaintStatus();

		$listComplainStatus = array();
		foreach ($restComplainStatus as $ct) {
			$listComplainStatus[] = '<option value="' . $ct['status'] . '">' . $ct['name'] . '</option>';
		}

		$listComplainStatus = '<option value="">--Choose--</option>' . implode('', $listComplainStatus);

		$restUnit = $this->um->getUnitList()->result_array();

		$listUnit = array();
		foreach ($restUnit as $ct) {
			$listUnit[] = '<option value="' . $ct['id'] . '">' . $ct['unitName'] . '</option>';
		}

		$listUnit = '<option value="">--Choose--</option>' . implode('', $listUnit);

		$restBts = $this->db->select('site_id,site_name')->from('bts')->order_by('site_name', 'asc')->get()->result();

		$listBts = array();
		foreach ($restBts as $ct) {
			$listBts[] = '<option value="' . $ct->site_id . '">' . $ct->site_name . '</option>';
		}

		$listBts = '<option value="">--Choose--</option>' . implode('', $listBts);

		$d = [
			'title'              => "Complaint Handling - Create Complaint :: Telkomcel",
			'linkView'           => $this->path . 'create_complain',
			'listFaq'            => $listFaq,
			'listComplaintType'  => $listComplaintType,
			'listCategory'       => $listCategory,
			'listComplainStatus' => $listComplainStatus,
			'listBts'            => $listBts,
			'listUnit'           => $listUnit,
			'username'           => $this->session->userdata('username'),
			'transactionCode'    => 'Will generated after submit',
			'id'                 => $id,
			'idh'                => $idh,
			'subCategory'        => '',
			'close'              => '',
			'mdnProblem'         => $msisdn,
			'btsLocation'        => $bts_sitename,
			'btsUrlMap'          => $btsUrlMap,
			'customerName'       => $customerName,
			'complaintTypeVal'   => $complaintTypeVal,
			'call_center'        => $call_center,
			'channel'            => $channel,
			'queue_no'			 => $queue_no
		];

		if ($id) {
			$comp = $this->chm->getComplaint($id)->row_array();

			if ($comp['status'] == 'C' && !$idh) { //close
				$idh = $this->chm->getLastHistoryId($id);
			}

			if ($idh) {
				$comph = $this->chm->getComplaintHistory($idh)->row_array();
			}

			//            unset($comp['transactionCode']);

			$comp['listCategory']      = '<option value="">' . $this->cm->getCategory($comp['categoryId'])->row()->categoryName . '</option>';
			$comp['subCategory']       = '<option value="">' . (isset($this->scm->getSubCategory($comp['subCategoryId'])->row()->subCategory) ? $this->scm->getSubCategory($comp['subCategoryId'])->row()->subCategory : '') . '</option>';
			$comp['listComplaintType'] = '<option value="">' . $this->chm->getListComplaintType($comp['complaintType'])[0]['name'] . '</option>';
			$comp['close']             = $comp['status'] == 'C' ? '1' : '';

			if ($idh) {
				unset($d['listComplainStatus']);
				unset($d['listUnit']);

				$unitInfo = $this->um->getUnitInfo($comph['unitId'])->row();

				$comph['listComplainStatus'] = '<option value="">' . $this->chm->getListComplaintStatus($comph['status'])[0]['name'] . '</option>';
				$comph['listUnit']           = '<option value="">' . (isset($unitInfo->unitName) ? $unitInfo->unitName : ' - ') . '</option>';
				$comph['userId']             = $this->usm->getUsers($comph['userId'])->row()->fullName;
			}

			unset($d['mdnProblem']);
			unset($d['btsLocation']);
			unset($d['customerName']);
			unset($d['close']);
			unset($d['listCategory']);
			unset($d['subCategory']);
			unset($d['listComplaintType']);
			unset($d['transactionCode']);

			$d = $d + $comp;

			unset($d['userId']);

			if ($idh) {
				$d = $d + $comph;
			}
		} else {
			$d['complainDate'] = date('Y-m-d');
			$d['complainTime'] = date('H:i:s');
		}

		$this->load->view('page/_main', $d);
	}

	public function generateTransactionCode()
	{
		//TL-0002/EMAA85001/13-2018

		$username  = $this->session->userdata('username');
		$number    = (string) ($this->chm->getComplainNumber() + 1);
		$numberMul = 4 - strlen($number);
		$number    = str_repeat('0', $numberMul) . $number;

		$kode = 'TL-' . $number . '/' . $username . '/' . date('d') . '-' . date('Y');

		return $kode;
	}

	public function getSubCategory($categoryId)
	{
		$resSubCategory = $this->scm->getSubCategoryActive($categoryId)->result_array();
		foreach ($resSubCategory as $ct) {
			echo '<option value="' . $ct['subCategoryId'] . '">' . $ct['subCategory'] . '</option>';
		}
	}

	public function showdthistory($idComplain = '')
	{
		echo $this->chm->dtshowhistory($idComplain);
	}

	public function inComplaint()
	{
		$log = [];

		//        if ($this->input->post('survei')) {
		//            $url = 'http://localhost:8000/survei/' . $this->input->post('survei') . '/' . $this->input->post('mdnProblem');
		//
		//            file_get_contents($url);
		//        }

		$object = $this->input->post();
		$object = json_encode($object);
		$object = json_decode($object, true);

		if ($this->session->has_userdata('id_counter_setting')) {
			$object['id_counter_setting'] = $this->session->userdata('id_counter_setting');
		}
		if ($this->session->has_userdata('id_counter')) {
			$object['id_counter'] = $this->session->userdata('id_counter');
		}


		if ($object['transactionCode'] == 'Will generated after submit') {
			$object['transactionCode'] = $this->generateTransactionCode();
		}

		$in = $this->chm->inComplaint($object);
		if ($in) {
			$log = [
				'msg' => 'Success Add Complaint Handling!',
				'id'  => $in,
				'idh' => $this->db->insert_id()
			];
		} else {
			$log = [
				'msg' => 'Failed Add Complaint Handling!',
				'id'  => ''
			];
		}

		echo json_encode($log);
	}

	public function upComplaint()
	{
		$id     = $this->input->post('id');
		$object = $this->input->post();
		$object = json_encode($object);
		$object = json_decode($object, true);

		unset($object['id']);
		unset($object['createDate']);
		unset($object['userId']);

		// // Insert Log Actvitiy
		// $msgLog = "User : " . $this->session->userdata('username') . " -> Update CDR";
		// $this->lm->id_user = $this->session->userdata('id');
		// $this->lm->inLogActivity($msgLog, json_encode($obj));

		echo json_encode($this->chm->upComplaint($object, $id));
	}

	public function inComplaintHistory()
	{
		$log = [];

		if ($this->input->post('status') == 'E') {
			$detailUnit          = $this->db->select('email,name')->from('unitinfo')->where('unit_id', $this->input->post('unitId'))->get()->row();
			$emailUnit           = $detailUnit->email;
			$emailNama           = $detailUnit->name;
			$unitJenis           = $this->db->select('unitName')->from('unit')->where('id', $this->input->post('unitId'))->get()->row()->unitName;
			$detailComplaint     = $this->db->select('*')->from('complain')->where('id', $this->input->post('id'))->get()->row();
			$detailComplaintType = $this->db->select('name')->from('complaintype')->where('id', $detailComplaint->complaintType)->get()->row()->name;
			$detailCategory      = $this->db->select('categoryName')->from('category')->where('categoryId', $detailComplaint->categoryId)->get()->row()->categoryName;
			$detailSubCategory   = $this->db->select('subCategory')->from('subcategory')->where('subCategoryId', $detailComplaint->subCategoryId)->get()->row();
			$detailUserCreate    = $this->db->select('fullName')->from('user')->where('id', $detailComplaint->userId)->get()->row()->fullName;

			if (isset($detailSubCategory->subCategory)) {
				$detailSubCategory = $detailSubCategory->subCategory;
			}
			ob_start();
?>
			<html>
			<title>Complaint Handling Escalation</title>

			<body>
				<h4>Halo, <?php echo $emailNama; ?> - <?php echo $unitJenis; ?></h4>
				<div>
					<p>According to new complaint from application helpdesk telkomcel, there was a new complaint escalation to your unit.</p>
					<p>Below detail refer that comlaint : </p>
					<table style="width:100%;margin-top:2rem;">
						<thead>
							<tr>
								<th style="text-align:left">Parameter</th>
								<th style="text-align:left">Value</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Transaction Code</td>
								<td><?php echo $detailComplaint->transactionCode; ?></td>
							</tr>
							<tr>
								<td>MdnProblem</td>
								<td><?php echo $detailComplaint->mdnProblem; ?></td>
							</tr>
							<tr>
								<td>Customer Name</td>
								<td><?php echo $detailComplaint->customerName; ?></td>
							</tr>
							<tr>
								<td>Complaint Type</td>
								<td><?php echo $detailComplaintType; ?></td>
							</tr>
							<tr>
								<td>Contact Person Customer</td>
								<td><?php echo $detailComplaint->contactPersonCustomer; ?></td>
							</tr>
							<tr>
								<td>District</td>
								<td><?php echo $detailComplaint->district; ?></td>
							</tr>
							<tr>
								<td>BTS Location</td>
								<td><?php echo $detailComplaint->btsLocation; ?></td>
							</tr>
							<tr>
								<td>Detail Location Customer</td>
								<td><?php echo $detailComplaint->detailLocationCustomer; ?></td>
							</tr>
							<tr>
								<td>Complaint Date</td>
								<td><?php echo $detailComplaint->complainDate; ?></td>
							</tr>
							<tr>
								<td>Complaint Time</td>
								<td><?php echo $detailComplaint->complainTime; ?></td>
							</tr>
							<tr>
								<td>Category</td>
								<td><?php echo $detailCategory; ?></td>
							</tr>
							<tr>
								<td>Sub Category</td>
								<td><?php echo $detailSubCategory; ?></td>
							</tr>
							<tr>
								<td>Detail Complaint</td>
								<td><?php echo $detailComplaint->detailComplain; ?></td>
							</tr>
							<tr>
								<td>User Create By</td>
								<td><?php echo $detailUserCreate; ?></td>
							</tr>
							<tr>
								<td>Solution</td>
								<td><?php echo $this->input->post('solution'); ?></td>
							</tr>
							<tr>
								<td>Notes</td>
								<td><?php echo $this->input->post('notes'); ?></td>
							</tr>
							<tr>
								<td>Unit Name</td>
								<td><?php echo $unitJenis; ?></td>
							</tr>
							<tr>
								<td>Solved Date</td>
								<td><?php echo $this->input->post('solvedDate'); ?></td>
							</tr>
							<tr>
								<td>Solved Time</td>
								<td><?php echo $this->input->post('solvedTime'); ?></td>
							</tr>
						</tbody>
					</table>
					<p style="margin-top:4rem;">
						Thank You
					</p>
				</div>
			</body>

			</html>
<?php
			$konten = ob_get_clean();
		}

		$in = $this->chm->inComplaintHistory();
		if ($in) {
			$log = [
				'msg' => 'Success Add Complaint Handling!',
			];
		} else {
			$log = [
				'msg' => 'Failed Add Complaint Handling!',
			];
		}

		echo json_encode($log);
	}

	public function upComplaintHistory()
	{
		$id     = $this->input->post('id');
		$object = $this->input->post();
		$object = json_encode($object);
		$object = json_decode($object, true);

		unset($object['id']);
		unset($object['createDate']);
		unset($object['userId']);

		// // Insert Log Actvitiy
		// $msgLog = "User : " . $this->session->userdata('username') . " -> Update CDR";
		// $this->lm->id_user = $this->session->userdata('id');
		// $this->lm->inLogActivity($msgLog, json_encode($obj));

		echo json_encode($this->chm->upComplaintHistory($object, $id));
	}

	public function showdtcomplaint($status = '')
	{
		echo $this->chm->dtshowcomplaint($status);
	}

	public function inbox_complain()
	{
		$resCategory = $this->cm->getCategoryActive()->result_array();

		$listCategory = array();
		foreach ($resCategory as $ct) {
			$listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
		}

		$listCategory = '<option value="">--Choose--</option>' . implode('', $listCategory);

		$restUnit = $this->um->getUnitList()->result_array();

		$listUnit = array();
		foreach ($restUnit as $ct) {
			$listUnit[] = '<option value="' . $ct['id'] . '">' . $ct['unitName'] . '</option>';
		}

		$listUnit = '<option value="">--Choose--</option>' . implode('', $listUnit);

		$restUser = $this->usm->getUsers()->result_array();

		$listUser = array();
		foreach ($restUser as $ct) {
			$listUser[] = '<option value="' . $ct['id'] . '">' . $ct['fullName'] . '</option>';
		}

		$listUser = '<option value="">--Choose--</option>' . implode('', $listUser);

		$restComplaintType = $this->db->select('*')->from('complaintype')->get()->result_array();

		$listComplainType = array();
		foreach ($restComplaintType as $ct) {
			$listComplainType[] = '<option value="' . $ct['id'] . '">' . $ct['name'] . '</option>';
		}

		$listComplainType = '<option value="">--All Complaint Type--</option>' . implode('', $listComplainType);

		$d = [
			'title'            => "Complain Handling - My Inbox Complaint :: Telkomcel",
			'linkView'         => $this->path . 'inbox_complain',
			'listCategory'     => $listCategory,
			'listComplainType' => $listComplainType,
			'listUnit'         => $listUnit,
			'listUser'         => $listUser
		];
		$this->load->view('page/_main', $d);
	}

	public function group_inbox()
	{
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
			'title'            => "Complain Handling - Group inbox Complaint :: Telkomcel",
			'linkView'         => $this->path . 'group_inbox',
			'listCategory'     => $listCategory,
			'listUnit'         => $listUnit,
			'listUser'         => $listUser,
			'listComplainType' => $listComplainType,
		];
		$this->load->view('page/_main', $d);
	}

	public function inbox_caring()
	{
		$resCategory = $this->cm->getCategoryActive()->result_array();

		$listCategory = array();
		foreach ($resCategory as $ct) {
			$listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
		}

		$listCategory = '<option value="">--Choose All Category--</option>' . implode('', $listCategory);

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
			'title'            => "Complain Handling - Inbox Caring :: Telkomcel",
			'linkView'         => $this->path . 'inbox_caring',
			'listCategory'     => $listCategory,
			'listUnit'         => $listUnit,
			'listUser'         => $listUser,
			'listComplainType' => $listComplainType,
		];
		$this->load->view('page/_main', $d);
	}

	public function my_inbox()
	{
		$resCategory = $this->cm->getCategoryActive()->result_array();

		$listCategory = array();
		foreach ($resCategory as $ct) {
			$listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
		}

		$listCategory = '<option value="">--Choose--</option>' . implode('', $listCategory);

		$restUnit = $this->um->getUnitList()->result_array();

		$listUnit = array();
		foreach ($restUnit as $ct) {
			$listUnit[] = '<option value="' . $ct['id'] . '">' . $ct['unitName'] . '</option>';
		}

		$listUnit = '<option value="">--Choose--</option>' . implode('', $listUnit);

		$restUser = $this->usm->getUsers()->result_array();

		$listUser = array();
		foreach ($restUser as $ct) {
			$listUser[] = '<option value="' . $ct['id'] . '">' . $ct['fullName'] . '</option>';
		}

		$listUser = '<option value="">--Choose--</option>' . implode('', $listUser);

		$d = [
			'title'        => "Complain Handling - My Inbox :: Telkomcel",
			'linkView'     => $this->path . 'my_inbox',
			'listCategory' => $listCategory,
			'listUnit'     => $listUnit,
			'listUser'     => $listUser
		];
		$this->load->view('page/_main', $d);
	}

	private function getBTSCode($msisdn)
	{
		//+67073020425 => 3G
		$ret = '';
		if (strlen($msisdn) > 5) {
			$msisdn = str_replace(array('*', '+'), '', $msisdn);
			// $url    = 'http://10.70.2.189:8080/mobicents/gmlc/rest?msisdn=' . $msisdn;
			$url    = 'http://172.20.212.17:8080/location/info?msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&channel=400';
			$ctn    = file_get_contents($url);
			$ctx    = explode(',', $ctn);

			// if ($ctx[0] == 'mcc=514') {
			$cellid = str_replace('cellid=', '', $ctx[3]);
			$lac = str_replace('lac=', '', $ctx[2]);

			// $ret = $this->db->select('site_id')->from('sitemap')->or_where('ci2g', $cellid)->or_where('ci3g_850', $cellid)->or_where('ci3g_2100_1', $cellid)->or_where('ci3g_2100_2', $cellid)->or_where('ci3g_2100_3', $cellid)->limit(1)->get()->row();
			$ret = $this->db->select('site_id')->from('sitemap')->where('cell_id', $cellid)->where('lac', $lac)->limit(1)->get()->row();
			$ret = isset($ret->site_id) ? $ret->site_id : '';
			// } elseif ($ctx[0] == 'mcc=-1') {
			// $ret = '4G';
			// }
		}
		return $ret;
	}
}
