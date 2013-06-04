<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paiement extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('notification/notif_model');
        $this->load->model('inscription/inscription_model');
        $this->load->model('paiement/paiement_model', '', TRUE);
         $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
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

            redirect('paiement/paiement/getactivpay/', 'refresh');
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
    // ca sera pour l'admin et non pas pour le commercant
           function saveAffil()
    {
              
     $data['affil'] = $this->paiement_model->getAffilConf()->row();
     
        // les methode appelant les vue
        $data['liencontrole'] = base_url().'paiement/paiement/controle';
        $data['lienechec'] = base_url().'paiement/paiement/echec';
        $data['liensucces'] = base_url().'paiement/paiement/success';
        
      $this->twig->render('admin/paiement/savepay_view',$data);


   
    }
    
           function modifaffil()
    {
 
        // les methode appelant les vue
        $data['liencontrole'] = base_url().'paiement/paiement/controle';
        $data['lienechec'] = base_url().'paiement/paiement/echec';
        $data['liensucces'] = base_url().'paiement/paiement/success';
        
      $this->twig->render('admin/paiement/modifpay_view',$data);

    }
    
     function updateAff()
    {
         $this->form_validation->set_rules('affil', 'affil', 'required|trim');
  
        if ($this->form_validation->run() == FALSE) {
            $data['liencontrole'] = base_url().'/paiement/paiement/controle';
        $data['lienechec'] = base_url().'/paiement/paiement/echec';
        $data['liensucces'] = base_url().'/paiement/paiement/success';
        
      $this->twig->render('admin/paiement/modifpay_view',$data);
        } else
         {//update the affilié and idadmin
               
         $idadmin = getsessionhelper()['id'];
            $this->paiement_model->updateAff($idadmin);
            redirect('admin/admin');
        }


   
    }
             function InsertAff()
    {
         $this->form_validation->set_rules('affil', 'affil', 'required|trim');
  
        if ($this->form_validation->run() == FALSE) {
            $data['liencontrole'] = base_url().'/paiement/paiement/controle';
        $data['lienechec'] = base_url().'/paiement/paiement/echec';
        $data['liensucces'] = base_url().'/paiement/paiement/success';
        
      $this->twig->render('admin/paiement/savepay_view',$data);
        } else
         {//save the affilié and idadmin
               
         $idadmin = getsessionhelper()['id'];
            $this->paiement_model->saveaffil($idadmin);
            redirect('admin/admin');
        }


   
    }
    function controle()
    { 
            if ( empty($_GET['Reference']) || empty($_GET['Action']) )
               // redirect to home if reference and action are empty
               $this->twig->render('home_page');

	$ref = $_GET['Reference'];
        $act = $_GET['Action'];
	 
	 //get the field ref from table commande where ref = $ref if the result is empty redirect to home
       
        $cmdRef = $this->paiement_model->getRef($ref);
        if (empty($cmdRef))
             $this->twig->render('home_page');


    switch ($act) 
    {
        case "DETAIL":    

         // access the database, and retrieve the amount
 //on récupère le montant total de la commande
         $montant = $this->paiement_model->getMontant($ref);
         if ($montant->num_rows() == 0) {
                    $this->twig->render('home_page'); // redirect to home page if empty
                }
                
//            if ( empty($montant) )
//              $this->twig->render('home_page'); // redirect to home page if empty

        echo "Reference=$ref&Action=$act&Reponse=$montant";
        break;


        case "ERREUR":
        
		echo "Reference=$ref&Action=$act&Reponse=OK";
        break;


        case "ACCORD":
        // access the database, register the authorization number (in param)
        $par = $_GET['Param'];
            //get data of the cmd : refcmd, datecmd and prixtotal
            $infCmd = $this->paiement_model->getInfoCmd($ref);
            // on récupère prix,date et ref to send it to client and admin
            foreach ($infCmd as $var)
            {
                $idcmd = $var->idcommande;
                $prix = $var->prixtotal;
                $date = $var->datecmd;
            }
 
          // send mail of succes of paiement to admin/client and to comm send mail of the command
       //send email to client and admin 
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'cmarwabriki@gmail.com',
                'smtp_pass' => 'marwa26041989',
                'mailtype' => 'html'
            );

            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");

            $this->email->from('cmarwabriki@gmail.com');
            $this->email->to(getsessionhelper()['email'],'missoby@hotmail.fr');//client connecté 

            $this->email->subject('succes de paiement');
            $msg = 'Paiement avec succes pour la commande de référence '.$ref.'de prix total'.$prix.'Le '.$date;
            $this->email->message($msg);
            
            if (!$this->email->send())
                show_error($this->email->print_debugger());
            else
            {                //send notif to client and admin
                $idclient = getsessionhelper()['id'];
                $msg = 'Un nouveau paiement a été effectué consulter votre mail';
                $this->paiement_model->NotifSuccessPay($idclient, $msg);
            }
            //send mail to list comm that they have product commanded
           $Listcomm =  $this->paiement_model->getListCommPay($idcmd, $ref, $date, $prix);
                
                 foreach ($Listcomm as $var)
                 {$idcom = $var['idcom']; 
                  $prixtot = 0;
                  foreach ($Listcomm as $q)
                  {
                      if($q['idcom'] == $idcom)
                      {
                          $libelle .=  $q['libelle'] . 'pour une quantité de' . $q['qty'];
                          $prixtot = $prixtot + $q['prixprod'];
                          
                          
                      }
                      
                  }
                  //send email to comm
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'cmarwabriki@gmail.com',
                'smtp_pass' => 'marwa26041989',
                'mailtype' => 'html'
            );

            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");

            $this->email->from('cmarwabriki@gmail.com');
            $this->email->to($var['email']);//To commercant 

            $this->email->subject('Produits Vendu');
            $msg = 'Liste de produit vendu '.$libelle.'Pour Un prix total de'.$prixtot.'La  commande de référence'.
                    $ref.'le '.$date;
            $this->email->message($msg);
            
            if (!$this->email->send())
                show_error($this->email->print_debugger());

            else
                {
                //send notif to comm
                 $msg = 'Un nouveau paiement a été effectué consulter votre mail';
                $this->paiement_model->NotifSuccessPayComm($var['idcom'], $msg);
                }
                     
                 }
           

        echo "Reference=$ref&Action=$act&Reponse=OK";
        break;


        case "REFUS":
        
        echo "Reference=$ref&Action=$act&Reponse=OK";
        break;


        case "ANNULATION":
        
        echo "Reference=$ref&Action=$act&Reponse=OK";
        break;

    }
    
    }
    
        function echec()
    {
$this->twig->render('paiement/echec_view');
    
    }
    
          function success()
    {
$this->twig->render('paiement/success_view');
    
    } 
    
    function listeAchat()
    {
        if(getsessionhelper()['login'] != NULL)
        {
       $data['connect'] = 'yes';
     $data['shopping'] = $this->shopping;
     $this->twig->render('paiement/ListeAchat_view', $data);
        }
        else 
        {
            // we need to display  error msg!!!
          $data['connect'] = 'no';
          $data['shopping'] = $this->shopping;
          $this->twig->render('paiement/ListeAchat_view', $data);

        }

        
    }
     function saveAdr()
     { 
         $data['shopping'] = $this->shopping;
         $this->twig->render('paiement/Adresse_view', $data);
        
    }
    
     function InsertAdr()
     { 
          $this->form_validation->set_rules('adresse', 'adresse', 'required|trim');
  
        if ($this->form_validation->run() == FALSE) {
            $data['shopping'] = $this->shopping;
         $this->twig->render('paiement/Adresse_view', $data);
        } else
         {//save the adr of the client
               
         $idclt = getsessionhelper()['id'];
            $this->paiement_model->saveAdr($idclt);
            redirect('paiement/paiement/livraison');
        
    }
     }
     
     function livraison()
     {
      $data['shopping'] = $this->shopping;
     $this->twig->render('paiement/Livraison_view', $data);
 
     }
     
    function saveCmd()
     { 
//     $this->shopping['content'] = $this->cart->contents();
//        $this->shopping['nbr'] = $this->cart->total_items(); 
//    
      $prixtotal = $this->shopping['total'] = $this->cart->total();
      $cmd =  $this->paiement_model->saveCmd($prixtotal);

        foreach ($this->cart->contents() as  $var)
        {
           $idp =  $var['options']['idp'];
           $qty = $var['qty'];
           $this->paiement_model->saveProdCmd($idp,$qty, $cmd);
           $this->paiement_model->updatestock($idp,$qty);
            
        }
      
            
           // $t = rand(); echo $t;
//             $prixtotal = $this->shopping['total'] = $this->cart->total();
//            $this->paiement_model->saveCmd($prixtotal);
              //redirect('home_page');// to form created by 3alé
             $data['refcmd'] = $this->paiement_model->getRefCmd($cmd);
             $data['affilCmd'] = $this->paiement_model->getAffilCmd();
             $data['idsess'] =  $this->session->userdata('session_id');
             $data['montant'] = $prixtotal;
            
            $this->twig->render('paiement/FormPay_view', $data);
// tu recupère le prix total, refference, affilier , et l'id du session courrantree w tab3athhom ilkol fi page fil vieww 8adika bech na3mel ena le formulaire OK ?

    }
    
         function getnotifpayclient()
    {
          //get notif for activate paiement
         $pay = $this->paiement_model->GetNotifClient();
         
         $data['pay'] = $pay;
         $this->twig->render('client/notif/notifpayclient_view',$data);

   
    }
    
    function GetAchat($idclt)
    {
        $data['shopping'] = $this->shopping;
        $data['achat'] = $this->paiement_model->getListAchatClt($idclt);
         // $achat =  $this->paiement_model->getListAchatClt($idclt);
//          foreach ($achat as $var)
//          {
//              echo $var['libelle'];
//          }
//          return;
        $data['path'] = site_url() . 'uploads/'; 
        $this->twig->render('client/ListAchatClt_view',$data);

    }
    
    function ListeVenteComm()
    {
        $data['shopping'] = $this->shopping;
        $data['vente'] = $this->paiement_model->getListVenteComm();
        $data['path'] = site_url() . 'uploads/'; 
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        $this->twig->render('commercant/ListAchatComm_view',$data);

        
        
    }
    
  

}

?>