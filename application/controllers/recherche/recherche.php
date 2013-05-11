<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Recherche extends CI_Controller {
    private $shopping = array();
    function __construct() {
        parent::__construct();
        
        $this->load->model('recherche/recherche_model');
        $this->load->model('produit/produit_model');
        $this->twig->addFunction('getsessionhelper');
        
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index() {
        
    }

    function search() {  
        $enscom = $this->produit_model->getcommercant();
        $result['comm'] = $enscom;
        $result['pathphoto'] = site_url() . 'uploads/';

        $check = $this->input->post('searchform');
        $mot = $this->input->post('mot');
        $myid_size = count($check);
        $chaine = '';
        $i = 1;
        if ((($myid_size == 1) AND ($check[0] == NULL)) || ($check[0] == 0)) {
            $result['produit'] = $this->recherche_model->getsearchmot($mot);
        } else {
            foreach ($check as $row) {
                if ($i == $myid_size) {
                    $chaine .= $row;
                } else {
                    $chaine .= $row . ', ';
                }
                $i++;
            }

            $result['produit'] = $this->recherche_model->getsearch($chaine, $mot);
        }
        $result['mot'] = $this->input->post('mot');
        $result['shopping'] = $this->shopping;
        $this->twig->render('recherche/recherche', $result);
    }
    
    function searchFromHome($id)
    {
        $enscom = $this->produit_model->getcommercant();
        $ensproduit = $this->produit_model->get_product_from_home($id)->result();
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';
        $data['shopping'] = $this->shopping;


        $this->twig->render('recherche/recherche', $data);
    }

   

}

