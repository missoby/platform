<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

    private $fb;
    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
        
        //Facebook Connect
        require_once 'assets/facebook_sdk_src/facebook.php';
        $param = array();
        $param['appId'] = '111176932418804';
        $param['secret'] = 'cff04e31d0fe52eb2777188dff2e71b8';
        $param['fileUpload'] = true; // pour envoyer des photos
        $param['cookie'] = false;
        $this->fb = new Facebook($param);
    }

    function index() {
        //$data['shopping'] = $this->shopping;
        
        if (getsessionhelper()) {
            if (getsessionhelper()['type'] == 'client')
                redirect('home_page');
            else if (getsessionhelper()['type'] == 'commercant')
                redirect('inscription/gestionprofil/viewprofile');
        }
        else
            redirect('inscription/login', 'refresh');
    }

    function logout() {
        $this->fb->destroySession();
        $this->session->unset_userdata('login_in');
        $this->session->sess_destroy();
        redirect('inscription/home', 'refresh');
    }

}

?>
