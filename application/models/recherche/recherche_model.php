<?php
class Recherche_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function getsearch($chaine, $mot) {
        $sql = "select * from produit p 
                where p.libelle  LIKE '%$mot%'
                AND p.souscategorie_categorie_idcategorie IN ($chaine) ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    function getsearchmot($mot) {
        $sql = "select * from produit p where p.libelle  LIKE '%$mot%' ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}

?>