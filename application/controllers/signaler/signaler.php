<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Signaler extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('signaler/signaler_model');
    }


  function addSign($idsign, $type, $idp = NULL)
  {
      //test sécurité
        if(empty($idsign) and empty($type))
            redirect ('inscription/login');
         //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
      $data['idsign'] = $idsign;
      $data['type'] = $type;
      $data['idp'] = $idp;
     $this->twig->render('signaler/signaler_view', $data);
  }
  function saveSign($idsign, $type, $idp = NULL)
  {
      //test sécurité
        if(empty($idsign) and empty($type))
            redirect ('inscription/login');
         //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
       $this->form_validation->set_rules('titre', 'titre', 'required|trim');
        $this->form_validation->set_rules('description', 'description', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
       $data['idsign'] = $idsign;
      $data['type'] = $type;
     $this->twig->render('signaler/signaler_view', $data);
        } else {
       $this->signaler_model->addsign($idsign, $type);
       if($type == 'produit')
       {
      redirect('produit/afficheproduit/details/'.$idsign);
       }
       elseif(($type == 'avis') AND ($idp != NULL))
       {
      redirect('produit/afficheproduit/details/'.$idp);
       }
       elseif($idp != NULL)
       {
           $idsujet = $idp;
        redirect('forum/forum/afficherDetail/'.$idsujet);
  
       }
       
        }
  }
  
      function deleteSign($id) {
          //test sécurité
        if(empty($id))
            redirect ('inscription/login');
         //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        // delete notif
        $this->signaler_model->deleteSign($id);

        // redirect to notif list 
            redirect('admin/admin/getnotif/', 'refresh');
    }
}

?>