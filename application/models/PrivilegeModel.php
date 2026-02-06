<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class PrivilegeModel extends CI_Model {

    private $t = 'privilege';
    
    public function getPrivilege($id='',$q='',$obj='')
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

/* End of file PrivilegeModel.php */

