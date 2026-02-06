<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainHistoryModel extends CI_Model {

    public $tabel = 'complainthistory';

    public function showdtcomplainthistory() {
// Definisi
        $condition = '';
        $data      = [];

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

// Set table name
        $CI->dt->table         = $this->tabel;
// Set orderable column fields
        $CI->dt->column_order  = array(null, 'id', 'complainId', 'solution', 'notes', 'unitId', 'solvedDate', 'solvedTime', 'status', 'userId', 'createDate');
// Set searchable column fields
        $CI->dt->column_search = array('id', 'complainId', 'solution', 'notes', 'unitId', 'solvedDate', 'solvedTime', 'status', 'userId', 'createDate');
// Set select column fields
        $CI->dt->select        = $this->tabel . '.*,c.mdnProblem,c.transactionCode,c.channel,c.detailComplain,c.customerName,u.fullName,ct.name as complaintName, cs.name as statusName, c.complainDate as startDate, c.complainTime as startTime,' . $this->tabel . '.solvedDate as endDate, ' . $this->tabel . '.solvedTime as endTime, ut.unitName as unitName';
// Set default order
        $CI->dt->order         = array($this->tabel . '.id' => 'DESC');

        $condition = [
            ['join', 'complain c', $this->tabel . '.complainId=c.id', 'inner'],
            ['join', 'user u', $this->tabel . '.userId=u.id', 'inner'],
            ['join', 'complaintype ct', 'c.complaintType=ct.id', 'inner'],
            ['join', 'complainstatus cs', $this->tabel . '.status=cs.status', 'inner'],
            ['join', 'unit ut', $this->tabel . '.unitId=ut.id', 'inner']
        ];

        $filter = $this->input->get();

        if (isset($filter['transactionCode'])) {
            $uc = count($condition[0]) + 1;

            foreach ($filter as $k => $v) {
                if ($v) {
                    $condition[$uc] = array();

                    if ($k == 'startdate') {
                        $condition[$uc][0] = '>=';
                        $condition[$uc][1] = 'c.complainDate';
                        $condition[$uc][2] = $v;
                    } elseif ($k == 'enddate') {
                        $condition[$uc][0] = '<=';
                        $condition[$uc][1] = 'c.complainDate';
                        $condition[$uc][2] = $v;
                    } else {
                        if ($k == 'unitId' || $k == 'userId') {
                            $tableS = $this->tabel;
                        } else {
                            $tableS = 'c';
                        }
                        $condition[$uc][0] = 'where';
                        $condition[$uc][1] = $tableS . '.' . $k;
                        $condition[$uc][2] = $v;
                    }

                    $uc++;
                }
            }
        }

// Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;

            $sla = strtotime($dt->endDate . ' ' . $dt->endTime) - strtotime($dt->startDate . ' ' . $dt->startTime);

            $data[] = array(
                ' <input onclick="edit(' . $dt->complainId . ',' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                $i,
                $dt->transactionCode,
                $dt->customerName,
                $dt->mdnProblem,
                $dt->complaintName,
                $dt->detailComplain,
                $dt->solution,
                $dt->channel,
                $dt->statusName,
                ($dt->status == 'C' && $sla > 0 ? $sla : 0),
                $dt->startDate . ' ' . $dt->startTime,
                ($dt->status == 'C' ? $dt->endDate . ' ' . $dt->endTime : ''),
                $dt->unitName,
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

    // funsgi delete user login
}

/* End of file CategoryModel.php */
