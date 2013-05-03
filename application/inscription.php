<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inscription extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->library('twig');
        $this->load->library('form_validation');
        $this->load->model('inscription_model');
        $this->twig->addFunction('validation_errors'); 
        
        $this->load->helper('sessionnzo');
        $this->twig->addFunction('getsessionhelper');    
    }
    
    public function index()
	{    
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
            $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');

            if ($this->form_validation->run() == FALSE)
            {
                    $this->twig->render('inscription');
            }
            else
            {        
                $this->inscription_model->insert_inscription();
                $this->twig->render('affiche');
            }
	}
        
        public function login (){
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]|callback_check_login');
            if ($this->form_validation->run() == FALSE)
            {
                    $this->twig->render('login');
            }
            else
            {      
              
                $this->twig->render('affiche');
            }
            
        }
        
        function check_login(){
            $query = $this->inscription_model->login();
            if(!$query){
                $this->form_validation->set_message('check_login', 'Ce login est introuvable!');
                return FALSE;
            }
             else{
                    foreach($query as $val){
//                        if (!$val->activer) {
//                            $this->form_validation->set_message('check_login', 'Utilisateur non Activer! ');
//                            return false;
//                        }
                    $login_in = array('id' => $val->id, 'email' => $val->email);
                    $this->session->set_userdata('login_in', $login_in);
                    }
                    return TRUE;
                }
        }
        
       function logout(){
            $this->session->unset_userdata('login_in');
            $this->session->sess_destroy();
            redirect('welcome');
        }

}
