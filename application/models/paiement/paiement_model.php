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
        
           function saveaffil($idcom) {
       
               $data = array(
           'lienControl' => $this->input->post('control'),
            'lienSucces' => $this->input->post('succes'),
            'lienEchec' => $this->input->post('echec'),
            'numAffilier' => $this->input->post('affil'),
            'dateActv' => date("y-m-d H:i:s"),
            'idcom' => $idcom
           
        );
        $this->db->insert('paiementconfiguration', $data);
       
    }

       
}

