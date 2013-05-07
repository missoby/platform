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
        // recupérer les comm
        $enscomm = $this->message_model->getcomm();
        $data['enscomm'] = $enscomm;
        $this->twig->render('message/admin/msgadmintcomm', $data);
        //
    }

    function sendmsg() {
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
        $data['msgs'] = $this->message_model->get_list_comm_admin();
        $this->twig->render('message/admin/listSenderMsg_view', $data);
    }
    // get conversation between admin and one comm
      function msg_conv_comm_admin($idcom, $nom)
    {
          
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
    
    
    
    
    
    
    
    
    
    
    
    
    
//
//    // fonction to get msg sent by the admin
//    function getmsgsentbyadmin() {
//
//        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
//
//        $offset = 0;
//        $listcomm = $this->message_model->get_list_comm_sent($idadmin);
//
//        if ($listcomm != '') {
//            $idpersonne_comm = $this->message_model->get_id_comm_pers($listcomm);
//
//            if ($idpersonne_comm != '') {
//                $login_pers = $this->message_model->get_login_pers($idpersonne_comm);
//
//                $data['title'] = 'msg sent by admin to commercant ';
//                if ($login_pers != False) {
//                    $this->load->library('table');
//                    $this->table->set_empty("&nbsp;");
//                    $this->table->set_heading('Nom', 'Prénom', 'Action');
//                    $i = 0 + $offset;
//                    foreach ($login_pers as $pers) {
//                        $this->table->add_row($pers['nom'], $pers['prenom'], anchor('message/msgcommadmin/view/' . $pers['idpersonne'] . '/' . $pers['nom'], '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//                        );
//                    }
//                    $data['table'] = $this->table->generate();
//                    $this->twig->render('message/msgclientcommrecu_view', $data);
//                } else {
//                    $data['table'] = 'vous n\'avez aucun message';
//                    $this->twig->render('message/msgclientcommrecu_view', $data);
//                }
//            }
//        } else {
//            $data['table'] = 'vous n\'avez envoyé aucun message';
//            $this->twig->render('message/msgclientcommrecu_view', $data);
//        }
//    }
//
//    function view($id, $nom) {
//        $offset = 0;
//        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
//        // fct to get idclient
//        $idcomm = $this->message_model->getidcom($id)->row()->idcommercant;
//        //echo 'client = '. $idclient;
//        // fct to get all msg send by client to customer
//        $msgcom = $this->message_model->getmsgcomm($idcomm, $idadmin);
//        $data['title'] = 'msg sent by comm';
////         foreach ($msgclients->result_array() as $row)
////         {
////             echo $row['sujet'];
////         }
//
//
//        $this->load->library('table');
//        $this->table->set_empty("&nbsp;");
//        $this->table->set_heading('Nom', 'Sujet', 'Action');
//        $i = 0 + $offset;
//        foreach ($msgcom->result_array() as $msg) {
//            $this->table->add_row($nom, $msg['sujet'], anchor('message/msgcommadmin/viewmsg_onecom/' . $msg['idMsgAdmin'] . '/' . $idcomm, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//            );
//        }
//        $data['table'] = $this->table->generate();
//        $data['nom'] = $nom;
//        $this->twig->render('message/allmsgclientcomm_view', $data);
//    }
//
//    function viewmsg_onecom($idmsg, $idcomm) {
//        // get one msg of list msg send by client
//        $msg = $this->message_model->getmsgcombyid($idmsg)->row();
//        $data['msg'] = $msg;
//        $data['idcom'] = $idcomm;
//        $data['idmsg'] = $idmsg;
//        $data['title'] = 'msg sent by admin';
//        $this->twig->render('message/admin/message_view', $data);
//    }
////
////    function reponseadmin($idcom, $idmsg) {
////        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
////        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
////        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
////        if ($this->form_validation->run() == FALSE) {
////            $msg = $this->message_model->getmsgcombyid($idmsg)->row();
////            $data['msg'] = $msg;
////            $data['idcom'] = $idcomm;
////            $data['idmsg'] = $idmsg;
////            $this->twig->render('message/admin/message_view', $data);
////        } else {
////            $this->message_model->reponseadmincomm($idcom, $idadmin);
////            redirect('message/msgcommadmin/getmsgsentbyadmin');
////        }
////    }
//
//    function getmsgreceivedadmin() {// recupérer les msg recu from comm
//        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
//        $offset = 0;
//        $listcomm = $this->message_model->get_list_comm_recu($idadmin);
//
//        if ($listcomm != '') {
//            $idpersonne_comm = $this->message_model->get_id_comm_pers($listcomm);
//
//            if ($idpersonne_comm != '') {
//                $login_pers = $this->message_model->get_login_pers($idpersonne_comm);
//                $data['title'] = 'msg recu par l\'admin / envoyé par commercant';
//                if ($login_pers != False) {
//                    $this->load->library('table');
//                    $this->table->set_empty("&nbsp;");
//                    $this->table->set_heading('Nom', 'Prénom', 'Action');
//                    $i = 0 + $offset;
//                    foreach ($login_pers as $pers) {
//                        $this->table->add_row($pers['nom'], $pers['prenom'], anchor('message/msgcommadmin/viewreceived/' . $pers['idpersonne'] . '/' . $pers['nom'], '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//                        );
//                    }
//                    $data['table'] = $this->table->generate();
//                    $this->twig->render('message/msgclientcommrecu_view', $data);
//                } else {
//                    $data['table'] = 'vous n\'avez aucun message';
//                    $this->twig->render('message/msgclientcommrecu_view', $data);
//                }
//            }
//        } else {
//            $data['table'] = 'vous n\'avez envoyé aucun message';
//            $this->twig->render('message/msgclientcommrecu_view', $data);
//        }
//    }
//
//    function viewreceived($idpers, $nom) {
//        $offset = 0;
//        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
//        // fct to get idcomm
//        $idcomm = $this->message_model->getidcom($idpers)->row()->idcommercant;
//
//        // fct to get all msg received by admin from customer
//        $msgcom = $this->message_model->getmsgreceivedcom($idcomm, $idadmin);
//        $data['title'] = 'msg received by admin / sent by comm';
////         foreach ($msgclients->result_array() as $row)
////         {
////             echo $row['sujet'];
////         }
//
//
//        $this->load->library('table');
//        $this->table->set_empty("&nbsp;");
//        $this->table->set_heading('Nom', 'Sujet', 'Action');
//        $i = 0 + $offset;
//        foreach ($msgcom->result_array() as $msg) {
//            $this->table->add_row($nom, $msg['sujet'], anchor('message/msgcommadmin/viewmsgreceived_onecomm/' . $msg['idMsgCommAdmin'] . '/' . $idcomm, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//            );
//        }
//        $data['table'] = $this->table->generate();
//        $data['nom'] = $nom;
//        $this->twig->render('message/allmsgclientcomm_view', $data);
//    }
//
//    function viewmsgreceived_onecomm($idmsg, $idcomm) {
//        // get one msg of list msg send by client
//        $msg = $this->message_model->getmsgcomreceiv_byid($idmsg)->row();
//        $data['msg'] = $msg;
//        $data['idcom'] = $idcomm;
//        $data['idmsg'] = $idmsg;
//        $data['title'] = 'msg received by admin/ sent by com';
//        $this->twig->render('message/admin/message_view', $data);
//    }
//
//    function msgreceived_admin() {
//        $offset = 0;
//        $idcom = getsessionhelper()['id'];
//        $msgadmin = $this->message_model->getmsgadmin($idcom);
//        $data['title'] = 'msg received by comm from admin';
////         foreach ($msgclients->result_array() as $row)
////         {
////             echo $row['sujet'];
////         }
//
//
//        $this->load->library('table');
//        $this->table->set_empty("&nbsp;");
//        $this->table->set_heading('Nom', 'Sujet', 'Action');
//        $i = 0 + $offset;
//        foreach ($msgadmin->result_array() as $msg) {
//            $this->table->add_row('admin', $msg['sujet'], anchor('message/msgcommadmin/viewmsg_oneadmin/' . $msg['idMsgAdmin'] . '/' . $idcom, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//            );
//        }
//        $data['table'] = $this->table->generate();
//        $data['nom'] = 'admin';
//        $this->twig->render('message/allmsgclientcomm_view', $data);
//    }
//
//    function viewmsg_oneadmin($idmsg, $idcom) {
//        //getmsgcombyid
//        // get one msg of list msg send by admin / received by comm
//        $msg = $this->message_model->getmsgcombyid($idmsg)->row();
//        $data['msg'] = $msg;
//        $data['idcom'] = $idcom;
//        $data['idmsg'] = $idmsg;
//        $data['title'] = 'msg sent by admin/ received by comm';
//        $this->twig->render('message/messagecomm_view', $data);
//    }
//
//    function reponsecommadmin($idcom, $idmsg) {
//        $idadmin = $this->admin_model->getidadminformsg()->row()->idadmin;
//        $this->form_validation->set_rules('sujet', 'Sujet', 'required|trim');
//        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
//        if ($this->form_validation->run() == FALSE) {
//            $msg = $this->message_model->getmsgcombyid($idmsg)->row();
//            $data['msg'] = $msg;
//            $data['idcom'] = $idcomm;
//            $data['idmsg'] = $idmsg;
//            $this->twig->render('message/messagecomm_view', $data);
//        } else {
//            $this->message_model->reponsecommadmin($idcom, $idadmin);
//            redirect('message/msgcommadmin/msgreceived_admin');
//        }
//    }
//
//    function msgsent_admin() {//envoyé du com to admin
//        $offset = 0;
//        $idcom = getsessionhelper()['id'];
//        $msgadmin = $this->message_model->getmsgadmin_sent($idcom);
//        $data['title'] = 'msg sent by comm to admin';
////         foreach ($msgclients->result_array() as $row)
////         {
////             echo $row['sujet'];
////         }
//
//
//        $this->load->library('table');
//        $this->table->set_empty("&nbsp;");
//        $this->table->set_heading('Nom', 'Sujet', 'Action');
//        $i = 0 + $offset;
//        foreach ($msgadmin->result_array() as $msg) {
//            $this->table->add_row('admin', $msg['sujet'], anchor('message/msgcommadmin/viewmsgsent_oneadmin/' . $msg['idMsgCommAdmin'] . '/' . $idcom, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"')
//            );
//        }
//        $data['table'] = $this->table->generate();
//        $data['nom'] = 'admin';
//        $this->twig->render('message/allmsgclientcomm_view', $data);
//    }
//
//    function viewmsgsent_oneadmin($idmsg, $idcom) {
//        // get one msg of list msg send to admin / sent by comm
//        $msg = $this->message_model->getmsgcomreceiv_byid($idmsg)->row();
//        $data['msg'] = $msg;
//        $data['idcom'] = $idcom;
//        $data['idmsg'] = $idmsg;
//        $data['title'] = 'msg sent by comm/ received by admin';
//        $this->twig->render('message/messagecomm_view', $data);
//    }

}

?>
