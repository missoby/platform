<?php

Class Signaler_model extends CI_Model {
  
     function addSign($idsign, $type)
     {
         if($type == 'produit')
         {
              $data = array(
             'titre' => $this->input->post('titre'),
             'type' =>$type,
             'dateenvoi' => date("y-m-d H:i:s"),
             'description' =>$this->input->post('description') , 
             'produit_idproduit' => $idsign
             
         );
         $this->db->insert('signaler', $data);
         // update notifaction admin
        $this->db->where('idadmin', 1);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');
         }       
         elseif($type == 'avis')
         {
             $data = array(
             'titre' => $this->input->post('titre'),
             'type' =>$type,
             'dateenvoi' => date("y-m-d H:i:s"),
             'description' =>$this->input->post('description') , 
             'commentaire_idcommentaire' => $idsign
             
         );
         $this->db->insert('signaler', $data);
         // update notifaction admin
        $this->db->where('idadmin', 1);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');
         }
         elseif($type == 'forum')
         {
           $data = array(
             'titre' => $this->input->post('titre'),
             'type' =>$type,
             'dateenvoi' => date("y-m-d H:i:s"),
             'description' =>$this->input->post('description') , 
             'Msgforum_idMsgforum' => $idsign
             
         );
         $this->db->insert('signaler', $data);
         // update notifaction admin
        $this->db->where('idadmin', 1);
        $this->db->set('notifaction', "notifaction+1", FALSE);
        $this->db->update('admin');  
         }

     }
      public function Getsign() {
         
              return $this->db->select('*')
                               ->order_by('dateenvoi', 'desc')
                            ->get('signaler')
                            ->result();
        }
     
        public function GetAvis($idavis) {
         
              return $this->db->select('*')
                            ->where('idcommentaire',$idavis )
                            ->get('commentaire')
                            ->result();
        }
        
        function delete($id){
		$this->db->where('idcommentaire', $id);
		$this->db->delete('commentaire');
	}
        
        
          public function getMsgForum($idmsgF) {
         
              return $this->db->select('*')
                            ->where('idMsgforum',$idmsgF )
                            ->get('msgforum')
                            ->result();
        }
        
        
        function deletemsgf($id){
		$this->db->where('idMsgforum', $id);
		$this->db->delete('msgforum');
	}

        
}

