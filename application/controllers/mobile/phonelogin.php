<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');

class Phonelogin extends CI_Controller
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
        
        public function login()
        {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[4]|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]|xss_clean|callback_check_login');
            if ($this->form_validation->run() == FALSE)
            {
                //echo  $this->tableau = array('error' => validation_errors());
                echo json_encode( array('error' => validation_errors()) );
               // echo validation_errors();
            }
            else
            {
//                    /$data = array('error' => 0, 'test' => 'abc');
//                    echo json_encode($data);/
                   echo json_encode($this->tableau); // print_r($this->tableau);
            }
        }
        
        public function check_login()
        {
            $req = $this->phonelogin_model->login();
            if(!$req)
            {
                $this->form_validation->set_message('check_login', 'Email ou mot de passe incorrecte!');
                return false;
            }
            else
            {
                foreach($req as $val)
                {
                    if (!$val->activer)
                    {
                        $this->form_validation->set_message('check_login', 'Utilisateur non Activer! ');
                        return false;
                    }
                    
                    $this->tableau = array('error' => 0, 'id' => $val->id, 'email' => $val->email, 'nom' => $val->nom);
                      //  return $tableau;
                }
                
                return TRUE;
             }
        }
        
        public function logout()
        {
            $this->session->unset_userdata('login_in');
            $this->session->sess_destroy();
            redirect('frontend');
        }
        
        function setstatut()
        {
            $this->phonelogin_model->setStatut();
            echo "true";
        }

        
        function upload()
	{
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = '*'; //'jpg|png';
            $config['max_size']	= '1000';
            $config['max_width']  = '1024';
            $config['max_height']  = '1024';
            $config['encrypt_name']  = TRUE;
           // $config['file_name'] = $config['file_name'] . '.jpg';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('file'))
            {
                   echo $this->upload->display_errors();
            }
            else
            {
                $this->phonelogin_model->saveuploadimage($this->upload->data());                    
            }
	}
}


