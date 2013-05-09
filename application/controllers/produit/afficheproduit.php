<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Afficheproduit extends CI_Controller {

    private $fb;
    function __construct() {
        parent::__construct();
        //Facebook Connect
        require_once 'assets/facebook_sdk_src/facebook.php';
        $param = array();
        $param['appId'] = '111176932418804';
        $param['secret'] = 'cff04e31d0fe52eb2777188dff2e71b8';
        $param['fileUpload'] = true; // pour envoyer des photos
        $param['cookie'] = false;
        $this->fb = new Facebook($param);
        
        $this->load->model('produit/produit_model', '', TRUE);
        $this->load->model('annonce/annonce_model', '', TRUE);
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('avis/avis_model');
        $this->load->model('forum/forum_model');
        
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index() {
        $enscom = $this->produit_model->getcommercant();
        $ensproduit = $this->produit_model->get_all_product();
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';

        // les annonces
        $ensannonce = $this->annonce_model->get_all_annonce();
        $data['annonce'] = $ensannonce;
        $data['shopping'] = $this->shopping;
        $this->twig->render('produit/afficheproduit/produithome_view', $data);
    }

    function details($id) {
        $data['shopping'] = $this->shopping;

        $req = $this->produit_model->get_by_id($id)->row();
        $data['produit'] = $req;

        $data['finalpath'] = site_url() . 'uploads/' . $req->photo;
        //**********
        $data['namep'] = $req->libelle;
        $avis = $this->avis_model->getavis($id);
         foreach ($avis as $value) {
          
           $value->idclient = $value->client_idclient;
             $value->client_idclient = $this->forum_model->getProprietaireDelAvis($value->client_idclient);
            // $value->client_idclient = $this->forum_model->getProprietaireDelAvis($value->client_idclient);
           
                 }
    
        $data['avis'] = $avis;
       

        $data['idp'] = $id;
        $data['idclt'] = getsessionhelper()['id'];

     

        $this->twig->render('produit/afficheproduit/produit_view', $data);
    }

    function view_prod_comm($id) {
        
      
        if(($this->input->post('tri') == 'aucun') OR $this->input->post('tri') == NULL)
        {
        
         $ensproduit = $this->produit_model->get_product_comm($id);
        }
        elseif($this->input->post('tri') == 'prix')
        {
          
            $ensproduit = $this->produit_model->get_product_comm_Tri_Prix($id);
        }
        elseif($this->input->post('tri') == 'libelle')
        {
          
   $ensproduit = $this->produit_model->get_product_comm_Tri_Libelle($id);
            
        }
      
        $data['shopping'] = $this->shopping;
        $enscom = $this->produit_model->getcommercant();
       
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['link_back'] = anchor('produit/afficheproduit/index/', 'Retour a la page d\'accueil', array('class' => 'back'));
        $data['pathphoto'] = site_url() . 'uploads/';
        // les annonces
        $ensannonce = $this->annonce_model->get_annonce_comm($id);
        $data['annonce'] = $ensannonce;
        
         // afficher les categories selon le commercant ds l'interface comm
        $data['categorie'] = $this->produit_model->Get_Cat_Comm($id);
    
        //afficher les sous categories selon le commercant ds l'interface comm
        $data['souscategorie'] = $this->produit_model->Get_Sous_Cat_Comm($id);
        $data['idcom'] = $id;
        
        $slider = $this->produit_model->Get_Slider($id);
        $i = $slider->num_rows();
        $data['nbPhoto'] =  $i;
       
      
        if($i != 0)
        {
        $data['finalpath'] = site_url() . 'uploads/';
        }
         $data['slider'] = $slider;

        $this->twig->render('produit/afficheproduit/produitcomm_view', $data);
    }
    
     function getProductByCat($id, $idcat) {

         $ensproduit = $this->produit_model->get_product_comm_by_cat($id, $idcat);
 
        $data['shopping'] = $this->shopping;
        $enscom = $this->produit_model->getcommercant();
       
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['link_back'] = anchor('produit/afficheproduit/index/', 'Retour a la page d\'accueil', array('class' => 'back'));
        $data['pathphoto'] = site_url() . 'uploads/';
        // les annonces
        $ensannonce = $this->annonce_model->get_annonce_comm($id);
        $data['annonce'] = $ensannonce;
        
         // afficher les categories selon le commercant ds l'interface comm
        $data['categorie'] = $this->produit_model->Get_Cat_Comm($id);
    
        //afficher les sous categories selon le commercant ds l'interface comm
        $data['souscategorie'] = $this->produit_model->Get_Sous_Cat_Comm($id);
        $data['idcom'] = $id;

        $this->twig->render('produit/afficheproduit/produitCommByCat_view', $data);
    }
    
       function getProductBySousCat($id, $idsouscat) {

         $ensproduit = $this->produit_model->get_product_comm_by_Sous_cat($id, $idsouscat);
 
        $data['shopping'] = $this->shopping;
        $enscom = $this->produit_model->getcommercant();
       
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['link_back'] = anchor('produit/afficheproduit/index/', 'Retour a la page d\'accueil', array('class' => 'back'));
        $data['pathphoto'] = site_url() . 'uploads/';
        // les annonces
        $ensannonce = $this->annonce_model->get_annonce_comm($id);
        $data['annonce'] = $ensannonce;
        
         // afficher les categories selon le commercant ds l'interface comm
        $data['categorie'] = $this->produit_model->Get_Cat_Comm($id);
    
        //afficher les sous categories selon le commercant ds l'interface comm
        $data['souscategorie'] = $this->produit_model->Get_Sous_Cat_Comm($id);
        $data['idcom'] = $id;

        $this->twig->render('produit/afficheproduit/produitCommByCat_view', $data);
    }

    function detailsannonce($id) {
        $req = $this->annonce_model->get_by_id($id)->row();
        $data['annonce'] = $req;
        $data['link_back'] = anchor('produit/afficheproduit/index/', 'Retour a la page d\'accueil', array('class' => 'back'));
        $data['finalpath'] = site_url() . 'uploads/' . $req->photo;

        $data['shopping'] = $this->shopping;
        $this->twig->render('produit/afficheproduit/annonce_view', $data);
    }
    
    //ajouter un avis
    
    function ajouterAvis($idp, $idclt)
    {
        $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim|xss_clean');
        
        if ($this->form_validation->run() == FALSE) {
            redirect('/produit/afficheproduit/details/'.$idp);
        }
        else {
        
            $this->avis_model->setAvis($idp, $idclt);
             redirect('/produit/afficheproduit/details/'.$idp);
        }
    }
    
public function publierfb($id)
    {
        if(empty($id) OR $id == 0)
        {
            echo 'Erreur ID <br /> Function: produit/afficherproduit/publierfb/';
            return;
        }

        //Initialisation
        $path  = '/uploads/';
        $res   = $this->produit_model->getProductById($id);
        $photo = $path . $res->photo;
        $msg   = $res->description;

        $uid = $this->fb->getUser();

        if (empty($uid)) //User non connecté sur facebook
        {
            $param = array();
            $param['redirect_uri'] = base_url().'produit/afficherproduit/publierfb/' . $id;
            redirect($this->fb->getLoginUrl($param));
        }
        else //User connecté sur Facebook
        {
            try
            {
                //Partage d'un lien sur facebook avec la photo du produit et un statut
                //Ne marche pas en local, marche si le site est hebergé
                
                $params = array('message' => $res->libelle,
                                'name' => 'www.test.com',
                                'caption' => 'Plate forme E-commerce',
                                'link' => 'www.test.com/produit/afficheproduit/publierfb/' . $id,
                                'description' => $msg,
                                'picture' => 'www.test.com/uploads/photo1.jpg',
                                );
                $this->fb->api('/me/feed', 'POST', $params);
                
                redirect('produit/afficheproduit/details/'.$id);
            }
            catch(FacebookApiException $e)
            {
                echo 'Exception:';
                echo '<br />';
                echo $e->getType();
                echo '<br />';
                echo $e->getMessage();
            }   
        }
    }
    
       
    function supprimerAvis($idavis, $idp)
    {
        // delete comment
        $this->avis_model->deleteAvis($idavis);

        // redirect to product list page
                redirect('/produit/afficheproduit/details/'.$idp);
    }
    
    
    function getProdRemise() {
        $enscom = $this->produit_model->getcommercant();
        $ensproduit = $this->produit_model->get_remise_product();
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';
 
        
        $data['shopping'] = $this->shopping;
        $this->twig->render('produit/afficheproduit/produitRemise_view', $data);
    }

}

?>