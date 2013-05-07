<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class forum extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('produit/produit_model');
        $this->load->model('forum/forum_model');
                $this->load->model('notification/notif_model');


        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] =$this->cart->total_items();
    }

    function index() {
        $ddd['shopping'] = $this->shopping;
        $ddd['categorie'] = $this->produit_model->getcategorie();
        $this->twig->render('forum/espaceforum_view', $ddd);
    }
    
    public function afficherSujet($idcat) {
        if(empty($idcat))
            redirect ('forum/forum');
        $ddd['shopping'] = $this->shopping;
        $ddd['res'] = $this->forum_model->getAllSujets($idcat);
        $ddd['idcat'] = $idcat;
        $this->twig->render('forum/affichersujet_view', $ddd);
        
    }
    
   public function afficherDetail($id, $idnotif = NULL) {
        if(empty($id))
            redirect ('forum/forum');
        
        if($idnotif != NULL)
        {
            if(getsessionhelper()['type'] == 'client')
            {
          $idclt = getsessionhelper()['id'];
        $this->notif_model->setVueClient($idclt, $idnotif);
            }
            else
            {
               $idcom = getsessionhelper()['id'];
        $this->notif_model->SetVue($idcom, $idnotif);
            }
        }
        
        $data = array();
        $res = $this->forum_model->getSujet($id);
        $data['row'] = $res;
        
        if($res->client_idclient == NULL)
        {
            //on passe le coordonnées du proprietaire du sujet pour envoyer notif
            $data['table'] = 'commercant';
            $data['id'] = $res->commercant_idcommercant;
            //
            $data['nom'] = $this->forum_model->getSociete($res->commercant_idcommercant)->societe;
        }
        else //client
        {
            //on passe le coordonnées du proprietaire du sujet pour envoyer notif
            $data['table'] = 'client';
            $data['id'] = $res->client_idclient;
            //
            $data['nom'] = $this->forum_model->getProprietaire($res->client_idclient);
        }
        
        $msg = $this->forum_model->getMsgForum($id);
        
        foreach ($msg as $value) {
            if($value->client_idclient == NULL)
            {
                $value->client_idclient = $this->forum_model->getSociete($value->commercant_idcommercant)->societe;
            }
            else //client
            {
                $value->commercant_idcommercant = $value->client_idclient;
                $value->client_idclient = $this->forum_model->getProprietaire($value->client_idclient);
            }
        }
        
        $data['msg'] = $msg;
        $data['idsujet'] = $id;
        
         $data['shopping'] = $this->shopping;
        
        $this->twig->render('forum/afficherdetail_view', $data);
    }
    
       public function ajouterMsg($ids, $table= NULL, $id = NULL) {
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim|xss_clean');
        
        //$this->form_validation->set_error_delimiters('<span class="error" name="errortotal">', '</span>');
        
        if ($this->form_validation->run() == FALSE) {
            redirect('/forum/forum/afficherDetail/'.$ids);
        }
        else {
            if(getsessionhelper()['type'] == 'client') {
                $idclt = getsessionhelper()['id'];
                $idcom = NULL;
            }
            else {
                $idclt = NULL;
                $idcom = getsessionhelper()['id'];
            }
            
            $this->forum_model->setMsgForum($ids, $idclt, $idcom);
            // set the notification
            if (($id != NULL) AND ($table != NULL)  ){
                $msg = 'Un nouveau commentaire a été ajouté pour votre sujet forum';
                
            $this->notif_model->notifActionForum($id,$table, $msg, $ids);
        }
            redirect('/forum/forum/afficherDetail/'.$ids);
        }
        
    }
    
    public function ajouterSujet($idcat) {
        $data = array();
        $data['idcat'] = $idcat;
        
        $this->form_validation->set_rules('titre', 'Titre', 'required|trim|xss_clean|max_length[255]');
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim|xss_clean');
        
        //$this->form_validation->set_error_delimiters('<span class="error" name="errortotal">', '</span>');
        
        if ($this->form_validation->run() == FALSE) {
            $this->twig->render('forum/nouveausujet_view', $data);
        }
        else {
            if(getsessionhelper()['type'] == 'client') {
                $idclt = getsessionhelper()['id'];
                $idcom = NULL;
            }
            else {
                $idclt = NULL;
                $idcom = getsessionhelper()['id'];
            }
            
            $this->forum_model->ajouterSujet($idcat, $idclt, $idcom);
            redirect('/forum/forum/afficherSujet/'.$idcat);
        }
    }
    public function deletemsg($idmsg, $idsujet)
    {
        // delete forum msg
        $this->forum_model->deletemsg($idmsg);

        // redirect to product list page
        redirect('forum/forum/afficherDetail/'.$idsujet, 'refresh');
    }
    
    function resoudre($idsj)
    {
          //Résoudre 
        if ($this->forum_model->resoudre($idsj) == true) {
        redirect('forum/forum/afficherDetail/'.$idsj, 'refresh');
        } else {
                    redirect('forum/forum/afficherDetail/'.$idsj, 'refresh');
    }

}

   function ModifierSujet($idsj) {
        $sujet = $this->forum_model->getSujet($idsj);
      

        //end of tester si une image existe ou pas

        foreach ($sujet as $row) {
            $data = array(
                'id' => $idsj,
                'titre' => $sujet->titre,
                'contenu' => $sujet->contenu,

            );
        }
        
        $this->twig->render('forum/ForumEdit_view', $data);
    }
    
    function insertModif($idsj)
    {
         //récupérer les données apartir du formulaire
        $this->form_validation->set_rules('titre', 'titre', 'required|trim|xss_clean|max_length[45]|min_length[8]');
        $this->form_validation->set_rules('contenu', 'contenu', 'required|trim|xss_clean|max_length[200]|min_length[30]');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            $sujet = $this->forum_model->getSujet($idsj);
      

        //end of tester si une image existe ou pas

        foreach ($sujet as $row) {
            $data = array(
                'id' => $idsj,
                'titre' => $sujet->titre,
                'contenu' => $sujet->contenu,

            );
        }
        
        $this->twig->render('forum/ForumEdit_view', $data);
        } else {
            
            if($this->input->post('titrehidden') != $this->input->post('titre'))
            {
                $titre = $this->input->post('titre');
            }
            else
            {
                $titre = $this->input->post('titrehidden');
            } 
            
         $form_data = array('titre' => $titre,
                'contenu' => $this->input->post('contenu'),

            );
            if ($this->forum_model->InsertModif($idsj, $form_data) == true) {
                $data = array(
                    'msg' => 'Sujet modifié avec succes',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('Success_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec modification Sujet',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('Echec_view', $data);
            }
    }

}
}
