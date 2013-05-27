<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');

class Listprodmobile extends CI_Controller {

    public $tableauglob = array();
    public function __construct() {
        parent::__construct();
        $this->load->model('produit/produit_model', '', TRUE);
        $this->load->model('avis/avis_model');
        $this->load->model('forum/forum_model');
        $this->load->model('inscription/inscription_model');
        $this->load->model('annonce/annonce_model');
        $this->load->model('recherche/recherche_model');
         $this->load->model('inscription/user_model', '', TRUE);

        $this->twig->addFunction('validation_errors');
        $this->twig->addFunction('getsessionhelper');
    }

    public function index() {
    }

    public function listprod() {
        $ensproduit = $this->produit_model->listprodmobile($this->input->post('id'));
        $data['produit'] = $ensproduit;
        echo json_encode($data);
    }

    public function detailprod() {
        $data['detail'] = $this->produit_model->get_by_id($this->input->post('id'))->row();
        echo json_encode($data);
    }

    public function avismobile() {
        $avis = $this->avis_model->getavis($this->input->post('id'));
        foreach ($avis as $value) 
            {
                $value->idclient = $value->client_idclient;
                $value->client_idclient = $this->forum_model->getProprietaireDelAvis($value->client_idclient);
            }
        $data['avis'] = $avis;
        echo json_encode($data);
    }

    public function commercantinfo() {
        $idcomm = $this->input->post('id');
        $idpersonne = $this->inscription_model->getidpers($idcomm)->row();
        $personne = $this->inscription_model->getparent($idpersonne->personne_idpersonne)->row();
        $commercant = $this->inscription_model->getchildcomm($personne->idpersonne)->row();
        $data['commercant'] = $commercant;
        echo json_encode($data);
    }

    public function prodcommmobile() {
        $idcomm = $this->input->post('id');
        $ensproduit = $this->produit_model->get_product_comm_mobile($idcomm);
        $data['produit'] = $ensproduit;
        echo json_encode($data);
    }

    public function annoncemobile() {
        $idcomm = $this->input->post('id');
        $ensannonce = $this->annonce_model->get_annonce_comm_mobile($idcomm);
        $data['annonce'] = $ensannonce;
        echo json_encode($data);
    }

    public function detailannonce() {
        $data['annonce'] = $this->annonce_model->get_by_id($this->input->post('id'))->row();
        echo json_encode($data);
    }

    public function forum_listsujet_mobile() {
        $id = $this->input->post('id');
        $data['sujet'] = $this->forum_model->getAllSujets_mobile($id);
        echo json_encode($data);
    }

    public function forum_sujet_mobile() {
        $id = $this->input->post('id');
        $res = $this->forum_model->getSujet($id);
        if ($res->client_idclient == NULL) 
            { $data['nom'] = $this->forum_model->getSociete($res->commercant_idcommercant)->societe;} 
        else 
            { $data['nom'] = $this->forum_model->getProprietaire($res->client_idclient); }
        $data['sujetdetail'] = $res;
        echo json_encode($data);
    }

    public function forum_comm_mobile() {
        $id = $this->input->post('id');
        $msg = $this->forum_model->getMsgForum($id);
        foreach ($msg as $value) {
            if ($value->client_idclient == NULL) {
                $value->client_idclient = $this->forum_model->getSociete($value->commercant_idcommercant)->societe;
            } else { //client
                $value->commercant_idcommercant = $value->client_idclient;
                $value->client_idclient = $this->forum_model->getProprietaire($value->client_idclient);
            }
        }
        $data['comm'] = $msg;
        echo json_encode($data);
    }
    
    public function recherche_mobile() {
        $mot = $this->input->post('mot');
        $data['produit'] = $this->recherche_model->getsearchmot($mot);
        echo json_encode($data);
    }
    
    
    public function login()
        {
             $this->form_validation->set_rules('login', 'Nom utilisateur', 'trim|required|xss_clean');
             $this->form_validation->set_rules('pwd', 'Mot de passe', 'trim|required|xss_clean|callback_check_login');
        if ($this->form_validation->run() == FALSE)
            {
                echo json_encode( array('error' => validation_errors()) );
             
            }
            else
            {
//                    /$data = array('error' => 0, 'test' => 'abc');
//                    echo json_encode($data);/
                   echo json_encode($this->tableauglob); // print_r($this->tableau);
            }
        }
        
        function check_login() 
        {
        if (!$result = $this->user_model->login()) {
            $this->form_validation->set_message('check_login', 'Mot de passe ou nom utilisateur incorrecte');
            return false;
        } else {
            //verifier si compte valide ou pas 
            foreach ($result as $row) {  //recuperer type 
                if ($row->active == 0) {
                    $this->form_validation->set_message('check_login', 'veuillez confirmer l\'inscription par mail');
                    return false;
                }
                $reqid = 0;
                if($row->type == 'client'){
                    $reqid = $this->user_model->getidclient($row->idpersonne)->row()->idclient;
                    $notif = $this->user_model->getnotif_client($row->idpersonne)->row();
                }
                elseif ($row->type == 'commercant') {
                    $enable = $this->user_model->getetatcomm($row->idpersonne)->row()->enable;
                      if ($enable == 0) {
                    $this->form_validation->set_message('check_login', 'En attente de la confirmation de la part de l\'administrateur');
                    return false;
                }
                    $reqid = $this->user_model->getidcommercant($row->idpersonne)->row()->idcommercant;
                    $notif = $this->user_model->getnotif_commercant($row->idpersonne)->row();
            }
                ;
                $this->tableauglob = array(
                    'error' => 0, 'id' => $reqid, 'idpersonne' => $row->idpersonne, 
                    'email' => $row->email, 'login' => $row->login, 'type' => $row->type, 
                    'notifaction' => $notif->notifaction, 'notifmsg' => $notif->notifmsg,
                    'adresse' => $row->adresse, 'pays' => $row->pays, 'ville'=> $row->adresse,
                    'nom' => $row ->nom, 'prenom' => $row ->prenom ,'tel' => $row ->tel
               );
            }
            return TRUE;
        }
    }
    
      public function commercantprofil() {
       $idpers = $this->input->post('id');
        $commercant = $this->inscription_model->getidcommprofil($idpers);
       
        $data['commercant'] = $commercant;
        echo json_encode($data);
    }
        

}