<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notif extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('notification/notif_model');
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
        $req = $this->notif_model->GetNotifComm();

        $data['req']= $req;
         $this->twig->render('commercant/notif/notifcomm_view',$data);

           
       
    }
//    // set the field of the notif as being seen
//    function SetVue($idnotif)
//    {
//        $idcom = getsessionhelper()['id'];
//        $this->notif_model->SetVue($idcom, $idnotif);
//        redirect('notification/notif/Getnotif');
//    } // deplacé ver le formulaire forum
    
     // get the notif for the client (table notifclient)
        function GetnotifClient()
    {
        $req = $this->notif_model->GetNotifClient();

        $data['req']= $req;
         $this->twig->render('client/notif/notifclient_view',$data);

           
       
    }
    
//     // set the field of the notif as being seen
//    function SetVueClient($idnotif)
//    {
//        $idclt = getsessionhelper()['id'];
//        $this->notif_model->setVueClient($idclt, $idnotif);
//        redirect('notification/notif/GetnotifClient');
//    } // deplacé ver le formulaire forum
    
   
    

}

?>