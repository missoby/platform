<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Statistique extends CI_Controller {


    function __construct() {
        parent::__construct();
        
        $this->twig->addFunction('getsessionhelper');
         $this->load->model('statistique/statistique_model');
         $this->load->model('inscription/inscription_model');
         $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();

    }
    
    function index(){
                    redirect ('inscription/login');

    }


    
    function statcomm()
    {
        
         //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else 
        {
            if(getsessionhelper()['type'] == 'commercant')
            {
            $idcom = getsessionhelper()['id'];
            $data['nbnote1'] = $this->statistique_model->GetNbNot1($idcom);
            $data['nbnote2'] = $this->statistique_model->GetNbNot2($idcom);
            $data['nbnote3'] = $this->statistique_model->GetNbNot3($idcom);
            $data['nbnote4'] = $this->statistique_model->GetNbNot4($idcom);
            $data['nbnote5'] = $this->statistique_model->GetNbNot5($idcom);
            $data['shopping'] = $this->shopping;
            $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
            $data['commercant'] = $commercant;
             $data['nbCmd'] = $this->statistique_model->getNbVenteComm();


            $this->twig->render('commercant/statistique/statistique_view', $data);
            
        }    
    }

}



}


