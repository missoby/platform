<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    private $fb;
    private $shopping = array();

    function __construct() {
        parent::__construct();
        //Facebook Connect
        require_once 'assets/facebook_sdk_src/facebook.php';
        $param = array();
        $param['appId'] = '111176932418804';
        $param['secret'] = 'cff04e31d0fe52eb2777188dff2e71b8';
        $param['fileUpload'] = true; // pour envoyer des photos
        $param['cookie'] = false;
        $this->fb = new Facebook($param);
        
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('inscription/user_model', '', TRUE); 
        $this->load->model('produit/produit_model', '', TRUE);

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
        
        
    }

    function index() {
        $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';

        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        $data['shopping'] = $this->shopping;
        $this->twig->render('login/login_view' , $data);
    }
    
    public function loginfb()
    {
        $uid = $this->fb->getUser();
        if (empty($uid))
        {
            $ppp = array();
            $ppp['scope'] = 'email, read_stream, publish_actions';
            $ppp['redirect_uri'] = base_url().'inscription/login/loginfb/';
            redirect($this->fb->getLoginUrl($ppp));
        }
        else //User connecté avec facebook
        {
            $me = $this->fb->api('/me');
            $res = $this->user_model->getUserByMail($me['email']);
            
            if(!$res)
            {
                $d = array();
                $d['msg'] = 'Utilisateur non reconnu. Utilisateur non inscri ou mail inscription est différent du mail facebook';
                $this->twig->render('Echec_view', $d);
                $this->fb->destroySession();
            }
            else
            {
                $reqid = 0;
                if($res->type == 'client')
                {
                    $reqid = $this->user_model->getidclient($res->idpersonne)->row()->idclient;
                    $login_in = array('id' => $reqid, 'idpersonne' => $res->idpersonne, 'email' => $res->email, 'login' => $res->login, 'type' => $res->type, 'panier' =>'');
                    $this->session->set_userdata('login_in', $login_in);
                    redirect('inscription/home');
                }
                elseif ($res->type == 'commercant')
                {
                    $enable = $this->user_model->getetatcomm($res->idpersonne)->row()->enable;
                    if ($enable == 0)
                    {
                        $data = array();
                        $data['msg'] = 'Commercant non enable';
                        $this->twig->render('Echec_view', $data);
                        $this->fb->destroySession();
                    }
                    else
                    {
                        $reqid = $this->user_model->getidcommercant($res->idpersonne)->row()->idcommercant;
                        $login_in = array('id' => $reqid, 'idpersonne' => $res->idpersonne, 'email' => $res->email, 'login' => $res->login, 'type' => $res->type, 'panier' =>'');
                        $this->session->set_userdata('login_in', $login_in);
                        redirect('inscription/home');
                    }
                }
            }
        }
    }
}

?>
