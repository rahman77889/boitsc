<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bts extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	var $token_registered = 'f4c8425f-e751-4ecf-8283-f213fd3c617d';

	public function info($msisdn)
	{

		$headers = apache_request_headers();
		$token = $headers['token'];

		if ($token == $this->token_registered) {

			header('content-type:application/json');

			$url    = 'http://172.20.212.17:8080/location/info?msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&channel=400';
			$ctn    = @file_get_contents($url);
			$ctx    = explode(',', $ctn);

			// if ($ctx[0] == 'mcc=514') {
			$cellid = str_replace('cellid=', '', $ctx[3]);
			$lac = str_replace('lac=', '', $ctx[2]);

			if ($msisdn == '12345678') {
				$cellid = '22';
				$lac = '37101';
			}

			// $retPre = $this->db->select('site_id')->from('sitemap')->or_where('ci2g', $cellid)->or_where('ci3g_850', $cellid)->or_where('ci3g_2100_1', $cellid)->or_where('ci3g_2100_2', $cellid)->or_where('ci3g_2100_3', $cellid)->limit(1)->get()->row();
			$retPre = $this->db->select('site_id')->from('sitemap')->where('cell_id', $cellid)->where('lac', $lac)->limit(1)->get()->row();

			if ($retPre != null && $retPre->site_id != null) {
				$tac_number = $ctx[8];
				$tac_number = str_replace('imei=', '', $tac_number);

				$bts    = $this->db->select('*')->from('bts')->where('site_id', $retPre->site_id)->get()->row();
			} else {
				$tac_number = $ctx[8];
				$tac_number = str_replace('imei=', '', $tac_number);
			}

			if ($msisdn == '12345678') {
				$tac_number = '35651706';
			}

			$device = $this->db->select('*')->from('devices')->where('tac', $tac_number)->get()->row();

			$ret = array('status' => false, 'data' => array());

			if ($retPre != null && $retPre->site_id != null) {
				$ret['status'] = true;
				$ret['data']['bts'] =  $bts;
			}

			$ret['data']['device'] = $device;

			echo json_encode($ret);
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
	}

	public function bts_by_lac_cellid()
	{
		$headers = apache_request_headers();
		$token = $headers['token'];

		if ($token == $this->token_registered) {
			header('content-type:application/json');

			$ret = array('status' => false, 'data' => array(), 'msg' => '');

			$lac = $_POST['lac'];
			$cell_id = $_POST['cell_id'];

			if ($lac && is_numeric($lac)) {

				if ($cell_id && $lac) {
					$retPre = $this->db->select('site_id')->from('sitemap')->where('cell_id', (int)$cell_id)->where('lac', (int)$lac)->limit(1)->get()->row();
				} else if ($lac) {
					$retPre = $this->db->select('site_id')->from('sitemap')->where('lac', (int)$lac)->limit(1)->get()->row();
				}

				if ($retPre != null && $retPre->site_id != null) {
					$tac_number = $ctx[8];
					$tac_number = str_replace('imei=', '', $tac_number);

					$bts    = $this->db->select('*')->from('bts')->where('site_id', $retPre->site_id)->get()->row();

					$ret['msg'] = 'Data has been successfully fetch';
					$ret['data'] = $bts;
					$ret['status'] = true;
				} else {
					$ret['msg'] = 'Information BTS not found';
				}
			} else {
				$ret['msg'] = 'Please provide valid parameter';
			}

			echo json_encode($ret);
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
	}
}
