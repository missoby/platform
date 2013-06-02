<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Produit extends CI_Controller {

    private $shopping = array();
    // num of records per page
    private $limit = 6;

    function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->model('produit/produit_model', '', TRUE);
        $this->load->model('inscription/inscription_model', '', TRUE);

        $this->twig->addFunction('getsessionhelper');

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index($offset = 0) {
        //recuperer l'id du commercant 
        $idcomm = getsessionhelper()['id'];
        // load data
        $produits = $this->produit_model->get_paged_list($idcomm, $this->limit, $offset);
        // generate pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('produit/produit/index/');
        $config['total_rows'] = $this->produit_model->count_all();
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Libelle', 'stock', 'prix', 'remise', 'active', 'Actions');
        $i = 0 + $offset;
        foreach ($produits as $produit) {
            $this->table->add_row($produit->libelle, $produit->stock, $produit->prix, $produit->remise, $produit->active, anchor('produit/produit/view/' . $produit->idproduit, '<i class="icon-eye-open"></i>', 'title="Voir Produit" class= "btn"') . ' ' .
                    anchor('produit/produit/update/' . $produit->idproduit, '<i class="icon-edit"></i>', 'title="Modifier Produit" class= "btn"') . ' ' .
                    anchor('produit/produit/delete/' . $produit->idproduit, '<i class="icon-trash"></i>', 'title="Supprimer Produit" class= "btn"', array('onclick' => "return confirm('Vous voulez vraiment supprimer ce produit?')")) . ' ' .
                    anchor('produit/produit/activer/' . $produit->idproduit, '<i class="icon-ok"></i>', 'title="Activer Produit" class= "btn"') . ' ' .
                    anchor('produit/produit/desactiver/' . $produit->idproduit, '<i class="icon-remove"></i>', 'title="Désactiver Produit" class= "btn"')
            );
        }
        $data['table'] = $this->table->generate();
        $data['shopping'] = $this->shopping;
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        // load view
        $this->twig->render('produit/produitList_view', $data);
    }

    function add() {   ///////My solution /////////
        $enscat = $this->produit_model->getcategorie();
        $enssouscat = $this->produit_model->getsouscategorie();
        $data['categorie'] = $enscat;
        $data['souscat'] = $enssouscat;
        $data['shopping'] = $this->shopping;
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        
        $this->twig->render('produit/produitajout_view', $data);
    }

    function addproduct() {
        $name = '';
        //login comm 
        $id = getsessionhelper()['id'];
        //récupérer les données apartir du formulaire
        $this->form_validation->set_rules('libelle', 'libelle', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('stock', 'stock', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('description', 'description', 'required|trim|xss_clean');
        $this->form_validation->set_rules('prix', 'prix', 'required|trim|xss_clean|max_length[60]|integer');
        $this->form_validation->set_rules('remise', 'remise', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('cat', 'catégorie', 'required');
        $this->form_validation->set_rules('souscat', 'souscat', 'required');
        $this->form_validation->set_rules('soussouscat', 'soussouscat', 'required');
        if (empty($_FILES['userfile']['name']))
{
    $this->form_validation->set_rules('userfile', 'Image', 'required');
}
        //$this->form_validation->set_rules('userfile', 'userfile', 'required');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            $enscat = $this->produit_model->getcategorie();
            $enssouscat = $this->produit_model->getsouscategorie();
            $data['categorie'] = $enscat;
            $data['souscat'] = $enssouscat;
            $this->twig->render('produit/produitajout_view', $data);
        } else {

            //------photo -----
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '10000';
            $config['max_width'] = '10000';
            $config['max_height'] = '8000';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                //echo 'error';
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $name = $data['file_name'];
            }
            //----- END photo -----
            if ($name == NULL) {
                $name = ' ';
            }

            $form_data = array('libelle' => $this->input->post('libelle'),
                'stock' => $this->input->post('stock'),
                'description' => $this->input->post('description'),
                'prix' => $this->input->post('prix'),
                'remise' => $this->input->post('remise'),
                'photo' => $name,
                'note' => 0,
                'active' => 1, //par defaut le produit est active
                'dateajout' => date("y-m-d H:i:s"),
                'souscategorie_idsouscategorie' => $this->input->post('souscat'),
                'souscategorie_categorie_idcategorie' => $this->input->post('cat'),
                'soussouscategorie_id' => $this->input->post('soussouscat'),
                'commercant_idcommercant' => $id// test
            );
            //paiement
            $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
            
            if ($this->produit_model->addproduct($form_data) == true) {
                $data = array(
                    'msg' => 'Produit ajouté avec sucèss',
                    'shopping' => $this->shopping,
                    'commercant'=> $commercant
                );
                $this->twig->render('produit/successadd_view', $data);
            } else {

                $data = array(
                    'msg' => 'Echec d\'ajout de votre produit',
                    'shopping' => $this->shopping,
                    'commercant'=> $commercant
                );
                $this->twig->render('produit/echecadd_view', $data);
            }
        }
    }

    function view($id) {
        $req = $this->produit_model->get_by_id($id)->row();
        $idcat = $req->souscategorie_categorie_idcategorie;
        $idscat = $req->souscategorie_idsouscategorie;
        $idsscat = $req->soussouscategorie_id;

        $categorie = $this->produit_model->getcat($idcat)->row(); //titre du cat :: normalment c unutile
        $souscategorie = $this->produit_model->getsouscatvoir($idscat)->row();
        $soussouscategorie = $this->produit_model->getsoussouscatvoir($idsscat)->row();

        $data['produit'] = $req;
        $data['categorie'] = $categorie;
        $data['souscat'] = $souscategorie;
        $data['soussouscat'] = $soussouscategorie;
        $data['finalpath'] = site_url() . 'uploads/' . $req->photo;
        
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;

        $data['shopping'] = $this->shopping;
        $this->twig->render('produit/produit_view', $data);
    }

    function update($id) {
        $produit = $this->produit_model->getproduct($id)->result();
        //recuperer categorie et sous categorie
        foreach ($produit as $row) {

            $idsoussouscat = $row->soussouscategorie_id;
            $idscat = $row->souscategorie_idsouscategorie;
            $idcat = $row->souscategorie_categorie_idcategorie;

            //recupÃ©rartion de l'image enregistrÃ©
            $photo = $row->photo;
        }
        $enscat = $this->produit_model->getcategorie();

        $enssouscat = $this->produit_model->getsouscategorie();
        //tester si une image existe ou pas
        if ($photo != NULL) {
            $path = site_url() . 'uploads/' . $photo;
        } else {
            $path = $photo;
        }

        //end of tester si une image existe ou pas
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        
        foreach ($produit as $row) {
            $data = array(
                'id' => $row->idproduit,
                'libelle' => $row->libelle,
                'stock' => $row->stock,
                'description' => $row->description,
                'prix' => $row->prix,
                'remise' => $row->remise,
                'active' => $row->active,
                'dateajout' => $row->dateajout,
                'photo' => $path,
                'namephoto' => $photo,
                'idcat' => $idcat,
                'idsoussouscat' => $idsoussouscat,
                'idsouscat' => $idscat,
                'categorie' => $enscat,
                'souscat' => $enssouscat,
                'shopping' => $this->shopping,
                'commercant'=> $commercant
            );
        }
        $this->twig->render('produit/produitEdittemp_view', $data);
    }

    function updateproduct($active, $namephoto = NULL, $id = NULL, $idsouscat, $idsoussouscat, $idcat) {
        //login comm 
        $idcomm = getsessionhelper()['id'];

        //rÃ©cupÃ©rer les donnÃ©es apartir du formulaire
        $this->form_validation->set_rules('libelle', 'libelle', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('stock', 'stock', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('description', 'description', 'required|trim|xss_clean');
        $this->form_validation->set_rules('prix', 'prix', 'required|trim|xss_clean|max_length[60]|integer');
        $this->form_validation->set_rules('remise', 'remise', 'required|trim|xss_clean|max_length[45]|integer');
 
//        $this->form_validation->set_rules('cat', 'cat', 'required');
//        $this->form_validation->set_rules('souscat', 'souscat', 'required');
//        $this->form_validation->set_rules('soussouscat', 'soussouscat', 'required');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            // echo 'erreur remplissage du form ';
            redirect('produit/produit/update/' . $id);
        } else {
            //------ photo -------
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '1000';
            $config['max_width'] = '10000';
            $config['max_height'] = '8000';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                
            } else {

                $data = $this->upload->data();
                $name = $data['file_name'];
            }

            //------ END photo -------
            //tester si on a ajouter une nouvelle photo

            if (!(empty($name))) {
                $updatedphoto = $name;
            } else {
                $updatedphoto = $namephoto;
            }
            if ($this->input->post('cat') != NULL) {
                $idcat = $this->input->post('cat');
            }
            if ($this->input->post('souscat') != NULL) {
                $idsouscat = $this->input->post('souscat');
            }
            if ($this->input->post('souscat') != NULL) {
                $idsoussouscat = $this->input->post('soussouscat');
            }
            //end tester nvl photo
            $form_data = array('libelle' => $this->input->post('libelle'),
                'stock' => $this->input->post('stock'),
                'description' => $this->input->post('description'),
                'prix' => $this->input->post('prix'),
                'remise' => $this->input->post('remise'),
                'active' => $active,
                'dateajout' => date("y-m-d H:i:s"),
                'photo' => $updatedphoto,
                'souscategorie_idsouscategorie' => $idsouscat,
                'souscategorie_categorie_idcategorie' => $idcat,
                'soussouscategorie_id' => $idsoussouscat,
                'commercant_idcommercant' => $idcomm
            );
            //paiement
            $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();

            if ($this->produit_model->updateproduct($form_data, $idcomm, $id) == true) {
                $data = array(
                    'msg' => 'Produit modifié avec succes',
                    'shopping' => $this->shopping,
                    'commercant'=> $commercant
                );
                $this->twig->render('produit/successadd_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec modification produit',
                    'shopping' => $this->shopping,
                    'commercant'=> $commercant
                );
                $this->twig->render('Echec_view', $data);
            }
        }
    }

    function delete($id) {
        // delete product
        $this->produit_model->delete($id);

        // redirect to product list page
        redirect('produit/produit/index/', 'refresh');
    }

    function activer($id) {
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        //activer produit
        if ($this->produit_model->activer($id) == true) {
            redirect('produit/produit/index/', 'refresh');
        } else {
            // echo 'erreur activation';
            $data = array(
                'msg' => 'Erreur activation produit',
                'shopping' => $this->shopping,
                'commercant'=> $commercant
            );
            $this->twig->render('Echec_view', $data);
        }
    }

    function desactiver($id) {
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        //desactiver produit
        if ($this->produit_model->desactiver($id) == true) {

            redirect('produit/produit/index/', 'refresh');
        } else {
            $data = array(
                'msg' => 'Erreur activation produit',
                'shopping' => $this->shopping,
                'commercant'=> $commercant
            );
            $this->twig->render('Echec_view', $data);
        }
    }

    public function getsouscategorie() {
        $cat = $this->input->post('id');
        $data['sous'] = $this->produit_model->getsouscat($cat);
        echo json_encode($data);
        return;
    }

    public function getsoussouscategorie() {
        $cat = $this->input->post('id');
        $data['soussous'] = $this->produit_model->getsoussouscat($cat);
        echo json_encode($data);
        return;
    }

    public function slider() {
        
        $id = getsessionhelper()['id'];
        $slider = $this->produit_model->Get_Slider($id);
        $i = $slider->num_rows();
        $data['nbPhoto'] = $i;


        if ($i != 0) {
            $data['finalpath'] = site_url() . 'uploads/';
        }
        $data['slider'] = $slider;
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;
        $this->twig->render('produit/Slider_view', $data);
    }

    public function addSlider() {
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();
        $data['commercant'] = $commercant;

        $this->twig->render('produit/addSlider_view', $data);
    }

    public function Insertslider() {
        $id = getsessionhelper()['id'];

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
        if ($name == NULL) {
            $name = ' ';
        }

        $form_data = array('photo' => $name,
            'commercant_idcommercant' => $id
        );
        //paiement
        $commercant = $this->inscription_model->getchildcomm(getsessionhelper()['idpersonne'])->row();

        if ($this->produit_model->InsertSlider($form_data) == true) {
            redirect('produit/produit/slider');
        } else {

            $data = array(
                'msg' => 'Echec ajout slider',
                'shopping' => $this->shopping,
                'commercant'=> $commercant
            );
            $this->twig->render('produit/echecadd_view', $data);
        }
    }

    function deleteSlider($id) {
        // delete tof of slider
        $this->produit_model->deleteSlider($id);

        // redirect to product list page
        redirect('produit/produit/slider');
    }

}

?>