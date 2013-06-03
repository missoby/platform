<?php
class produit_model extends CI_Model {
	
	private $tbl_produit= 'produit';
	
	function __construct(){
		parent::__construct();
	}
	
	function list_all(){
		$this->db->order_by('id','asc');
		return $this->db->get($tbl_produit);
	}
	
	function count_all(){
		return $this->db->count_all($this->tbl_produit);
	}
	
	function get_paged_list($idcomm, $limit, $offset){
		$this->db->order_by('idproduit','asc');
		$this->db->where('commercant_idcommercant', $idcomm);
                $this->db->limit($limit, $offset);
		return $this->db->get($this->tbl_produit)->result();
	}
	
	function get_by_id($id){
		$this->db->where('idproduit', $id);
		return $this->db->get($this->tbl_produit);
	}
        
        public function getProductById($id)
        {
            return $this->db->where('idproduit', $id)->get($this->tbl_produit)->row();
	}
	
	function save($produit){
		$this->db->insert($this->tbl_produit, $produit);
		return $this->db->insert_id();
	}
	
	function update($id, $produit){
		$this->db->where('idproduit', $id);
		$this->db->update($this->tbl_produit, $produit);
	}
	
	function delete($id){
		$this->db->where('idproduit', $id);
		$this->db->delete($this->tbl_produit);
	}
        
       function getsouscategorie()
        {
            return $this->db->get('souscategorie');
            
        }
        function getcategorie()
        {
            return $this->db->get('categorie');
            
        }
        //recuperer le produit avec l'id 
        function  getproduct($id)
        {
                    $this -> db -> where('idproduit', $id);
                    $this -> db -> limit(1);
                return $this->db->get('produit'); 
        }
		
		   //recuperer id du comm
        function getidcomm($id)
        {//recuperer l'id du commercant
            $q = $this 
                ->db
                ->where('idcommercant', $id)
                ->limit(1)
                ->get('personne');

                     if ($q)
                     {
                    
                    foreach ($q->result_array() as $row)
                                {
                                    $id = $row['idpersonne'];
                                }
                     
           $q2 = $this
            ->db
            ->where('personne_idpersonne', $id)
            ->limit(1)
            ->get('commercant');
           return $q2;
                     }
                     else {
                         return false;
                     }
                  
                   
        }
		
		function getcat($idcat)
        {
                  $this->db->select('titre');
                   $this->db->where('idcategorie',$idcat);
            return $this->db->get('categorie');
        }
        
		
		function updateproduct($form_data, $idcomm,$idprod)
        {
                    
          return $this->db->where('commercant_idcommercant', $idcomm)
                         ->where('idproduit', $idprod)
                        ->update('produit',$form_data); 

        }
		
		function addproduct($form_data)
        {
            $q = $this->db->insert('produit', $form_data);
            if ($q)
            {return true;}
            else {
                return false;
            }
        }
		
		function getcommercant()
        {
            return $this->db->get('commercant');
        }
        
        function get_product_from_home($id)
        {
            $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                 $this->db->where('soussouscategorie_id', $id);
		return $this->db->get($this->tbl_produit);
            
        }
		
		function get_all_product(){
		       $this->db->where('active', 1);
                $this->db->where('stock >', 0);
               
		return $this->db->get($this->tbl_produit);
	     }
             function get_product_by_note(){
	        $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->order_by('note', 'desc');
                $this->db->limit(8);
		return $this->db->get($this->tbl_produit);
	     }
		 function activer($id)
 {
     $this->db->set('active', true);
           $this->db->where('idproduit', $id);
           $this->db->update('produit'); 
           return true ;
     
     
 }
 
 function desactiver($id)
 {$this->db->set('active', false);
           $this->db->where('idproduit', $id);
           $this->db->update('produit'); 
           return true ;
     
 }
 
 function get_paged_list_active($limit = 10, $offset = 0, $id)
  {
     $this->db->order_by('idproduit','asc');
              $this->db->where('commercant_idcommercant', $id);
               $this->db->where('active', 1);
                $this->db->where('stock >', 0);
		return $this->db->get($this->tbl_produit, $limit, $offset);
     
     
  }
  function get_product_comm($id)
  {
                $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
		return $this->db->get($this->tbl_produit);
  }
  
  function getsouscatvoir($idscat)
    {
         $this->db->select('*');
         $this->db->from('souscategorie');
         $this->db->where('idsouscategorie',$idscat);
         return $this->db->get();
    }
  function getsoussouscatvoir($idsscat)
    {
        $this->db->select('*');
         $this->db->from('soussouscategorie');
         $this->db->where('id',$idsscat);
         return $this->db->get();
    }
  
