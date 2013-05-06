<?php

Class User_model extends CI_Model {

    function login() {
        $username = $this->input->post('login');
        $password = sha1($this->input->post('pwd'));
        $this->db->select('idpersonne, login, type, active, email');
        $this->db->from('personne');
        $this->db->where('login', $username);
        $this->db->where('pwd', $password);
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1)
            return $query->result();
        else
            return false;
    }
        function getetatcomm($idpersonne) {
        $this->db->select('enable');
        $this->db->from('commercant');
        $this->db->where('personne_idpersonne', $idpersonne);
        $this->db->limit(1);
        return $this->db->get();
    }

    function getidclient($idpersonne) {
        $this->db->select('idclient');
        $this->db->from('client');
        $this->db->where('personne_idpersonne', $idpersonne);
        $this->db->limit(1);
        return $this->db->get();
    }

    function getidcommercant($idpersonne) {
        $this->db->select('idcommercant');
        $this->db->from('commercant');
        $this->db->where('personne_idpersonne', $idpersonne);
        $this->db->limit(1);
        return $this->db->get();
    }
    
    /*public function getUserData($fid)
    {
        $query = $this->db->where('facebook_id', $fid)
                          ->get('personne');
        
        if($query->num_rows() == 1)
            return $query->row();
        else
            return false;
    }*/
    
    public function getUserByMail($mail)
    {
        $query = $this->db->where('email', $mail)->get('personne');
        
        if($query->num_rows() == 1)
            return $query->row();
        else
            return false;
    }
    
    function getnotif_client($idpersonne) {
        $this->db->select('*');
        $this->db->from('client');
        $this->db->where('personne_idpersonne', $idpersonne);
        $this->db->limit(1);
        return $this->db->get();
    }
    
    function getnotif_commercant($idpersonne) {
        $this->db->select('*');
        $this->db->from('commercant');
        $this->db->where('personne_idpersonne', $idpersonne);
        $this->db->limit(1);
        return $this->db->get();
    }
}

