<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');

class listprodmobile extends CI_Controller
{
    public $tableau = array();

        public function __construct()
        {
                parent::__construct();
                $this->load->model('inscription_model');
                $this->load->model('phonelogin_model');
                
                $this->twig->addFunction('validation_errors');
                $this->twig->addFunction('getsessionhelper');
        }

	public function index()
	{
            echo 'Nothing!!';
	}
        
        public function listprod()
        {
        }
}