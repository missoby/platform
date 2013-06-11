<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home_page extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('produit/produit_model', '', TRUE);
        $this->load->model('annonce/annonce_model', '', TRUE);
        $this->load->model('admin/admin_model');
        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index() {
        $enscom = $this->produit_model->getcommercant();
        $ensproduit = $this->produit_model->get_product_by_note();
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';

        // les annonces
        $ensannonce = $this->annonce_model->get_all_annonce();
        $data['annonce'] = $ensannonce;
        $data['shopping'] = $this->shopping;
        // $this->twig->render('home_page' , $data);
        // display product by date
        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
       // dispaly  product in sale
        $ensproduitsolde = $this->produit_model->get_remise_product();
        $data['produitsolde'] = $ensproduitsolde;
        
       // print_r($ensproduitsolde);return;
        
        $slider = $this->admin_model->GetSlider();
        $i = $slider->num_rows();
        $data['nbPhoto'] = $i;


        if ($i != 0) {
            $data['finalpath'] = site_url() . 'uploads/';
        }
        $data['slider'] = $slider;

        $this->twig->render('home_page', $data);
    }
     function mobile() {
          $slider = $this->admin_model->GetSlider();
        $i = $slider->num_rows();
        $data['nbPhoto'] = $i;


        if ($i != 0) {
            $data['finalpath'] = site_url() . 'uploads/';
        }
        $data['slider'] = $slider;
                 $data['shopping'] = $this->shopping;

         $this->twig->render('mobile_view', $data);
        
        
     }
function download($sys)
        {
            $this->load->helper('download');
            if($sys === 'android')
            {
                $data = file_get_contents("downloads/tunisieshopping.apk"); // Read the file's contents
                $name = 'tunisieshopping.apk';
                force_download($name, $data);
            }
            else
             redirect('/');
        }
}
