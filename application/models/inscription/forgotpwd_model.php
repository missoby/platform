<?php

class Forgotpwd_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
         function checkemail($email)
       {
           //verifier l'existance du mail 
           $q = $this
            ->db
            ->where('email', $email)
            ->limit(1)
            ->get('personne');
           
           return $q;           
           
       }
	 
	        function savepwd($pwdcrypt,$id)
       {
   $insert = $this->db->set('pwdoublier', $pwdcrypt);
             $this->db->where('idpersonne', $id);
             $this->db->update('personne');  
           return $insert;  
           
       }
       
	         function existcode($code)
       {
           //verifier l'existance du code
   $this -> db -> select('idpersonne');
   $this -> db -> from('personne');
   $this -> db -> where('pwdoublier', $code);
   $this -> db -> limit(1);

  return  $query = $this -> db -> get();
       }
             function rmztemp($code)
       {
  
   $insert = $this->db->set('pwdoublier', NULL);
             $this->db->where('pwdoublier', $code);
             $this->db->update('personne');  
           return $insert;
        
           
           
       }
	   
       function updatepwd($form_data, $id)
       {
        
           $this->db->set('pwd', $form_data['pwd']);
           $this->db->where('idpersonne', $id);
           $this->db->update('personne'); 
           return true ;

       }
}
?>