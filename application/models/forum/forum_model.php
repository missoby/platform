<?php
class forum_model extends CI_Model {
	
	private $tbl_produit= 'sujet';
	
	function __construct(){
		parent::__construct();
	}
	
        public function getAllSujets($idcat) {
            
           return $this->db->where('categorie_idcategorie', $idcat)
                     ->order_by('datepublication', 'desc')
                     ->get($this->tbl_produit);
            
        }
        
        public function getSujet($ids) {
            return $this->db->where('idsujet', $ids)
                     ->get($this->tbl_produit)
                     ->row();
        }
        
        public function getSociete($idc) {
            return $this->db->where('idcommercant', $idc)
                            ->get('commercant')
                            ->row();
        }
        
        public function getProprietaire($idc) {
            $res = $this->db->where('idclient', $idc)
                     ->get('client')
                     ->row();
            
            return $this->db->where('idpersonne', $res->personne_idpersonne)
                            ->get('personne')
                            ->row()->login;
            
        }
        
        public function getMsgForum($ids) {
            return $this->db->where('sujet_idsujet', $ids)
                            ->order_by('datepublication', 'asc')
                            ->get('msgforum')
                            ->result();
        }
        
        public function setMsgForum($ids, $idclt, $idcom) {
            $tab = array('contenu' => $this->input->post('contenu'),
                         'datepublication' => date("y-m-d"),
                         'sujet_idsujet' => $ids,
                         'client_idclient' => $idclt,
                         'commercant_idcommercant' => $idcom
                        );
            
           $this->db->insert('msgforum', $tab);
            
        }
        
        public function ajouterSujet($idcat, $idclt, $idcom) {
            $tab = array('titre' => $this->input->post('titre'),
                         'datepublication' => date("y-m-d"),
                         'categorie_idcategorie' => $idcat,
                         'client_idclient' => $idclt,
                         'commercant_idcommercant' => $idcom,
                         'contenu' => $this->input->post('contenu')
                        );
            
           $this->db->insert('sujet', $tab);
        }
        function deletemsg($idmsg){
		$this->db->where('idMsgforum', $idmsg);
		$this->db->delete('msgforum');
	}
        
         function resoudre($idsj)
            {
                $this->db->set('resolu', true);
                      $this->db->where('idsujet', $idsj);
                      $this->db->update('sujet'); 
                      return true ;


            }
        
       function InsertModif($id, $form_data)              
 {
     $titre = $form_data['titre'];
     $contenu = $form_data['contenu'];
           $this->db->set('titre', $titre);
           $this->db->set('contenu', $contenu);           
           $this->db->where('idsujet', $id);
           $this->db->update('sujet'); 
           return true ;
     
 }
  
        
        
}


