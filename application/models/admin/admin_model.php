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

    function getcommparent() {
        $this->db->order_by('idpersonne', 'asc');
        $this->db->where('type', 'commercant');
        return $this->db->get('personne');
    }

    function getcommchild($id) { //$this->db->select('*'); 
        $this->db->order_by('idcommercant', 'asc');
        $this->db->where('personne_idpersonne', $id);
        return $this->db->get('commercant');
    }

    function getclientparent() {
        $this->db->order_by('idpersonne', 'asc');
        $this->db->where('type', 'client');
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

}

?>
