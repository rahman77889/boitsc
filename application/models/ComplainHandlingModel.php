<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ComplainHandlingModel extends CI_Model
{

	public $tabel        = 'complain';
	public $tabelHistory = 'complainthistory';

	public function getListFAQ()
	{

		$q = $this->db->select('(select name_title from title where title.id_title=faq.id_title) as faqTitle,(select categoryName from category where category.categoryId=faq.categoryId) as categoryTitle,faqFile,videoUrl,videoFile,faqId')->from('faq')->where('status', 'Y')->order_by('categoryTitle asc, faqTitle asc')->get()->result_array();

		return $q;
	}

	public function getListComplaintType($id = '')
	{

		$this->db->select('*')->from('complaintype');

		if ($id) {
			$this->db->where('id', $id);
		}

		$q = $this->db->get()->result_array();

		return $q;
	}

	public function getListComplaintStatus($id = '')
	{

		$this->db->select('*')->from('complainstatus');

		if ($id) {
			$this->db->where('status', $id);
		}

		$q = $this->db->get()->result_array();

		return $q;
	}

	public function getComplainNumber()
	{
		return $this->db->select('id')->from($this->tabel)->where('userId', $this->session->userdata('id'))->where('date(createDate)', date('Y-m-d'))->get()->num_rows();
	}

	public function groupInboxTehnicalCco($status = '')
	{
		if ($status == '') {
			$status = $this->input->get('groupInboxTehnicalCco');
		}

		if ($status == "N") {
			$q = 'NO';
		} elseif ($status == "Y") {
			$q = 'YES';
		}

		return $q;
	}

	public function groupInboxTehnicalVas($status = '')
	{
		if ($status == '') {
			$status = $this->input->get('groupInboxTehnicalVas');
		}

		if ($status == "N") {
			$q = 'NO';
		} elseif ($status == "Y") {
			$q = 'YES';
		}

		return $q;
	}

	public function statusActive($status = '')
	{
		if ($status == '') {
			$status = $this->input->get('statusActive');
		}

		if ($status == "N") {
			$q = 'No Active';
		} elseif ($status == "Y") {
			$q = "Active";
		}

		return $q;
	}

	public function dtshowcomplaint($status = '')
	{

		$this->load->model('CategoryModel', 'cat');
		$this->load->model('SubCategoryModel', 'subcat');

		// Definisi
		$condition = '';
		$data      = [];

		$CI = &get_instance();
		$CI->load->model('DataTable', 'dt');

		// Set table name
		$CI->dt->table         = $this->tabel;
		// Set orderable column fields
		$CI->dt->column_order  = array(null, 'transactionCode', 'customerName', 'mdnProblem', 'categoryId', 'subCategoryId', 'status', 'notes', 'Last Update', 'SLG');
		// Set searchable column fields
		$CI->dt->column_search = array('transactionCode', 'customerName', 'mdnProblem');
		// Set select column fields
		$CI->dt->select        = $this->tabel . '.*';
		// Set default order
		$CI->dt->order         = array($this->tabel . '.createDate' => 'ASC');

		$filter = $this->input->get();

		$uc = 0;
		if (isset($filter['transactionCode'])) {
			$condition = array();

			foreach ($filter as $k => $v) {
				if ($v) {
					$condition[$uc] = array();

					if ($k == 'unitId' || $k == 'userId') {

						//                        $listCIF  = $this->db->select('complainId')->from($this->tabelHistory)->where($k, $v)->group_by('complainId')->get()->result_array();
						//                        $listCIFR = array();
						//
						//                        foreach ($listCIF as $vc) {
						//                            $listCIFR[] = $vc['complainId'];
						//                        }

						$condition[$uc][0] = 'where_in';
						$condition[$uc][1] = $this->tabel . '.id';
						$condition[$uc][2] = '(select chis.complainId from ' . $this->tabelHistory . ' chis where chis.' . $k . '=\'' . $v . '\' group by chis.complainId)';
					}

					if ($k != 'unitId') {

						if ($k == 'startdate') {
							$condition[$uc][0] = '>=';
							$condition[$uc][1] = $this->tabel . '.complainDate';
							$condition[$uc][2] = $v;
						} elseif ($k == 'enddate') {
							$condition[$uc][0] = '<=';
							$condition[$uc][1] = $this->tabel . '.complainDate';
							$condition[$uc][2] = $v;
						} else {
							$condition[$uc][0] = 'where';
							$condition[$uc][1] = $this->tabel . '.' . $k;
							$condition[$uc][2] = $v;
						}
					}

					$uc++;
				}
			}
		}

		if ($status && $status != 'me') {
			$condition[$uc][0] = 'where';
			$condition[$uc][1] = $this->tabel . '.status';
			$condition[$uc][2] = $status;
		} else if ($status && $status == 'me') {
			$condition[$uc][0] = 'where';
			$condition[$uc][1] = $this->tabel . '.userId';
			$condition[$uc][2] = $this->session->userdata('id');
		}

		if ($this->session->userdata('tipe') == '123') {
			$condition[$uc][0] = 'where_not_in';
			$condition[$uc][1] = $this->tabel . '.channel';
			$condition[$uc][2] = '"Via Call 147", "WhatsApp 147"';
		} else if ($this->session->userdata('tipe') == '147') {
			$condition[$uc][0] = 'where_in';
			$condition[$uc][1] = $this->tabel . '.channel';
			$condition[$uc][2] = '"Via Call 147", "WhatsApp 147"';
		} else if ($this->session->userdata('tipe') == '888') {
			$condition[$uc][0] = 'where_in';
			$condition[$uc][1] = $this->tabel . '.channel';
			$condition[$uc][2] = '"Via Call 888"';
		}

		// Fetch member's records
		$dataTabel = $this->dt->getRows($_POST, $condition);

		$i = $_POST['start'];
		foreach ($dataTabel as $dt) {
			$i++;

			$categ    = $this->cat->getCategory($dt->categoryId)->row();
			$subcateg = $this->subcat->getSubCategory($dt->subCategoryId)->row();

			$data[] = array(
				// $i,
				' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id" value="' . $dt->id . '">',
				$dt->transactionCode,
				$dt->customerName,
				$dt->mdnProblem,
				isset($categ->categoryName) ? $categ->categoryName : '',
				isset($subcateg->subCategory) ? $subcateg->subCategory : '',
				$dt->channel,
				$this->getListComplaintStatus($dt->status)[0]['name'],
				$dt->detailComplain,
				date('d F Y', strtotime($dt->complainDate)) . ' ' . $dt->complainTime,
				$this->getLastUpdate($dt->id),
				$this->getSLGAll($dt->status, $dt->id, $dt->complainDate . ' ' . $dt->complainTime),
			);
		}

		$output = array(
			"draw"            => $_POST['draw'],
			"recordsTotal"    => $this->dt->countAll($condition),
			"recordsFiltered" => $this->dt->countFiltered($_POST, $condition),
			"data"            => $data,
		);

		// Output to JSON format
		return json_encode($output);
	}

	private function getLastComplaint($idComplaint)
	{
		$res = $this->db->select('notes')->from($this->tabelHistory)->where('complainId', $idComplaint)->order_by('createDate', 'desc')->limit(1)->get()->row();

		if (isset($res->notes)) {
			return $res->notes;
		}

		return '';
	}

	private function getLastUpdate($idComplaint)
	{
		$res = $this->db->select('createDate')->from($this->tabelHistory)->where('complainId', $idComplaint)->order_by('createDate', 'desc')->limit(1)->get()->row();

		if (isset($res->createDate)) {
			return date('d F Y H:i:s', strtotime($res->createDate));
		}

		return '';
	}

	private function getSLGAll($status, $idComplaint, $create_date)
	{
		$last_update = $this->getLastUpdate($idComplaint);

		if ($status != 'C') { //belum close
			$delta = time() - strtotime($last_update);
		} elseif ($status == 'C') {
			$delta = strtotime($last_update) - strtotime($create_date);
		}

		return floor($delta / 60);
	}

	private function getSLG($status, $create_date, $last_update)
	{
		if ($status != 'C') { //belum 
			$delta = time() - strtotime($last_update);
		} elseif ($status == 'C') {
			$delta = strtotime($last_update) - strtotime($create_date);
		}

		return floor($delta / 60);
	}

	public function dtshowhistory($idComplain)
	{

		$this->load->model('UnitModel', 'unit');
		$this->load->model('UsersModel', 'user');

		// Definisi
		$condition = '';
		$data      = [];

		$CI = &get_instance();
		$CI->load->model('DataTable', 'dt');

		// Set table name
		$CI->dt->table         = $this->tabelHistory;
		// Set orderable column fields
		$CI->dt->column_order  = array(null, null, 'solution', 'notes', null);
		// Set searchable column fields
		$CI->dt->column_search = array('solution', 'notes');
		// Set select column fields
		$CI->dt->select        = $this->tabelHistory . '.*, c.channel, c.complainDate, c.complainTime ';
		// Set default order
		$CI->dt->order         = array($this->tabelHistory . '.createDate' => 'DESC');

		$condition = [
			['where', $this->tabelHistory . '.complainId', $idComplain],
			['join', 'complain c', $this->tabelHistory . '.complainId=c.id', 'inner'],
		];
		// Fetch member's records
		$dataTabel = $this->dt->getRows($_POST, $condition);

		$i = $_POST['start'];
		foreach ($dataTabel as $dt) {
			$userIdName = $this->user->getUsers($dt->userId)->row();

			$i++;
			$data[] = array(
				//                 $i,
				//                ' <input onclick="editHistory(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
				'<a href="' . base_url('ComplainHandling/create_complain/' . $dt->complainId . '/' . $dt->id) . '">Detail</a>',
				date('d F Y H:i:s', strtotime($dt->createDate)),
				$dt->channel,
				isset($userIdName->fullName) ? $userIdName->fullName : '',
				$dt->solution,
				$dt->notes,
				$this->getSLG($dt->status, $dt->complainDate . ' ' . $dt->complainTime, $dt->createDate)
			);
		}

		$output = array(
			"draw"            => $_POST['draw'],
			"recordsTotal"    => $this->dt->countAll($condition),
			"recordsFiltered" => $this->dt->countFiltered($_POST, $condition),
			"data"            => $data,
		);

		// Output to JSON format
		return json_encode($output);
	}

	// funsgi delete
	public function de($categoryId)
	{
		$categoryId = implode(",", $categoryId);

		$p = $this->db->query("DELETE  FROM category WHERE categoryId IN ($categoryId)");
		return true;
	}

	public function inComplaint($object = '')
	{

		if ($object == '') {

			$object = $this->input->post();
			$object = json_encode($object);
			$object = json_decode($object, true);

			if ($this->session->has_userdata('id_counter_setting')) {
				$object['id_counter_setting'] = $this->session->userdata('id_counter_setting');
			}
			if ($this->session->has_userdata('id_counter')) {
				$object['id_counter'] = $this->session->userdata('id_counter');
			}
		}

		$object['createDate'] = date('Y-m-d H:i:s');
		$object['userId']     = $this->session->userdata('id');

		$object1 = $object;
		$object2 = $object;

		if ($object1['survei']) { //if do survei
			$object1['survei_status'] = 'W';
		} else {
			$object1['survei_status'] = 'N';
		}

		unset($object1['id']);
		unset($object1['solution']);
		unset($object1['notes']);
		unset($object1['unitId']);
		unset($object1['solvedDate']);
		unset($object1['solvedTime']);

		unset($object2['id']);
		unset($object2['transactionCode']);
		unset($object2['mdnProblem']);
		unset($object2['customerName']);
		unset($object2['complaintType']);
		unset($object2['contactPersonCustomer']);
		unset($object2['district']);
		unset($object2['btsLocation']);
		unset($object2['bts_detail']);
		unset($object2['detailLocationCustomer']);
		unset($object2['complainDate']);
		unset($object2['complainTime']);
		unset($object2['categoryId']);
		unset($object2['subCategoryId']);
		unset($object2['detailComplain']);
		unset($object2['channel']);
		unset($object2['id_counter']);
		unset($object2['id_counter_setting']);
		unset($object2['queue_no']);

		$q1 = $this->db->insert($this->tabel, $object1);

		$object2['complainId'] = $this->db->insert_id();

		$q2 = $this->db->insert($this->tabelHistory, $object2);

		// Insert Log Actvitiy
		// $LM = &get_instance();
		// $LM->load->model('LogModel', 'lm');
		// $msgLog = "User : " . $this->session->userdata('username') . " -> Insert CDR";
		// $LM->lm->id_user = $this->session->userdata('id');
		// $LM->lm->inLogActivity($msgLog, json_encode($object));

		if ($q1 && $q2) {



			return $object2['complainId'];
		} else {
			return false;
		}
	}

	public function inComplaintHistory($object = '')
	{

		if ($object == '') {

			$object               = $this->input->post();
			$object               = json_encode($object);
			$object               = json_decode($object, true);
			$object['complainId'] = $object['id'];
			unset($object['id']);
		}

		$object['createDate'] = date('Y-m-d H:i:s');
		$object['userId']     = $this->session->userdata('id');

		$q = $this->db->insert($this->tabelHistory, $object);

		$obj2           = array();
		$obj2['status'] = $object['status'];

		$q2 = $this->db->update($this->tabel, $obj2, ['id' => $object['complainId']]);

		// Insert Log Actvitiy
		// $LM = &get_instance();
		// $LM->load->model('LogModel', 'lm');
		// $msgLog = "User : " . $this->session->userdata('username') . " -> Insert CDR";
		// $LM->lm->id_user = $this->session->userdata('id');
		// $LM->lm->inLogActivity($msgLog, json_encode($object));

		if ($q && $q2) {
			return true;
		} else {
			return false;
		}
	}

	public function getLastHistoryId($id)
	{
		$ret = $this->db->select('id')->from($this->tabelHistory)->where('complainId', $id)->order_by('id', 'desc')->limit(1)->get()->row();

		return $ret->id;
	}

	public function upComplaint($obj = '', $id = '', $based_on = '')
	{
		$log = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}

		$q = $this->db->update($this->tabel, $obj, $based_on);

		$log = [
			'response' => $q,
			'request'  => $obj,
			'msg'      => 'Data has been succesfully updated',
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function upComplaintHistory($obj = '', $id = '', $based_on = '')
	{
		$log = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}

		$q = $this->db->update($this->tabelHistory, $obj, $based_on);

		$obj2           = array();
		$obj2['status'] = $obj['status'];

		$q2 = $this->db->update($this->tabel, $obj2, ['id' => $obj['complainId']]);

		$log = [
			'response' => $q,
			'request'  => $obj,
			'msg'      => 'Data has been succesfully updated',
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function getComplaint($id = '')
	{
		if ($id == '') {
			$id = $this->input->get('id');
		}

		$q = $this->db->get_where($this->tabel, ['id' => $id]);
		return $q;
	}

	public function getComplaintHistory($id = '')
	{
		if ($id == '') {
			$id = $this->input->get('id');
		}

		$this->db->select('*')->from($this->tabelHistory);

		if ($id) {
			$this->db->where('id', $id);
		}

		$q = $this->db->order_by('id', 'desc')->limit(1)->get();

		return $q;
	}
}

/* End of file ComplainHandlingModel.php */
