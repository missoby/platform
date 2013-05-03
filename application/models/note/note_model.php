<?php

Class Note_model extends CI_Model {

   function getnote($idp)
   {
        $this->db->select('note');
        $this->db->from('produit');
        $this->db->where('idproduit', $idp);
        $this->db->limit(1);
        return $this->db->get();
   }
   
    function savenote($idp, $newnote)
   {
        $q = $this->db->set('note', $newnote);
        $this->db->where('idproduit', $idp);
        $this->db->update('produit');
        if ($q == true) {
            return true;
        } else {
            return false;
        }
   }
 
 

}

?>
