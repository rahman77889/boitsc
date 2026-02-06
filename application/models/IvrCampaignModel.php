<?php

defined('BASEPATH') or exit('No direct script access allowed');

class IvrCampaignModel extends CI_Model
{

	public $t     = 'ivr_campaign';
	public $tabel = 'ivr_campaign';
	public $tabel_result = 'ivr_campaign_result';

	public function getTitle($id_title = '', $q = '', $obj = '')
	{
		if ($id_title != '') {
			$obj = ['id_title' => $id_title];
		}

		if ($obj != 0) {
			$q = $this->db->get_where($this->tabel, $obj);
		} else if ($q != '') {
			$q = $this->db->query($q);
		} else {
			$q = $this->db->get($this->tabel);
		}

		return $q;
	}

	public function status($status = '')
	{
		if ($status == '') {
			$status = $this->input->get('status');
		}

		if ($status == "N") {
			$q = 'No Active';
		} elseif ($status == "Y") {
			$q = 'Active';
		}

		return $q;
	}

	public function get($id = '', $q = '', $obj = '')
	{

		if ($id != '') {
			$obj = ['id' => $id];
		}


		if ($obj != 0) {
			$q = $this->db->get_where($this->t, $obj);
		} else if ($q != '') {
			$q = $this->db->query($q);
		} else {
			$q = $this->db->get($this->t);
		}

		return $q;
	}

	public function add($obj = '')
	{
		$log = '';

		if ($obj != '') {
			$q = $this->db->insert($this->t, $obj);
		}

		$log = [
			'response' => $q,
			'request'  => $obj,
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function update($obj = '', $id = '')
	{
		$log      = '';
		$based_on = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}

		$q = $this->db->update($this->t, $obj, $based_on);

		$log = [
			'response' => $q,
			'request'  => $obj,
			'msg'      => '',
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function delete($id = '', $based_on = '')
	{
		$log = '';
		$q   = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}


		$q = $this->db->delete($this->t, $based_on);

		$log = [
			'response' => $q,
			'request'  => $based_on,
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function dtshow()
	{
		// Definisi
		$condition = '';
		$data      = [];
		$categoryName;

		$CI = &get_instance();
		$CI->load->model('DataTable', 'dt');

		// Set table name
		$CI->dt->table         = $this->tabel;
		// Set orderable column fields
		$CI->dt->column_order  = array(null, `id`, `slot`, `title`, `createDate`, `c.fullName`, `status`);
		// Set searchable column fields
		$CI->dt->column_search = array($this->tabel . '.title', 'c.fullName', $this->tabel . '.slot');
		// Set select column fields
		$CI->dt->select        = $this->tabel . '.*,c.fullName';
		// Set default order
		$CI->dt->order         = array($this->tabel . '.id' => 'DESC');

		$condition = [
			['join', 'user c', $this->tabel . '.createBy=c.id', 'inner'],
		];

		// Fetch member's records
		$dataTabel = $this->dt->getRows($_POST, $condition);

		$i = $_POST['start'];
		foreach ($dataTabel as $dt) {
			$i++;
			$data[] = array(
				' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
				$i,
				$dt->slot,
				$dt->title,
				$dt->createDate,
				$dt->fullName,
				$this->status($dt->status),
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

	public function de($id)
	{
		$id = implode(",", $id);

		$this->db->query("DELETE  FROM $this->tabel WHERE id IN ($id)");
		$this->db->query("DELETE  FROM $this->tabel_result WHERE id_campaign IN ($id)");

		return true;
	}

	function save($data)
	{
		$result = $this->db->insert($this->tabel, $data);
		return $this->db->insert_id();
	}

	function save_csv($data)
	{
		$result = $this->db->insert_batch($this->tabel_result, $data);
		return $result;
	}

	function delete_csv($id)
	{
		$p = $this->db->query("DELETE  FROM $this->tabel_result WHERE id_campaign IN ($id)");
		return true;
	}
}
