<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Privileges extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('PrivilegeModel','p');
    }
    
     // Privileges
     public function getPrivilege()
     {
         $p = $this->p->getPrivilege();
         echo json_encode($p->result());        
     }

}

/* End of file Privileges.php */
