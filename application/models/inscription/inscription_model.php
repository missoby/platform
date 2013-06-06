<?php

class Inscription_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    function get_all_desabled()
    {
        $query = $this->db->get_where('personne', array('active' => 0));
        return $query->result();
    }
    function update_activer($id)
    {
        $data = array('active' => 1);

        $this->db->update('personne', $data, array('idpersonne' => $id));
    }
    function verifemailforinsert()
    {
        $login = $this->input->post('login');
        $email = $this->input->post('email');

        $q = $this
                ->db
                ->where('login', $login)
                ->limit(1)
                ->get('personne');
        
        $q2 = $this
                ->db
                ->where('email', $email)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0) and ($q2->num_rows == 0))
        {return true;}
        else 
            return false;
    }
    function SaveForm() {
        $form_data = array(
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'civilite' => $this->input->post('civilite'),
            'email' => $this->input->post('email'),
            'login' => $this->input->post('login'),
            'pwd' => sha1($this->input->post('pwd')),
            'adresse' => $this->input->post('adresse'),
            'pays' => $this->input->post('pays'),
            'tel' => $this->input->post('tel'),
            'ville' => $this->input->post('ville'),
            'type' => 'client', // par defaut
            'active' => 0,
        );
        $login = $form_data['login'];
        $email = $form_data['email'];

        $q = $this
                ->db
                ->where('login', $login)
                ->limit(1)
                ->get('personne');
        
        $q2 = $this
                ->db
                ->where('email', $email)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0) and ($q2->num_rows == 0)) {
            $this->db->insert('personne', $form_data);
            // recuperer l\'id en utilisant le login qui est unique
            $id = $this->db->insert_id();

            // preparer le champ a inserer dans la table fille 

            $form_data2 = array(
                'personne_idpersonne' => $id,
            );

            if ($this->db->affected_rows() != '0') {
                $this->db->insert('client', $form_data2);
                return TRUE;
            }

            return FALSE;
        } else {

            return FAlSE ;
        }
    }
    function verifemailforinsertComm()
    {
        $login = $this->input->post('login');
        $email = $this->input->post('email');

        $q = $this
                ->db
                ->where('login', $login)
                ->limit(1)
                ->get('personne');
        
        $q2 = $this
                ->db
                ->where('email', $email)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0) and ($q2->num_rows == 0))
        {return true;}
        else 
            return false;
    }

    function SaveFormComm() {
       
         $form_data = array(
                'nom' => $this->input->post('nom'),
                'prenom' => $this->input->post('prenom'),
                'civilite' => $this->input->post('civilite'),
                'email' => $this->input->post('email'),
                'login' => $this->input->post('login'),
                'pwd' => sha1($this->input->post('pwd')),
                'adresse' => $this->input->post('adresse'),
                'pays' => $this->input->post('pays'),
                'tel' => $this->input->post('tel'),
                'ville' => $this->input->post('ville'),
                'type' => 'commercant', // par defaut
                'active' => 0,
             
            );
      
          $donnCom = $this->input->post('societe');
   
        $login = $form_data['login'];
        $email = $form_data['email'];
        //recuperer les logins
        $q = $this
                ->db
                ->where('login', $login)
                ->limit(1)
                ->get('personne');
        // recuperer les emails
        $q2 = $this
                ->db
                ->where('email', $email)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0) and ($q2->num_rows == 0)) {
            $this->db->insert('personne', $form_data);
            // recuperer l\'id en utilisant le login qui est unique
          $idcomm = $this->db->insert_id();

            // preparer le champ a inserer dans la table fille 

            $form_datacomm = array(
                'personne_idpersonne' => $idcomm,
                'societe' => $donnCom,
                'adrsoc' => $this->input->post('adrsoc'),
            'descsoc' => $this->input->post('descsoc'),
            'siteweb' => $this->input->post('siteweb'),
            'telpro' => $this->input->post('telpro'),
            'fax' => $this->input->post('fax'),
             'enable' => 0

            );


            if ($this->db->affected_rows() != '0') {

                $this->db->insert('commercant', $form_datacomm);

                return TRUE;
            }

             return FALSE;
        } else {

            return FAlSE ;
        }
       
    }


    function getparent($id) {
       // $this->db->order_by('idpersonne', 'asc');
        $this->db->where('idpersonne', $id);
        return $this->db->get('personne');
    }

    function getchild($id) {

        $this->db->order_by('idclient', 'asc');
        $this->db->where('personne_idpersonne', $id);
        return $this->db->get('client');
    }

    function getchildcomm($id) {

        $this->db->order_by('idcommercant', 'asc');
        $this->db->where('personne_idpersonne', $id);
        return $this->db->get('commercant');
    }

    function delete($id) {
        $this->db->where('idpersonne', $id);
        $this->db->delete('personne');
    }

    function getprofilparent($id) {

        $this->db->where('idpersonne', $id);
        $this->db->limit(1);
        return $this->db->get('personne');
        ;
    }

    function getprofilchild($id, $type) {
        if ($type == 'client') {

            $this->db->where('personne_idpersonne', $id);
            $this->db->limit(1);
            return $this->db->get('client');
            ;
        } else {

            $this->db->where('personne_idpersonne', $id);
            $this->db->limit(1);
            return $this->db->get('commercant');
        }
    }

    function insertmodif($id, $form_data, $form_datacomm) {

        $q =  $this->db->where('idpersonne', $id);
             $this->db->update('personne', $form_data);
            

        if ($q == TRUE) {
            $this->db->where('personne_idpersonne', $id);
             $this->db->update('commercant', $form_datacomm);
            return true;
        } else {
            return false;
        }
    }

    function insertmodifclient($id, $form_data) {

      $q = $this->db->where('idpersonne', $id);
        $this->db->update('personne', $form_data);

        if ($q == TRUE) {

            return true;
        } else {
            return false;
        }
    }

    function verifpwd($pwd, $login) {
        $q = $this
                ->db
                ->where('pwd', $pwd)
                ->where('login', $login)
                ->get('personne');
        if ($q->num_rows == 1) {


            return true;
        } else {

            return false;
        }
    }

    function updatpwd($newpwd, $login) {
        $q = $this->db->set('pwd', $newpwd);
        $this->db->where('login', $login);
        $this->db->update('personne');
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    function getlogincourant($id) {
        $this->db->select('login');
        $this->db->where('idpersonne', $id);
        return $this->db->get('personne');
    }

    function getmailcourant($id) {
        $this->db->select('email');
        $this->db->where('idpersonne', $id);
        return $this->db->get('personne');
    }

    function verifmail($mail) {
        $q = $this
                ->db
                ->where('email', $mail)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0)) {

            return true;
        } else {
            return false;
        }
    }

    function veriflogin($log) {
        $q = $this
                ->db
                ->where('login', $log)
                ->limit(1)
                ->get('personne');

        if (($q->num_rows == 0)) {

            return true;
        } else {
            return false;
        }
    }
    
    function NewsLetter()
    {
       $email= array(
              'email'=> $this->input->post('email')
               );
        $this->db->insert('newsletter', $email);
    }
    
    function getidpers($idcomm) {
       // $this->db->order_by('idpersonne', 'asc');
        $this->db->where('idcommercant', $idcomm);
        return $this->db->get('commercant');
    }
    function getidcommprofil($idpers) {

        
        $this->db->select('*');
        $this->db->from('commercant');
              $this->db->where('personne_idpersonne', $idpers);
                 $query = $this->db->get();
                  return $query->result();
    }
    
    //get informations about comm
    function getInfoComm($idcom) {

        $this->db->where('idcommercant', $idcom);
        return $this->db->get('commercant');
    }
    //get informations about comm
    function getInfoPersComm($idpers) {

        $this->db->where('idpersonne', $idpers);
        return $this->db->get('personne');
    }

    

}

?>