<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Annonce extends CI_Controller {

    // num of records per page
    private $limit = 10;
    private $shopping = array();
    function __construct() {
        parent::__construct();

        $this->load->model('annonce/annonce_model', '', TRUE);
        $this->twig->addFunction('getsessionhelper');
        
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index($offset = 0) {
        //recuperer l'id du commercant 

        $idcomm = getsessionhelper()['id'];
        // offset
        $uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

        // load data
        $annonces = $this->annonce_model->get_paged_list($idcomm,$this->limit, $offset);

        // generate pagination
        $this->load->library('pagination');
        $config['base_url'] = site_url('annonce/annonce/index/');
        $config['total_rows'] = $this->annonce_model->count_all();
        $config['per_page'] = $this->limit;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Titre', 'Contenu', 'active', 'Actions');
        $i = 0 + $offset;
        foreach ($annonces as $annonce) {

            $this->table->add_row($annonce->titre, $annonce->contenu, $annonce->active, anchor('annonce/annonce/view/' . $annonce->idannonce, '<i class="icon-eye-open"></i>', 'title="Voir Annonce" class=btn', array('class' => 'btn')) . ' ' .
                    anchor('annonce/annonce/update/' . $annonce->idannonce, '<i class="icon-edit"></i>', 'title="Modifier Annonce" class=btn', array('class' => 'btn')) . ' ' .
                    anchor('annonce/annonce/delete/' . $annonce->idannonce, '<i class="icon-trash"></i>', 'title="Supprimer Annonce" class=btn', array('class' => 'btn', 'onclick' => "return confirm('Vous voulez vraiment supprimer cette annonce?')")) . ' ' .
                    anchor('annonce/annonce/activer/' . $annonce->idannonce, '<i class="icon-ok"></i>', 'title="Activer Annonce" class=btn', array('class' => 'update')) . ' ' .
                    anchor('annonce/annonce/desactiver/' . $annonce->idannonce, '<i class="icon-remove"></i>', 'title="Désactiver Annonce" class=btn', array('class' => 'update'))
            );
        }
        $data['table'] = $this->table->generate();
        $data['shopping'] = $this->shopping;
        // load view
        $this->twig->render('annonce/annonceList_view', $data);
    }

    function add() {
        $data['shopping'] = $this->shopping;
        $this->twig->render('annonce/annonceajout_view', $data);
    }

    function addannonce() {
        //login comm  

        $id = getsessionhelper()['id'];
        //récupérer les données apartir du formulaire
        $this->form_validation->set_rules('titre', 'titre', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('contenu', 'contenu', 'required|trim|xss_clean|max_length[45]');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            echo 'erreur remplissage du form ';
            $this->twig->render('annonce/annonceajout_view');
        } else {
            /////// photo ///////////:
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '10000';
            $config['max_width'] = '10000';
            $config['max_height'] = '8000';
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                echo 'error';
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $name = $data['file_name'];
            }
            /////////// END photo///////////////////// 
 
            $form_data = array('titre' => $this->input->post('titre'),
                'contenu' => $this->input->post('contenu'),
                'photo' => $name,
                'active' => 1,
                'commercant_idcommercant' => $id// test
            );

            if ($this->annonce_model->addannonce($form_data) == true) {
                $data = array(
                    'msg' => 'Ajout annonce avec succes',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('annonce/successadd_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec ajout annonce',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('annonce/echecadd_view', $data);
//                   
            }
        }
    }

    function view($id) {
        $req = $this->annonce_model->get_by_id($id)->row();

        $data['annonce'] = $req;
        $data['finalpath'] = site_url() . 'uploads/' . $req->photo;
        $data['shopping'] = $this->shopping;
        $this->twig->render('annonce/annonce_view', $data);
    }

    function update($id) {
        $annonce = $this->annonce_model->getannonce($id)->result();
        //recupérer categorie et sous categorie
        foreach ($annonce as $row) {
            $photo = $row->photo;
        }
        //tester si une image existe ou pas
        if ($photo != NULL) {
            $path = site_url() . 'uploads/' . $photo;
        } else {
            $path = $photo;
        }

        //end of tester si une image existe ou pas

        foreach ($annonce as $row) {
            $data = array(
                'id' => $row->idannonce,
                'titre' => $row->titre,
                'contenu' => $row->contenu,
                'active' => $row->active,
                'photo' => $path,
                'namephoto' => $photo,
                'shopping' => $this->shopping
               
            );
        }
        
        $this->twig->render('annonce/annonceEdittemp_view', $data);
    }

    function updateannonce($active, $namephoto = NULL, $id = NULL) {
        //login comm 


        $idcomm = getsessionhelper()['id'];
        //récupérer les données apartir du formulaire
        $this->form_validation->set_rules('titre', 'titre', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('contenu', 'contenu', 'required|trim|xss_clean|max_length[45]');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            // echo 'erreur remplissage du form ';
            redirect('annonce/annonce/update/' . $id);
        } else {

            /////// photo ///////////:
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

/////////// END photo/////////////////////
            
            //tester si on a ajouter une nouvelle photo

            if (!(empty($name))) {
                $updatedphoto = $name;
            } else {
                $updatedphoto = $namephoto;
            }
            //end tester nvl photo				  
            $form_data = array('titre' => $this->input->post('titre'),
                'contenu' => $this->input->post('contenu'),
                'active' => $active,
                'photo' => $updatedphoto,
                'commercant_idcommercant' => $idcomm// test
            );
            if ($this->annonce_model->updateannonce($form_data, $idcomm, $id) == true) {
                $data = array(
                    'msg' => 'Annonce modifiée avec succes',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('annonce/successadd_view', $data);
            } else {
                $data = array(
                    'msg' => 'Echec modification annonce',
                    'shopping' => $this->shopping
                    );
                $this->twig->render('annonce/echecadd_view', $data);
            }
        }
    }

    function delete($id) {
        // delete annonce
        $this->annonce_model->delete($id);

        // redirect to annonce list page
        redirect('annonce/annonce/index/', 'refresh');
    }


    function activer($id) {
        //activer produit
        if ($this->annonce_model->activer($id) == true) {

            redirect('annonce/annonce/index/', 'refresh');
        } else {
            $data = array(
                'msg' => 'Erreur activation produit',
                'shopping' => $this->shopping
                );
            $this->twig->render('Echec_view', $data);
        }
    }

    function desactiver($id) {
        //desactiver produit
        if ($this->annonce_model->desactiver($id) == true) {

            redirect('annonce/annonce/index/', 'refresh');
        } else {
            $data = array(
                'msg' => 'Erreur activation produit',
                'shopping' => $this->shopping
                );
            $this->twig->render('Echec_view', $data);
        }
    }

}

?>