  function getsouscat($idscat)
    {
         $this->db->select('*');
         $this->db->from('souscategorie');
         $this->db->where('categorie_idcategorie',$idscat);
         $query = $this->db->get();
         return $query->result();
    }
    
    function getsoussouscat($idscat)
    {    $this->db->select('*');
         $this->db->from('soussouscategorie');
         $this->db->where('souscategorie_idsouscategorie',$idscat);
         $query = $this->db->get();
         return $query->result();
        
    }
    
    
      function get_product_by_date(){
	        $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->order_by('dateajout', 'desc');
                $this->db->limit(8);
		return $this->db->get($this->tbl_produit);
	     }
  
      function Get_Cat_Comm($id) {

        $sql = "select DISTINCT p.souscategorie_categorie_idcategorie
                  from produit p
                  where p.commercant_idcommercant = '$id'";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get information about person (table personne)
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            $cat = $this->db->where('idcategorie', $res['souscategorie_categorie_idcategorie'])
                    ->get('categorie')
                    ->row();
            
         
            $tableau[$i] = array('cat' => $cat->titre, 'idcat'=>$cat->idcategorie);
            $i++;
        }
        return $tableau;
    }
    
    
      function Get_Sous_Cat_Comm($id) {

        $sql = "select DISTINCT p.souscategorie_idsouscategorie
                  from produit p
                  where p.commercant_idcommercant = '$id'";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get information about person (table personne)
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            $cat = $this->db->where('idsouscategorie', $res['souscategorie_idsouscategorie'])
                    ->get('souscategorie')
                    ->row();
            
         
            $tableau[$i] = array('souscat' => $cat->titre, 'idsouscat'=>$cat->idsouscategorie,'idforeigncat'=>$cat->categorie_idcategorie);
            $i++;
        }
        return $tableau;
    }
    
     function get_product_comm_Tri_Prix($id)
  {
                $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
                $this->db->order_by('prix', 'desc');
		return $this->db->get($this->tbl_produit);
  }
  
     function get_product_comm_Tri_Libelle($id)
  {
                $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
                $this->db->order_by('libelle', 'asc');
		return $this->db->get($this->tbl_produit);
  }
  
    function get_product_comm_by_cat($id, $idcat)
  {
                $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
                $this->db->where('souscategorie_categorie_idcategorie', $idcat);
		return $this->db->get($this->tbl_produit);
  }
  
      function get_product_comm_by_Sous_cat($id, $idsouscat)
  {
                $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
                $this->db->where('souscategorie_idsouscategorie', $idsouscat);
		return $this->db->get($this->tbl_produit);
  }
  
  function Get_Slider($id){
		$this->db->where('commercant_idcommercant', $id);
		return $this->db->get('slidercomm');
	}
 
   function InsertSlider($form_data)
   {
      $q = $this->db->insert('slidercomm', $form_data);
            if ($q)
            {return true;}
            else {
                return false;
            } 
   }
   
   
	function deleteSlider($id){
		$this->db->where('id', $id);
		$this->db->delete('slidercomm');
	}
        
           	function getSouscatadmin($idsouscat)
        {
                  $this->db->select('titre');
                   $this->db->where('idsouscategorie',$idsouscat);
            return $this->db->get('souscategorie');
        }
        
        //get produit soldé
           //get list client who sent msg to comm
    function get_remise_product() {
     $val = 0;
        $sql = "select *
                  from produit p
                  where p.remise > '$val'
                AND p.stock > '$val' ";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get the old and new price
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            
           $newprix = $res['prix'] - ($res['prix'] * $res['remise']/100);
        $tableau[$i] = array('idp' => $res['idproduit'], 'oldprice' => $res['prix'], 'newprice' => $newprix, 'libelle' => $res['libelle'], 'remise' => $res['remise'], 'idcomm' => $res['commercant_idcommercant'], 'photo'=> $res['photo']);
            $i++;
        }
        return $tableau;
    }
    
    
    
    
    
    function listprodmobile($id)
    {

        $this->db->select('*');
        $this->db->from($this->tbl_produit);
                 $this->db->where('souscategorie_categorie_idcategorie', $id);
                 $query = $this->db->get();
                  return $query->result();
		
    }
    function get_product_comm_mobile($id)
  {
        $this->db->select('*');
        $this->db->from($this->tbl_produit);
                 $this->db->where('active', 1);
                $this->db->where('stock >', 0);
                $this->db->where('commercant_idcommercant', $id);
                 $query = $this->db->get();
                  return $query->result();
                  
           
  }
  
  
}
