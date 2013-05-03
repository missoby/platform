<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
class annonce_model extends CI_Model {
	
	private $tbl_annonce= 'annonce';
	
	function __construct(){
		parent::__construct();
	}
	
	function list_all(){
		$this->db->order_by('id','asc');
		return $this->db->get($tbl_annonce);
	}
	
	function count_all(){
		return $this->db->count_all($this->tbl_annonce);
	}
	
	function get_paged_list($idcomm, $limit = 10, $offset = 0){
		$this->db->order_by('idannonce','asc');
		$this->db->where('commercant_idcommercant', $idcomm);
                $this->db->limit($limit, $offset);
                return $this->db->get($this->tbl_annonce)->result();
		//return $this->db->get($this->tbl_annonce, $limit, $offset)->result();
	}


        function get_by_id($id){
		$this->db->where('idannonce', $id);
		return $this->db->get($this->tbl_annonce);
	}
	
	function save($annonce){
		$this->db->insert($this->tbl_annonce, $annonce);
		return $this->db->insert_id();
	}
	
	function update($id, $annonce){
		$this->db->where('idannonce', $id);
		$this->db->update($this->tbl_annonce, $annonce);
	}
	
	function delete($id){
		$this->db->where('idannonce', $id);
		$this->db->delete($this->tbl_annonce);
	}
        
        function getsouscategorie()
        {
            return $this->db->get('souscategorie');
            
        }
        function getcategorie()
        {
            return $this->db->get('categorie');
            
        }
		
		  function getidcomm($login)
        {//recuperer l'id du commercant
            $q = $this 
                ->db
                ->where('login', $login)
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
        
		function addannonce($form_data)
        {
            return $this->db->insert('annonce', $form_data);
            
        }
        
        function getcat($idcat)
        {
                  $this->db->select('titre');
                   $this->db->where('idcategorie',$idcat);
            return $this->db->get('categorie');
        }
        function getsouscat($idscat)
        {
             $this->db->select('titre');
             $this->db->where('idsouscategorie',$idscat);
            return $this->db->get('souscategorie');
        }
		
		 function  getannonce($id)
        {
                     $this -> db -> where('idannonce', $id);
                    $this -> db -> limit(1);
            return $this->db->get('annonce'); 
        }
        
         function updateannonce($form_data, $idcomm,$idannonce)
        {
             return $this->db->where('commercant_idcommercant', $idcomm)
                         ->where('idannonce', $idannonce)
                        ->update('annonce',$form_data); 
        }
		
		function activer($id)
 {
     $this->db->set('active', true);
           $this->db->where('idannonce', $id);
           $this->db->update('annonce'); 
           return true ;
     
     
 }
 
 function desactiver($id)
 {$this->db->set('active', false);
           $this->db->where('idannonce', $id);
           $this->db->update('annonce'); 
           return true ;
     
 }
  function get_all_annonce(){
            
                $this->db->where('active', 1);
		return $this->db->get($this->tbl_annonce);
	}
	function get_annonce_comm($id)
  {
                $this->db->where('active', 1);
                $this->db->where('commercant_idcommercant', $id);
		return $this->db->get($this->tbl_annonce);
  }
}
?>