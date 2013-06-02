<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {
    private $shopping = array();
    function __construct() {
        parent::__construct();
        
        $this->load->model('inscription/user_model', '', TRUE);
        $this->twig->addFunction('validation_errors');
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('produit/produit_model', '', TRUE);
        
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] =$this->cart->total_items();
    }

    function index() {
        //This method will have the credentials validation
        $this->form_validation->set_rules('login', 'Nom utilisateur', 'trim|required|xss_clean');
        $this->form_validation->set_rules('pwd', 'Mot de passe', 'trim|required|xss_clean|callback_check_login');
        // cookie remeber 
        if (isset($_POST['remember'])) {
            setcookie("cookiemail", $_POST['login'], time() + 60 * 60 * 24 * 100, "/");
            setcookie("cookiepass", $_POST['pwd'], time() + 60 * 60 * 24 * 100, "/");
        } else {
            setcookie("cookiemail", "", NULL, "/");
            setcookie("cookiepass", "", NULL, "/");
        }
        $data['shopping'] = $this->shopping;
        if ($this->form_validation->run() == FALSE) {
            $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';
             $ensproduitdate = $this->produit_model->get_product_by_date();
             $data['produitdate'] = $ensproduitdate;
            //Field validation failed.  User redirected to login page
            $this->twig->render('login/login_view', $data);
        } else {
            //Go to private area
            redirect('inscription/home');
        }
    }

    function check_login() {
        //Field validation succeeded.  Validate against database
        //query the database
        if (!$result = $this->user_model->login()) {
            $this->form_validation->set_message('check_login', 'Mot de passe ou nom utilisateur incorrecte');
            return false;
        } else {
            //verifier si compte valide ou pas 
            foreach ($result as $row) {  //recuperer type 
                if ($row->active == 0) {
                    $this->form_validation->set_message('check_login', 'veuillez confirmer l\'inscription par mail');
                    return false;
                }
                $reqid = 0;
                if($row->type == 'client'){
                    $reqid = $this->user_model->getidclient($row->idpersonne)->row()->idclient;
                    $notif = $this->user_model->getnotif_client($row->idpersonne)->row();
                }
                elseif ($row->type == 'commercant') {
                    $enable = $this->user_model->getetatcomm($row->idpersonne)->row()->enable;
                      if ($enable == 0) {
                    $this->form_validation->set_message('check_login', 'En attente de la confirmation de la part de l\'administrateur');
                    return false;
                }
                    $reqid = $this->user_model->getidcommercant($row->idpersonne)->row()->idcommercant;
                    $notif = $this->user_model->getnotif_commercant($row->idpersonne)->row();
            }
                ;
                $login_in = array('id' => $reqid, 'idpersonne' => $row->idpersonne, 'email' => $row->email, 'login' => $row->login, 'type' => $row->type, 'panier' =>'', 'notifaction' => $notif->notifaction, 'notifmsg' => $notif->notifmsg);
                $this->session->set_userdata('login_in', $login_in);
            }
            return TRUE;
        }
    }

}

?>