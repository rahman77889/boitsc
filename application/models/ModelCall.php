<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ModelCall extends CI_Model {

    public function topagent($tgl) {
        $query1    = $this->db->query("SELECT COUNT(*) as jumlah,id_user,u.fullName as agen FROM `call_log_incoming` INNER JOIN user u ON u.id=call_log_incoming.id_user WHERE date(call_log_incoming.call_start)='" . $tgl . "' and u.privilegeId=3 group by id_user")->result();
        $query2    = $this->db->query("SELECT COUNT(*) as jumlah,id_user,u.fullName as agen FROM `call_log_outgoing` INNER JOIN user u ON u.id=call_log_outgoing.id_user WHERE date(call_log_outgoing.call_start)='" . $tgl . "' and u.privilegeId=3 group by id_user")->result();
        $hasil     = array();
        $totalCall = 0;

        foreach ($query1 as $data) {
            if ($data->jumlah > 0) {
                if (!isset($hasil[$data->id_user])) {
                    $hasil[$data->id_user]           = array();
                    $hasil[$data->id_user]['jumlah'] = 0;
                }

                $hasil[$data->id_user]['jumlah'] += $data->jumlah;
                $hasil[$data->id_user]['name']   = $data->agen;

                $totalCall += $data->jumlah;
            }
        }
        foreach ($query2 as $data) {
            if ($data->jumlah > 0) {
                if (!isset($hasil[$data->id_user])) {
                    $hasil[$data->id_user]           = array();
                    $hasil[$data->id_user]['jumlah'] = 0;
                }

                $hasil[$data->id_user]['jumlah'] += $data->jumlah;
                $hasil[$data->id_user]['name']   = $data->agen;

                $totalCall += $data->jumlah;
            }
        }

        if (count($hasil) > 0) {
            foreach ($hasil as $khl => $hsl) {
                $hasil[$khl]['jumlah'] = $totalCall > 0 ? $hsl['jumlah'] / $totalCall * 100 : 0;
            }
        }

        return $hasil;
    }

}

/* End of file ModelCall.php */
