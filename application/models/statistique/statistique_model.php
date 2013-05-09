<?php
class Statistique_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    
    
    
    function GetNbClientInscri()
    {
        $this->db->select('*')
                ->from('personne')
                ->where('type', 'client' );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
     function GetNbCommInscri()
    {
        $this->db->select('*')
                ->from('personne')
                ->where('type', 'commercant' );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
      function GetNbClientConf()
    {
        $this->db->select('*')
                ->from('personne')
                ->where('type', 'client') 
                ->where('active', 1 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
        function GetNbCommConf()
    {
        $this->db->select('*')
                ->from('personne')
                ->where('type', 'commercant') 
                ->where('active', 1 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
         function GetNbCommEnabl()
    {
        $this->db->select('*')
                ->from('commercant')
                ->where('enable', 1 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
          function GetNbNot1($idcomm)
    {
        $this->db->select('*')
                ->from('produit')
                ->where('commercant_idcommercant', $idcomm)
                ->where('note', 1 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
    function GetNbNot2($idcomm)
    {
        $this->db->select('*')
                ->from('produit')
                ->where('commercant_idcommercant', $idcomm)
                ->where('note', 2 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
    function GetNbNot3($idcomm)
    {
        $this->db->select('*')
                ->from('produit')
                ->where('commercant_idcommercant', $idcomm)
                ->where('note', 3 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
    
    function GetNbNot4($idcomm)
    {
        $this->db->select('*')
                ->from('produit')
                ->where('commercant_idcommercant', $idcomm)
                ->where('note', 4 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }
  
    function GetNbNot5($idcomm)
    {
        $this->db->select('*')
                ->from('produit')
                ->where('commercant_idcommercant', $idcomm)
                ->where('note', 5 );

            $query = $this->db->get();
            return $rowcount = $query->num_rows();
    }


    
}