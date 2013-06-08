<?php

Class Admin_model extends CI_Model {

    function login($username, $password) {
        $this->db->select('login, pwd');
        $this->db->from('admin');
        $this->db->where('login', $username);
        $this->db->where('pwd', $password);

        $this->db->limit(1);

        $query = $this->db->get();


        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    function getcommparent($limit, $offset) {
        $this->db->order_by('idpersonne', 'asc');
        $this->db->where('type', 'commercant');
         $this->db->limit($limit, $offset);
        return $this->db->get('personne');
    }

    function getcommchild($id) { //$this->db->select('*'); 
        $this->db->order_by('idcommercant', 'asc');
        $this->db->where('personne_idpersonne', $id);
        return $this->db->get('commercant');
    }

    function getclientparent($limit, $offset) {
        $this->db->order_by('idpersonne', 'asc');
        $this->db->where('type', 'client');
         $this->db->limit($limit, $offset);
        return $this->db->get('personne');
    }

    function getclientchild($id) { //$this->db->select('*'); 
        $this->db->order_by('idclient', 'asc');
        $this->db->where('personne_idpersonne', $id);
        return $this->db->get('client');
    }

    function delete($id) {
        $this->db->where('idpersonne', $id);
        $this->db->delete('personne');
    }

    function activer($id) {
        $this->db->set('enable', true);
        $this->db->where('personne_idpersonne', $id);
        $this->db->update('commercant');
        return true;
    }

    function desactiver($id) {
        $this->db->set('enable', false);
        $this->db->where('personne_idpersonne', $id);
        $this->db->update('commercant');
        return true;
    }

    function count_all() {
        return $this->db->count_all('commercant');
    }
    function count_all_client() {
        return $this->db->count_all('client');
    }
      function count_all_produit($id) {
         $this->db->where('commercant_idcommercant', $id);
               $this->db->where('active', 1);
                $this->db->where('stock >', 0);
               return $this->db->count_all_results('produit');
            
        
    }

    function getparent($id) {

        $this->db->where('idpersonne', $id);
        return $this->db->get('personne');
    }

    function getidadmin($login) {
        $this->db->select('*');
        $this->db->from('admin');
        $this->db->where('login', $login);
        $this->db->limit(1);
        return $this->db->get();
    }

    function getidadminformsg() {
        $this->db->select('idadmin');
        $this->db->from('admin');
        $this->db->limit(1);
        return $this->db->get();
    }

    function Get_Slider($id) {
        $this->db->where('admin_idadmin', $id);
        return $this->db->get('slideradmin');
    }
    //slider de la page d'acceuil
    function GetSlider() {
        return $this->db->get('slideradmin');
    }

    function InsertSlider($form_data) {
        $q = $this->db->insert('slideradmin', $form_data);
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    function deleteSlider($id) {
        $this->db->where('id', $id);
        $this->db->delete('slideradmin');
    }
    
    //verifier existance du pwd
    function verifpwd($pwd, $login) {
        $q = $this
                ->db
                ->where('pwd', $pwd)
                ->where('login', $login)
                ->get('admin');
        if ($q->num_rows == 1) {


            return true;
        } else {

            return false;
        }
    }
    
    function updatpwd($newpwd, $login) {
        $q = $this->db->set('pwd', $newpwd);
        $this->db->where('login', $login);
        $this->db->update('admin');
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

}

?>
