<?php

defined('BASEPATH') or exit('No direct script access allowed');

class History extends CI_Controller
{

	public $tabel = 'complain';

	public function __construct()
	{
		parent::__construct();
	}

	public function customer($msisdn)
	{

		$headers = apache_request_headers();
		$token = $headers['token'];

		if ($token == 'f4c8425f-e751-4ecf-8283-f213fd3c617d') {

			header('content-type:application/json');
			$result = $this->db->select('*,
			(select cos.name from complainstatus cos where complain.status=cos.status) as complaintstatusname,
			(select com.name from complaintype com where complain.complaintType=com.id) as complaintypename,
			(select c.categoryName from category c where complain.categoryId=c.categoryId) as categoryName,
			(select sub.subCategory from subcategory sub where sub.subCategoryId=complain.subCategoryId) as subCategory
			')->from('complain')->where('mdnProblem', $msisdn)->order_by('createDate', 'desc')->limit(5)->get()->result();

			$ret = array('status' => false, 'data' => []);

			if (count($result) > 0) {
				$ret['status'] = true;
				$ret['data'] = $result;
			}

			echo json_encode($ret);
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
	}
}
