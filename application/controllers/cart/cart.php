<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->load->model('cart/cart_model');
        $this->load->model('produit/produit_model', '', TRUE);
        $this->twig->addFunction('getsessionhelper');

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] =$this->cart->total_items();
    }

    function index() {
         
//            //test sécurité
//       if (!getsessionhelper())
//        {
//            redirect ('inscription/login');
//        }
//        
//        //
        $enscom = $this->produit_model->getcommercant();
        $ensproduit = $this->produit_model->get_all_product();
        $data['shopping'] = $this->shopping;
        $data['produit'] = $ensproduit;
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/'; 
       
        $this->twig->render('cart/detailCart', $data);
    }

    function add_cart_item() { // ds notre view on ajoute just l'appl a cette fonction <=> work done on view product 
         
      
        
        if ($this->cart_model->validate_add_cart_item() == TRUE) {

            // Check if user has javascript enabled
            if ($this->input->post('ajax') != '1') {
                redirect('cart/cart'); // If javascript is not enabled, reload the page with new data
            } else {
                echo 'true'; // If javascript is enabled, return true, so the cart gets updated
            }
        }
    }

    function update_cart() {
       
       
        $this->cart_model->validate_update_cart();
        redirect('cart/cart');
    }

    function show_cart() {
         
       
        $this->load->view('cart/detailCart');
    }

    function empty_cart() {
       
        $this->cart->destroy();
        redirect('cart/cart');
    }
    
    function add_cart_item_mobile() { // ds notre view on ajoute just l'appl a cette fonction <=> work done on view product 
       $idp = $this->input->post('product_id');
       $this->produit_model->deletecmdmobile($idp);
        if ($this->cart_model->validate_add_cart_item() == TRUE) {

            // Check if user has javascript enabled
            if ($this->input->post('ajax') != '1') {
                redirect('cart/cart'); // If javascript is not enabled, reload the page with new data
            } else {
                echo 'true'; // If javascript is enabled, return true, so the cart gets updated
            }
        }
    }

}
