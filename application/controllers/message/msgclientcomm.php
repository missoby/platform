<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Msgclientcomm extends CI_Controller {
    private $shopping = array();
    function __construct() {
        parent::__construct();
        $this->load->model('message/message_model');
        $this->load->model('notification/notif_model');
        $this->load->model('produit/produit_model', '', TRUE);

        $this->twig->addFunction('getsessionhelper');
        
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }
    
      // prepare the form to send msg to the admin
      function sendMsgToAdmin() {
        $id = getsessionhelper()['id'];
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            
            $this->twig->render('message/commercant/sendMsgToAdmin');
        } else {
            //update champ notifmsg de la table admin
            $this->message_model->msgCommAdmin($id);
            redirect('message/msgclientcomm/listmsgclientcomm');
        }
    }
    
    // save message sent from client to commercant (msgclient)
    function send($id, $idprod) {
        
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $data['idcom'] = $id;
            $data['idprod']= $idprod;
             $data['shopping'] = $this->shopping;
            $this->twig->render('message/client/sendMsgToComm', $data);
        } else {
          
            $this->message_model->msgclientcomm($id);
            redirect('produit/afficheproduit/details/'.$idprod);
        }
    }
    
    //get list client who sent msg to comm
    function listmsgclientcomm() {
        $data['msgs'] = $this->message_model->get_list_client();
        $data['shopping'] = $this->shopping;
        $this->twig->render('message/commercant/listSenderMsg_view', $data);
    }
    
    //get conversation between client and current commercant (msgclient, msgcommclient)
    function voirmsgclientcomm($idclient, $idp, $nom) {
        $conv = $this->message_model->get_conv_client_comm($idclient);
                //mettre a zero le champ notifmsg de la table commercant
        $rmz = $this->notif_model->Rmz_Notif_Msg_Comm(getsessionhelper()['id']);
        //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }

                    return $ad < $bd ? 1 : -1;
                });
        
        $data['conv'] = $conv;
        $data['idcl'] = $idclient;
        $data['nom'] = str_replace("%20", " ", $nom);
        $data['shopping'] = $this->shopping;
        $this->twig->render('message/commercant/ListMsg_view', $data);
    }
    
    // the response of the comm to a client (be save on table msgclientcomm)
     function reponsecomm($idclient, $nom) {
        $idcom = getsessionhelper()['id'];
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $conv = $this->message_model->get_conv_client_comm($idclient);
        //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }

                    return $ad < $bd ? 1 : -1;
                });

        $data['conv'] = $conv;
        $data['idcl'] = $idclient;
        $data['nom'] = str_replace("%20", " ", $nom);
        $data['shopping'] = $this->shopping;
        $this->twig->render('message/commercant/ListMsg_view', $data);
        } else {
             //save and update champ notifmsg de la table client
            $this->message_model->reponsecommclient($idclient);
            redirect('message/msgclientcomm/listmsgclientcomm');
        }
    }
    
    function msg_conv_comm_admin()
    {//mettre a zero le champ notifmsg de la table commercant
        $rmz = $this->notif_model->Rmz_Notif_Msg_comm(getsessionhelper()['id']);
        $conv = $this->message_model->get_conv_comm_admin();
     
        
         //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }
                    return $ad < $bd ? 1 : -1;
                });
        $data['conv'] = $conv;
        $data['shopping'] = $this->shopping;
        $this->twig->render('message/commercant/ListMsgAdmin_view', $data);
    }
    
    function ReponseCommAdmin()
    {
        $idcom = getsessionhelper()['id'];
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {  
            $conv = $this->message_model->get_conv_comm_admin();
   
         //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }
                    return $ad < $bd ? 1 : -1;
                });
        $data['conv'] = $conv;
        $data['shopping'] = $this->shopping;
        $this->twig->render('message/commercant/ListMsgAdmin_view', $data);
        } else {
            $this->message_model->ReponseCommAdmin(getsessionhelper()['id']);
            redirect('message/msgclientcomm/msg_conv_comm_admin');
        }
        
    }
    
       
    //get list of comm who sent msg to client
    function listmsgcomm() {
               $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';


        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        $data['msgs'] = $this->message_model->get_list_client();
        $data['msgs'] = $this->message_model->get_list_comm();
         $data['shopping'] = $this->shopping;
        $this->twig->render('message/client/listSenderMsg_view', $data);
    }
    
    //get conversation between comm and current client (msgclient, msgcommclient)
    function voirmsgcommclient($idcom, $idp, $nom) {
        $conv = $this->message_model->get_conv_for_client($idcom);
               //mettre a zero le champ notifmsg de la table commercant
        $rmz = $this->notif_model->Rmz_Notif_Msg_client(getsessionhelper()['id']);
        //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }

                    return $ad < $bd ? 1 : -1;
                });

        $data['conv'] = $conv;
        $data['nom'] = str_replace("%20", " ", $nom);
        $data['idcom'] = $idcom;
         $data['shopping'] = $this->shopping;
        $this->twig->render('message/client/ListMsg_view', $data);
    }
    
      // the response of the client to a com (be save on table msgclient)
     function reponseclient($idcom, $nom) {
        $idclt = getsessionhelper()['id'];
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $conv = $this->message_model->get_conv_for_client($idcom);
        //sort the table of conversation by date
        usort($conv, function($a, $b) {
                    $ad = new DateTime($a['dateenvoi']);
                    $bd = new DateTime($b['dateenvoi']);

                    if ($ad == $bd) {
                        return 0;
                    }
                    return $ad < $bd ? 1 : -1;
                });

        $data['conv'] = $conv;
         $data['nom'] = str_replace("%20", " ", $nom);
        $data['idcom'] = $idcom;
         $data['shopping'] = $this->shopping;
        $this->twig->render('message/client/ListMsg_view', $data);
        } else {
            $this->message_model->reponseclient($idcom);
            redirect('message/msgclientcomm/listmsgcomm');
        }
    }
    
    
}

?>
