<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paiement extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('notification/notif_model');
        $this->load->model('paiement/paiement_model', '', TRUE);
    }

        function DemActivePay($idcomm)
    {
                $msg = "Une Nouvelle demande d'activation de paiement";
                $this->paiement_model->notifadmin(1,$idcomm, $msg);
                redirect('inscription/gestionprofil/viewprofile');
   
    }
    
       function getnotifpay()
    {
          //get notif for activate paiement
         $pay = $this->paiement_model->GetNotif();
          foreach ($pay as $value) {
             
         
                //put the idpersonne of the comm  in idclt
                $value->idpers = $this->notif_model->getProprietaireCom($value->idcomm);
           
                
            }
         $data['pay'] = $pay;
         $this->twig->render('admin/notif/notifadminpay_view',$data);

   
    }
    
     function activer($id) {
         $idcom = $this->paiement_model->getidcom($id);
         $msg = "Votre demande activation paiement a été acceptée";
        //activer paiement pour commercant
        if ($this->paiement_model->activer($id, $msg, $idcom) == true) {

            redirect('paiement/paiement/getnotifpay/', 'refresh');
        } else {
            $data = array('msg' => 'Erreur activation paiement pour commercant');
            $this->twig->render('Echec_view', $data);
            
        }
    }
    
      function delete($id) {
        // delete notif
        $this->paiement_model->delete($id);

        // redirect to notif list 
            redirect('paiement/paiement/getnotifpay/', 'refresh');
    }
    
      function getactivpay() {
       
         $res = $this->paiement_model->getComm();
       
         foreach ($res as $value) {
             
         
                //get the information of comm from table personne
                $value->nom = $this->paiement_model->getNomCom($value->personne_idpersonne);
                $value->prenom = $this->paiement_model->getPrenomCom($value->personne_idpersonne);               
           
                
            }
        
        $data['res'] = $res;
         $this->twig->render('admin/notif/getactivpay_view',$data);
        
          
    }
    
      function desactiver($id) {
        // $idcom = $this->paiement_model->getidcom($id);
         $msg = "Votre activation est annulée";
        //activer paiement pour commercant
        if ($this->paiement_model->desactiver($id, $msg) == true) {

            redirect('paiement/paiement/getnotifpay/', 'refresh');
        } else {
            $data = array('msg' => 'Erreur activation paiement pour commercant');
            $this->twig->render('Echec_view', $data);
            
        }
    }
    
        function getnotifpaycomm()
    {
          //get notif for activate paiement
         $pay = $this->paiement_model->GetNotifComm();
         
         $data['pay'] = $pay;
         $this->twig->render('commercant/notif/notifpaycomm_view',$data);

   
    }
    
           function saveAffil($idnotifcomm = NULL)
    {
                if($idnotifcomm != NULL)
        {
            $update = $this->paiement_model->updateVueComm($idnotifcomm);
           
        }
     
        // les methode appelant les vue
        $data['liencontrole'] = base_url().'paiement/paiement/controle';
        $data['lienechec'] = base_url().'paiement/paiement/echec';
        $data['liensucces'] = base_url().'paiement/paiement/success';
        
      $this->twig->render('commercant/paiement/savepay_view',$data);


   
    }
    
             function InsertAff()
    {
         $this->form_validation->set_rules('affil', 'affil', 'required|trim');
  
        if ($this->form_validation->run() == FALSE) {
            $data['liencontrole'] = base_url().'/paiement/paiement/controle';
        $data['lienechec'] = base_url().'/paiement/paiement/echec';
        $data['liensucces'] = base_url().'/paiement/paiement/success';
        
      $this->twig->render('commercant/paiement/savepay_view',$data);
        } else
         {//save the affilié and idcomm
                 //set champ notifmsg du comm (incerement)
         $idcomm = getsessionhelper()['id'];
            $this->paiement_model->saveaffil($idcomm);
            redirect('inscription/gestionprofil/viewprofile');
        }


   
    }
    function controle()
    {
$this->twig->render('paiement/controle_view');
    
    }
    
        function echec()
    {
$this->twig->render('paiement/echec_view');
    
    }
    
          function success()
    {
$this->twig->render('paiement/success_view');
    
    } 
    
  

}

?>