<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class FaqModel extends CI_Model {

    public $t     = 'faq';
    public $tabel = 'faq';

    public function getTitle($id_title = '', $q = '', $obj = '') {
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

    public function status($status = '') {
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

    public function get($id = '', $q = '', $obj = '') {

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

    public function add($obj = '') {
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

    public function update($obj = '', $id = '') {
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

    public function delete($id = '', $based_on = '') {
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

    public function setActive($v = '', $id = '') {
        $q = '';

        if ($v != '') {
            $q = $this->db->update($this->t, ['active' => $v], ['id' => $id]);
        }

        $log = [
            'response' => $q,
            'msg'      => 'Success to set Active field',
            'request'  => $v,
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function setRemove($v = '', $id = '') {
        $q = '';

        if ($v != '') {
            $q = $this->db->update($this->t, ['remove' => $v], ['id' => $id]);
        }

        $log = [
            'response' => $q,
            'msg'      => 'Success to set Remove field',
            'request'  => $v,
            'date'     => date('Y-m-d H:i:s'),
        ];

        return $log;
    }

    public function dtshowcreatefaq() {
        // Definisi
        $condition = '';
        $data      = [];
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
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, `faqId`, `categoryId`, `faqTitle`, `faqFile`, `uploadDate`, `userId`, `status`); //, 'subCategory'//, `subCategoryId`
        // Set searchable column fields
        $CI->dt->column_search = array($this->tabel . '.categoryId', 's.name_title'); //,'c.categoryName','s.subCategory'
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*,c.categoryName,s.name_title'; //,s.subCategory
        // Set default order
        $CI->dt->order         = array($this->tabel . '.faqId' => 'DESC');

        $condition = [
            ['join', 'category c', $this->tabel . '.categoryId=c.categoryId', 'inner'],
            ['join', 'title s', $this->tabel . '.id_title=s.id_title', 'inner'],
//                ['join','subcategory s', $this->tabel . '.subCategoryId=s.subCategoryId', 'inner']
        ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;
            $data[] = array(
                ' <input onclick="hapus(' . $dt->faqId . ')" class="oke" type="checkbox" name="faqId[]" value="' . $dt->faqId . '">',
                $i,
                $dt->categoryName,
//                $dt->subCategory,
                $dt->name_title,
//                $dt->faqFile,
                $dt->uploadDate,
                $dt->userId,
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

    public function dtshowfaq() {
        // Definisi
        $condition = '';
        $data      = [];
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
        $CI->dt->table         = $this->tabel;
        // Set orderable column fields
        $CI->dt->column_order  = array(null, `faqId`, `categoryId`, `faqTitle`, `faqFile`, `uploadDate`, `userId`, `status`); //, `subCategoryId`, 'subCategory'
        // Set searchable column fields
        $CI->dt->column_search = array($this->tabel . '.categoryId', 't.name_title', 'c.categoryName', 'faqFile', 'faqTitle'); //, 's.subCategory'
        // Set select column fields
        $CI->dt->select        = $this->tabel . '.*,c.categoryName,t.name_title';
        // Set default order
        $CI->dt->order         = array($this->tabel . '.faqId' => 'DESC');

        $condition = [
            ['join', 'category c', $this->tabel . '.categoryId=c.categoryId', 'inner'],
//                ['join','subcategory s', $this->tabel . '.subCategoryId=s.subCategoryId', 'inner'],
            ['join', 'title t', $this->tabel . '.id_title=t.id_title', 'inner'],
        ];

        // Fetch member's records
        $dataTabel = $this->dt->getRows($_POST, $condition);

        $i = $_POST['start'];
        foreach ($dataTabel as $dt) {
            $i++;

            $detailInfo = '';

            if (is_file('pdf/' . $dt->faqFile)) {
                $detailInfo = '<a href="' . base_url('pdf/' . $dt->faqFile) . '"  target="_blank"><i class="fa fa-file-pdf-o fa-3x" style="color:red;"></i></a>';
            } elseif (is_file('video/' . $dt->videoFile)) {
                $detailInfo = '<a href="player/video/?file=' . base_url('video/' . $dt->videoFile) . '"  target="_blank"><i class="fa fa-file-video-o fa-3x" style="color:red;"></i></a>';
            } else {
                $detailInfo = '<a href="' . $dt->videoUrl . '"  target="_blank"  ><i class="fa fa-youtube fa-3x" style="color:red;"></i></a>';
            }

            $data[] = array(
                $i,
                $dt->categoryName,
                $dt->name_title,
                $detailInfo
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

    public function de($faqId) {
        $faqId = implode(",", $faqId);

        $p = $this->db->query("DELETE  FROM faq WHERE faqId IN ($faqId)");
        return true;
    }

    function simpan_faq($categoryId, $subCategoryId, $id_title, $faqFile, $status, $videoFile, $videoUrl) {
        $data   = array(
            'categoryId' => $categoryId,
//            'subCategoryId' => $subCategoryId,
            'id_title'   => $id_title,
            'faqFile'    => $faqFile,
            'videoFile'  => $videoFile,
            'videoUrl'   => $videoUrl,
            'uploadDate' => date('Y-m-d H:i:s'),
            'status'     => $status
        );
        $result = $this->db->insert('faq', $data);
        return $data;
    }

}

/* End of file faqModel.php */
?>
