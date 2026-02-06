<?php 


defined('BASEPATH') OR exit('No direct script access allowed');

class ModelComplainHistory extends CI_Model {

    public $tabel = "complainthistory";

    public function dtshowcomplainhistory()
    {
        // Definisi
        $condition = '';
        $data = [];
        $categoryName;


        //  if ($this->input->get('categoryName') != "") {
        //     $condition =  [
        //         // ['where', $this->tabel . '.aktif', '1'],
        //         ['where', $this->tabel . '.categoryName', $this->input->get('categoryName')],
                
        //     ];
        // }

        $CI = &get_instance();
        $CI->load->model('DataTable', 'dt');

        // Set table name
        $CI->dt->table = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order = array(null,'id', 'complainId', 'solution', 'notes', 'unitId', 'solvedDate', 'solvedTime', 'status', 'userId', 'createDate');
        // Set searchable column fields
        $CI->dt->column_search = array('id', 'complainId', 'solution', 'notes', 'unitId', 'solvedDate', 'solvedTime', 'status', 'userId', 'createDate');
        // Set select column fields
        $CI->dt->select = $this->tabel . '.*';
        // Set default order
        $CI->dt->order = array($this->tabel . '.id' => 'DESC');

        // $condition =
        //     [
        //         ['where', $this->tabel . '.aktif', '1']
        //     ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                // $i,
                ' <input onclick="edit(' . $dt->id . ')" class="oke" type="checkbox" name="id[]" value="' . $dt->id . '">',
                // '<a href="' . site_url("Project_List/charter?id_pc=" . $dt->id) . '" > ' . $dt->task . '</a>',
                $dt->complainId,
                $dt->categoryType,
                $this->groupInboxTehnicalCco($dt->groupInboxTehnicalCco),
                $this->groupInboxTehnicalVas($dt->groupInboxTehnicalVas),
                $this->statusActive($dt->statusActive),
                   
            );
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->dt->countAll($condition),
            "recordsFiltered" => $this->dt->countFiltered($_POST, $condition),
            "data" => $data,
        );

        // Output to JSON format
        return json_encode($output);
    } 
   
}

/* End of file ModelComplainHistory.php */
