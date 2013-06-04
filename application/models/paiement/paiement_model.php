<?php

Class Paiement_model extends CI_Model {
  
     function notifadmin($idadmin, $iduser, $msg)
     {
         $data = array(
             'message' => $msg,
             'admin_idadmin' =>$idadmin,
             'idcomm' => $iduser,
             'vue' =>0,
             'dateajout'=> date("y-m-d H:i:s"),
             'notifpaiement'=>1
         );
         $this->db->insert('notifadmin', $data);
         // update notifaction admin
        $this->db->where('idadmin', $idadmin);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');
        //update champ dempaiement de la table comm
        $this->db->where('idcommercant', $iduser);
        $this->db->set('dempaiement', 1);
        $this->db->update('commercant');
        
     }
     
       public function GetNotif() {
         
              return $this->db->select('*')
                            ->where('notifpaiement', 1)
                            ->get('notifadmin')
                            ->result();
        }
        
        function getidcom($id)
        {
               return $this->db->where('personne_idpersonne',$id)
                            ->get('commercant')
                            ->row()->idcommercant;
        }
        
     function activer($id, $msg, $idcom) {
        $this->db->set('paiement', 1);
        $this->db->where('personne_idpersonne', $id);
        $this->db->update('commercant');
        ///
         $data = array(
             'message' => $msg,
             'commercant_idcommercant' =>$idcom,
             'vue' =>0,
             'notifpaiement' => 1,
             'objnotif'=>1,
             'dateajout'=> date("y-m-d H:i:s")
             
         );
         $this->db->insert('notifcommercant', $data);
         // update notifaction comm
        $this->db->where('idcommercant', $idcom);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('commercant');
        return true;
    }
    
     function delete($id){
		$this->db->where('idnotifAdmin', $id);
		$this->db->delete('notifadmin');
	} 
        
      public function getNomCom($idp) {
          
            return $this->db->where('idpersonne', $idp)
                            ->get('personne')
                            ->row()->nom;
            
        }  
        
          public function getPrenomCom($idp) {
          
            return $this->db->where('idpersonne', $idp)
                            ->get('personne')
                            ->row()->prenom;
            
        } 
        
          public function getComm() {
         
              return $this->db->select('*')
                            ->where('paiement', 1)
                            ->get('commercant')
                            ->result();
        }
        
             function desactiver($id, $msg) {
        $this->db->set('paiement', 0);
        $this->db->where('idcommercant', $id);
        $this->db->update('commercant');
        ///
         $data = array(
             'message' => $msg,
             'commercant_idcommercant' =>$id,
             'vue' =>0,
             'notifpaiement' => 1,
             'objnotif' => 0,
             'dateajout'=> date("y-m-d H:i:s")
             
         );
         $this->db->insert('notifcommercant', $data);
         // update notifaction comm
        $this->db->where('idcommercant', $id);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('commercant');
        return true;
    }
    
       public function GetNotifComm() {
         
              return $this->db->select('*')
                            ->where('notifpaiement', 1)
                            ->where('commercant_idcommercant', getsessionhelper()['id'])
                            ->get('notifcommercant')
                            ->result();
        }
        
          //update champ vue pour l'admin : table notifadmin
        function updateVueComm($idnotif)
        {
            $idcom = getsessionhelper()['id'];
            // update notifaction comm
        $this->db->where('idnotifCommercant', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('notifcommercant');
        
        //
          $vue =  $this->db->select('*')
                             ->where('idcommercant', $idcom)
                            ->get('commercant')
                            ->row()->notifaction;
           
          if($vue > 0)
          {
               // update notifaction comm
        $this->db->where('idcommercant', $idcom);
        $this->db->set('notifaction', "notifaction-1", FALSE);
        $this->db->update('commercant');
          }
        }
        
           function saveaffil($idadmin) {
       
               $data = array(
           'lienControl' => $this->input->post('control'),
            'lienSucces' => $this->input->post('succes'),
            'lienEchec' => $this->input->post('echec'),
            'numAffilier' => $this->input->post('affil'),
            'dateActv' => date("y-m-d H:i:s"),
            'idadmin' => $idadmin
           
        );
        $this->db->insert('paiementconfiguration', $data);
       
    }
    
     function saveCmd($prixtot)
     {
         $data = array(
             'datecmd'=> date("y-m-d H:i:s"),
             'refcmd'=>rand(),
             'prixtotal' => $prixtot,
             'paye'=> 0,
             'client_idclient'=> getsessionhelper()['id']
         );
         $this->db->insert('commande', $data);
         return $this->db->insert_id();
        
     }
     
      function saveProdCmd($idp,$qty, $cmd)
     {
         $data = array(
             'produit_idproduit'=>$idp,
             'commande_idcommande' => $cmd,
             'qtecom'=> $qty,
         );
         $this->db->insert('quantite', $data);
        
        
     }
     
     function getRef($ref)
     {
          return  $this->db->select('*')
                             ->where('refcmd', $ref)
                            ->get('commande')
                            ->result();
         
     }
      function getMontant($ref)
      {
     return $this->db->where('refcmd',$ref)
                     ->get('commande');
                     //->row();
                     //->row()->prixtotal;

     
      }
      //récupérer les données concernant la cmd
       function getInfoCmd($ref)
     {
          return  $this->db->select('*')
                             ->where('refcmd', $ref)
                            ->get('commande')
                            ->result();
         
     }
    //send notif to client and admin to tell about paiement
       function NotifSuccessPay($idclient, $msg)
     {
           //notif to admin
         $data = array(
             'message' => $msg,
             'admin_idadmin' =>1,
             'idclt' => $idclient,
             'vue' =>0,
             'notifpaiement' => 1,
             'dateajout' => date("y-m-d H:i:s")
         );
         $this->db->insert('notifadmin', $data);
         // update notifaction admin
        $this->db->where('idadmin', 1);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');
        
        //notif to client
         $data = array(
             'message' => $msg,
             'client_idclient' => $idclient,
             'vue' =>0,
             'notifpaiement' => 1,
             'dateajout' => date("y-m-d H:i:s")
         );
         $this->db->insert('notifclient', $data);
         // update notifaction admin
        $this->db->where('idclient', $idclient);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('client');
        
     }
     
      function getListCommPay($idcmd, $ref, $date, $prix) {
        // get cmd from table qty
        $sql = "select * 
                  from quantite q 
                  where q.commande_idcommande = '$idcmd'";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get idcomm email product from tables produit, commercant and personne
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            // récupérer libelle et idcomm du comm de produit
            $produit = $this->db->where('idproduit', $res['produit_idproduit'])
                    ->get('produit')
                    ->row();
            //get idpersonne from table comm to get email from table personne
            $comm = $this->db->where('idcommercant', $produit->commercant_idcommercant)
                    ->get('commercant')
                    ->row();
            //get email of the comm
            $pers = $this->db->where('idpersonne', $comm->personne_idpersonne)
                    ->get('personne')
                    ->row();
            
            $tableau[$i] = array('idprod' => $res['produit_idproduit'],'prixprod'=> $produit->prix ,'refcmd' => $ref,'prix'=> $prix,'date'=>$date,'qty'=>$res['qtecom']
              ,'libelle'=>$produit->libelle,'idcom'=> $produit->commercant_idcommercant,'idpersonne' => $comm->personne_idpersonne, 
                'email' => $pers->email);
            $i++;
        }
        return $tableau;
    }
    
      function NotifSuccessPayComm($idcom, $msg)
     {
           //notif to comm
         $data = array(
             'message' => $msg,
             'commercant_idcommercant' =>$idcom,
             'vue' =>0,
             'notifpaiement' => 1,
             'dateajout' => date("y-m-d H:i:s")
         );
         $this->db->insert('notifcommercant', $data);
         // update notifaction comm
        $this->db->where('idcommercant', $idcom);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('commercant');
        
        
     }
     
     function getRefCmd($cmd)
     {
          return  $this->db->select('*')
                             ->where('idcommande', $cmd)
                            ->get('commande')
                            ->row()->refcmd;
         
     }
      function getAffilCmd()
     {
          return  $this->db->select('*')
                             ->where('idadmin', 1)
                            ->get('paiementconfiguration')
                            ->row()->numAffilier;
         
     }
     
      function getAffilConf()
     {
          return  $this->db->select('*')
                             ->where('idadmin', 1)
                            ->get('paiementconfiguration');
                           // ->row()->numAffilier;
         
     }
     
        public function GetNotifClient() {
         
              return $this->db->select('*')
                            ->where('notifpaiement', 1)
                            ->where('client_idclient', getsessionhelper()['id'])
                            ->get('notifclient')
                            ->result();
        }
        
             //update champ vue pour l'admin : table notifadmin
        function updateVueClient($idnotif)
        {
            $idclt = getsessionhelper()['id'];
            // update notifaction client
        $this->db->where('idnotifClient', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('notifclient');
        
        //
          $vue =  $this->db->select('*')
                             ->where('idclient', $idclt)
                            ->get('client')
                            ->row()->notifaction;
           
          if($vue > 0)
          {
               // update notifaction client
        $this->db->where('idclient', $idclt);
        $this->db->set('notifaction', "notifaction-1", FALSE);
        $this->db->update('client');
          }
        }
 
        function saveAdr($idclt) {
       
           $adr = $this->input->post('adresse');
               
        $this->db->where('idclient', $idclt);
        $this->db->set('adrlivraison', $adr);
        $this->db->update('client');
       
    }
    
      function getListAchatClt($idclt) {
        // get achat from table commande
        $sql = "select * 
                  from commande c 
                  where c.client_idclient = '$idclt'
                ORDER BY c.datecmd DESC";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get idproduct from table quantité and then get information from table produit 
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            // récupérer id produit
            $produitquantite = $this->db->where('commande_idcommande', $res['idcommande'])
                    ->get('quantite')
                    ->row();
            //get info prod from table produit
            $prod = $this->db->where('idproduit', $produitquantite->produit_idproduit)
                    ->get('produit')
                    ->row();
            
            $tableau[$i] = array('idprod' => $prod->idproduit,'prixprod'=> $prod->prix 
              ,'libelle'=>$prod->libelle, 'photo' => $prod->photo,'date'=>$res['datecmd']);
            $i++;
        }
        return $tableau;
    }
 
    function  getListVenteComm()
     {
        // get vente of comm from table commande
        $sql = "select * 
                  from commande c 
                ORDER BY c.datecmd DESC";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get idproduct from table quantité and then get information from table produit 
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            // récupérer id produit
            $produitquantite = $this->db->where('commande_idcommande', $res['idcommande'])
                    ->get('quantite')
                    ->row();
           
            //get info prod from table produit
            $prod = $this->db->where('idproduit', $produitquantite->produit_idproduit)
                    ->where('commercant_idcommercant', getsessionhelper()['id'])
                    ->get('produit')
                    ->row();
            if ($prod != NULL)
            {
            $tableau[$i] = array('idprod' => $prod->idproduit,'prixprod'=> $prod->prix 
              ,'libelle'=>$prod->libelle, 'photo' => $prod->photo,'date'=>$res['datecmd']);
            $i++;
            }
        }
        return $tableau;
    }
    
       function updateAff($idadmin) {
       
           $affil = $this->input->post('affil');
               
        $this->db->where('idadmin', $idadmin);
        $this->db->set('numAffilier', $affil);
        $this->db->update('paiementconfiguration');
       
    }
    
     function updatestock($idp,$qty)
     {
         
         $stock =   $this->db->select('*')
                             ->where('idproduit', $idp)
                            ->get('produit')
                            ->row()->stock;

         if($stock > $qty )
         {
            $this->db->where('idproduit', $idp);
            $this->db->set('stock', $stock - $qty);
        $this->db->update('produit'); 
         }
         else 
         {
           $this->db->where('idproduit', $idp);
            $this->db->set('stock', 0);
            $this->db->update('produit');   
         }
         
     
     }
}

