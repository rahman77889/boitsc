<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CallMonitoringModel extends CI_Model
{

    public $tabel = 'user';

    private function time2string($timeline)
    {
        $periods = array('hour' => 3600, 'min' => 60, 'sec' => 1);
        $ret     = '';
        foreach ($periods as $name => $seconds) {
            $num      = floor($timeline / $seconds);
            $timeline -= ($num * $seconds);
            $ret      .= $num . ' ' . $name . (($num > 1) ? '' : '') . ' ';
        }

        return trim($ret);
    }

    public function showdtcallmonitoring()
    {

        //pencarian
        //    $stdate = $this->input->get('stDate');
        //     $enddate = $this->input->get('endDate');
        //     if ($sn) {
        //         $this->db->where('serialNumber', $sn);
        //     }
        //     if ($denom) {
        //         $this->db->where('refillId', $denom);
        //     }
        //     if ($stdate != "" and $enddate != "") {
        //         $this->db->where('DATE(tanggalPurchase) >=', date('Y-m-d', strtotime($stdate)));
        //         $this->db->where('DATE(tanggalPurchase) <=', date('Y-m-d', strtotime($enddate)));
        //     }
        // Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, 'id',);
        // Set searchable column fields
        $CI->dt->column_search = array('id',);
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*, (select sum(duration) from call_log_incoming where date(call_start)=\'' . date('Y-m-d') . '\' and id_user=' . $this->tabel . '.id ) as sum_call'; //,c1.msisdn as msisdn1, c2.msisdn as msisdn2, c1.call_start as call_start1, c2.call_start as call_start2, c1.call_answer as call_answer1,c2.call_answer as call_answer2
        // Set default order
        $CI->dt->order         = array($this->tabel . '.fullName' => 'ASC');

        $condition = [
            //            ['join', 'call_log_incoming c1', $this->tabel . '.id=c1.id_user', 'left'],
            //            ['join', 'call_log_outgoing c2', $this->tabel . '.id=c2.id_user', 'left'],
            ['where', $this->tabel . '.privilegeId', '3']
        ];

        if ($this->session->userdata('tipe') == '123') {
            $notIn       = array('147', '888');
            $condition[] = array('where_not_in', $this->tabel . '.tipe', $notIn);
        } else if ($this->session->userdata('tipe') == '147') {
            $notIn       = array('123', '888');
            $condition[] = array('where_not_in', $this->tabel . '.tipe', $notIn);
        } else if ($this->session->userdata('tipe') == '888') {
            $notIn       = array('123', '147');
            $condition[] = array('where_not_in', $this->tabel . '.tipe', $notIn);
        }

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);
        $total_sum = 0;

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {

            $establish  = $dt->status == 'busy' ? true : false;
            $msisdn     = $dt->status_call_msisdn;
            $msisdn     = str_replace('+', '', $msisdn);
            $call_start = $dt->status_call_start;

            $i++;
            $data[] = array(
                $i,
                $dt->fullName,
                $dt->extend_number,
                $dt->statusLogin == 1 ? ($dt->status_adc == 'Y' ? '<label class="btn btn-success">Ready</label>' : '<label class="btn btn-warning">Break</label>') : '<label class="btn btn-danger">Off</label>',
                $dt->sum_call ? $this->time2string($dt->sum_call) : '0 hour 0 min 0 sec',
                $establish ? ucfirst($dt->status_call_type) . ' Call to <b>' . $msisdn . '</b> <br><br>call start : ' . date('d-m-Y H:i:s', strtotime($call_start)) . '<br><br>call center number : ' . $dt->status_call_center_number . '<br><br>language : ' . $dt->status_call_language : 'Idle',
                $establish ? '<a href = "javascript:spy(' . $dt->extend_number . ')" class = "btn btn-success">Spy</a> <a href = "javascript:whisper(' . $dt->extend_number . ')" class = "btn btn-warning">Whisper</a>' : ''
            );

            $total_sum += $dt->sum_call;
        }

        $data[] = array('', '', '', '<b>Total : </b>', '<b>' . ($total_sum ? $this->time2string($total_sum) : '0 hour 0 min 0 sec') . '</b>', '', '');

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

/* End of file CallMonitorngModel.php */
