<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notif extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('notification/notif_model');
        $this->load->model('produit/produit_model', '', TRUE);
          $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }


     function notifajax()
    {  
       $var['action'] = $this->notif_model->getnotifactionfromuser()->notifaction;
       $var['msg'] = $this->notif_model->getnotifmsgfromuser()->notifmsg;
 
        echo json_encode($var);
        
    }
    // get the notif for the commercant (table notifcomm)
        function Getnotif()
    {
           
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
       $data['shopping'] = $this->shopping;
        $req = $this->notif_model->GetNotifComm();

        $data['req']= $req;
         $this->twig->render('commercant/notif/notifcomm_view',$data);

           
       
    }

    
     // get the notif for the client (table notifclient)
        function GetnotifClient()
    {
           
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
            $data['shopping'] = $this->shopping;
        $req = $this->notif_model->GetNotifClient();
        $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';

        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        $data['req']= $req;
        $this->twig->render('client/notif/notifclient_view',$data);
   
    }
    

    
   function deleteclient($id) {
        if(empty($id))
            redirect ('inscription/login');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        // delete client
        $this->notif_model->deleteclient($id);

        // redirect to product list page
        redirect('notification/notif/GetnotifClient', 'refresh');
    } 
    
    function deletecomm($id) {
         if(empty($id))
            redirect ('inscription/login');
             //test sécurité de cnx
       if (!getsessionhelper())
        {
            redirect ('inscription/login');
        }
        
        //
        // delete product
        $this->notif_model->deletecomm($id);

        // redirect to product list page
        redirect('notification/notif/Getnotif', 'refresh');
    } 
    
    
    
   
    

}

?>