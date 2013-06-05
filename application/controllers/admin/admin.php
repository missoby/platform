<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends CI_Controller {

    private $limit = 10;

    function __construct() {
        parent::__construct();
        $this->load->model('admin/admin_model');
        $this->load->model('produit/produit_model');
        $this->load->library('twig');
        $this->load->helper('sessionnzo');
        $this->twig->addFunction('getsessionhelper');
         $this->load->model('notification/notif_model');
         $this->load->model('signaler/signaler_model');
         $this->load->model('statistique/statistique_model');

    }

    function index() {
        if (!getsessionhelper())
        {
            redirect('admin/admin/login');
        }
       $this->twig->render('admin/administration/administration_view');
    }

    function login() {
        $this->twig->render('admin/administration/login_view');
    }

    function verifylogin() {
        $login = $this->input->post('login');
        $pwd = sha1($this->input->post('pwd'));

        $res = $this->admin_model->login($login, $pwd);
        if ($res) {

                    $reqid = $this->admin_model->getidadmin($login)->row();
             if (!empty($reqid))
             {
                $login_in = array('id' => $reqid->idadmin, 'type'=> 'admin', 'notifaction' => $reqid->notifaction, 'notifmsg' => $reqid->notifmsg, 'login' => $login);
                $this->session->set_userdata('login_in', $login_in);
                $this->twig->render('admin/administration/administration_view');
            } 
        } else {
            $this->twig->render('admin/administration/login_view');
        }
    }
// liste des commercant
    function afficheComm() {
        //generer une table pour passer les donnees au vue 
        $res = $this->admin_model->getcommparent()->result();
        if ($res) {
            //générer la pagination 
            // offset
            $uri_segment = 4;
            $offset = $this->uri->segment($uri_segment);
            //generate pagination
            $this->load->library('pagination');
            $config['base_url'] = site_url('admin/admin/afficheComm/');
            $config['total_rows'] = $this->admin_model->count_all();
            $config['per_page'] = $this->limit;
            $config['uri_segment'] = $uri_segment;
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();
            $data['link_back'] = anchor('admin/admin/index/', 'Retour a la page d\'administration', array('class' => 'back'));
            $data['titre'] = 'Gestion des commercant';
            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");
            $this->table->set_heading('login', 'email','compte actif', 'societe', 'Commercant accepté', 'action');
            $i = 0;
            foreach ($res as $row) {
                $id = $row->idpersonne;
                $res2 = $this->admin_model->getcommchild($id)->result();
                foreach ($res2 as $row2) {
                    $this->table->add_row($row->login, $row->email, $row->active, $row2->societe, $row2->enable,
                            anchor('admin/admin/viewdetails/' . $row->idpersonne, '<i class="icon-eye-open"></i>', 'title="Voir Profil" class= "btn"').
                            anchor('admin/admin/delete/' . $row->idpersonne, '<i class="icon-trash"></i>', 'title="Supprimer Commerçant" class= "btn"', array('onclick' => "return confirm('Vous voulez vraiment supprimer ce produit?')")) . ' ' .
                            anchor('admin/admin/activer/' . $row->idpersonne, '<i class="icon-ok"></i>', 'title="Activer Commerçant" class= "btn"') . ' ' .
                            anchor('admin/admin/desactiver/' . $row->idpersonne, '<i class="icon-remove"></i>', 'title="Désactiver Commerçant" class= "btn"') . ' ' .
                            anchor('admin/admin/affichproduit/' . $row2->idcommercant, '<i class="icon-shopping-cart"></i>', 'title="Afficher produit" class= "btn"')
                            );
                }
            }
            $data['table'] = $this->table->generate();
            $this->twig->render('admin/administration/listeutilisateur_view', $data);
        }
    }

    function delete($id) {
        // delete commercant
        $this->admin_model->delete($id);

        // redirect to commercant list page
        redirect('admin/admin/afficheComm/', 'refresh');
    }
    
     function deleteclient($id) {
        // delete commercant
        $this->admin_model->delete($id);

        // redirect to commercant list page
        redirect('admin/admin/afficheClient/', 'refresh');
    }

    function activer($id) {
        //activer commercant
        if ($this->admin_model->activer($id) == true) {

            redirect('admin/admin/afficheComm/', 'refresh');
        } else {
            $data = array('msg' => 'Erreur activation commercant');
            $this->twig->render('Echec_view', $data);
            
        }
    }

    function desactiver($id) {
        //desactiver commercant
        if ($this->admin_model->desactiver($id) == true) {

            redirect('admin/admin/afficheComm/', 'refresh');
        } else {
            $data = array('msg' => 'Erreur desactivation commercant');
            $this->twig->render('Echec_view', $data);
            
        }
    }

    function affichproduit($id) {
        // offset
        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        // load data
        $products = $this->produit_model->get_paged_list_active($this->limit, $offset, $id)->result();



        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Libelle', 'stock', 'prix', 'remise', 'Actions');
        $i = 0 + $offset;
        foreach ($products as $product) {
            $this->table->add_row( $product->libelle, $product->stock, $product->prix, $product->remise, 
                    anchor('admin/admin/viewproduct/' . $product->idproduit, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"') . ' ' .
                    anchor('admin/admin/deleteproduct/' . $product->idproduit, '<i class="icon-trash"></i>', 'title="Supprimer Produit" class= "btn"', array('onclick' => "return confirm('Vous voulez vraiment supprimer ce produit?')"))
            );
        }
        $data['table'] = $this->table->generate();

        // load view
        $this->twig->render('admin/administration/produitList_view', $data);
    }

    function deleteproduct($id) {
        // delete product
        $this->produit_model->delete($id);

        // redirect to person list page
        redirect('admin/admin/afficheComm/', 'refresh');
    }

   
    function viewproduct($id, $idnotifadmin = NULL) {
        if($idnotifadmin != NULL)
        {
            $update = $this->notif_model->updateVueSign($idnotifadmin);
           
        }
        $req = $this->produit_model->get_by_id($id)->row();

        $idcat = $req->souscategorie_categorie_idcategorie;

        $idscat = $req->souscategorie_idsouscategorie;
        $categorie = $this->produit_model->getcat($idcat)->row(); //titre du cat :: normalment c unutile

        $souscategorie = $this->produit_model->getSouscatadmin($idscat)->row(); //
        $data['produit'] = $req;
        $data['categorie'] = $categorie;
        $data['souscat'] = $souscategorie;
         $data['link_back'] = anchor('admin/admin/afficheComm/','Retour a la page d\'accueil',array('class'=>'back'));
        $data['finalpath'] = site_url() . 'uploads/' . $req->photo;
        $data['idproduit'] = $id;
        $this->twig->render('admin/administration/produit_view', $data);
    }
    
   function afficheClient($logclient = NULL, $idnotif = NULL)
    {
         if($idnotif != NULL)
        {
            $update = $this->notif_model->updateVue($idnotif);
        }
        
      $res = $this->admin_model->getclientparent()->result();
        if ($res) {
            //générer la pagination 
            // offset
            $uri_segment = 4;
            $offset = $this->uri->segment($uri_segment);
            //generate pagination
            $this->load->library('pagination');
            $config['base_url'] = site_url('admin/admin/afficheClient/');
            $config['total_rows'] = $this->admin_model->count_all();
            $config['per_page'] = $this->limit;
            $config['uri_segment'] = $uri_segment;
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();
            $data['link_back'] = anchor('admin/admin/index/', 'Retour a la page d\'administration', array('class' => 'back'));
            $data['titre'] = 'Gestion des clients';
            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");
            $this->table->set_heading('nom', 'prenom', 'login', 'pays', 'ville', 'tel', 'email', 'active', 'action');
            $i = 0;
            foreach ($res as $row) {
                $id = $row->idpersonne;
                $res2 = $this->admin_model->getclientchild($id)->result();
                foreach ($res2 as $row2) {
                    $this->table->add_row($row->nom, $row->prenom, $row->login, $row->pays, $row->ville, $row->tel, $row->email, $row->active,
                            anchor('admin/admin/deleteclient/' . $row->idpersonne, '<i class="icon-trash"></i>', 'title="Supprimer client" class= "btn"', array('onclick' => "return confirm('Vous voulez vraiment supprimer ce produit?')")) 
                              );
                }
            }
            $data['table'] = $this->table->generate();
            if ($logclient != NULL)
            {
            $data['login'] = $logclient;
            }
            $this->twig->render('admin/administration/listeutilisateur_view', $data);
        }
    }
    
     
    function viewdetails($id, $idnotifadmin = NULL) 
    {
         
        if($idnotifadmin != NULL)
        {
            $update = $this->notif_model->updateVue($idnotifadmin);
           
        }
       
        $personne = $this->admin_model->getparent($id)->row();
        if ($personne) {
         
            
                $commercant = $this->admin_model->getcommchild($personne->idpersonne)->row();
                $data = array('personne' => $personne, 'commercant' => $commercant);
                $this->twig->render('admin/administration/voirprofile_view_comm', $data);
        }
    }
      function logout(){
            $this->session->unset_userdata('login_in');
            $this->session->sess_destroy();
            redirect('admin/admin/login', 'refresh');
        }
        
      function slider()
      {
       $id = getsessionhelper()['id'];
        $slider = $this->admin_model->Get_Slider($id);
        $i = $slider->num_rows();
        $data['nbPhoto'] =  $i;
       
      
        if($i != 0)
        {
        $data['finalpath'] = site_url() . 'uploads/';
        }
         $data['slider'] = $slider;
         $this->twig->render('admin/slider/Slider_view', $data);
      }
      
       public function addSlider()
    {
        
              $this->twig->render('admin/slider/addSlider_view');
    }
    
     public function Insertslider()
    {        $id = getsessionhelper()['id'];

            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '10000';
            $config['max_width'] = '10000';
            $config['max_height'] = '8000';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $name = $data['file_name'];
            }
            //----- END photo -----
            if ($name == NULL)
            {$name = ' ';}

            $form_data = array('photo' => $name,
                'admin_idadmin' => $id
            );

            if ($this->admin_model->InsertSlider($form_data) == true) {
                redirect('admin/admin/slider');
            } else {

                $data = array(
                    'msg' => 'Echec ajout slider',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('produit/echecadd_view', $data);
            }
        
    }
    
     function deleteSlider($id)
    {
           // delete tof of slider
        $this->admin_model->deleteSlider($id);

        // redirect to product list page
                redirect('admin/admin/slider');
    }
    
     function Getnotif()
    {
        $req = $this->notif_model->GetNotif();
//        foreach ($req->result_array() as $row)
//        {
//            echo $row['message'];
//        }

         foreach ($req as $value) {
             
          
          if($value->idclt == NULL)
            {
                $value->type = 'commercant';
                //put the idpersonne of the comm  in idclt
               
                $value->idclt = $this->notif_model->getProprietaireCom($value->idcomm);
            }
            else //client
            {
                $value->type = 'client';
                //put the idpersonne of the comm  in idclt
                $value->idcomm = $this->notif_model->getProprietaireClt($value->idclt);           
                } 
           
                
            }
        $data['req']= $req;
        //get signal 
        $sign = $this->signaler_model->GetSign();
          
        $data['sign']= $sign;
         $this->twig->render('admin/notif/notifadmin_view',$data);

           
       
    }
    // va etre enlevé et fusionneé avec getnotif
      function GetSign()
    {
        $req = $this->signaler_model->GetSign();

       
        $data['req']= $req;
         $this->twig->render('admin/notif/signaladmin_view',$data);

           
       
    }
    
     function getAvis($idavis, $idnotif = NULL)
    {
          if($idnotif != NULL)
        {
            $update = $this->notif_model->updateVueSign($idnotif);
           
        }
        
        $req = $this->signaler_model->GetAvis($idavis);

       
        $data['req']= $req;
         $this->twig->render('admin/notif/avisSign_view',$data);
        
    }
    
          function deleteAvis($id) {
        // delete avis
        $this->signaler_model->delete($id);

        
        redirect('admin/admin/GetSign/', 'refresh');
    }
    //get comment forum
    function getMsgForum($idmsgf, $idnotif = NULL)
    {
          if($idnotif != NULL)
        {
            $update = $this->notif_model->updateVueSign($idnotif);
           
        }
        
        $req = $this->signaler_model->getMsgForum($idmsgf);

       
        $data['req']= $req;
         $this->twig->render('admin/notif/forumSign_view',$data);
        
    }
     function deleteMsgForum($id) {
        // delete avis
        $this->signaler_model->deletemsgf($id);

        
        redirect('admin/admin/GetSign/', 'refresh');
    }
    function statistique()
    {
        if (!$this->session->userdata('login_in'))
            redirect('/');
        else 
        {
            $data['nbclientinscri'] = $this->statistique_model->GetNbClientInscri();
            $data['nbcomminscri'] = $this->statistique_model->GetNbCommInscri();
            $data['nbclientconfirm'] = $this->statistique_model->GetNbClientConf();
            $data['nbcommconfirm'] = $this->statistique_model->GetNbCommConf();
            $data['nbcommenable'] = $this->statistique_model->GetNbCommEnabl();
        
            $this->twig->render('admin/statistique/statistique_view', $data);
        }    
    }
    
        function deleteNotif($id) {
        // delete notif
        $this->notif_model->delete($id);

        // redirect to notif list 
            redirect('admin/admin/getnotif/', 'refresh');
    }
    //update pwd admin 
      function UpdatePwd() {
       
        // afficher la vue pour saisir le nouveau pwd
        $this->twig->render('admin/administration/updatepwd_view');
    }
    
    function SaveUpdatepwd() {//mettre a jour le pwd
        $this->form_validation->set_rules('currentpwd', 'Mot de passe courant', 'trim|max_length[255]|callback_change_password');
        $this->form_validation->set_rules('newpwd', 'Nouveau Mot de passe', 'trim|max_length[255]|matches[confpwd]');
        $this->form_validation->set_rules('confpwd', 'Conformatin du nouveau Mot de passe', 'trim|max_length[255]');
        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) {
            
            // validation hasn't been passed
        $this->twig->render('admin/administration/updatepwd_view');
        } else {
           redirect('admin/admin');
        }
    }
    
     function change_password($pwdd) {
        $pwd = sha1($pwdd);
        $login = getsessionhelper()['login'];
        $newpwd = sha1($this->input->post('newpwd'));
        //verifier l'existance de l'ancien mot de passe
        if ($this->admin_model->verifpwd($pwd, $login) == true) {
            if ($this->admin_model->updatpwd($newpwd, $login) == true) {//update succeded
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
