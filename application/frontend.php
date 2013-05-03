<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->library('twig');
        
        $this->load->helper('sessionnzo');
        $this->twig->addFunction('getsessionhelper');    
    }
	public function index()
	{                 
            $this->twig->render('affiche');
            //$this->load->view('welcome_message');
	}
        
        public function test($id){
            $data = array('valeur' => $id);
            $this->twig->render('affiche', $data);
            
        }
}
