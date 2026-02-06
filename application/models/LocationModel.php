<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class LocationModel extends CI_Model {
    
    private $t = 'location';
    
    public function getLocation($id='',$q='',$obj='')
    { 
        if ($id != '') {
            $obj = ['id' => $id];
        }
        
        if ($obj != 0) {
            $q = $this->db->get_where($this->t,$obj);
        }else if ($q != '') {
            $q = $this->db->query($q);
        }else{
            $q = $this->db->get($this->t);
        }
        
        return $q;
    }
    
}

/* End of file LocationModel.php */
