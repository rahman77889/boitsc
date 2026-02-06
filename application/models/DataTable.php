<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class DataTable extends CI_Model
{

    function __construct()
    {
        // Set table name
        $this->table         = '';
        // Set orderable column fields
        $this->column_order  = array();
        // Set searchable column fields
        $this->column_search = array();
        // Set select column fields
        $this->select        = '';
        // Set default order
        $this->order         = array();
    }

    /*
     * Fetch members data from the database
     * @param $_POST filter data based on the posted parameters
     */

    public function getRows($postData, $condition = '')
    {
        $this->_get_datatables_query($postData);
        if ($postData['length'] != -1) {
            $this->db->limit($postData['length'], $postData['start']);
        }
        $this->db->select($this->select);

        if ($condition != '') {
            for ($i = 0; $i < count($condition); $i++) {
                $this->condition($i, $condition);
            }
        }

        $query = $this->db->get();

        //        if ($this->table == 'recording') {
        //            echo $this->db->last_query();
        //            exit();
        //        }
        return $query->result();
    }

    // private function condition($i = 0, $condition = '') {
    //     $kondisi = $condition[$i][0];
    //     if ($kondisi == "join") {
    //         $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2], $condition[$i][3]);
    //     } else if ($kondisi == "where" || "or_where" || "where_not_in" || "where_in") {
    //         $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2]);
    //     } else if ($kondisi == "like") {
    //         $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2], $condition[$i][3]);
    //     } else if ($kondisi == "group_by") {
    //         $query = $this->db->$kondisi($condition[$i][1]);
    //     }
    //     return $query;
    // }

    private function condition($i = 0, $condition = '')
    {
        $kondisi = $condition[$i][0];

        if ($kondisi == "join") {
            $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2], $condition[$i][3]);
        } else if ($kondisi == "where_in") {
            $query = $this->db->where($condition[$i][1] . ' in (' . $condition[$i][2] . ')');
        } else if ($kondisi == "where" || $kondisi == "or_where" || $kondisi == "where_not_in") {
            $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2]);
        } else if ($kondisi == "like") {
            $query = $this->db->$kondisi($condition[$i][1], $condition[$i][2], $condition[$i][3]);
        } else if ($kondisi == "group_by") {
            $query = $this->db->$kondisi($condition[$i][1]);
        } else if ($kondisi == "limit") {
            $query = $this->db->$kondisi($condition[$i][1]);
        } else if ($kondisi == "select") {
            $query = $this->db->$kondisi($condition[$i][1]);
        } else if ($kondisi == ">=") {
            $query = $this->db->where($condition[$i][1] . '>=\'' . $condition[$i][2] . '\'');
        } else if ($kondisi == "<=") {
            $query = $this->db->where($condition[$i][1] . '<=\'' . $condition[$i][2] . '\'');
        } else if ($kondisi == "group_start") {
            $query = $this->db->$kondisi();
            foreach ($condition[$i] as $ik => $kon) {
                if ($ik > 0) {
                    $this->db->or_where($kon);
                }
            }
            $query = $this->db->group_end();
        }
        return $query;
    }

    /*
     * Count all records
     */

    public function countAll($condition = '')
    {
        $this->db->from($this->table);

        if ($condition != '') {
            for ($i = 0; $i < count($condition); $i++) {
                $this->condition($i, $condition);
            }
        }

        return $this->db->count_all_results();
    }

    /*
     * Count records based on the filter params
     * @param $_POST filter data based on the posted parameters
     */

    public function countFiltered($postData, $condition = '')
    {
        $this->_get_datatables_query($postData);

        if ($condition != '') {
            for ($i = 0; $i < count($condition); $i++) {
                $this->condition($i, $condition);
            }
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    /*
     * Perform the SQL queries needed for an server-side processing requested
     * @param $_POST filter data based on the posted parameters
     */

    private function _get_datatables_query($postData, $condition = '')
    {

        $this->db->from($this->table);

        if ($condition != '') {
            for ($i = 0; $i < count($condition); $i++) {
                $this->condition($i, $condition);
            }
        }

        $i = 0;
        // loop searchable columns 
        foreach ($this->column_search as $item) {
            // if datatable send POST for search
            if ($postData['search']['value']) {
                // first loop
                if ($i === 0) {
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                } else {
                    $this->db->or_like($item, $postData['search']['value']);
                }

                // last loop
                if (count($this->column_search) - 1 == $i) {
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($postData['order'])) {
            $this->db->order_by($this->column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
}
