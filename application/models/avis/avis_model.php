<?php
class Avis_model extends CI_Model {
	
	
	
	function __construct(){
		parent::__construct();
	}
	
    
        
        public function getavis($idp) {
         
              return $this->db->where('produit_idproduit', $idp)
                            ->order_by('datepublication', 'desc')
                            ->get('commentaire')
                            ->result();
        }
        
        public function setAvis($idp, $idclt)
        {
              $tab = array('contenu' => $this->input->post('contenu'),
                         'datepublication' => date("y-m-d"),
                         'produit_idproduit' => $idp,
                         'client_idclient' => $idclt,
                         
                        );
            
           $this->db->insert('commentaire', $tab);
        }

        
               
        
}


