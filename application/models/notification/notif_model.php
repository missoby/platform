<?php

Class Notif_model extends CI_Model {
  
     function notifadmin($idadmin, $iduser, $msg)
     {
         $data = array(
             'message' => $msg,
             'admin_idadmin' =>$idadmin,
             'idcomm' => $iduser,
             'vue' =>0
         );
         $this->db->insert('notifadmin', $data);
         // update notifaction admin
        $this->db->where('idadmin', $idadmin);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');
     }

        
        public function getCommercantFromPersonne($idp) {
            return  $this->db->where('personne_idpersonne', $idp)
                     ->get('commercant')
                     ->row();  
           
        }
        
        function getnotifactionfromuser()
        {
            if(getsessionhelper()['type'] == 'commercant')
            {$id = 'idcommercant'; 
            $table = 'commercant';
               }
            elseif(getsessionhelper()['type'] == 'client')
            {  $id = 'idclient';
            $table = 'client';}
            else
            {
            $id = 'idadmin';
            $table = 'admin';
            }
            
            return $this->db->select('*')
            ->where($id , getsessionhelper()['id'])
            ->get($table)
            ->row();
        }
        
           function getnotifmsgfromuser()
        {
            if(getsessionhelper()['type'] == 'commercant')
            { $id = 'idcommercant'; 
            $table = 'commercant';
               }
            elseif(getsessionhelper()['type'] == 'client')
            {  $id = 'idclient';
            $table = 'client';}
            else
            {
               $id = 'idadmin';
            $table = 'admin';
            }
            
            return $this->db->select('*')
            ->where($id , getsessionhelper()['id'])
            ->get($table)
            ->row();
        }
        
        function Rmz_Notif_Msg_Comm($idcom)
        {
              // update notifaction comm
        $this->db->where('idcommercant', $idcom);
        $this->db->set('notifmsg', 0);
        $this->db->update('commercant');
            
        }
        
        function Rmz_Notif_Msg_client($idclt)
        {
              // update notifaction comm
        $this->db->where('idclient', $idclt);
        $this->db->set('notifmsg', 0);
        $this->db->update('client');
            
        }
         function Rmz_Notif_Msg_Admin()
        {
              // update notifaction comm
        $this->db->where('idadmin', 1);
        $this->db->set('notifmsg', 0);
        $this->db->update('admin');
            
        }
       
        
//        function GetNotif()
//        {
//            
//               $this->db->select('*');
//        return $this->db->get('notifadmin');
//   
//        }
          public function GetNotif() {
         
              return $this->db->select('*')
                            ->get('notifadmin')
                            ->result();
        }
        
          public function getProprietaireClt($idclt) {
            $res = $this->db->where('idclient', $idclt)
                     ->get('client')
                     ->row();
            
            return $this->db->where('idpersonne', $res->personne_idpersonne)
                            ->get('personne')
                            ->row()->login;
            
        }
          public function getProprietaireCom($idcom) {
            $res = $this->db->where('idcommercant', $idcom)
                     ->get('commercant')
                     ->row();
            
            return $this->db->where('idpersonne', $res->personne_idpersonne)
                            ->get('personne')
                            ->row()->idpersonne;
            
        }
        //update champ vue pour l'admin : table notifadmin
        function updateVue($idnotif)
        {
            $idadmin = getsessionhelper()['id'];
            // update notifaction admin
        $this->db->where('idnotifAdmin', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('notifadmin');
        
        //
          $vue =  $this->db->select('*')
                            ->get('admin')
                            ->row()->notifaction;
          if($vue > 0)
          {
               // update notifaction admin
        $this->db->where('idadmin', $idadmin);
        $this->db->set('notifaction', "notifaction-1", FALSE);
        $this->db->update('admin');
          }
        }
        
          function notifActionForum($id,$table, $msg, $ids)
     {
              if ($table == 'client')//  prop du sujet c le client
              {
         $data = array(
             'message' => $msg,
             'client_idclient' =>$id,
             'vue' =>0,
             'objNotif' => $ids
         );
         $this->db->insert('notifclient', $data);
         // update notifaction client
        $this->db->where('idclient', $id);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('client');
              }
              else 
              {
                  $data = array(
             'message' => $msg,
             'commercant_idcommercant' =>$id,
             'vue' =>0,
            'objNotif' => $ids
         );
         $this->db->insert('notifcommercant', $data);
         // update notifaction comm
        $this->db->where('idcommercant', $id);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('commercant');
                  
              }
     }
     
       public function GetNotifComm() {
           
         
              return $this->db->select('*')
                            ->where('commercant_idcommercant', getsessionhelper()['id'])
                            ->get('notifcommercant')
                            ->result();
        }
        
        public function setVue($idcom, $idnotif)
        {
            // update notifaction comm
        $this->db->where('idnotifCommercant', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('notifcommercant');
        
        //
          $vue =  $this->db->select('*')
                            ->where('idcommercant',$idcom)
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
        
        //notif of the client
         
       public function GetNotifClient() {
           
         
              return $this->db->select('*')
                            ->where('client_idclient', getsessionhelper()['id'])
                            ->get('notifclient')
                            ->result();
        }
        
           public function setVueClient($idclt, $idnotif)
        {
            // update notifaction comm
        $this->db->where('idnotifClient', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('notifclient');
        
        //
          $vue =  $this->db->select('*')
                            ->where('idclient',$idclt)
                            ->get('client')
                            ->row()->notifaction;
          if($vue > 0)
          {
              
               // update notifaction comm
        $this->db->where('idclient', $idclt);
        $this->db->set('notifaction', "notifaction-1", FALSE);
        $this->db->update('client');
          } 
    
        }
        
            //update champ vue pour l'admin : table notifadmin
        function updateVueSign($idnotif)
        {
            $idadmin = getsessionhelper()['id'];
            // update notifaction admin
        $this->db->where('idsignaler', $idnotif);
        $this->db->set('vue', 1);
        $this->db->update('signaler');
        
        //
          $vue =  $this->db->select('*')
                            ->get('admin')
                            ->row()->notifaction;
          if($vue > 0)
          {
               // update notifaction admin
        $this->db->where('idadmin', $idadmin);
        $this->db->set('notifaction', "notifaction-1", FALSE);
        $this->db->update('admin');
          }
        }
      function deleteclient($id){
		$this->db->where('idnotifClient', $id);
		$this->db->delete('notifclient');
	}
        function deletecomm($id){
		$this->db->where('idnotifCommercant', $id);
		$this->db->delete('notifcommercant');
	}
        
          function delete($id){
		$this->db->where('idnotifAdmin', $id);
		$this->db->delete('notifadmin');
	} 
        
       
       
        
}

