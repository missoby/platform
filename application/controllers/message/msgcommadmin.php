<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Msgcommadmin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('message/message_model');
        $this->load->model('admin/admin_model');
        $this->load->model('notification/notif_model'); 
        $this->twig->addFunction('getsessionhelper');
    }

    function send() {
        
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('admin/admin');
        }
        
        //
        // recupérer les comm
        $enscomm = $this->message_model->getcomm();
        $data['enscomm'] = $enscomm;
        $this->twig->render('message/admin/msgadmintcomm', $data);
        //
    }

    function sendmsg() {
       
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('admin/admin');
        }
        
        //
        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
        // recupérer les comm
        $enscomm = $this->message_model->getcomm();
        //
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        $this->form_validation->set_rules('com', 'Commercant', 'required');

        if ($this->form_validation->run() == FALSE) {
            $enscomm = $this->message_model->getcomm();
            $data['enscomm'] = $enscomm;
            $this->twig->render('message/admin/msgadmintcomm', $data);
        } else {
                 //set champ notifmsg du comm (incerement)
         
            $this->message_model->msgadmincomm($idadmin);
            redirect('admin/admin');
        }
    }
    
      
     //get list comm who sent msg to admin
    function listmsgcomm() {
       
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('admin/admin');
        }
        
        //
        $data['msgs'] = $this->message_model->get_list_comm_admin();
        $this->twig->render('message/admin/listSenderMsg_view', $data);
    }
    // get conversation between admin and one comm
      function msg_conv_comm_admin($idcom, $nom)
    {
          if(empty($idcom) and empty($nom))
            redirect ('admin/admin');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('admin/admin');
        }
        
        //
        //mettre a zero le champ notifmsg de la table admin
        $rmz = $this->notif_model->Rmz_Notif_Msg_Admin();
        
        $conv = $this->message_model->get_conv_admin($idcom);
        
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
        //$data['nom'] = $nom;
         $data['idcom'] = $idcom;
     $this->twig->render('message/admin/ListMsgAdmin_view', $data);
    }
    
      function VoirDetailMsgAdmin($idmsg, $table, $idcom)
    {
          if(empty($idmsg) and empty($table) and empty($idcom))
            redirect ('admin/admin');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('admin/admin');
        }
        
        //
         // display the continue of one message by his id ($idmsg)
        if($table == 'msgadmin')
        {
        $msg = $this->message_model->getmsgbyid_msgadmin($idmsg)->row();
        }
        else
        {
        $msg = $this->message_model->getmsgbyid_msgadmincomm($idmsg)->row();
        }
        $data['msg'] = $msg;
        $data['idcom'] = $idcom;
        $data['idmsg'] = $idmsg;
        $data['table'] = $table;
        //$data['title'] = 'msg sent by client';
        $this->twig->render('message/admin/messageAdmin_view', $data);
        
    }
    
      function ReponseAdmin($idcom, $nom)
    {
          if(empty($idcom) and empty($nom))
            redirect ('inscription/login');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        //$idcom = getsessionhelper()['id'];
        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
                 $conv = $this->message_model->get_conv_admin($idcom);
        
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
     $this->twig->render('message/admin/ListMsgAdmin_view', $data);
        } else {
            //update notifmsg of the comm
            $this->message_model->ReponseAdmin($idcom);
            redirect('message/msgcommadmin/msg_conv_comm_admin/'.$idcom.'/'.$nom);
        }
        
    }
    

}

?>
