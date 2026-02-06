<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UsersModel extends CI_Model
{

	public $t       = 'user';
	public $id_user = 0;
	private $level  = 0;

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		$this->level = $this->session->userdata('level');
	}

	public function update($id, $data)
	{
		$this->db->where('id', $id);

		if (isset($data['userManagerId']) && ($data['userManagerId'] == '' || $data['userManagerId'] == '0')) {
			unset($data['userManagerId']);
		}
		if (isset($data['unitId']) && ($data['unitId'] == '' || $data['unitId'] == '0')) {
			unset($data['unitId']);
		}
		if (isset($data['locationId']) && ($data['locationId'] == '' || $data['locationId'] == '0')) {
			unset($data['locationId']);
		}

		$this->db->update('user', $data);
	}

	public function updatephoto($id, $photo)
	{
		return $this->db->query("UPDATE `user` SET `photo` = '$photo' WHERE `user`.`id` = $id;");
	}

	public function dtUsers()
	{
		// Definisi
		$condition = '';

		$CI = &get_instance();
		$CI->load->model('DataTable', 'dt');

		// Set table name
		$CI->dt->table         = $this->t;
		// Set orderable column fields
		$CI->dt->column_order  = array(null, null, 'username', 'fullName', 'privilegeName', 'userSpvId', 'userManagerId', 'l.locationName', 'unitName');
		// Set searchable column fields
		$CI->dt->column_search = array('username', 'fullName');
		// Set select column fields
		$CI->dt->select        = $this->t . '.*,p.privilegeName,l.locationName,u.unitName';
		// Set default order
		$CI->dt->order         = array($this->t . '.id' => 'desc');

		$data = $row  = array();

		$condition = [
			['join', 'privilege p', $this->t . '.privilegeId = p.id', 'left'],
			['join', 'location l', $this->t . '.locationId = l.id', 'left'],
			['join', 'unit u', $this->t . '.unitId = u.id', 'left'],
		];

		// Fetch member's records
		$dataTabel = $this->dt->getRows($_POST, $condition);

		$i = @$_POST['start'];
		foreach ($dataTabel as $dt) {
			$i++;
			$userSupervisor = $dt->userSpvId > 0 ? $this->getUsers($dt->userSpvId)->row() : '';
			$userManager    = $dt->userManagerId > 0 ? $this->getUsers($dt->userManagerId)->row() : '';

			$data[] = array(
				'<input type="checkbox" class="userCheck"  onclick="edit(' . $dt->id . ')" name="id[]" value="' . $dt->id . '">',
				$i,
				$dt->username,
				$dt->fullName,
				$dt->extend_number,
				$dt->privilegeName,
				isset($userSupervisor->username) ? $userSupervisor->username : '',
				isset($userManager->username) ? $userManager->username : '',
				$dt->locationName,
				$dt->unitName,
				$this->cekSign($dt->statusLogin),
				$dt->active == 0 ? 'Not Active' : 'Active',
				'<button class="btn btn-danger" onclick="forceLogout(\'' . $dt->id . '\');">Force Logout</button>'
			);
		}

		$output = array(
			"draw"            => @$_POST['draw'],
			"recordsTotal"    => $this->dt->countAll($condition),
			"recordsFiltered" => $this->dt->countFiltered($_POST, $condition),
			"data"            => $data,
		);

		// Output to JSON format
		return json_encode($output);
	}

	public function getSubUser($id = '')
	{
		if ($id == '') {
			$id = $this->input->get('id');
		}

		$q = $this->db->get_where($this->t, ['id' => $id]);
		return $q;
	}

	public function getUsers($id = '', $q = '', $obj = '')
	{

		if ($id != '') {
			$obj = ['id' => $id];
		}


		if ($obj != 0) {
			$this->db->from($this->t);
			foreach ($obj as $kok => $vok) {
				$this->db->where($kok, $vok, true);
			}

			$q = $this->db->get();
		} else if ($q != '') {
			$q = $this->db->query($q);
		} else {
			$q = $this->db->select('*')->from($this->t)->order_by('username', 'asc')->get();
		}

		return $q;
	}

	public function inUsers($obj = '')
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

	public function upUsers($obj = '', $id = '')
	{
		$log      = '';
		$based_on = '';

		// if ($id != '') {
		$based_on = ['id' => $id];
		// }

		if (isset($obj['userManagerId']) && ($obj['userManagerId'] == '' || $obj['userManagerId'] == '0')) {
			unset($obj['userManagerId']);
		}
		if (isset($obj['unitId']) && ($obj['unitId'] == '' || $obj['unitId'] == '0')) {
			unset($obj['unitId']);
		}
		if (isset($obj['locationId']) && ($obj['locationId'] == '' || $obj['locationId'] == '0')) {
			unset($obj['locationId']);
		}

		$q = $this->db->update($this->t, $obj, $based_on);

		$log = [
			'response' => $q,
			'request'  => $obj,
			'msg'      => 'Sukses ubah Profile',
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function deUsers($id = '')
	{
		$log = '';
		$q   = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}


		//        $q = $this->db->delete($this->t, $based_on);

		$log = [
			'response' => $q,
			'request'  => $based_on,
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function disableuser($id = '')
	{
		$log = '';
		$q   = '';

		if ($id != '') {
			$based_on = ['id' => $id];
		}


		// $q = $this->db->delete($this->t, $based_on);
		$q = $this->db->query("UPDATE user set active=0 WHERE id='$id'");

		$log = [
			'response' => $q,
			'request'  => $based_on,
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	public function setSaldo($id = '', $saldo = '')
	{
		$log  = '';
		$q    = '';
		$cond = '';

		if ($id != '' && $saldo != '') {
			$cond     = ['saldo' => $saldo];
			$based_on = ['id' => $id];

			$q   = $this->db->update($this->t, $cond, $based_on);
			$msg = "Berhasil set saldo";
		} else {
			$msg = "Gagal set saldo";
		}


		$log = [
			'response' => $q,
			'msg'      => $msg,
			'request'  => $based_on,
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	// OPTIONAL

	public function cekStatus($v = '')
	{
		switch ($v) {
			case '1':
				$val = 'Seller';
				break;
			case '2':
				$val = 'Admin';
				break;
			case '3':
				$val = 'Super Admin';
				break;

			default:
				$val = 'Unknow';
				break;
		}

		return $val;
	}

	public function cekLevelBoleh($v = '')
	{
		if ($v == '') {
			$v = $this->level;
		}

		switch ($v) {
			case '1':
				$val = 'readonly';
				break;
			case '2':
				$val = '';
				break;
			case '3':
				$val = '';
				break;

			default:
				$val = 'disabled';
				break;
		}

		return $val;
	}

	#History User Login

	// public function getUserStatus($id = '', $q = '', $obj = '') {
	//     if ($id != '') {
	//         $obj = ['id' => $id];
	//     }
	//     if ($obj != 0) {
	//         $q = $this->db->get_where('userStatus', $obj);
	//     } else if ($q != '') {
	//         $q = $this->db->query($q);
	//     } else {
	//         $q = $this->db->get('userStatus');
	//     }
	//     return $q;
	// }

	public function inUserStatus($obj = '')
	{
		$log = [];
		$msg = '';

		if ($obj['userId']) {
			try {
				if ($obj != '') {
					$q   = $this->db->insert('userStatus', $obj);
					$msg = "Berhasil menambahkan userStatus";
				} else {
					$msg = "Tidak Ada parameter yg ingin di insert";
				}
			} catch (Exception $e) {
				$msg = "Tidak Ada parameter yg ingin di insert";
			}
		}

		$log = [
			'msg'  => $msg,
			'date' => date('Y-m-d H:i:s'),
		];

		return $log;
	}

	// public function cekSign($id = '') {
	//     $q = $this->getUserStatus('', "SELECT concat(IF(statusSign = 1,'Sign In','Sign Out'),' ',ipLocation) as statusLogin,statusSign FROM call_centre.userStatus where userId= " . $id . " order by id desc ;");
	//     if ($q->num_rows() > 0) {
	//         $q = $q->row();
	//         if ($q->statusSign == 1) {
	//             $data = "<span style='color:green;'>" . $q->statusLogin . "</span>";
	//         } else if ($q->statusSign == 0) {
	//             $data = "<span style='color:red;'>" . $q->statusLogin . "</span>";
	//         }
	//     } else {
	//         $data = "<span style='color:red;'>Sign Out - </span>";
	//     }
	//     return $data;
	// }




	public function cekSign($status = '')
	{
		if ($status == '') {
			$status = $this->input->get('statusLogin');
		}

		switch ($status) {
			case 1:
				$q = 'Sign In';
				break;
			case 2:
				$q = 'Logout';
				break;
			default:
				$q = 'Unknow';
				break;
		}
		return $q;
	}

	public function getUserManager($select = '*')
	{
		$this->db->select($select);
		$q = $this->u->getUsers('', '', ['privilegeId' => 1]);
		if ($q->num_rows() > 0) {
			$d = $q->result();
		} else {
			$d = [];
		}

		return $d;
	}

	public function getUserByPrivilege($privilege)
	{
		$this->db->select($select);
		$q = $this->u->getUsers('', '', ['privilegeId' => $privilege]);
		if ($q->num_rows() > 0) {
			$d = $q->result();
		} else {
			$d = [];
		}

		return $d;
	}

	public function getUserSupervisor($select = '*')
	{
		//        $this->db->select($select);
		$q = $this->u->getUsers();
		//        $q = $this->u->getUsers('', '', ['privilegeId' => 2]);
		if ($q->num_rows() > 0) {
			$d = $q->result();
		} else {
			$d = [];
		}

		return $d;
	}

	public function getAccount($id_user = '')
	{
		$this->db->select('p.privilegeName,u.fullName,u.username,u.extend_number,u.tipe,u.id_counter,u.id_counter_setting');
		$this->db->join('privilege p', 'u.privilegeId = p.id', 'inner');
		$this->db->where('u.id', $id_user);
		$u = $this->db->get('user u');

		return $u;
	}

	// update user
	public function upSubUser($obj = '', $id = '', $based_on = array())
	{
		$log = '';

		// if ($id != '') {
		$based_on = ['id' => $id];
		// }

		if (isset($obj['userManagerId']) && ($obj['userManagerId'] == '' || $obj['userManagerId'] == '0')) {
			unset($obj['userManagerId']);
		}
		if (isset($obj['unitId']) && ($obj['unitId'] == '' || $obj['unitId'] == '0')) {
			unset($obj['unitId']);
		}
		if (isset($obj['locationId']) && ($obj['locationId'] == '' || $obj['locationId'] == '0')) {
			unset($obj['locationId']);
		}

		$q = $this->db->update($this->t, $obj, $based_on);

		$log = [
			'response' => $q,
			'request'  => $obj,
			'msg'      => 'Sukses ubah User ',
			'date'     => date('Y-m-d H:i:s'),
		];

		return $log;
	}
}

/* End of file UsersModel.php */
/* Location: ./application/models/UsersModel.php */
