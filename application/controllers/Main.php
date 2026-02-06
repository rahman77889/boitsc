<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
    
    private $path = 'page/main/';
    
	public function index()
	{
        $d = [
            'title' => "Helpdesk :: Telkomcel",
            'linkView' => $this->path.'main'
        ];
		$this->load->view('page/_main',$d);
	}
}
