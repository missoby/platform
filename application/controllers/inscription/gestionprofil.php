<?php

class Gestionprofil extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->load->model('inscription/inscription_model');
      $this->load->model('paiement/paiement_model');
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('produit/produit_model', '', TRUE);

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index() {
        $data['shopping'] = $this->shopping;
        $this->twig->render('accueilinscri_view', $data);
    }

    function viewprofile($idnotif = NULL, $typeclient = NULL) {
       
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
         
        if($idnotif != NULL)
        {
            if(($typeclient!= NULL))
            {
            $update = $this->paiement_model->updateVueClient($idnotif);
   
            }
            else
            $update = $this->paiement_model->updateVueComm($idnotif);
           
        }
        
        $id = getsessionhelper()['idpersonne'];
        $type = getsessionhelper()['type'];
        $personne = $this->inscription_model->getparent($id)->row();
        $enscom = $this->produit_model->getcommercant();
        $ensproduitdate = $this->produit_model->get_product_by_date();
        if ($personne) {
            if ($type == 'client') {
                $client = $this->inscription_model->getchild($personne->idpersonne)->row();
                $data = array(
                    'personne' => $personne, 'client' => $client,
                    'shopping' => $this->shopping,
                    'produitdate' => $ensproduitdate,
                    'comm' => $enscom,
                    'pathphoto' => site_url() . 'uploads/'
                    
                );
                $this->twig->render('client/voirprofile_view_client', $data);
            } else if ($type == 'commercant') {
                $commercant = $this->inscription_model->getchildcomm($personne->idpersonne)->row();
                $data = array(
                    'personne' => $personne, 'commercant' => $commercant,
                    'shopping' => $this->shopping,
                    
                );
                $this->twig->render('commercant/voirprofile_view_comm', $data);
            }
        }
    }

    function modifier($id) {
         if(empty($id))
            redirect ('inscription/login');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        $type = getsessionhelper()['type'];
        $parent = $this->inscription_model->getprofilparent($id)->result();
        $child = $this->inscription_model->getprofilchild($id, $type)->result();
        $personne = $this->inscription_model->getparent($id)->row();
        $enscom = $this->produit_model->getcommercant();
        $ensproduitdate = $this->produit_model->get_product_by_date();
        if ($type == 'client') { //data for client
            foreach ($child as $row) {
                $idclient = $row->idclient;
            }

            foreach ($parent as $row) {
                $data = array(
                    'nom' => $row->nom,
                    'prenom' => $row->prenom,
                    'login' => $row->login,
                    'adresse' => $row->adresse,
                    'pays' => $row->pays,
                    'ville' => $row->ville,
                    'tel' => $row->tel,
                    'email' => $row->email,
                    'id' => $row->idpersonne,
                    'shopping' => $this->shopping,
                    'produitdate' => $ensproduitdate,
                    'comm' => $enscom,
                    'pathphoto' => site_url() . 'uploads/'
                );
            }
            $this->twig->render('client/editprofileclient_view', $data);
        } else {
            foreach ($child as $row) {
                $societe = $row->societe;
                $adrsoc = $row->adrsoc;
                $descsoc = $row->descsoc;
                $siteweb = $row->siteweb;
                $telpro = $row->telpro;
                $fax = $row->fax;
            }

            foreach ($parent as $row) {
                $data = array(
                    'nom' => $row->nom,
                    'prenom' => $row->prenom,
                    'login' => $row->login,
                    'adresse' => $row->adresse,
                    'pays' => $row->pays,
                    'ville' => $row->ville,
                    'tel' => $row->tel,
                    'email' => $row->email,
                    'societe' => $societe,
                    'adrsoc' => $adrsoc,
                    'descsoc' => $descsoc,
                    'siteweb' => $siteweb,
                    'telpro' => $telpro,
                    'fax' => $fax,
                    'shopping' => $this->shopping
                );
            }
            $this->twig->render('commercant/editprofilecom_view', $data);
        }
    }

    function updateprofilcomm() {
         
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //

        $id = getsessionhelper()['idpersonne'];
        //récupérer les données apartir du formulaire
        $this->form_validation->set_rules('callback', ' ', 'callback_checkMailLogin');
        $this->form_validation->set_rules('nom', 'nom', 'required|trim|xss_clean|max_length[45]|');
        $this->form_validation->set_rules('prenom', 'prenom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');
        $this->form_validation->set_rules('login', 'login', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('societe', 'societe', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('adresse', 'adresse', 'required|trim|xss_clean|max_length[60]');
        $this->form_validation->set_rules('pays', 'pays', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('ville', 'ville', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('tel', 'Numéro telephone', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('adrsoc', 'Adresse Societe', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('descsoc', 'Description Societe', 'required|trim|xss_clean');
        $this->form_validation->set_rules('siteweb', 'Site Web', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('telpro', ' Telephone professionnel', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('fax', 'Fax', 'required|trim|xss_clean|max_length[45]|integer');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            $data = array('nom' => $this->input->post('nom'),
                'prenom' => $this->input->post('prenom'),
                'login' => $this->input->post('login'),
                'adresse' => $this->input->post('adresse'),
                'pays' => $this->input->post('pays'),
                'ville' => $this->input->post('ville'),
                'tel' => $this->input->post('tel'),
                'email' => $this->input->post('email'),
                'societe' => $this->input->post('societe'),
                'adrsoc' => $this->input->post('adrsoc'),
                'descsoc' => $this->input->post('descsoc'),
                'siteweb' => $this->input->post('siteweb'),
                'telpro' => $this->input->post('telpro'),
                'fax' => $this->input->post('fax'),
                'shopping' => $this->shopping
            );
            $this->twig->render('commercant/editprofilecom_view', $data);
        } else {
            //recupèrer login et email courant 
            $form_data = array(
                'nom' => set_value('nom'),
                'prenom' => set_value('prenom'),
                'email' => set_value('email'),
                'login' => set_value('login'),
                'adresse' => set_value('adresse'),
                'pays' => set_value('pays'),
                'tel' => set_value('tel'),
                'ville' => set_value('ville'),
            );
            $form_datacomm = array(
                'societe' => set_value('societe'),
                'adrsoc' => set_value('adrsoc'),
                'descsoc' => set_value('descsoc'),
                'siteweb' => set_value('siteweb'),
                'telpro' => set_value('telpro'),
                'fax' => set_value('fax'),
            );

            if ($this->inscription_model->insertmodif($id, $form_data, $form_datacomm) == true) {

                $data = array(
                    'msg' => 'Modification avec succes',
                    'shopping' => $this->shopping
                );
                $this->twig->render('Success_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec de modification',
                    'shopping' => $this->shopping
                );

                $this->twig->render('Echec_view', $data);
            }
        }
    }

    function checkMailLogin() {
        $id = getsessionhelper()['idpersonne']; 
        $logincourant = $this->inscription_model->getlogincourant($id)->row(); 
        $emailcourant = $this->inscription_model->getmailcourant($id)->row(); 
        $log = $this->input->post('login');
        $mail = $this->input->post('email');
        if ($logincourant->login != $log) {
            //verifier new login unique si nn erreur redirect affichage profil 
            if ($this->inscription_model->veriflogin($log) == false) { //unique
                // echo 'login existant';
                $this->form_validation->set_message('checkMailLogin', 'Nom utilisateur existant');
                return false;
            }
        } elseif ($emailcourant->email != $mail) {
            //verifier new login unique si nn erreur  redirect affichage profil
            if ($this->inscription_model->verifmail($mail) == false) { //unique
                // echo 'email existant';
                $this->form_validation->set_message('checkMailLogin', 'Email existant');
                return false;
            }
        } else
            return true;
    }

    function updateprofilclient() {
         
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //

        //récupérer les données apartir du formulaire
        $id = getsessionhelper()['idpersonne'];
        $this->form_validation->set_rules('callback', ' ', 'callback_checkMailLogin');
        $this->form_validation->set_rules('nom', 'nom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('prenom', 'prenom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');
        $this->form_validation->set_rules('login', 'login', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('adresse', 'adresse', 'required|trim|xss_clean|max_length[60]');
        $this->form_validation->set_rules('pays', 'pays', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('ville', 'ville', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('tel', 'Numéro telephone', 'required|trim|xss_clean|max_length[45]|integer');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
		    $enscom = $this->produit_model->getcommercant();
        $ensproduitdate = $this->produit_model->get_product_by_date();
            $data = array('nom' => $this->input->post('nom'),
                'prenom' => $this->input->post('prenom'),
                'login' => $this->input->post('login'),
                'adresse' => $this->input->post('adresse'),
                'pays' => $this->input->post('pays'),
                'ville' => $this->input->post('ville'),
                'tel' => $this->input->post('tel'),
                'email' => $this->input->post('email'),
                'shopping' => $this->shopping,
				   'produitdate' => $ensproduitdate,
                    'comm' => $enscom,
                    'pathphoto' => site_url() . 'uploads/'
            );
            $this->twig->render('client/editprofileclient_view', $data);
        } else {


            $form_data = array(
                'nom' => set_value('nom'),
                'prenom' => set_value('prenom'),
                'email' => set_value('email'),
                'login' => set_value('login'),
                'adresse' => set_value('adresse'),
                'pays' => set_value('pays'),
                'tel' => set_value('tel'),
                'ville' => set_value('ville'),
            );


            if ($this->inscription_model->insertmodifclient($id, $form_data) == true) {
                $data = array(
                    'msg' => 'Modification Réuissie',
                    'shopping' => $this->shopping
                );
                $this->twig->render('Success_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec de modification',
                    'shopping' => $this->shopping
                );

                $this->twig->render('Echec_view', $data);
            }
        }
    }

    function newpwd() {
         
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        $data['shopping'] = $this->shopping;
        // afficher la vue pour saisir le nouveau pwd
        $this->twig->render('login/updatepwd_view', $data);
    }

    function updatepwd() {//mettre a jour le pwd
         
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        $this->form_validation->set_rules('currpwd', 'Mot de passe courant', 'trim|max_length[255]|callback_change_password');
        $this->form_validation->set_rules('newpwd', 'Nouveau Mot de passe', 'trim|max_length[255]|matches[confpwd]');
        $this->form_validation->set_rules('confpwd', 'Conformatin du nouveau Mot de passe', 'trim|max_length[255]');
        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            $data['shopping'] = $this->shopping;
            // validation hasn't been passed
            $this->twig->render('login/updatepwd_view', $data);
        } else {
            $data = array(
                'msg' => 'Mot de passe modifiée avec succes',
                'shopping' => $this->shopping
            );
            $this->twig->render('Success_view', $data);
        }
    }

    function change_password($pwdd) {
        $pwd = sha1($pwdd);
        $login = getsessionhelper()['login'];
        $newpwd = sha1($this->input->post('newpwd'));
        //verifier l'existance de l'ancien mot de passe
        if ($this->inscription_model->verifpwd($pwd, $login) == true) {
            if ($this->inscription_model->updatpwd($newpwd, $login) == true) {//update succeded
                return true;
            } else {

                $this->form_validation->set_message('change_password', 'problème insertion dans la base');
                return false;
            }
        } else {

            $this->form_validation->set_message('change_password', 'Mot de passe courant inexistant');
            return false;
        }
    }

}

?>