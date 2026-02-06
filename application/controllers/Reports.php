<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller
{

	private $path = 'page/reports/';

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
				$update_last_activity = array();
				$update_last_activity['last_activity'] = date('Y-m-d H:i:s');

				$this->db->where('id', $this->session->userdata('id'));
				$this->db->update('user', $update_last_activity);
			}
		}
	}

	private function time2string($timeline)
	{
		$periods = array('hour' => 3600, 'min' => 60, 'sec' => 1);
		$ret = '';
		foreach ($periods as $name => $seconds) {
			$num = floor($timeline / $seconds);
			$timeline -= ($num * $seconds);
			$ret .= $num . ' ' . $name . (($num > 1) ? '' : '') . ' ';
		}

		return trim($ret);
	}

	public function ch_group_report()
	{
		$report_type = $this->input->get('report_type') ? $this->input->get('report_type') : 'by_escalation';
		$user_group = $this->input->get('user_group') ? $this->input->get('user_group') : '0';
		$userId = $this->input->get('userId') ? $this->input->get('userId') : 'ALL';
		$status = $this->input->get('status') ? $this->input->get('status') : 'P';
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-t');
		$complaintType = $this->input->get('complaintType') ? $this->input->get('complaintType') : 'ALL';
		$categoryId = $this->input->get('categoryId') ? $this->input->get('categoryId') : '';
		$page = $this->input->get('page') ? (int) $this->input->get('page') : 1;
		$export = $this->input->get('export') ? $this->input->get('export') : '';
		$content_report = '';
		$tableHead = '';
		$categoryMap = array();
		$userList = '';
		$complaintTypeList = '';
		$total_count = 0;

		$this->load->model('CategoryModel', 'cm');

		$resCategory = $this->cm->getCategoryActive()->result_array();

		$listCategory = array();
		foreach ($resCategory as $ct) {
			$listCategory[] = '<option value="' . $ct['categoryId'] . '">' . $ct['categoryName'] . '</option>';
		}

		$listCategory = '<option value="">--Choose All Cetegory--</option>' . implode('', $listCategory);

		$userRec = $this->db->select('id,fullName')->from('user')->where('privilegeId', $user_group)->get()->result();
		$userList .= '<option value="ALL">-- All UserId --</option>';

		foreach ($userRec as $ur) {
			$userList .= '<option value="' . $ur->id . '">' . $ur->fullName . '</option>';
		}

		$comTypeRec = $this->db->select('*')->from('complaintype')->get()->result();
		$complaintTypeList .= '<option value="ALL">-- All Complaint Type --</option>';

		foreach ($comTypeRec as $ct) {
			$complaintTypeList .= '<option value="' . $ct->id . '">' . $ct->name . '</option>';
		}


		$listCategori = $this->db->select('*')->from('category')->get()->result();

		foreach ($listCategori as $ca) {
			$categoryMap[$ca->categoryId] = $ca->categoryName;
		}

		$whereComplaint = '';
		if ($this->session->userdata('tipe') == '123') {
			$whereComplaint = ' and  ch.channel not in ("Via Call 147", "WhatsApp 147")';
		} else if ($this->session->userdata('tipe') == '147') {
			$whereComplaint = ' and  ch.channel in ("Via Call 147", "WhatsApp 147")';
		} else if ($this->session->userdata('tipe') == '888') {
			$whereComplaint = ' and  ch.channel in ("Via Call 888")';
		}

		switch ($report_type) {

			case 'by_escalation';
				$listCategoriDB = $this->db->query("SELECT * FROM category GROUP BY categoryId")->result();
				$listUnitDB = $this->db->query("SELECT * FROM unit GROUP BY id")->result();
				$listData = $this->db->query(""
					. "SELECT COUNT(*) as jumlahunitcategory,ch.categoryId,ch2.unitId ,unit.unitName,categoryName "
					. "FROM complain ch "
					. "INNER JOIN complainthistory ch2 ON ch2.complainId=ch.id "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "INNER JOIN unit ON unit.id=ch2.unitId "
					. "INNER JOIN user ON user.id=ch.userId "
					. "WHERE  ch2.status='$status' AND user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch2.createDate >= '$tgl1' AND ch2.createDate <= '$tgl2' "
					. ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '')
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '') . $whereComplaint
					.
					"GROUP BY ch.categoryId,ch2.unitId
                    " . (!$export ? " LIMIT " . (($page - 1) * 50) . ",50" : ''))->result();


				$total_count_pre = $this->db->query("SELECT count(ch.id) as count FROM complain ch " . "INNER JOIN complainthistory ch2 ON ch2.complainId=ch.id "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "INNER JOIN unit ON unit.id=ch2.unitId "
					. "INNER JOIN user ON user.id=ch.userId "
					. "WHERE  ch2.status='$status' AND user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch2.createDate >= '$tgl1' AND ch2.createDate <= '$tgl2' "
					. ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '')
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '') . $whereComplaint
					. "GROUP BY ch.categoryId,ch2.unitId")->result();
				$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;

				$arr_multi_dimension = array();
				$listCategori = array('id' => 'grand_total', 'nama' => 'grand_total');
				$listReturn = array();
				//$listUnit  = array();
				//masukin category
				foreach ($listCategoriDB as $lc) {
					@$listReturn[$lc->categoryId] = $listReturn[$lc->categoryId];
				}
				//masukin unit dan data-nya
				foreach ($listData as $ld) {
					@$listReturn[$ld->categoryId][$ld->unitId] = $ld;
					@$listReturn[$ld->categoryId]['total_group'] += $ld->jumlahunitcategory;
					@$listReturn['grand_total'][$ld->unitId]['jumlahunitcategory'] += $ld->jumlahunitcategory;

					// @$listReturn['grand_total']['total_group'] += $ld->jumlahunitcategory;
				}
				$return = array('data' => $listReturn, 'groupingcat' => $listCategoriDB, 'groupingcol' => $listUnitDB);
				$return['groupingcat']['grand_total'] = array('categoryName' => "grand_total", "jumlahunitcategory" => 0, "categoryId" => 'grand_total');

				// echo json_encode($return);
				// return;
				$no = (($page - 1) * 50) + 1;


				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_ch_group_report_' . $report_type . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('No', 'Category', 'CALL CENTER AGENT', 'CUSTOMER SERVICE OFFICER (CSO)', 'Customer Care', 'Service Solution (SS)', 'Network and Core', 'Mobile Network', 'T-PAY Dedicated Agent');

					fputcsv($output, $header_args);

					$no = 1;

					foreach ($return['groupingcat'] as $dataRow) {
						$data_item = array(
							$no++
						);

						if (@$dataRow->categoryName != null) {

							$row = $dataRow->categoryName;
							$idrow = $dataRow->categoryId;
						} else {
							$idrow = $dataRow['categoryId'];
							$row = $dataRow['categoryName'];
						}

						$rownya = $row;
						if ($row == "grand_total") {
							$rownya = "Grand Total";
						}
						$data_item[] = $rownya;
						foreach ($return['groupingcol'] as $dataCol) {
							$idcol = $dataCol->id;
							$nilai = 0;
							// echo $idcol;
							$colname = $dataCol->unitName;

							//cek jika row category nya ga null
							if (@$return['data'][$idrow] != null) {
								//cek column value nya ga null
								if (@$return['data'][$idrow][$idcol] != null) {

									// buat masukin nilai value ke jumlah unit category
									if (@$return['data'][$idrow][$idcol]->jumlahunitcategory) { //cek jika ini bukan grand-total
										$unitId = $return['data'][$idrow][$idcol]->unitId;
										$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahunitcategory) ? $return['data'][$idrow][$idcol]->jumlahunitcategory : 0;
									} else { //ini buat grand total
										$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahunitcategory']) ? $return['data'][$idrow][$idcol]['jumlahunitcategory'] : 0;
									}

									// echo json_encode($return['data'][$idrow][$idcol]);
									$nilai = $jumlahCount;
								}
							}
							if ($idcol != 'grand_total') {
								$data_item[] = $nilai;
							}
						}

						fputcsv($output, $data_item);
					}

					exit();
				}

				foreach ($return['groupingcat'] as $dataRow) {
					$no = 0;
					if (@$dataRow->categoryName != null) {

						$row = '<a href="' . base_url("Reports/detail_report?status=" . $status . "&categoryId=" . $dataRow->categoryId) . '"     >' . $dataRow->categoryName . '</a>';
						$idrow = $dataRow->categoryId;
					} else {
						$idrow = $dataRow['categoryId'];
						$row = $dataRow['categoryName'];
					}

					$content_report .= "<tr>";
					$content_report .= "<td>" . $no++ . "</td>";

					$rownya = $row;
					if ($row == "grand_total") {
						$rownya = "<b>Grand Total</b>";
					}
					$content_report .= "<td style=' font-weight: bold;'>" . $rownya . "</td>";
					foreach ($return['groupingcol'] as $dataCol) {
						$idcol = $dataCol->id;
						$nilai = 0;
						// echo $idcol;
						$colname = $dataCol->unitName;

						//cek jika row category nya ga null
						if (@$return['data'][$idrow] != null) {
							//cek column value nya ga null
							if (@$return['data'][$idrow][$idcol] != null) {

								// buat masukin nilai value ke jumlah unit category
								if (@$return['data'][$idrow][$idcol]->jumlahunitcategory) { //cek jika ini bukan grand-total
									$unitId = $return['data'][$idrow][$idcol]->unitId;
									$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahunitcategory) ? $return['data'][$idrow][$idcol]->jumlahunitcategory : 0;
								} else { //ini buat grand total
									$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahunitcategory']) ? $return['data'][$idrow][$idcol]['jumlahunitcategory'] : 0;
								}

								// echo json_encode($return['data'][$idrow][$idcol]);
								$nilai = $jumlahCount;
							}
						}
						if ($idcol != 'grand_total') {
							$content_report .= "<td>" . $nilai . "</td>";
						}
					}

					$content_report .= "</tr>";
				}


				foreach ($return['groupingcol'] as $lu) {
					$tableHead .= '<th style="border: 1px solid #bdbcbc; ">' . $lu->unitName . '</th>';
				}
				// echo json_encode($return);
				// return;
				break;

			case 'by_category';
				$listCategoriDB = $this->db->query("SELECT * FROM category GROUP BY categoryId " . (!$export ? " LIMIT " . (($page - 1) * 50) . ",50" : ''))->result();
				$listStatusDB = $this->db->select('status,name')->from('complainstatus')->get()->result();
				$listData = $this->db->query(""
					. "SELECT COUNT(*) as jumlahperstatus,category.categoryId,category.categoryName,ch.status,ch.createDate "
					. "FROM complain ch "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "INNER JOIN user ON user.id=ch.userId "
					. "WHERE user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch.status='$status' AND ch.createDate >= '$tgl1' AND ch.createDate <= '$tgl2' "
					. ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '')
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '') . $whereComplaint
					.
					"GROUP BY ch.status")->result();

				$total_count_pre = $this->db->query("SELECT count(*) as count FROM category GROUP BY categoryId")->result();
				$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;

				$arr_multi_dimension = array();
				$listCategori = array('id' => 'grand_total', 'nama' => 'grand_total');

				$listStatus = array();
				foreach ($listStatusDB as $lst) {
					$listStatus[$lst->status] = $lst->name;
				}

				$listReturn = array();
				//masukin category
				foreach ($listCategoriDB as $lc) {
					@$listReturn[$lc->categoryId] = $listReturn[$lc->categoryId];
				}
				//masukin location dan data-nya
				foreach ($listData as $ld) {
					@$listReturn[$ld->categoryId][$ld->status] = $ld;
					@$listReturn[$ld->categoryId]['total_group'] += $ld->jumlahperstatus;
					@$listReturn['grand_total'][$ld->status]['jumlahperstatus'] += $ld->jumlahperstatus;
				}
				$return = array('data' => $listReturn, 'groupingcat' => $listCategoriDB, 'groupingcol' => $listStatus);
				$return['groupingcat']['grand_total'] = array('categoryName' => "grand_total", "jumlahperstatus" => 0, "categoryId" => 'grand_total');
				// echo json_encode($return['groupingcol'][0]['name']);
				// return;
				$no = (($page - 1) * 50) + 1;

				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_ch_group_report_' . $report_type . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('No', 'Category', 'CALL CENTER AGENT', 'CUSTOMER SERVICE OFFICER (CSO)', 'Customer Care', 'Service Solution (SS)', 'Network and Core', 'Mobile Network', 'T-PAY Dedicated Agent');

					fputcsv($output, $header_args);

					$no = 1;

					foreach ($return['groupingcat'] as $dataRow) {
						if (@$dataRow->categoryName != null) {
							$row = $dataRow->categoryName;
							$idrow = $dataRow->categoryId;
						} else {
							$idrow = $dataRow['categoryId'];
							$row = $dataRow['categoryName'];
						}
						$data_item = array($no++);

						// $content_report .= "<td>" . $row . "</td>";
						$rownya = $row;
						if ($row == "grand_total") {
							$rownya = "Grand Total";
						}

						$data_item[] = $rownya;

						foreach ($return['groupingcol'] as $kd => $dataCol) {
							$idcol = $kd;
							//                        echo $idcol;
							$nilai = 0;

							$colname = $dataCol;
							if (@$return['data'][$idrow] != null) {
								//cek column value nya ga null
								if (@$return['data'][$idrow][$idcol] != null) {

									// buat masukin nilai value ke jumlah unit category
									if (@$return['data'][$idrow][$idcol]->jumlahperstatus) { //cek jika ini bukan grand-total
										$unitId = $return['data'][$idrow][$idcol]->status;
										$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahperstatus) ? $return['data'][$idrow][$idcol]->jumlahperstatus : 0;
									} else { //ini buat grand total
										$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahperstatus']) ? $return['data'][$idrow][$idcol]['jumlahperstatus'] : 0;
									}

									// echo json_encode($return['data'][$idrow][$idcol]);
									$nilai = $jumlahCount;
								}
							}
							if ($idcol != 'grand_total') {
								$data_item[] = $nilai;
							}
						}

						fputcsv($output, $data_item);
					}

					exit();
				}

				foreach ($return['groupingcat'] as $dataRow) {

					if (@$dataRow->categoryName != null) {
						$row = '<a href="' . base_url("Reports/detail_category?status=" . $status . "&categoryId=" . $dataRow->categoryId) . '"     >' . $dataRow->categoryName . '</a>';
						$idrow = $dataRow->categoryId;
					} else {
						$idrow = $dataRow['categoryId'];
						$row = $dataRow['categoryName'];
					}
					$content_report .= "<tr>";
					$content_report .= "<td>" . $no++ . "</td>";

					// $content_report .= "<td>" . $row . "</td>";
					$rownya = $row;
					if ($row == "grand_total") {
						$rownya = "<b>Grand Total</b>";
					}

					$content_report .= "<td>" . $rownya . "</td>";

					foreach ($return['groupingcol'] as $kd => $dataCol) {
						$idcol = $kd;
						//                        echo $idcol;
						$nilai = 0;

						$colname = $dataCol;
						if (@$return['data'][$idrow] != null) {
							//cek column value nya ga null
							if (@$return['data'][$idrow][$idcol] != null) {

								// buat masukin nilai value ke jumlah unit category
								if (@$return['data'][$idrow][$idcol]->jumlahperstatus) { //cek jika ini bukan grand-total
									$unitId = $return['data'][$idrow][$idcol]->status;
									$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahperstatus) ? $return['data'][$idrow][$idcol]->jumlahperstatus : 0;
								} else { //ini buat grand total
									$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahperstatus']) ? $return['data'][$idrow][$idcol]['jumlahperstatus'] : 0;
								}

								// echo json_encode($return['data'][$idrow][$idcol]);
								$nilai = $jumlahCount;
							}
						}
						if ($idcol != 'grand_total') {
							$content_report .= "<td>" . $nilai . "</td>";
						}
					}

					$content_report .= "</tr>";
				}


				foreach ($return['groupingcol'] as $lu) {
					$tableHead .= '<th style="border: 1px solid #ccc; ">' . $lu . '</th>';
				}
				break;
			case 'by_location';
				$listCategoriDB = $this->db->query("SELECT * FROM category GROUP BY categoryId")->result();
				$listBtsDB = $this->db->query("SELECT * FROM bts GROUP BY site_name")->result();

				$listData = $this->db->query(""
					. "SELECT COUNT(*) as jumlahlocation,ch.status,bts.site_name,ch.categoryId, ch.createDate,category.categoryName "
					. "FROM complain ch "
					. "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "LEFT JOIN bts ON bts.site_id=ch.btsLocation "
					. "INNER JOIN user ON user.id=ch.userId  "
					. "WHERE user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch.status='$status' AND ch.createDate >= '$tgl1' AND ch.createDate <= '$tgl2' "
					. ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '') . $whereComplaint
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '')
					.
					"GROUP BY bts.site_name,ch.categoryId
                    " . (!$export ? " LIMIT " . (($page - 1) * 50) . ",50" : ''))->result();

				$total_count_pre = $this->db->query("SELECT count(ch.id) as count FROM complain ch " . "INNER JOIN category ON category.categoryId=ch.categoryId "
					. "LEFT JOIN bts ON bts.site_id=ch.btsLocation "
					. "INNER JOIN user ON user.id=ch.userId  "
					. "WHERE user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch.status='$status' AND ch.createDate >= '$tgl1' AND ch.createDate <= '$tgl2' "
					. ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '') . $whereComplaint
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '')
					. "GROUP BY bts.site_name,ch.categoryId")->result();
				$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;

				$arr_multi_dimension = array();
				$listCategori = array('id' => 'grand_total', 'nama' => 'grand_total');
				$listReturn = array();
				// $listUnit  = array();
				//masukin category
				foreach ($listCategoriDB as $lc) {
					@$listReturn[$lc->categoryId] = $listReturn[$lc->categoryId];
				}
				//masukin location dan data-nya
				foreach ($listData as $ld) {
					@$listReturn[$ld->categoryId][$ld->site_name] = $ld;
					@$listReturn[$ld->categoryId]['total_group'] += $ld->jumlahlocation;
					@$listReturn['grand_total'][$ld->site_name]['jumlahlocation'] += $ld->jumlahlocation;
				}
				$return = array('data' => $listReturn, 'groupingcat' => $listCategoriDB, 'groupingcol' => $listBtsDB);
				$return['groupingcat']['grand_total'] = array('categoryName' => "grand_total", "jumlahlocation" => 0, "categoryId" => 'grand_total');

				$no = (($page - 1) * 50) + 1;

				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_ch_group_report_' . $report_type . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('No', 'Category', 'CALL CENTER AGENT', 'CUSTOMER SERVICE OFFICER (CSO)', 'Customer Care', 'Service Solution (SS)', 'Network and Core', 'Mobile Network', 'T-PAY Dedicated Agent');

					fputcsv($output, $header_args);

					$no = 1;

					foreach ($return['groupingcat'] as $dataRow) {

						if (@$dataRow->categoryName != null) {

							$row = $dataRow->categoryName;
							$idrow = $dataRow->categoryId;
						} else {
							$idrow = $dataRow['categoryId'];
							$row = $dataRow['categoryName'];
						}

						$data_item = array($no++);

						// $content_report .= "<td>" . $row . "</td>";
						$rownya = $row;
						if ($row == "grand_total") {
							$rownya = "Grand Total";
						}
						$data_item[] = $rownya;
						foreach ($return['groupingcol'] as $dataCol) {
							$idcol = $dataCol->site_name;
							$nilai = 0;

							// echo $idcol;
							$colname = $dataCol->site_name;
							if (@$return['data'][$idrow] != null) {
								//cek column value nya ga null
								if (@$return['data'][$idrow][$idcol] != null) {

									// buat masukin nilai value ke jumlah unit category
									if (@$return['data'][$idrow][$idcol]->jumlahlocation) { //cek jika ini bukan grand-total
										$unitId = $return['data'][$idrow][$idcol]->site_name;
										$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahlocation) ? $return['data'][$idrow][$idcol]->jumlahlocation : 0;
									} else { //ini buat grand total
										$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahlocation']) ? $return['data'][$idrow][$idcol]['jumlahlocation'] : 0;
									}

									// echo json_encode($return['data'][$idrow][$idcol]);
									$nilai = $jumlahCount;
								}
							}
							if ($idcol != 'grand_total') {
								$data_item[] = $nilai;
							}
						}

						fputcsv($output, $data_item);
					}

					exit();
				}

				foreach ($return['groupingcat'] as $dataRow) {
					// $row   = '<a href="' . base_url("Reports/detail_location?categoryId=" . $dataRow->categoryId) . '"     >' . $dataRow->categoryName . '</a>';
					// $row   = '<a href="' . base_url("Reports/detail_location?status=".$status."&categoryId=" . $dataRow->categoryId) . '"     >' . $dataRo   w->categoryName . '</a>';
					// $idrow = $dataRow->categoryId;
					if (@$dataRow->categoryName != null) {

						$row = '<a href="' . base_url("Reports/detail_location?status=" . $status . "&categoryId=" . $dataRow->categoryId) . '"     >' . $dataRow->categoryName . '</a>';
						$idrow = $dataRow->categoryId;
					} else {
						$idrow = $dataRow['categoryId'];
						$row = $dataRow['categoryName'];
					}

					$content_report .= "<tr>";
					$content_report .= "<td>" . $no++ . "</td>";

					// $content_report .= "<td>" . $row . "</td>";
					$rownya = $row;
					if ($row == "grand_total") {
						$rownya = "<b>Grand Total</b>";
					}
					$content_report .= "<td>" . $rownya . "</td>";
					foreach ($return['groupingcol'] as $dataCol) {
						$idcol = $dataCol->site_name;
						$nilai = 0;

						// echo $idcol;
						$colname = $dataCol->site_name;
						if (@$return['data'][$idrow] != null) {
							//cek column value nya ga null
							if (@$return['data'][$idrow][$idcol] != null) {

								// buat masukin nilai value ke jumlah unit category
								if (@$return['data'][$idrow][$idcol]->jumlahlocation) { //cek jika ini bukan grand-total
									$unitId = $return['data'][$idrow][$idcol]->site_name;
									$jumlahCount = @($return['data'][$idrow][$idcol]->jumlahlocation) ? $return['data'][$idrow][$idcol]->jumlahlocation : 0;
								} else { //ini buat grand total
									$jumlahCount = @($return['data'][$idrow][$idcol]['jumlahlocation']) ? $return['data'][$idrow][$idcol]['jumlahlocation'] : 0;
								}

								// echo json_encode($return['data'][$idrow][$idcol]);
								$nilai = $jumlahCount;
							}
						}
						if ($idcol != 'grand_total') {
							$content_report .= "<td>" . $nilai . "</td>";
						}
					}

					$content_report .= "</tr>";
				}


				foreach ($return['groupingcol'] as $lu) {
					$tableHead .= '<th style="border: 1px solid #bdbcbc; ">' . $lu->site_name . '</th>';
				}
				break;

			case 'by_date';
				//(select un.unitName from unit un where un.id=(select ch.unitId from complainthistory ch where ch.id=ch.id order by createDate desc limit 1)) as unitName
				$resCl = $this->db->query(""
					. "SELECT ch.userId,ch.customerName,ch.status,ch.subCategoryId,ch.contactPersonCustomer,ch.complainDate,ch.complainTime,ch.channel,ch.mdnProblem,ch.transactionCode,ch.btsLocation,ch.bts_detail,ch.detailLocationCustomer,ch.complaintType,ch.categoryId ,
                                    (select chh.unitId from complainthistory chh where chh.complainId=ch.id and chh.solvedDate order by solvedTime desc limit 1) as unitId ,
                                    (select chh.solvedDate from complainthistory chh where chh.complainId=ch.id order by solvedTime desc limit 1) as solvedDate ,
                                    (select chh.solvedTime from complainthistory chh where chh.complainId=ch.id order by solvedTime desc limit 1) as solvedTime ,
                                    (select chh.solution from complainthistory chh where chh.complainId=ch.id order by solvedTime desc limit 1) as solution,
                                    user.fullName as fullName,
                                    (select bt.site_name from bts bt where ch.btsLocation=bt.site_id) as btsLocationName,
                                    (select cos.name from complainstatus cos where ch.status=cos.status) as complaintstatusname,
                                    (select com.name from complaintype com where ch.complaintType=com.id) as complaintypename,
                                    (select c.categoryName from category c where ch.categoryId=c.categoryId) as categoryName,
                                    (select sub.subCategory from subcategory sub where sub.subCategoryId=ch.subCategoryId) as subCategory,
                                    ch.rating_1,
                                    ch.rating_2,
                                    ch.rating_3,
                                    ch.rating_4,
                                    ch.rating_5
                                FROM complain ch
                                INNER JOIN user ON user.id=ch.userId
                                WHERE user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch.status='$status' AND ch.complainDate >= '$tgl1' AND ch.complainDate <= '$tgl2'
                                    " . ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '') . $whereComplaint
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '') . "
                                ORDER BY ch.complainDate, ch.complainTime
                                " . (!$export ? " LIMIT " . (($page - 1) * 50) . ",50" : ''))->result();

				$no = (($page - 1) * 50) + 1;


				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_ch_group_report_' . $report_type . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('No', 'Date', 'Time', 'Channel', 'Transaction Code', 'MSISDN', 'Customer Name', 'BTS loc', 'Detail Loc', 'BTS Detail', 'Complain Type', 'Category', 'Sub Category', 'Status', 'Solved Date', 'Solved Time', 'Duration', 'Friendliness', 'Solution', 'Price & Quality', 'Network', 'Facilities');

					fputcsv($output, $header_args);

					foreach ($resCl as $row) {
						$data_item = array(
							$no++,
							date('d F Y', strtotime($row->complainDate)),
							$row->complainTime,
							$row->channel,
							$row->transactionCode,
							$row->mdnProblem,
							$row->customerName,
							$row->btsLocationName,
							$row->detailLocationCustomer,
							$row->bts_detail,
							$row->complaintypename,
							$row->categoryName,
							$row->subCategory,
							$row->complaintstatusname,
							(strtotime($row->solvedDate) > 0 ? date('d F Y', strtotime($row->solvedDate)) : ''),
							(strtotime($row->solvedTime) > 0 && $row->solvedTime != '00:00:00' ? $row->solvedTime : ''),
							(strtotime($row->solvedDate . ' ' . $row->solvedTime) - strtotime($row->complainDate . ' ' . $row->complainTime)) . ' Sec',
							$row->rating_1,
							$row->rating_2,
							$row->rating_3,
							$row->rating_4,
							$row->rating_5,
							$row->fullName
						);

						fputcsv($output, $data_item);
					}

					exit();
				}


				$total_count_pre = $this->db->query("SELECT count(ch.id) as count FROM complain ch  INNER JOIN user ON user.id=ch.userId  WHERE user.privilegeId='$user_group' " . ($userId != 'ALL' ? "AND user.id='" . $userId . "'" : '') . " AND ch.status='$status' AND ch.complainDate >= '$tgl1' AND ch.complainDate <= '$tgl2'
                                    " . ($complaintType != 'ALL' ? "AND ch.complaintType='$complaintType' " : '') . $whereComplaint
					. ($categoryId ? " AND ch.categoryId='$categoryId'" : '') . "")->result();
				$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;

				foreach ($resCl as $row) {
					$content_report .= '<tr>
                                          <td style = "text-align:center">' . $no++ . '</td>
                                          <td style = "text-align:center">' . date('d F Y', strtotime($row->complainDate)) . '</td>
                                          <td style = "text-align:center">' . $row->complainTime . '</td>
                                          <td style = "text-align:center">' . $row->channel . '</td>
                                          <td style = "text-align:center">' . $row->transactionCode . '</td>
                                          <td style = "text-align:center">' . $row->mdnProblem . '</td>
                                          <td style = "text-align:center">' . $row->customerName . '</td>
                                          <td style = "text-align:center">' . $row->btsLocationName . '</td>
                                          <td style = "text-align:center">' . $row->detailLocationCustomer . '</td>
                                          <td style = "text-align:center">' . $row->bts_detail . '</td>
                                          <td style = "text-align:center">' . $row->complaintypename . '</td>
                                          <td style = "text-align:center">' . $row->categoryName . '</td>
                                          <td style = "text-align:center">' . $row->subCategory . '</td>
                                          <td style = "text-align:center">' . $row->complaintstatusname . '</td>
                                          <td style = "text-align:center">' . (strtotime($row->solvedDate) > 0 ? date('d F Y', strtotime($row->solvedDate)) : '') . '</td>
                                          <td style = "text-align:center">' . (strtotime($row->solvedTime) > 0 && $row->solvedTime != '00:00:00' ? $row->solvedTime : '') . '</td>
                                          <td style = "text-align:center">' . (strtotime($row->solvedDate . ' ' . $row->solvedTime) - strtotime($row->complainDate . ' ' . $row->complainTime)) . ' Seconds</td>
                                          <td style = "text-align:center">' . $row->rating_1 . '</td>
                                          <td style = "text-align:center">' . $row->rating_2 . '</td>
                                          <td style = "text-align:center">' . $row->rating_3 . '</td>
                                          <td style = "text-align:center">' . $row->rating_4 . '</td>
                                          <td style = "text-align:center">' . $row->rating_5 . '</td>
                                          <td style = "text-align:center">' . $row->fullName . '</td>
                                       </tr>';
				}

				break;
		}


		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'ch_group_report',
			'report_type' => $report_type,
			'content_report' => $content_report,
			'status' => $status,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'tableHead' => $tableHead,
			'userList' => $userList,
			'user_group' => $user_group,
			'userId' => $userId,
			'complaintTypeList' => $complaintTypeList,
			'complaintType' => $complaintType,
			'listCategory' => $listCategory,
			'categoryId' => $categoryId,
			'total_count' => $total_count,
			'page' => $page
		];
		$this->load->view('page/_main', $d);
	}

	public function call_center_report()
	{

		$report_type = $this->input->get('report_type') ? $this->input->get('report_type') : 'inbound';
		$group_by = $this->input->get('group_by') ? $this->input->get('group_by') : 'daily';
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$call_center_number = $this->input->get('call_center_number') ? $this->input->get('call_center_number') : '';
		$page = $this->input->get('page') ? (int) $this->input->get('page') : 1;
		$export = $this->input->get('export') ? $this->input->get('export') : '';
		$total_count = 0;

		if ($this->session->userdata('tipe') == '123') {
			$call_center_number = '123';
		} elseif ($this->session->userdata('tipe') == '147') {
			$call_center_number = '147';
		} elseif ($this->session->userdata('tipe') == '888') {
			$call_center_number = '888';
		}

		$listAgent = array();
		$content_report = '';

		//query - query atau panggil data ke modal
		switch ($report_type) {
			case 'inbound':

				if ($group_by == 'ALL') {
					$group_by = 'daily';
				}

				switch ($group_by) {
					case 'hourly':
						$field = 'concat(date(call_start),\'_\',hour(call_start))';
						$fieldAbandon = 'concat(date(tgl),\'_\',hour(tgl))';
						$orderby = 'field asc';
						break;
					case 'daily':
						$field = 'date(call_start)';
						$fieldAbandon = 'date(tgl)';
						$orderby = 'field asc';
						break;
					case 'agent':
						$field = 'IF(isnull((select u.fullName from user u where u.id=call_log_incoming.id_user)),\'ZZZZZZZZZZTentative Calls\',(select u.fullName from user u where u.id=call_log_incoming.id_user))';
						$fieldAbandon = 'IF(isnull((select u.fullName from user u where u.id=call_log_abandon.id_user)),\'ZZZZZZZZZZTentative Calls\',(select u.fullName from user u where u.id=call_log_abandon.id_user))';
						$orderby = 'field asc';
						break;
				}

				$resCLP = $this->db->select(''
					//                                . '(sum(if(abandon=\'Y\',1,0))+count(answer_time)) as jumlah,'
					. 'count(answer_time) as jumlah,'
					. 'sum(duration) as jumlah_duration,'
					. 'count(answer_time) as jumlahanswer,'
					//                                . 'sum(if(abandon=\'Y\',1,0)) as jumlahabandon,'
					. 'round(AVG(duration),2) as totalavgduration, '
					// . 'sum(if(duration<=20,1,0)) as call20, '
					. 'sum(if(call_answer-call_start<=20,1,0)) as call20, '
					. 'AVG(answer_time) as totalavgacd, '
					. 'call_log_incoming.id_user,'
					. '' . $field . ' as field')->from('call_log_incoming')->where('status="hangup"')->
					//                        where('(abandon=\'Y\' or duration>0)')->
					where('duration>0')->where('date(call_start)>=', $tgl1)->where('date(call_start)<=', $tgl2)->group_by($field)->order_by('field', 'asc');

				$where_call_center = '';

				if ($call_center_number) {

					if ($this->input->get('call_center_number') == 'other') {
						$notIn = array('+123', '+147');
						$resCLP->where_not_in('call_center_number', $notIn);
						$where_call_center = ' and call_center_number not in (\'+123\',\'+147\')';
					} else {
						$resCLP->where('call_center_number', '+' . $this->input->get('call_center_number'));
						$where_call_center = ' and call_center_number =\'' . $this->input->get('call_center_number') . '\'';
					}
				}

				if (!$export) {
					$resCLP->limit(50, (($page - 1) * 50));

					$total_count_pre = $this->db->query('SELECT COUNT(*) as count FROM (SELECT ' . $field . ' as field FROM call_log_incoming where status="hangup" and duration>0 and date(call_start)>="' . $tgl1 . '" and date(call_start)<="' . $tgl2 . '" ' . $where_call_center . ' group by ' . $field . ') AS T')->result();
					$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;
				}

				$resCL = $resCLP->get()->result();

				$resAbandon = $this->db->select('sum(if(abandon=\'C\',1,0)) as jumlah, '
					. 'id_user,'
					. '' . $fieldAbandon . ' as field')->from('call_log_abandon')->where('abandon', 'C')->where('date(tgl)>=', $tgl1)->where('date(tgl)<=', $tgl2)->group_by($fieldAbandon)->order_by('field', 'asc');

				if ($call_center_number) {

					if ($this->input->get('call_center_number') == 'other') {
						$notIn = array('+123', '+147');
						$resAbandon->where_not_in('call_center_number', $notIn);
					} else {
						$resAbandon->where('call_center_number', '+' . $this->input->get('call_center_number'));
					}
				}

				$resAbandonFinal = $resAbandon->get()->result();

				$abandonData = array();
				foreach ($resAbandonFinal as $raf) {
					if ($group_by == 'hourly') {
						$item = str_replace('_', ' ', $raf->field) . ':00:00';
					} elseif ($group_by == 'agent') {
						$item = $raf->id_user;
					} else {
						$item = $raf->field;
					}

					$abandonData[$item] = $raf->jumlah;
				}

				$jumlah = [];
				$jumlahanswer = [];
				$jumlahabandon = [];
				$call20 = [];
				$totalavgduration = [];
				$isi = [];
				$totalavgduration = [];
				$totalavgacd = [];

				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_call_center_report_' . $report_type . '_' . $group_by . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('Interval', 'Total Call', 'Total Call Answered', 'Total Abandoned Calls', 'Total Calls Answered In 20', 'Avg Handling Time', 'Duration Handling Time', 'Abadon Rate', 'SCR', 'Avg ACD Time');

					fputcsv($output, $header_args);

					$no = 1;

					foreach ($resCL as $row) {
						$data_item = array();

						if ($group_by == 'hourly') {
							$item = str_replace('_', ' ', $row->field) . ':00:00';
						} elseif ($group_by == 'agent') {
							$item = $row->id_user;
						} else {
							$item = $row->field;
						}

						$abandonVal = isset($abandonData[$item]) ? $abandonData[$item] : 0;

						if ($row->field != 'ZZZZZZZZZZTentative Calls') {

							$data_item[] = ($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : str_replace('ZZZZZZZZZZ', '', $row->field));
							$data_item[] = ($row->jumlah + $abandonVal);
							$data_item[] = $row->jumlahanswer;
							$data_item[] = $abandonVal;
							$data_item[] = $row->call20;
							$data_item[] = $row->totalavgduration . ' Second';
							$data_item[] = $row->jumlah_duration . ' Second';
							$data_item[] = ($row->jumlah > 0 ? round($abandonVal / $row->jumlah * 100, 2) : '0') . '%';
							$data_item[] = $row->totalavgduration . ' Second';
							$data_item[] = round($row->totalavgacd, 2) . ' Second';


							if ($row) {
								($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field);
								$jumlah[] = $row->jumlah + $abandonVal;
								$jumlahanswer[] = $row->jumlahanswer;
								$jumlahabandon[] = $abandonVal;
								$call20[] = $row->call20;
								$totalavgduration[] = $row->totalavgduration;
								$jumlah_duration[] = $row->jumlah_duration;
								$isi[] = ($row->jumlah > 0 ? round($abandonVal / $row->jumlah * 100, 2) : '0');
								//                        $totalavgduration[] = $row->totalavgduration;
								$totalavgacd[] = round($row->totalavgacd, 2);
							}
						}

						fputcsv($output, $data_item);
					}

					$data_item = array();
					$data_item[] = 'Total';
					$data_item[] = array_sum($jumlah);
					$data_item[] = array_sum($jumlahanswer);
					$data_item[] = array_sum($jumlahabandon);
					$data_item[] = array_sum($call20);
					$data_item[] = array_sum($totalavgduration) . ' Second';
					$data_item[] = array_sum($jumlah_duration);
					$data_item[] = array_sum($isi) . ' %';
					$data_item[] = array_sum($totalavgduration) . ' Second';
					$data_item[] = array_sum($totalavgacd) . ' Second';

					fputcsv($output, $data_item);

					exit();
				}

				foreach ($resCL as $row) {

					if ($group_by == 'hourly') {
						$item = str_replace('_', ' ', $row->field) . ':00:00';
					} elseif ($group_by == 'agent') {
						$item = $row->id_user;
					} else {
						$item = $row->field;
					}

					$abandonVal = isset($abandonData[$item]) ? $abandonData[$item] : 0;

					if ($row->field != 'ZZZZZZZZZZTentative Calls') {
						$content_report .= '<tr>

                                          <td style="text-align:center">' . ($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : str_replace('ZZZZZZZZZZ', '', $row->field)) . '</td>
                                          <td style = "text-align:center;">' . ($row->jumlah + $abandonVal) . '</td>
                                          <td style = "text-align:center">' . $row->jumlahanswer . '</td>
                                          <td style = "text-align:center"><a href="' . base_url('Reports/detail_cc_abandon') . '?groupBy=' . $group_by . '&item=' . $item . '&tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '">' . $abandonVal . '</a></td>
                                          <td style = "text-align:center">' . $row->call20 . '</td>
                                          <td style = "text-align:center">' . $row->totalavgduration . ' Second</td>
                                          <td style = "text-align:center">' . $row->jumlah_duration . ' Second</td>
                                          <td style = "text-align:center">' . ($row->jumlah > 0 ? round($abandonVal / $row->jumlah * 100, 2) : '0') . '%</td>
                                          <td style = "text-align:center">' . $row->totalavgduration . ' Second</td>
                                          <td style = "text-align:center">' . round($row->totalavgacd, 2) . ' Second</td>
                                        </tr>';

						if ($row) {
							($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field);
							$jumlah[] = $row->jumlah + $abandonVal;
							$jumlahanswer[] = $row->jumlahanswer;
							$jumlahabandon[] = $abandonVal;
							$call20[] = $row->call20;
							$totalavgduration[] = $row->totalavgduration;
							$jumlah_duration[] = $row->jumlah_duration;
							$isi[] = ($row->jumlah > 0 ? round($abandonVal / $row->jumlah * 100, 2) : '0');
							//                        $totalavgduration[] = $row->totalavgduration;
							$totalavgacd[] = round($row->totalavgacd, 2);
						}
					}
				}


				@$total = '<tr>
                         <td>Total</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($jumlah) . '</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($jumlahanswer) . '</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($jumlahabandon) . '</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($call20) . '</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($totalavgduration) . ' Second</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($jumlah_duration) . '</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($isi) . ' %</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($totalavgduration) . ' Second</td>
                         <td style = "text-align:center; font-weight: bold;">' . array_sum($totalavgacd) . ' Second</td>

                        </tr>';

				break;
			case 'outbound':
				if ($group_by == 'ALL') {
					$group_by = 'daily';
				}

				switch ($group_by) {
					case 'hourly':
						$field = 'concat(date(call_start),\'_\',hour(call_start))';
						$orderby = 'field asc';
						break;
					case 'daily':
						$field = 'date(call_start)';
						$orderby = 'field asc';
						break;
					case 'agent':
						$field = '(select u.fullName from user u where u.id=call_log_outgoing.id_user)';
						$orderby = 'field asc';
						break;
				}

				$resCLP = $this->db->select(''
					. 'count(call_start) as jumlah, '
					. 'count(answer_time) as jumlahanswer, '
					. 'sum(if(abandon=\'C\',1,0)) as jumlahabandon, '
					. 'avg(duration) as jumlahtalktime, '
					. '' . $field . ' as field')->from('call_log_outgoing')->where('date(call_start)>=', $tgl1)->where('date(call_start)<=', $tgl2)->group_by($field);

				$where_call_center = '';
				if ($call_center_number) {

					if ($this->input->get('call_center_number') == 'other') {
						$notIn = array('+123', '+147');
						$resCLP->where_not_in('call_center_number', $notIn);
						$where_call_center = ' and call_center_number not in (\'+123\',\'+147\')';
					} else {
						$resCLP->where('call_center_number', '+' . $this->input->get('call_center_number'));
						$where_call_center = ' and call_center_number =\'' . $this->input->get('call_center_number') . '\'';
					}
				}


				if (!$export) {
					$resCLP->limit(50, (($page - 1) * 50));

					$total_count_pre = $this->db->query('SELECT COUNT(*) as count FROM (SELECT ' . $field . ' as field FROM call_log_outgoing where date(call_start)>="' . $tgl1 . '" and date(call_start)<="' . $tgl2 . '" ' . $where_call_center . ' group by ' . $field . ') AS T')->result();
					$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;
				}


				$resCL = $resCLP->get()->result();

				$jumlah = [];
				$jumlahanswer = [];
				$jumlahabandon = [];
				$jumlahtalktime = [];
				$hasil = [];
				$hasil_dua = [];

				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_call_center_report_' . $report_type . '_' . $group_by . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('Interval', 'Outgoing Calls', 'Outgoing Calls Answered', 'Outgoing Abandoned Calls', 'AVG Talktime', 'Abandon Rate', 'Answered Rate');

					fputcsv($output, $header_args);

					foreach ($resCL as $row) {
						$data_item = array();

						$data_item[] = ($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field);
						$data_item[] = $row->jumlah;
						$data_item[] = $row->jumlahanswer;
						$data_item[] = $row->jumlahabandon;
						$data_item[] = round($row->jumlahtalktime, 2) . ' Second';
						$data_item[] = ($row->jumlah > 0 ? round($row->jumlahanswer / $row->jumlah * 100, 2) : '0') . '%';
						$data_item[] = ($row->jumlah > 0 ? round($row->jumlahabandon / $row->jumlah * 100, 2) : '0') . '%';

						if ($row) {
							($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field);
							$jumlah[] = $row->jumlah;
							$jumlahanswer[] = $row->jumlahanswer;
							$jumlahabandon[] = $row->jumlahabandon;
							$jumlahtalktime[] = round($row->jumlahtalktime, 2);
							$hasil[] = ($row->jumlah > 0 ? round($row->jumlahanswer / $row->jumlah * 100, 2) : '0');
							$hasil_dua[] = ($row->jumlah > 0 ? round($row->jumlahabandon / $row->jumlah * 100, 2) : '0');
						}

						fputcsv($output, $data_item);
					}

					$data_item = array();
					$data_item[] = 'Total';
					$data_item[] = array_sum($jumlah);
					$data_item[] = array_sum($jumlahanswer);
					$data_item[] = array_sum($jumlahabandon);
					$data_item[] = array_sum($jumlahtalktime) . ' Second';
					$data_item[] = array_sum($hasil) . '%';
					$data_item[] = array_sum($hasil_dua) . ' %';

					fputcsv($output, $data_item);

					exit();
				}

				foreach ($resCL as $row) {

					$content_report .= '<tr>
                                        <td style="text-align:center">' . ($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field) . '</td>
                                          <td style = "text-align:center">' . $row->jumlah . '</td>
                                          <td style = "text-align:center">' . $row->jumlahanswer . '</td>
                                          <td style = "text-align:center">' . $row->jumlahabandon . '</td>
                                          <td style = "text-align:center">' . round($row->jumlahtalktime, 2) . ' Second</td>
                                          <td style = "text-align:center">' . ($row->jumlah > 0 ? round($row->jumlahanswer / $row->jumlah * 100, 2) : '0') . '%</td>
                                          <td style = "text-align:center">' . ($row->jumlah > 0 ? round($row->jumlahabandon / $row->jumlah * 100, 2) : '0') . '%</td>
                    </tr>';

					if ($row) {
						($group_by == 'hourly' ? str_replace('_', ' Time : ' . (strlen($row->field) == 12 ? '0' : ''), $row->field) . ':00' : $row->field);
						$jumlah[] = $row->jumlah;
						$jumlahanswer[] = $row->jumlahanswer;
						$jumlahabandon[] = $row->jumlahabandon;
						$jumlahtalktime[] = round($row->jumlahtalktime, 2);
						$hasil[] = ($row->jumlah > 0 ? round($row->jumlahanswer / $row->jumlah * 100, 2) : '0');
						$hasil_dua[] = ($row->jumlah > 0 ? round($row->jumlahabandon / $row->jumlah * 100, 2) : '0');
					}
				}
				$total = '<tr>
                         <td>Total</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($jumlah) . '</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($jumlahanswer) . '</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($jumlahabandon) . '</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($jumlahtalktime) . ' Second</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($hasil) . '%</td>
                         <td style = "text-align:center;  font-weight: bold;">' . array_sum($hasil_dua) . ' %</td>



                        </tr>';
				break;
			case 'agent':
				if (!$group_by || $group_by == 'daily' || $group_by == 'hourly' || $group_by == 'agent') {
					$group_by = 'ALL';
				}

				$field = 'date(call_start)';
				$orderby = 'field asc';

				$this->db->select(''
					. 'call_log_incoming.id_user,'
					. 'user.fullName,'
					. 'sum(if(abandon=\'C\' or duration > 0, 1, 0)) as acd, '
					. 'sum(duration) as acd_times, '
					. 'sum(if(duration > 0, round(call_answer - call_start), 0)) as r2a, '
					. 'avg(duration) as acd_avg, '
					. 'avg(if(duration > 0, round(call_answer - call_start), 0)) as r2a_avg, '
					. 'sum(if(abandon = \'Y\', round(call_answer - call_start), 0)) as aux, '
					. '(select sum(ags.duration) from agentStatus ags where ags.userId=call_log_incoming.id_user and date(ags.time_login)=date(call_start) and date(ags.time_logout)=date(call_start) ) as avail, '
					. '(select sum(us.duration) from userStatus us where us.userId=call_log_incoming.id_user and date(us.time_login)=date(call_start) and date(us.time_logout)=date(call_start) ) as staff, '
					. 'sum(if(duration>0,1,0)) as connected , '
					. 'sum(if(call_log_incoming.status=\'waiting\',1,0)) as held , '
					. 'sum(if(abandon=\'C\',1,0)) as abandon , '
					. 'avg(if(duration>0,duration,null)) as aht , '
					. '' . $field . ' as field')->from('call_log_incoming')->where('date(call_start)>=', $tgl1)->join('user', 'user.id = call_log_incoming.id_user')->where('date(call_start)<=', $tgl2)->group_by($field);

				$where_call_center = '';
				if ($call_center_number) {

					if ($this->input->get('call_center_number') == 'other') {
						$notIn = array('+123', '+147');
						$this->db->where_not_in('call_center_number', $notIn);
						$where_call_center = ' and call_center_number not in (\'+123\',\'+147\')';
					} else {
						$this->db->where('call_center_number', '+' . $this->input->get('call_center_number'));
						$where_call_center = ' and call_center_number =\'' . $this->input->get('call_center_number') . '\'';
					}
				}

				$where_id_user = '';
				if ($group_by != 'ALL') {
					$this->db->where('call_log_incoming.id_user', $group_by);
					$where_id_user = ' and call_log_incoming.id_user=\'' . $group_by . '\'';
				}

				if (!$export) {
					$this->db->limit(50, (($page - 1) * 50));

					$total_count_pre = $this->db->query('SELECT COUNT(*) as count FROM (SELECT ' . $field . ' as field FROM call_log_incoming where date(call_start)>="' . $tgl1 . '" and date(call_start)<="' . $tgl2 . '" ' . $where_call_center . ' ' . $where_id_user . ' group by ' . $field . ') AS T')->result();
					$total_count = $total_count_pre && isset($total_count_pre[0]) && count($total_count_pre) > 0 ? $total_count_pre[0]->count : 0;
				}

				$resCLDb = $this->db->get()->result();

				$resCL = array();
				foreach ($resCLDb as $nr => $row) {
					$resCL[$row->field] = $row;
				}

				$loginStatus = array();
				if ($group_by != 'ALL') {
					$loginStatusDb = $this->db->query('select ut.time_login from userStatus ut where ut.userId=\'' . $group_by . '\' and date(time_login)>=\'' . $tgl1 . '\' and date(time_login)<=\'' . $tgl2 . '\' order by time_login desc')->result();

					foreach ($loginStatusDb as $lst) {
						$tgl = date('Y-m-d', strtotime($lst->time_login));
						$loginStatus[$tgl] = date('H:i:s', strtotime($lst->time_login));
					}
				}

				$logoutStatus = array();
				if ($group_by != 'ALL') {
					$logoutStatusDb = $this->db->query('select ut.time_logout from userStatus ut where ut.userId=\'' . $group_by . '\' and date(time_login)>=\'' . $tgl1 . '\' and date(time_login)<=\'' . $tgl2 . '\' order by time_logout asc')->result();

					foreach ($logoutStatusDb as $lst) {
						$tgl = date('Y-m-d', strtotime($lst->time_logout));
						$logoutStatus[$tgl] = date('H:i:s', strtotime($lst->time_logout));
					}
				}

				$workingStatus = array();
				if ($group_by != 'ALL') {
					$workingStatusDb = $this->db->query('select ut.duration,ut.time_login from userStatus ut where ut.userId=\'' . $group_by . '\' and date(time_login)>=\'' . $tgl1 . '\' and date(time_login)<=\'' . $tgl2 . '\' order by time_login asc')->result();

					foreach ($workingStatusDb as $lst) {
						$tgl = date('Y-m-d', strtotime($lst->time_login));

						isset($workingStatus[$tgl]) ? '' : $workingStatus[$tgl] = 0;

						$workingStatus[$tgl] += $lst->duration;
					}
				}

				$listAgentDb = $this->db->select('fullName,id')->from('user')->where('privilegeId', '3')->get()->result();
				$listAgent = array();

				foreach ($listAgentDb as $ag) {
					$listAgent[$ag->id] = $ag->fullName;
				}

				$period_pre = new DatePeriod(
					new DateTime($tgl1),
					new DateInterval('P1D'),
					new DateTime(date('Y-m-d', strtotime($tgl2) + 86400))
				);

				$period = array();

				if (!$export) {
					$nop = 0;
					foreach ($period_pre as $kp => $vp) {
						$nop++;

						if ($nop > ($page - 1) * 50 && $nop <= $page * 50) {
							$period[$kp] = $vp;
						}
					}
				} else {
					$period = $period_pre;
				}

				$satu = [];
				$dua = [];
				$tiga = [];
				$empat = [];
				$lima = [];
				$enam = [];
				$tujuh = [];
				$delapan = [];
				$sembilan = [];
				$sepuluh = [];
				$sebelas = [];
				$duabelas = [];

				if ($export) {
					header('Content-Type: text/csv; charset=utf-8');
					header('Content-Disposition: attachment; filename=report_call_center_report_' . $report_type . '_' . $group_by . '.csv');

					$output = fopen('php://output', 'w');

					ob_end_clean();

					$header_args = array('No', 'Date', 'Log In Time', 'ACD Calls', 'ACD Times', 'R2A(Ring to Answer)', 'AUX Time', 'Avail Time', 'Staffed Time', 'Connected Calls', 'Held Calls', 'Abandon Calls', 'AHT', 'Logout Out Time', 'WorkingTime');

					fputcsv($output, $header_args);

					foreach ($period as $key => $value) {
						$data_item = array();

						$tglR = $value->format('Y-m-d');
						$row = isset($resCL[$tglR]) ? $resCL[$tglR] : null;

						$data_item[] = ($key + 1);
						$data_item[] = $value->format('d F Y');

						if ($row) {
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : ''));
							$data_item[] = $row->acd;
							$data_item[] = $this->time2string($row->acd_times);
							$data_item[] = $this->time2string($row->r2a);
							$data_item[] = $this->time2string($row->aux);
							$data_item[] = ($row->avail > 0 ? $this->time2string($row->avail) : 0);
							$data_item[] = ($row->staff > 0 ? $this->time2string($row->staff) : 0);
							$data_item[] = $row->connected;
							$data_item[] = $row->held;
							$data_item[] = $row->abandon;
							$data_item[] = $this->time2string($row->aht);
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : ''));
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : ''));
						} else {
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : ''));
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = '';
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : ''));
							$data_item[] = ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : ''));
						}

						if ($row) {
							($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : ''));
							$satu[] = $row->acd;
							$dua[] = $row->acd_times;
							$tiga[] = $row->r2a;
							$empat[] = $row->aux;
							$lima[] = ($row->avail > 0 ? $row->avail : 0);
							$enam[] = ($row->staff > 0 ? $row->staff : 0);
							$tujuh[] = $row->connected;
							$delapan[] = $row->held;
							$sembilan[] = $row->abandon;
							$sepuluh[] = $row->aht;
							$sebelas[] = ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : ''));
							$duabelas[] = ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : ''));
						}

						fputcsv($output, $data_item);
					}

					$data_item = array();
					$data_item[] = 'Total';
					$data_item[] = array_sum($satu);
					$data_item[] = $this->time2string(array_sum($dua));
					$data_item[] = $this->time2string(array_sum($tiga));
					$data_item[] = $this->time2string(array_sum($empat));
					$data_item[] = $this->time2string(array_sum($lima));
					$data_item[] = $this->time2string(array_sum($enam));
					$data_item[] = array_sum($tujuh);
					$data_item[] = array_sum($delapan);
					$data_item[] = array_sum($sembilan);
					$data_item[] = $this->time2string(array_sum($sepuluh));
					$data_item[] = array_sum($sebelas);
					$data_item[] = array_sum($duabelas);


					fputcsv($output, $data_item);

					exit();
				}

				foreach ($period as $key => $value) {
					$tglR = $value->format('Y-m-d');
					$row = isset($resCL[$tglR]) ? $resCL[$tglR] : null;

					$content_report .= '<tr>';
					$content_report .= '<td style="text-align:center">' . ($key + 1) . '.</td>';
					$content_report .= '<td style="text-align:center">' . $value->format('d F Y') . '</td>';
					if ($row) {
						$content_report .= '
                                          <td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : '')) . '</td>
                                          <td style = "text-align:center">' . $row->acd . '</td>
                                          <td style = "text-align:center">' . $this->time2string($row->acd_times) . '</td>
                                          <td style = "text-align:center">' . $this->time2string($row->r2a) . '</td>
                                          <td style = "text-align:center">' . $this->time2string($row->aux) . '</td>
                                          <td style = "text-align:center">' . ($row->avail > 0 ? $this->time2string($row->avail) : 0) . '</td>
                                          <td style = "text-align:center">' . ($row->staff > 0 ? $this->time2string($row->staff) : 0) . '</td>
                                          <td style = "text-align:center">' . $row->connected . '</td>
                                          <td style = "text-align:center">' . $row->held . '</td>
                                          <td style = "text-align:center">' . $row->abandon . '</td>
                                          <td style = "text-align:center">' . $this->time2string($row->aht) . '</td>
                                          <td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : '')) . '</td>
                                          <td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : '')) . '</td>';
					} else {
						$content_report .= '<td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : '')) . '</td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style="text-align:center"></td>';
						$content_report .= '<td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : '')) . '</td>';
						$content_report .= '<td style = "text-align:center">' . ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : '')) . '</td>';
					}
					$content_report .= '</tr>';

					if ($row) {
						($group_by == 'ALL' ? '' : (isset($loginStatus[$tglR]) ? $loginStatus[$tglR] : ''));
						$satu[] = $row->acd;
						$dua[] = $row->acd_times;
						$tiga[] = $row->r2a;
						$empat[] = $row->aux;
						$lima[] = ($row->avail > 0 ? $row->avail : 0);
						$enam[] = ($row->staff > 0 ? $row->staff : 0);
						$tujuh[] = $row->connected;
						$delapan[] = $row->held;
						$sembilan[] = $row->abandon;
						$sepuluh[] = $row->aht;
						$sebelas[] = ($group_by == 'ALL' ? '' : (isset($logoutStatus[$tglR]) ? $logoutStatus[$tglR] : ''));
						$duabelas[] = ($group_by == 'ALL' ? '' : (isset($workingStatus[$tglR]) ? $this->time2string($workingStatus[$tglR]) : ''));
					}
				}

				$total = '<tr>
                         <td></td>
                         <td style="text-align:center;  font-weight: bold;">Total</td>
                         <td style="text-align:center;  font-weight: bold;"></td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($satu) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($dua)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($tiga)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($empat)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($lima)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($enam)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($tujuh) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($delapan) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($sembilan) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . $this->time2string(array_sum($sepuluh)) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($sebelas) . '</td>
                         <td style="text-align:center;  font-weight: bold;">' . array_sum($duabelas) . '</td>





                        </tr>';

				break;
		}

		$d = [
			'title' => "Reports - Call Center Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'call_center_report',
			'report_type' => $report_type,
			'content_report' => $content_report,
			'total' => $total,
			'group_by' => $group_by,
			'listAgent' => $listAgent,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'call_center_number' => $call_center_number,
			'total_count' => $total_count,
			'page' => $page
		];
		$this->load->view('page/_main', $d);
	}

	public function dashboard_history()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$call_center_number = $this->input->get('call_center_number') ? $this->input->get('call_center_number') : '';

		if ($this->session->userdata('tipe') == '123') {
			$call_center_number = '123';
		} elseif ($this->session->userdata('tipe') == '147') {
			$call_center_number = '147';
		} elseif ($this->session->userdata('tipe') == '888') {
			$call_center_number = '888';
		}


		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'dashboard_history',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'call_center_number' => $call_center_number
		];
		$this->load->view('page/_main', $d);
	}

	public function activation_log()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$page = $this->input->get('page') ? $this->input->get('page') : 1;
		$all = $this->input->get('all') ? $this->input->get('all') : false;
		$limit = $all ? 1000000000000 : 250;

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);

		$listDescripsi = file_get_contents('https://apimytelkomcel.telkomcel.tl:8445/CMSTelkomcel/packagename?page=1&start=0&limit=100000&sort=&filter=', false, stream_context_create($arrContextOptions));
		$listDescripsi = json_decode($listDescripsi, true);
		$listDescripsiMap = array();
		$listDescripsiMapName = array();

		foreach ($listDescripsi['rows'] as $pk) {
			if ($pk['language']['pk'] == 2) {
				$listDescripsiMap[$pk['refilId']] = $pk['name'] . '<br><br>' . $pk['description'];
				$listDescripsiMapName[$pk['refilId']] = $pk['refilId'] . '<br>' . $pk['name'] . '<br><br>' . $pk['description'];
			}
		}

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		$totalPage = 0;

		$listData = $this->db->select('*')->from('activation_log')->where('date(tgl)>=', $tgl1)->where('date(tgl)<=', $tgl2)->order_by('tgl', 'asc')->get()->result();
		$totalPage = ceil(count($listData) / $limit);

		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->tgl)) . '</td>';
					$reportData .= '<td>' . $ld->refill . '</td>';
					$reportData .= '<td>' . (isset($listDescripsiMap[$ld->refill]) ? $listDescripsiMap[$ld->refill] : '-') . '</td>';
					$reportData .= '<td>' . $ld->msisdn . '</td>';
					$reportData .= '</tr>';
				}
				!isset($reportChartPre[$ld->refill]) ? $reportChartPre[$ld->refill] = 0 : '';

				$reportChartPre[$ld->refill] += 1;
			}

			foreach ($reportChartPre as $kc => $rc) {
				$kc = str_replace('x', '', $kc);
				$reportChart[] = array('name' => isset($listDescripsiMapName[$kc]) ? $listDescripsiMapName[$kc] : $kc, 'y' => $rc);
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no activation log in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'activation_log',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'page' => $page,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'totalPage' => $totalPage,
			'all' => $all
		];

		if ($all) {
			$this->load->view($this->path . 'activation_log', $d);
		} else {
			$this->load->view('page/_main', $d);
		}
	}

	public function activation_log_sosmed()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$page = $this->input->get('page') ? $this->input->get('page') : 1;
		$all = $this->input->get('all') ? $this->input->get('all') : false;
		$limit = $all ? 1000000000000 : 250;

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);

		$listDescripsi = file_get_contents('https://apimytelkomcel.telkomcel.tl:8445/CMSTelkomcel/packagename?page=1&start=0&limit=100000&sort=&filter=', false, stream_context_create($arrContextOptions));
		$listDescripsi = json_decode($listDescripsi, true);
		$listDescripsiMap = array();
		$listDescripsiMapName = array();

		foreach ($listDescripsi['rows'] as $pk) {
			if ($pk['language']['pk'] == 2) {
				$listDescripsiMap[$pk['refilId']] = $pk['name'] . '<br><br>' . $pk['description'];
				$listDescripsiMapName[$pk['refilId']] = $pk['refilId'] . '<br>' . $pk['name'] . '<br><br>' . $pk['description'];
			}
		}

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		$totalPage = 0;

		$listData = $this->db->select('*')->from('activation_log_sosmed')->where('date(tgl)>=', $tgl1)->where('date(tgl)<=', $tgl2)->order_by('tgl', 'asc')->get()->result();
		$totalPage = ceil(count($listData) / $limit);

		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->tgl)) . '</td>';
					$reportData .= '<td>' . $ld->refill . '</td>';
					$reportData .= '<td>' . (isset($listDescripsiMap[$ld->refill]) ? $listDescripsiMap[$ld->refill] : '-') . '</td>';
					$reportData .= '<td>' . $ld->msisdn . '</td>';
					$reportData .= '<td>' . $ld->channel . '</td>';
					$reportData .= '</tr>';
				}
				!isset($reportChartPre[$ld->refill]) ? $reportChartPre[$ld->refill] = 0 : '';

				$reportChartPre[$ld->refill] += 1;
			}

			foreach ($reportChartPre as $kc => $rc) {
				$kc = str_replace('x', '', $kc);
				$reportChart[] = array('name' => isset($listDescripsiMapName[$kc]) ? $listDescripsiMapName[$kc] : $kc, 'y' => $rc);
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no activation log in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'activation_log_sosmed',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'page' => $page,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'totalPage' => $totalPage,
			'all' => $all
		];

		if ($all) {
			$this->load->view($this->path . 'activation_log_sosmed', $d);
		} else {
			$this->load->view('page/_main', $d);
		}
	}

	public function call_log()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$call_center_number = $this->input->get('call_center_number') ? $this->input->get('call_center_number') : '';
		$page = $this->input->get('page') ? $this->input->get('page') : 1;
		$limit = 50;

		if ($this->session->userdata('tipe') == '123') {
			$call_center_number = '123';
		} elseif ($this->session->userdata('tipe') == '147') {
			$call_center_number = '147';
		} elseif ($this->session->userdata('tipe') == '888') {
			$call_center_number = '888';
		}

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		$totalPage = 0;

		$resCLO = $this->db->query("select call_start as tgl,msisdn, 'incoming',if(exten='activation','activation',if(abandon='C' or answer_time>0,'complaint','product')) as jenis from call_log_incoming where date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' order by tgl desc");
		if ($call_center_number) {

			$whereCLO = '';
			if ($this->input->get('call_center_number') == 'other') {
				$whereCLO = ' and call_center_number not in (\'+123\',\'+147\')';
			} else {
				$whereCLO = ' and call_center_number=\'+' . $call_center_number . '\'';
			}

			$resCLO = $this->db->query("select call_start as tgl,msisdn, 'incoming',if(exten='activation','activation',if(abandon='C' or answer_time>0,'complaint','product')) as jenis from call_log_incoming where date(call_start)>='" . $tgl1 . "' and date(call_start)<='" . $tgl2 . "' " . $whereCLO . " order by tgl desc");
		}
		$listData = $resCLO->result();
		$totalPage = ceil(count($listData) / $limit);

		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->tgl)) . '</td>';
					$reportData .= '<td>' . $ld->msisdn . '</td>';
					$reportData .= '<td>' . $ld->jenis . '</td>';
					$reportData .= '</tr>';
				}
				!isset($reportChartPre[$ld->jenis]) ? $reportChartPre[$ld->jenis] = 0 : '';

				$reportChartPre[$ld->jenis] += 1;
			}

			foreach ($reportChartPre as $kc => $rc) {
				$reportChart[] = array('name' => ucfirst($kc), 'y' => $rc);
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no call log in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'call_log',
			'call_center_number' => $call_center_number,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'page' => $page,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'totalPage' => $totalPage
		];
		$this->load->view('page/_main', $d);
	}

	public function ivr()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		//        $call_center_number = $this->input->get('call_center_number') ? $this->input->get('call_center_number') : '';
		$status = $this->input->get('status') ? $this->input->get('status') : '';
		$page = $this->input->get('page') ? $this->input->get('page') : 1;
		$limit = 50;

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		$totalPage = 0;
		$whereStatus = '';

		if ($status) {
			$whereStatus = ' and result=\'' . $status . '\' ';
		}
		//
		//        $whereCallCenter = '';
		//        if ($call_center_number) {
		//
		//            if ($call_center_number == 'other') {
		//                $notIn           = array('+123', '+147');
		//                $whereCallCenter = ' and call_center_number not in (\'+147\',\'+123\') ';
		//            } else {
		//                $whereCallCenter = ' and call_center_number=\'+' . $call_center_number . '\' ';
		//            }
		//        }
		//
		//        $whereStatus .= $whereCallCenter;

		$listData = $this->db->query("select * from autocall_ivr where date(tgl)>='" . $tgl1 . "' and date(tgl)<='" . $tgl2 . "' " . $whereStatus . " order by tgl desc")->result();
		$totalPage = ceil(count($listData) / $limit);

		$page--;

		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				if (($nd) >= $page * $limit && ($nd) <= ($page * $limit) + $limit - 1) {
					$reportData .= '<tr>';
					$reportData .= '<td style="text-align:center">' . ($nd + 1) . '.</td>';
					$reportData .= '<td>' . date('d F Y H:i:s', strtotime($ld->tgl)) . '</td>';
					$reportData .= '<td>' . $ld->msisdn . '</td>';
					$reportData .= '<td>' . strtoupper($ld->lang) . '</td>';
					$reportData .= '<td>' . ucfirst($ld->status) . '</td>';
					$reportData .= '<td>' . ucfirst($ld->result) . '</td>';
					$reportData .= '</tr>';
				}
				!isset($reportChartPre[$ld->result]) ? $reportChartPre[$ld->result] = 0 : '';

				$reportChartPre[$ld->result] += 1;
			}

			foreach ($reportChartPre as $kc => $rc) {
				$reportChart[] = array('name' => ucfirst($kc), 'y' => $rc);
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no IVR call log in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - CH Group Report :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'ivr',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'page' => $page,
			'status' => $status,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'totalPage' => $totalPage
		];
		$this->load->view('page/_main', $d);
	}

	public function cobaview()
	{
		$query['data'] = $this->db->query("SELECT * FROM user")->result();

		$this->load->view('page/reports/cobaview', $query);
	}

	public function test()
	{
		$this->load->model('UnitModel', 'u');
		$this->load->model('CategoryModel', 'c');
		$tampung = [];

		$ok = $this->db->query("SELECT COUNT(*) as jumlahunitcategory,ch.categoryId,ch2.unitId ,unit.unitName,categoryName FROM complain ch INNER JOIN complainthistory ch2 ON ch2.complainId=ch.id INNER JOIN category ON category.categoryId=ch.categoryId INNER JOIN unit ON unit.id=ch2.unitId INNER JOIN user ON user.id=ch.userId WHERE ch2.status='V' AND user.privilegeId='0' AND ch2.createDate >= '2018-07-15 12:28:48' AND ch2.createDate <= '2020-07-15 12:28:48' GROUP BY ch.categoryId,ch2.unitId")->result();
		foreach ($ok as $v) {
			$dt[$v->categoryId][$v->unitId] = $v->jumlahunitcategory;
			array_push($tampung, $dt);
		}
		$data = [
			'unit' => $this->u->getUnit()->result(),
			'category' => $this->c->getCategoryName()->result(),
			'jml' => $tampung
		];

		$this->load->view('test', $data);
	}

	public function detail_report()
	{
		$categoryId = $this->input->get('categoryId');
		$status = $this->input->get('status');
		$unitId = $this->input->get('unitId');

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_report',
			'data' => $this->db->query("SELECT ch.*,c.categoryId,c.complainDate,c.transactionCode,c.district,c.detailComplain,cc.categoryName,u.unitName FROM `complainthistory` ch INNER JOIN complain c ON c.id = ch.complainId INNER JOIN category cc ON cc.categoryId=c.categoryId INNER JOIN unit u ON u.id=ch.unitId WHERE c.categoryId='$categoryId' AND ch.status = '$status'")->result()
		];

		// echo $this->db->last_query();
		$this->load->view('page/_main', $d);
	}

	public function detail_category()
	{
		$categoryId = $this->input->get('categoryId');
		$status = $this->input->get('status');

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_category',
			'data' => $this->db->query("SELECT c.complainDate,c.status,c.id,unit.unitName,c.transactionCode,c.btsLocation,c.createDate,c.district,c.detailComplain,c.status,c.categoryId, ch.solution, ch.unitid FROM complain c inner join complainthistory ch on c.id=ch.complainid INNER JOIN unit ON unit.id=ch.unitId WHERE c.categoryId='$categoryId' AND c.status=ch.status AND c.status='$status'")->result()
		];
		$this->load->view('page/_main', $d);
	}

	public function detail_location()
	{
		$categoryId = $this->input->get('categoryId');
		$status = $this->input->get('status');

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_location',
			'data' => $this->db->query("SELECT c.id,unit.unitName,c.transactionCode,c.btsLocation,c.createDate,c.district,c.detailComplain,c.status,c.categoryId, ch.solution, ch.unitid FROM complain c inner join complainthistory ch on c.id=ch.complainid INNER JOIN unit ON unit.id=ch.unitId WHERE c.categoryId='$categoryId' AND c.status=ch.status AND c.status='$status'")->result()
		];
		$this->load->view('page/_main', $d);
	}

	public function detail_cc_abandon()
	{
		$groupBy = $this->input->get('groupBy');
		$item = $this->input->get('item');
		$tgl1 = $this->input->get('tgl1');
		$tgl2 = $this->input->get('tgl2');
		$page = $this->input->get('page');

		$page = $page ? $page : 1;

		$where = '';
		switch ($groupBy) {
			case 'daily':
				$where = 'date(tgl)=\'' . $item . '\'';
				break;
			case 'hourly':
				$where = 'date(tgl)=\'' . date('Y-m-d', strtotime($item)) . '\' and hour(tgl)=\'' . date('H', strtotime($item)) . '\'';
				break;
			case 'agent':
				$where = 'id_user=\'' . $item . '\'';
				break;
		}

		$where .= ' and abandon=\'C\' and date(tgl)>=\'' . $tgl1 . '\' and date(tgl)<=\'' . $tgl2 . '\' ';

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_cc',
			'count' => $this->db->query("SELECT count(id) as jumlah from call_log_abandon where $where")->result(),
			'data' => $this->db->query("SELECT tgl, (select cin.msisdn from call_log_incoming as cin where cin.uniqueid=call_log_abandon.uniqueid) as msisdn from call_log_abandon where $where order by tgl asc limit " . (($page - 1) * 50) . ", 50")->result(),
			'page' => $page,
			'query' => 'groupBy=' . $groupBy . '&item=' . $item . '&tgl1=' . $tgl1 . '&tgl2=' . $tgl2
		];

		$this->load->view('page/_main', $d);
	}

	public function detail_cc_answered()
	{
		$groupBy = $this->input->get('groupBy');
		$item = $this->input->get('item');
		$tgl1 = $this->input->get('tgl1');
		$tgl2 = $this->input->get('tgl2');
		$page = $this->input->get('page');

		$page = $page ? $page : 1;

		$where = '';
		switch ($groupBy) {
			case 'daily':
				$where = 'date(tgl)=\'' . $item . '\'';
				break;
			case 'hourly':
				$where = 'date(tgl)=\'' . date('Y-m-d', strtotime($item)) . '\' and hour(tgl)=\'' . date('H', strtotime($item)) . '\'';
				break;
			case 'agent':
				$where = 'id_user=\'' . $item . '\'';
				break;
		}

		$where .= ' and abandon!=\'Y\' and date(tgl)>=\'' . $tgl1 . '\' and date(tgl)<=\'' . $tgl2 . '\' ';

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_cc',
			'count' => $this->db->query("SELECT count(id) as jumlah from call_log_abandon where $where")->result(),
			'data' => $this->db->query("SELECT tgl, (select cin.msisdn from call_log_incoming as cin where cin.uniqueid=call_log_abandon.uniqueid) as msisdn from call_log_abandon where $where order by tgl asc limit " . (($page - 1) * 50) . ", 50")->result(),
			'page' => $page,
			'query' => 'groupBy=' . $groupBy . '&item=' . $item . '&tgl1=' . $tgl1 . '&tgl2=' . $tgl2
		];

		$this->load->view('page/_main', $d);
	}

	public function detail_cc_all()
	{
		$groupBy = $this->input->get('groupBy');
		$item = $this->input->get('item');
		$tgl1 = $this->input->get('tgl1');
		$tgl2 = $this->input->get('tgl2');
		$page = $this->input->get('page');

		$page = $page ? $page : 1;

		$where = '';
		switch ($groupBy) {
			case 'daily':
				$where = 'date(tgl)=\'' . $item . '\'';
				break;
			case 'hourly':
				$where = 'date(tgl)=\'' . date('Y-m-d', strtotime($item)) . '\' and hour(tgl)=\'' . date('H', strtotime($item)) . '\'';
				break;
			case 'agent':
				$where = 'id_user=\'' . $item . '\'';
				break;
		}

		$where .= ' and date(tgl)>=\'' . $tgl1 . '\' and date(tgl)<=\'' . $tgl2 . '\' ';

		$d = [
			'title' => "Helpdesk :: Detail Complain",
			'linkView' => $this->path . 'detail_cc',
			'count' => $this->db->query("SELECT count(id) as jumlah from call_log_abandon where $where")->result(),
			'data' => $this->db->query("SELECT tgl, (select cin.msisdn from call_log_incoming as cin where cin.uniqueid=call_log_abandon.uniqueid) as msisdn from call_log_abandon where $where order by tgl asc limit " . (($page - 1) * 50) . ", 50")->result(),
			'page' => $page,
			'query' => 'groupBy=' . $groupBy . '&item=' . $item . '&tgl1=' . $tgl1 . '&tgl2=' . $tgl2
		];

		$this->load->view('page/_main', $d);
	}

	public function counter_summary()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_plaza = $this->input->get('id_plaza') ? $this->input->get('id_plaza') : '';

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		// $listAgentPre = array();
		$dateRange = [];

		// $resAg = $this->db->query("select fullName as name, id
		// 							from user
		// 							where id_counter is not null
		// 							" . ($id_plaza ? "and id_counter_setting='" . $id_plaza . "'" : "") . "
		// 							order by fullName asc");
		// $listAgentPre  = $resAg->result();

		// if (count($listData) > 0) {
		// 	foreach ($listData as $nd => $ld) {
		// 		$listAgentPre[$ld['id']] = $ld['name'];
		// 	}
		// }

		$resCLO = $this->db->query("select date(tgl) as tgl, msisdn, nama
									from queue_list
									where date(tgl)>='" . $tgl1 . "' and date(tgl)<='" . $tgl2 . "'
									" . ($id_plaza ? "and id_counter_setting='" . $id_plaza . "'" : "") . "
									order by tgl asc");
		$listData = $resCLO->result();

		$date1 = strtotime($tgl1);
		$date2 = strtotime($tgl2);

		// Loop through dates from start to end, inclusive
		while ($date1 <= $date2) {
			$dateRange[] = date('Y-m-d', $date1);
			$date1 += 86400;
		}

		// echo '<pre>';
		// print_r($dateRange);
		// exit();


		if (count($listData) > 0) {
			foreach ($listData as $nd => $ld) {
				!isset($reportChartPre[$ld->tgl]) ? $reportChartPre[$ld->tgl] = 0 : '';

				$reportChartPre[$ld->tgl] += 1;
			}

			foreach ($dateRange as $d) {
				$val = (isset($reportChartPre[$d]) ? $reportChartPre[$d] : 0);
				$reportChart[] = array('name' => date('d-m-Y', strtotime($d)), 'y' => $val);

				$reportData .= '<tr>';
				$reportData .= '<td style="text-align:center">' . date('d-m-Y', strtotime($d)) . '</td>';
				$reportData .= '<td>' . $val . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no counter data in this period</td></tr>';
		}

		$listPlaza = $this->db->query('select * from queue_setting')->result();

		// global $call_center_number;

		$d = [
			'title' => "Reports - Counter Summary :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'counter_summary',
			// 'call_center_number' => $call_center_number,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'dateRange' => $dateRange,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'listPlaza' => $listPlaza,
			'id_plaza' => $id_plaza,
		];

		$this->load->view('page/_main', $d);
	}

	public function cso_performance()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_plaza = $this->input->get('id_plaza') ? $this->input->get('id_plaza') : '';

		$reportData = '';
		$reportChart = array();
		$reportChartPre = array();
		$listAgentPre = array();
		$dateRange = [];

		$resAg = $this->db->query("select fullName as name, id, username
									from user
									where id_counter is not null
									" . ($id_plaza ? "and id_counter_setting='" . $id_plaza . "'" : "") . "
									order by fullName asc");
		$listAgent = $resAg->result();

		$resCLO = $this->db->query("select date(ph.date_history) as date_activity, ph.activity, ph.id_user
									from plaza_history ph
									where date(ph.date_history)>='" . $tgl1 . "' and date(ph.date_history)<='" . $tgl2 . "'
									" . ($id_plaza ? "and ph.id_plaza='" . $id_plaza . "'" : "") . "
									order by date_activity asc");
		$listData = $resCLO->result();

		$date1 = strtotime($tgl1);
		$date2 = strtotime($tgl2);

		// Loop through dates from start to end, inclusive
		while ($date1 <= $date2) {
			$dateRange[] = date('Y-m-d', $date1);
			$date1 += 86400;
		}

		// echo '<pre>';
		// print_r($dateRange);
		// exit();


		if (count($listData) > 0) {
			$reportPerAgent = array();

			foreach ($listData as $nd => $ld) {
				!isset($reportChartPre[$ld->date_activity]) ? $reportChartPre[$ld->date_activity] = 0 : '';
				!isset($reportPerAgent[$ld->id_user]) ? $reportPerAgent[$ld->id_user] = array() : '';
				!isset($reportPerAgent[$ld->id_user][$ld->activity]) ? $reportPerAgent[$ld->id_user][$ld->activity] = 0 : '';

				$reportPerAgent[$ld->id_user][$ld->activity] += 1;
				$reportChartPre[$ld->date_activity] += 1;
			}

			foreach ($dateRange as $d) {
				$val = (isset($reportChartPre[$d]) ? $reportChartPre[$d] : 0);
				$reportChart[] = array('name' => date('d-m-Y', strtotime($d)), 'y' => $val);
			}

			$listActivity = array('information', 'penjualan', 'registration', 'complaint');

			foreach ($listAgent as $la) {
				$reportData .= '<tr>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_performance?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->username . '</a></td>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_performance?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->name . '</a></td>';

				foreach ($listActivity as $vt) {
					$reportData .= '<td style="text-align:center">' . (isset($reportPerAgent[$la->id][$vt]) ? $reportPerAgent[$la->id][$vt] : 0) . '</td>';
				}

				$reportData .= '<td style="text-align:center">' . (isset($reportPerAgent[$la->id]) ? array_sum($reportPerAgent[$la->id]) : 0) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$listPlaza = $this->db->query('select * from queue_setting')->result();

		$d = [
			'title' => "Reports - CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'cso_performance',
			// 'call_center_number' => $call_center_number,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'dateRange' => $dateRange,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'listPlaza' => $listPlaza,
			'id_plaza' => $id_plaza,
		];

		$this->load->view('page/_main', $d);
	}

	public function detail_cso_performance()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_cso = $this->input->get('id_cso') ? $this->input->get('id_cso') : '';

		$reportData = '';

		$cso_name = $this->db->query("select fullName as name from user where user.id='$id_cso'")->row();

		$resCLO = $this->db->query("select *
									from plaza_history ph
									where date(ph.date_history)>='" . $tgl1 . "' and date(ph.date_history)<='" . $tgl2 . "'
									" . ($id_cso ? "and ph.id_user='" . $id_cso . "'" : "") . "
									order by date_history asc");
		$listData = $resCLO->result();

		if (count($listData) > 0) {
			foreach ($listData as $la) {
				$reportData .= '<tr>';
				$reportData .= '<td>' . date('d F Y H:i:s', strtotime($la->date_history)) . '</td>';
				$reportData .= '<td>' . $la->msisdn . '</td>';
				$reportData .= '<td>' . $la->name . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->queue_no . '</td>';
				$reportData .= '<td>' . ucfirst($la->activity) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - Detail CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'detail_cso_performance',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'reportData' => $reportData,
			'cso_name' => isset($cso_name->name) ? $cso_name->name : '-',
		];

		$this->load->view('page/_main', $d);
	}

	public function cso_purchase()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_plaza = $this->input->get('id_plaza') ? $this->input->get('id_plaza') : '';

		$reportData = '';
		$reportChart = array();
		$reportCategory = array();
		$listReportCategory = array();
		$reportChartPre = array();
		$listAgentPre = array();
		$dateRange = [];

		$resAg = $this->db->query("select fullName as name, id, username,outlet_number
									from user
									where outlet_number is not null
									" . ($id_plaza ? "and id_counter_setting='" . $id_plaza . "'" : "") . "
									order by fullName asc");
		$listAgent = $resAg->result();

		$list_number = [];
		$map_id_user = [];

		foreach ($listAgent as $la) {
			$list_number[] = '"670' . $la->outlet_number . '"';
			$map_id_user['670' . $la->outlet_number] = $la->id;

			$reportCategory[] = $la->name;
			$listReportCategory[] = $la->id;
		}

		$response_cso = '';
		$response_sell_thru = '';

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


			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.telkomcel.tl/tetum-dashboard/api/performance/sell-thru/list-by-cso',
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

			$response_sell_thru = curl_exec($curl);
			curl_close($curl);
		}

		$listData_cso = json_decode($response_cso, true);
		$listData_sell_thru = json_decode($response_sell_thru, true);

		$listData = (isset($listData_cso['code']) && $listData_cso['code'] == 200 && isset($listData_cso['data']) ? $listData_cso['data'] : []) +
			(isset($listData_sell_thru['code']) && $listData_sell_thru['code'] == 200 && isset($listData_sell_thru['data']) ? $listData_sell_thru['data'] : []);

		$date1 = strtotime($tgl1);
		$date2 = strtotime($tgl2);

		// Loop through dates from start to end, inclusive
		while ($date1 <= $date2) {
			$dateRange[] = date('Y-m-d', $date1);
			$date1 += 86400;
		}

		// echo '<pre>';
		// print_r($dateRange);
		// exit();


		if (count($listData) > 0) {
			$reportPerAgent = array();
			$reportPerAgentPrice = array();
			$reportCode = array();

			$series = array();

			foreach ($listData as $nd => $ld) {
				// $trx_date = date('Y-m-d', strtotime($ld['created_at']));
				$id_user = $map_id_user[$ld['customer_msisdn']];

				$keyCateg = $ld['product_type'];

				if (!in_array($keyCateg, $series)) {
					$series[] = $ld['product_type'];
				}

				!isset($reportChartPre[$id_user]) ? $reportChartPre[$id_user] = [] : '';
				!isset($reportChartPre[$id_user][$keyCateg]) ? $reportChartPre[$id_user][$keyCateg] = 0 : '';

				!isset($reportPerAgent[$id_user]) ? $reportPerAgent[$id_user] = 0 : '';
				!isset($reportPerAgentPrice[$id_user]) ? $reportPerAgentPrice[$id_user] = 0 : '';

				$reportPerAgent[$id_user] += isset($ld['quantity']) && $ld['quantity'] > 0 ? $ld['quantity'] : 1;
				$reportPerAgentPrice[$id_user] += (float) $ld['price'];
				$reportChartPre[$id_user][$keyCateg] += isset($ld['quantity']) && $ld['quantity'] > 0 ? $ld['quantity'] : 1;
				$reportCode[$id_user] = $ld['customer_code'];
			}

			foreach ($series as $dc) {
				$dataStack = array();
				foreach ($listReportCategory as $d) {
					$val = (isset($reportChartPre[$d][$dc]) ? $reportChartPre[$d][$dc] : 0);

					$dataStack[] = $val;
				}

				$reportChart[] = array('name' => $dc, 'data' => $dataStack);
			}

			foreach ($listAgent as $la) {
				$reportData .= '<tr>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_purchase?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->username . '</a></td>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_purchase?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->name . '</a></td>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_purchase?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $reportCode[$la->id] . '</a></td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportPerAgent[$la->id]) ? $reportPerAgent[$la->id] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">$' . (isset($reportPerAgentPrice[$la->id]) ? number_format($reportPerAgentPrice[$la->id]) : 0) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$listPlaza = $this->db->query('select * from queue_setting')->result();

		$d = [
			'title' => "Reports - CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'cso_purchase',
			// 'call_center_number' => $call_center_number,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'dateRange' => $dateRange,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'reportCategory' => $reportCategory,
			'listPlaza' => $listPlaza,
			'id_plaza' => $id_plaza,
		];

		$this->load->view('page/_main', $d);
	}
	public function cso_satisfaction()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_plaza = $this->input->get('id_plaza') ? $this->input->get('id_plaza') : '';

		$reportData = '';
		$reportChart = array();
		$reportCategory = array();
		$listReportCategory = array();
		$reportChartPre = array();
		$listAgentPre = array();
		$dateRange = [];

		$resAg = $this->db->query("select fullName as name, id, username,outlet_number
									from user
									where outlet_number is not null
									" . ($id_plaza ? "and id_counter_setting='" . $id_plaza . "'" : "") . "
									order by fullName asc");
		$listAgent = $resAg->result();

		foreach ($listAgent as $la) {
			$reportCategory[] = $la->name;
			$listReportCategory[] = $la->id;
		}

		$listData = $this->db->query("select
									avg(rating_1) as avg_rating_1,
									avg(rating_2) as avg_rating_2,
									avg(rating_3) as avg_rating_3,
									avg(rating_4) as avg_rating_4,
									avg(rating_5) as avg_rating_5,
									count(id) as total_customer,
									userId
									from complain
									where
									complainDate>='$tgl1' and
									complainDate<='$tgl2' and
									rating_1>0 and
									userId in (" . implode(',', $listReportCategory) . ")
									group by userId")->result();

		$list_count = array();

		if (count($listData) > 0) {
			$series = array('Friendliness', 'Solution', 'Price & Quality', 'Network', 'Facilities');

			foreach ($listData as $nd => $ld) {
				// $trx_date = date('Y-m-d', strtotime($ld['created_at']));
				$id_user = $ld->userId;

				!isset($reportChartPre[$id_user]) ? $reportChartPre[$id_user] = [] : '';
				!isset($reportChartPre[$id_user][$series[0]]) ? $reportChartPre[$id_user][$series[0]] = 0 : '';
				!isset($reportChartPre[$id_user][$series[1]]) ? $reportChartPre[$id_user][$series[1]] = 0 : '';
				!isset($reportChartPre[$id_user][$series[2]]) ? $reportChartPre[$id_user][$series[2]] = 0 : '';
				!isset($reportChartPre[$id_user][$series[3]]) ? $reportChartPre[$id_user][$series[3]] = 0 : '';
				!isset($reportChartPre[$id_user][$series[4]]) ? $reportChartPre[$id_user][$series[4]] = 0 : '';

				$reportChartPre[$id_user][$series[0]] = round($ld->avg_rating_1, 2);
				$reportChartPre[$id_user][$series[1]] = round($ld->avg_rating_2, 2);
				$reportChartPre[$id_user][$series[2]] = round($ld->avg_rating_3, 2);
				$reportChartPre[$id_user][$series[3]] = round($ld->avg_rating_4, 2);
				$reportChartPre[$id_user][$series[4]] = round($ld->avg_rating_5, 2);
				$list_count[$id_user] = (int) $ld->total_customer;
			}

			foreach ($series as $dc) {
				$dataStack = array();
				foreach ($listReportCategory as $d) {
					$val = (isset($reportChartPre[$d][$dc]) ? $reportChartPre[$d][$dc] : 0);

					$dataStack[] = $val;
				}

				$reportChart[] = array('name' => $dc, 'data' => $dataStack);
			}

			foreach ($listAgent as $la) {
				$reportData .= '<tr>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_satisfaction?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->username . '</a></td>';
				$reportData .= '<td><a href="' . base_url('Reports/detail_cso_satisfaction?tgl1=' . $tgl1 . '&tgl2=' . $tgl2 . '&id_cso=' . $la->id) . '">' . $la->name . '</a></td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportChartPre[$la->id]) ? $reportChartPre[$la->id][$series[0]] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportChartPre[$la->id]) ? $reportChartPre[$la->id][$series[1]] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportChartPre[$la->id]) ? $reportChartPre[$la->id][$series[2]] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportChartPre[$la->id]) ? $reportChartPre[$la->id][$series[3]] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">' . (isset($reportChartPre[$la->id]) ? $reportChartPre[$la->id][$series[4]] : 0) . '</td>';
				$reportData .= '<td style="text-align:center">' . (isset($list_count[$la->id]) ? $list_count[$la->id] : 0) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$listPlaza = $this->db->query('select * from queue_setting')->result();

		$d = [
			'title' => "Reports - CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'cso_satisfaction',
			// 'call_center_number' => $call_center_number,
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'dateRange' => $dateRange,
			'reportData' => $reportData,
			'reportChart' => $reportChart,
			'reportCategory' => $reportCategory,
			'listPlaza' => $listPlaza,
			'id_plaza' => $id_plaza,
		];

		$this->load->view('page/_main', $d);
	}

	public function detail_cso_purchase()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_cso = $this->input->get('id_cso') ? $this->input->get('id_cso') : '';

		$reportData = '';

		$cso_name = $this->db->query("select fullName as name,outlet_number from user where user.id='$id_cso'")->row();


		$response_cso = '';
		$response_sell_thru = '';

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
												"value": ["670' . $cso_name->outlet_number . '"]
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


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.telkomcel.tl/tetum-dashboard//api/performance/sell-thru/list-by-cso',
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
								"key": "start_date",
								"value": "' . $tgl1 . '"
							},
							{
								"key": "end_date",
								"value": "' . $tgl2 . '"
							},
							{
								"key": "ref_msisdn",
								"value": ["670' . $cso_name->outlet_number . '"]
							}
						]
					}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Basic MzYwQHRldHVtLnRsOkN1NXQzNm9AMjAyNQ=='
			),
		));

		$response_sell_thru = curl_exec($curl);
		curl_close($curl);

		$listData_cso = json_decode($response_cso, true);
		$listData_sell_thru = json_decode($response_sell_thru, true);

		$listData = (isset($listData_cso['code']) && $listData_cso['code'] == 200 && isset($listData_cso['data']) ? $listData_cso['data'] : []) +
			(isset($listData_sell_thru['code']) && $listData_sell_thru['code'] == 200 && isset($listData_sell_thru['data']) ? $listData_sell_thru['data'] : []);


		if (count($listData) > 0) {
			foreach ($listData as $la) {
				$qty = isset($la['quantity']) && $la['quantity'] > 0 ? $la['quantity'] : 1;

				$reportData .= '<tr>';
				$reportData .= '<td>' . date('d F Y H:i:s', strtotime($la['created_at'])) . '</td>';
				$reportData .= '<td>' . $la['transaction_code'] . '</td>';
				$reportData .= '<td>' . $la['customer_code'] . '</td>';
				$reportData .= '<td>' . $la['product_type'] . '</td>';
				$reportData .= '<td>' . $la['product_category'] . '</td>';
				$reportData .= '<td>' . $la['product_name'] . '</td>';
				$reportData .= '<td>' . $qty . '</td>';
				$reportData .= '<td style="text-align:center">$' . number_format($la['price']) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - Detail CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'detail_cso_purchase',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'reportData' => $reportData,
			'cso_name' => isset($cso_name->name) ? $cso_name->name : '-',
		];

		$this->load->view('page/_main', $d);
	}

	public function detail_cso_purchase_area()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$area = $this->input->get('area') ? $this->input->get('area') : '';

		$reportData = '';

		$whereArea = ' and area=\'' . $area . '\' ';

		// $cso_name = $this->db->query("select fullName as name,outlet_number from user where user.id='$id_cso'")->row();

		$resAg = $this->db->query("select fullName as name, id, username,outlet_number
									from user
									where outlet_number is not null
									" . $whereArea . "
									order by fullName asc");
		$listAgent = $resAg->result();

		$list_number = [];

		foreach ($listAgent as $la) {
			$list_number[] = '"670' . $la->outlet_number . '"';
		}


		$response_cso = '';
		$response_sell_thru = '';

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


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.telkomcel.tl/tetum-dashboard//api/performance/sell-thru/list-by-cso',
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

		$response_sell_thru = curl_exec($curl);
		curl_close($curl);

		$listData_cso = json_decode($response_cso, true);
		$listData_sell_thru = json_decode($response_sell_thru, true);

		$listData = (isset($listData_cso['code']) && $listData_cso['code'] == 200 && isset($listData_cso['data']) ? $listData_cso['data'] : []) +
			(isset($listData_sell_thru['code']) && $listData_sell_thru['code'] == 200 && isset($listData_sell_thru['data']) ? $listData_sell_thru['data'] : []);


		if (count($listData) > 0) {
			foreach ($listData as $la) {
				$qty = isset($la['quantity']) && $la['quantity'] > 0 ? $la['quantity'] : 1;

				$reportData .= '<tr>';
				$reportData .= '<td>' . date('d F Y H:i:s', strtotime($la['created_at'])) . '</td>';
				$reportData .= '<td>' . $la['transaction_code'] . '</td>';
				$reportData .= '<td>' . $la['customer_code'] . '</td>';
				$reportData .= '<td>' . $la['product_type'] . '</td>';
				$reportData .= '<td>' . $la['product_category'] . '</td>';
				$reportData .= '<td>' . $la['product_name'] . '</td>';
				$reportData .= '<td>' . $qty . '</td>';
				$reportData .= '<td style="text-align:center">$' . number_format($la['price']) . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - Detail CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'detail_cso_purchase',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'reportData' => $reportData,
			'cso_name' => isset($area) && $area ? $area : 'All Area',
		];

		$this->load->view('page/_main', $d);
	}


	public function detail_cso_satisfaction()
	{
		$tgl1 = $this->input->get('tgl1') ? $this->input->get('tgl1') : date('Y-m-01');
		$tgl2 = $this->input->get('tgl2') ? $this->input->get('tgl2') : date('Y-m-d');
		$id_cso = $this->input->get('id_cso') ? $this->input->get('id_cso') : '';

		$reportData = '';

		$cso_name = $this->db->query("select fullName as name,outlet_number from user where user.id='$id_cso'")->row();

		$listData = $this->db->query("select
									rating_1,
									rating_2,
									rating_3,
									rating_4,
									rating_5,
									userId,
									complainDate,
									complainTime,
									mdnProblem,
									customerName
									from complain
									where
									complainDate>='$tgl1' and
									complainDate<='$tgl2' and
									rating_1>0 and
									userId='$id_cso'")->result();


		if (count($listData) > 0) {
			foreach ($listData as $la) {
				$reportData .= '<tr>';
				$reportData .= '<td>' . date('d F Y', strtotime($la->complainDate)) . ' ' . $la->complainTime . '</td>';
				$reportData .= '<td>' . $la->mdnProblem . '</td>';
				$reportData .= '<td>' . $la->customerName . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->rating_1 . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->rating_2 . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->rating_3 . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->rating_4 . '</td>';
				$reportData .= '<td style="text-align:center">' . $la->rating_5 . '</td>';
				$reportData .= '</tr>';
			}
		} else {
			$reportData = '<tr><td colspan="100%" style="text-align:center">There\'s no data in this period</td></tr>';
		}

		$d = [
			'title' => "Reports - Detail CSO Performance :: Telkomcel Helpdesk",
			'linkView' => $this->path . 'detail_cso_satisfaction',
			'tgl1' => $tgl1,
			'tgl2' => $tgl2,
			'reportData' => $reportData,
			'cso_name' => isset($cso_name->name) ? $cso_name->name : '-',
		];

		$this->load->view('page/_main', $d);
	}
}
