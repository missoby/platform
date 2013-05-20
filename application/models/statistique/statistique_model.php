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
    
     function  getNbVenteComm()
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
        return $i;
    }


    
}