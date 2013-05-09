<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Statistique extends CI_Controller {


    function __construct() {
        parent::__construct();
        
        $this->twig->addFunction('getsessionhelper');
         $this->load->model('statistique/statistique_model');

    }


    
    function statcomm()
    {
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
  
            $this->twig->render('commercant/statistique/statistique_view', $data);
            
        }    
    }

}



}


