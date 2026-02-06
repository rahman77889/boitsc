<?php

defined('BASEPATH') or exit('No direct script access allowed');

class RecodingsModel extends CI_Model
{

    public $tabel = 'recording';

    public function showdtrecordings()
    {

        //pencarian
        //  if ($id) {
        //     $this->db->where('id', $id);
        // }
        // if ($msisdn) {
        //     $this->db->where('msisdn', $msisdn);
        // }
        // if ($startdate != "" and $enddate != "") {
        //     $this->db->where('DATE(created_date) >=', date('Y-m-d', strtotime($startdate)));
        //     $this->db->where('DATE(created_date) <=', date('Y-m-d', strtotime($enddate)));
        // }
        // Definisi
        $condition  = '';
        $data       = [];
        $condition  = '';
        $data       = [];
        $wPriority  = '';
        $wStartdate = '';
        $wEnddate   = '';
        $wStartEnd  = '';

        $kondisi = [
            ['join', 'user u', $this->tabel . '.id_user=u.id', 'left'],
            // ['join', 'call_log_incoming ic', $this->tabel . '.rec_id=ic.uniqueid', 'left'],
            // ['join', 'call_log_outgoing oc', $this->tabel . '.rec_id=oc.uniqueid', 'left'],
            // ['join', 'user u1', 'u1.id=ic.id_user', 'left'],
            // ['join', 'user u2', 'u2.id=oc.id_user', 'left']
        ];

        if ($this->input->get('startdate')) {
            $kondisi[] = array('where', 'date(' . $this->tabel . '.tgl) >=', $this->input->get('startdate'));
        }
        if ($this->input->get('enddate')) {
            $kondisi[] = array('where', 'date(' . $this->tabel . '.tgl) <=', $this->input->get('enddate'));
        }
        if ($this->input->get('msisdn')) {
            $kondisi[] = array('like', $this->tabel . '.msisdn', $this->input->get('msisdn'), 'before');
        }
        if ($this->input->get('id') != 'null' && trim($this->input->get('id'))) {
            //            $kondisi[] = array('where', $this->tabel . '.id_user', $this->input->get('id'));
            $kondisi[] = array('group_start', $this->tabel . '.id_user=\'' . $this->input->get('id') . '\'', 'ic.id_user=\'' . $this->input->get('id') . '\'', 'oc.id_user=\'' . $this->input->get('id') . '\'');
        }
        // if ($this->input->get('call_center_number') != 'null' && trim($this->input->get('call_center_number'))) {
        //     if ($this->input->get('call_center_number') == 'other') {
        //         $notIn     = array('+123', '+147');
        //         $kondisi[] = array('where_not_in', 'ic.call_center_number', $notIn);
        //         $kondisi[] = array('where_not_in', 'oc.call_center_number', $notIn);
        //     } else {
        //         $kondisi[] = array('group_start', 'ic.call_center_number=\'+' . $this->input->get('call_center_number') . '\'', 'oc.call_center_number=\'+' . $this->input->get('call_center_number') . '\'');
        //     }
        // }

        // $kondisi[] = array('join', 'call_log_incoming ic', $this->tabel . '.rec_id=ic.uniqueid', 'left');
        // $kondisi[] = array('join', 'call_log_outgoing oc', $this->tabel . '.rec_id=oc.uniqueid', 'left');

        // selesai

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'recording.tgl desc');
        // Set searchable column fields
        $CI->dt->column_search = array('rec_id', 'recording.msisdn', 'recording.extend_number');
        // Set select column fields
        // $CI->dt->select        = $this->tabel . '.*,u.fullName as f1,u.username as u1,u1.fullName as f2,u1.username as u2,u2.fullName as f3,u2.username as u3,ic.call_center_number as icc, oc.call_center_number as occ';
        $CI->dt->select        = $this->tabel . '.*,u.fullName as f1,u.username as u1';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.tgl' => 'DESC');

        // if ($this->session->userdata('tipe') == '123') {
        //     $notIn     = array('+147', '+888');
        //     $kondisi[] = array('where_not_in', 'ic.call_center_number', $notIn);
        // } else if ($this->session->userdata('tipe') == '147') {
        //     $notIn     = array('+123', '+888');
        //     $kondisi[] = array('where_not_in', 'ic.call_center_number', $notIn);
        // } else if ($this->session->userdata('tipe') == '888') {
        //     $notIn     = array('+123', '+147');
        //     $kondisi[] = array('where_not_in', 'ic.call_center_number', $notIn);
        // }

        // $kondisi[] = array('limit', 1000);
        $kondisi[] = array('select', $CI->dt->select);

        $condition = $kondisi;

        //        print_r($condition);
        //        exit();
        // $condition =
        //     [
        //      ['join', 'call_log c', $this->tabel . '.id=c.id_user', 'inner'],
        //     ['where', $this->tabel . '.privilegeId', '3']
        //     ];
        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input data-filename="' . $dt->filename . '" onclick="edit(\'' . $dt->id . '\')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->rec_id,
                // ($dt->u1 ? $dt->u1 : ($dt->u2 ? $dt->u2 : $dt->u3)),
                // ($dt->f1 ? $dt->f1 : ($dt->f2 ? $dt->f2 : $dt->f3)),
                // $dt->u1,
                // $dt->f1,
                $dt->extend_number,
                $dt->msisdn,
                $dt->filename,
                $dt->filesize,
                $dt->tgl,
                $dt->duration,
                // $dt->icc ? $dt->icc : ($dt->occ ? $dt->occ : '-')
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
}

/* End of file RecordingsModel.php */
