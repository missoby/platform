<?php

Class Message_model extends CI_Model {
    //insertion message client vers commercant
    function msgclientcomm($idcom) {
        $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'client_idclient' => getsessionhelper()['id'],
            'commercant_idcommercant' => $idcom
        );
        $this->db->insert('msgclient', $data);
         // update notifaction comm
        $this->db->where('idcommercant', $idcom);
        $this->db->set('notifmsg', "notifmsg+1", FALSE);
        $this->db->update('commercant');
    }
    
       //insertion message from comm to admin
    function msgCommAdmin($idcom) {
        $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'admin_idadmin' => 1,
            'commercant_idcommercant' => $idcom
        );
        $this->db->insert('msgcommadmin', $data);
              // update notifaction comm
        $this->db->where('idadmin', 1);
        $this->db->set('notifmsg', "notifmsg+1", FALSE);
        $this->db->update('admin');
    }
    //get list client who sent msg to comm
    function get_list_client() {
        
        $id = getsessionhelper()['id'];
        $sql = "select DISTINCT  com.client_idclient, com.vue
                  from msgclient com
                  where com.commercant_idcommercant = '$id'";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get information about person (table personne)
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            $client = $this->db->where('idclient', $res['client_idclient'])
                    ->get('client')
                    ->row();
            
            $pers = $this->db->where('idpersonne', $client->personne_idpersonne)
                    ->get('personne')
                    ->row();
            $tableau[$i] = array('idp' => $pers->idpersonne, 'nom' => $pers->nom, 'prenom' => $pers->prenom, 'mail' => $pers->email, 'idcl' => $client->idclient, 'vue' => $res['vue']);
            $i++;
        }
        return $tableau;
    }
    //get conversation between client and current commercant (msgclient, msgcommclient)
    function get_conv_client_comm($idclient) {

        $comm = getsessionhelper()['id'];

        $this->db->where('client_idclient', $idclient);
        $this->db->where('commercant_idcommercant', $comm);
        $tabcl = $this->db->get('msgclient');

        $this->db->where('client_idclient', $idclient);
        $this->db->where('commercant_idcommercant', $comm);
        $tabcom = $this->db->get('msgcommclient');

        $tab = array();
        $i = 0;

        foreach ($tabcl->result_array() as $res) {
            $tab[$i] = array(
                'idmsg' => $res['idMsgClient'],
                'table' => 'msgclient',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idclt' => $res['client_idclient'],
            );
            $i++;
        }

        $tab2 = array();
        $j = 0;

        foreach ($tabcom->result_array() as $res) {
            $tab2[$j] = array(
                 'idmsg' => $res['idMsgCommClient'],
                 'table' => 'msgcommclient',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idclt' => $res['client_idclient'],
            );
            $j++;
        }
        $tabmerge = array();
        $tabmerge = array_merge($tab, $tab2);
    
        return $tabmerge;
    }

    function get_conv_comm_admin()
    {
        $idcomm = getsessionhelper()['id'];
        
        $this->db->where('commercant_idcommercant', $idcomm);
        $tabac = $this->db->get('msgadmin');

        $this->db->where('commercant_idcommercant', $idcomm);
        $tabca = $this->db->get('msgcommadmin');
        
        
         $tab = array();
        $i = 0;

        foreach ($tabac->result_array() as $res) {
            $tab[$i] = array(
                'idmsg' => $res['idMsgAdmin'],
                'table' => 'msgadmin',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $i++;
        }

        $tab2 = array();
        $j = 0;

        foreach ($tabca->result_array() as $res) {
            $tab2[$j] = array(
                 'idmsg' => $res['idMsgCommAdmin'],
                 'table' => 'msgcommadmin',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $j++;
        }
        $tabmerge = array();
        $tabmerge = array_merge($tab, $tab2);
    
        return $tabmerge;

    }
    
     function reponsecommclient($idclient) {
        $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'client_idclient' => $idclient,
            'commercant_idcommercant' => getsessionhelper()['id']
        );
        $this->db->insert('msgcommclient', $data);
             // update notifaction client
        $this->db->where('idclient', $idclient);
        $this->db->set('notifmsg', "notifmsg+1", FALSE);
        $this->db->update('client');
    }
    
     function getmsgbyid_msgclient($idmsg) {
        $this->db->where('idMsgClient', $idmsg);
        return $this->db->get('msgclient');
    }
    
    function getmsgbyid_msgclientcomm($idmsg) {
        $this->db->where('idMsgCommClient', $idmsg);
        return $this->db->get('msgcommclient');
    }
    
    function ReponseCommAdmin($idcom)
    {
         $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'admin_idadmin' => 1,
            'commercant_idcommercant' => $idcom
        );
        $this->db->insert('msgcommadmin', $data);
        
    }
    
        //get list comm who sent msg to client
        function get_list_comm() {
        
        $id = getsessionhelper()['id'];
        $sql = "select DISTINCT  com.commercant_idcommercant, com.vue
                  from msgcommclient com
                  where com.client_idclient = '$id'";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get information about person (table personne)
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            $comm = $this->db->where('idcommercant', $res['commercant_idcommercant'])
                    ->get('commercant')
                    ->row();
            
            $pers = $this->db->where('idpersonne', $comm->personne_idpersonne)
                    ->get('personne')
                    ->row();
            $tableau[$i] = array('idp' => $pers->idpersonne, 'nom' => $pers->nom, 'prenom' => $pers->prenom, 'mail' => $pers->email, 'idcom' => $comm->idcommercant, 'vue' => $res['vue']);
            $i++;
        }
        return $tableau;
    }
    
      //get conversation between client and current commercant (msgclient, msgcommclient)
    function get_conv_for_client($idcom) {

        $clt = getsessionhelper()['id'];

        $this->db->where('client_idclient', $clt);
        $this->db->where('commercant_idcommercant', $idcom);
        $tabcl = $this->db->get('msgclient');

        $this->db->where('client_idclient', $clt);
        $this->db->where('commercant_idcommercant', $idcom);
        $tabcom = $this->db->get('msgcommclient');

        $tab = array();
        $i = 0;

        foreach ($tabcl->result_array() as $res) {
            $tab[$i] = array(
                'idmsg' => $res['idMsgClient'],
                'table' => 'msgclient',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $i++;
        }

        $tab2 = array();
        $j = 0;

        foreach ($tabcom->result_array() as $res) {
            $tab2[$j] = array(
                 'idmsg' => $res['idMsgCommClient'],
                 'table' => 'msgcommclient',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $j++;
        }
        $tabmerge = array();
        $tabmerge = array_merge($tab, $tab2);
    
        return $tabmerge;
    }
    
      function reponseclient($idcom) {
        $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'client_idclient' => getsessionhelper()['id'],
            'commercant_idcommercant' => $idcom
        );
        $this->db->insert('msgclient', $data);
    }
    
    // get list of comm to send msg from admin
        function getcomm() {
        return $this->db->get('commercant');
    }
    
       function msgadmincomm($idadmin) {
        $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'admin_idadmin' => 1,
            'commercant_idcommercant' => $this->input->post('com')
        );
        $this->db->insert('msgadmin', $data);
            // update notifaction comm
        $this->db->where('idcommercant', $this->input->post('com'));
        $this->db->set('notifmsg', "notifmsg+1", FALSE);
        $this->db->update('commercant');
    }
    
       function get_conv_admin($idcom)
    {
        
        
        $this->db->where('commercant_idcommercant', $idcom);
        $tabac = $this->db->get('msgadmin');

        $this->db->where('commercant_idcommercant', $idcom);
        $tabca = $this->db->get('msgcommadmin');
        
        
         $tab = array();
        $i = 0;

        foreach ($tabac->result_array() as $res) {
            $tab[$i] = array(
                'idmsg' => $res['idMsgAdmin'],
                'table' => 'msgadmin',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $i++;
        }

        $tab2 = array();
        $j = 0;

        foreach ($tabca->result_array() as $res) {
            $tab2[$j] = array(
                 'idmsg' => $res['idMsgCommAdmin'],
                 'table' => 'msgcommadmin',
                'sujet' => $res['sujet'],
                'contenu' => $res['contenu'],
                'dateenvoi' => $res['dateenvoi'],
                'vue' => $res['vue'],
                'idcom' => $res['commercant_idcommercant'],
            );
            $j++;
        }
        $tabmerge = array();
        $tabmerge = array_merge($tab, $tab2);
    
        return $tabmerge;

    }
    
     function getmsgbyid_msgadmin($idmsg) {
        $this->db->where('idMsgAdmin', $idmsg);
        return $this->db->get('msgadmin');
    }
    
    function getmsgbyid_msgadmincomm($idmsg) {
        $this->db->where('idMsgCommAdmin', $idmsg);
        return $this->db->get('msgcommadmin');
    }
    
     function ReponseAdmin($idcom)
    {
         $data = array('contenu' => $this->input->post('contenu'),
            'sujet' => $this->input->post('sujet'),
            'vue' => 0,
            'dateenvoi' => date("y-m-d H:i:s"),
            'admin_idadmin' => 1,
            'commercant_idcommercant' => $idcom
        );
        $this->db->insert('msgadmin', $data);
        
    }
    
           //get list comm who sent msg to admin
    function get_list_comm_admin() {
        
        $id = getsessionhelper()['id'];
        $sql = "select DISTINCT  ca.commercant_idcommercant, ca.vue
                  from msgcommadmin ca
                  ";
        $query = $this->db->query($sql);
        $tab = $query->result_array();

        //*************
        // get information about person (table personne)
        $tableau = array();
        $i = 0;
        foreach ($tab as $res) {
            $comm = $this->db->where('idcommercant', $res['commercant_idcommercant'])
                    ->get('commercant')
                    ->row();
            
            $pers = $this->db->where('idpersonne', $comm->personne_idpersonne)
                    ->get('personne')
                    ->row();
            $tableau[$i] = array('idp' => $pers->idpersonne, 'nom' => $pers->nom, 'prenom' => $pers->prenom, 'mail' => $pers->email, 'idcom' => $comm->idcommercant, 'vue' => $res['vue']);
            $i++;
        }
        return $tableau;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//    ////////***************
//    function get_login_client($idcom) {
//        $chaine = '';
//        $chainepers = '';
//
//        $sql = "select DISTINCT  client_idclient
//                      from msgclient m
//                      where m.commercant_idcommercant = '$idcom'
//                     
//                        ";
//        $query = $this->db->query($sql);
//        // print_r($query->result_array());
////                foreach ($query->result_array() as $row)  
////              {
////              $chaine .= $row.', ';
////                  
////              }
//        if ($query->num_rows > 1) {
//            foreach ($query->result_array() as $row) {
//
//
//                $chaine .= $row['client_idclient'] . ', ';
//            }
//            $chaine .= $chaine . '123445';
////              echo 'subsyr'. $chaine;
////              echo 'size = '. strlen($chaine);
////               $chaine = substr($chaine, 0, count($chaine));
////               echo 'chaine'. $chaine;
//        } elseif ($query->num_rows == 0) {
//            return FALSE;
//        } else {
//            foreach ($query->result_array() as $row) {
//                $chaine = $row['client_idclient'];
//            }
//        }
//
//        $sqlclient = "select personne_idpersonne
//                      from client c
//                      where c.idclient  IN ($chaine)
//                     
//                        ";
//        $queryclient = $this->db->query($sqlclient);
//
////               foreach ($queryclient->result_array() as $row)  
////             {
////             echo  $row['personne_idpersonne'];
////                 
////             }
//
//        if ($queryclient->num_rows > 1) {
//            foreach ($queryclient->result_array() as $row) {
//                $chainepers .= $row['personne_idpersonne'] . ', ';
//            }
//            $chainepers .= $chainepers . '123445';
//        } else {
//            foreach ($queryclient->result_array() as $row) {
//                $chainepers = $row['personne_idpersonne'];
//            }
//        }
//
//        $sqlloginclient = "select *
//                      from personne p
//                      where p.idpersonne  IN ($chainepers)
//                     
//                      ";
//        $queryloginclient = $this->db->query($sqlloginclient);
//
//
//
//
////                foreach ($queryloginclient->result_array() as $row)  
////             {
////             echo  $row['idpersonne'];
////                 
////             }
//
//        return $queryloginclient->result_array();
//
//
////           
////           
////           
//        // $query->result_array();
////               $tab = array();
////               $i = 0;
////              foreach ($query->result_array() as $row)  
////              {
////                  echo 'ttt'.$row['client_idclient'];
////                  $tab[$i] = $this->db->get('client');
////                             $this->db->where('idclient',$row['client_idclient']);
////                             $i++;
////                  
////              }
////              print_r($tab);
//        // return $tab;
//    }
//
//    function get_list_client_recu() {
//        $idcom = getsessionhelper()['id'];
//        $sql = "select DISTINCT  client_idclient
//                      from msgclient cl
//                      where cl.commercant_idcommercant = '$idcom'
//                     
//                        ";
//        $query = $this->db->query($sql);
//
//        $chaine = '';
//        $nb_resultat = 0;
//        $i = 1;
//        $numrows = $query->num_rows;
//        if ($numrows > 0) {
//            foreach ($query->result_array() as $row) {
//                if ($i < $numrows) {
//                    $chaine .= $row['client_idclient'] . ', ';
//                } else {
//                    $chaine .= $row['client_idclient'];
//                }
//                $i++;
//            }
//            return $chaine;
//        } else {
//            return $chaine;
//        }
//    }
//
//    function getidclient($id) {
//        $this->db->select('idclient');
//        $this->db->from('client');
//        $this->db->where('personne_idpersonne', $id);
//        $this->db->limit(1);
//        return $this->db->get();
//    }
//
//    function getmsgclient($idclient, $idcom) {
//        ////////::
////         $sql = "select cl.contenu, cl.sujet, comm.contenu, comm.sujet, 
////                      from msgclient cl, msgcommclient comm 
////                      where cl.client_idclient = '$idclient'
////                       AND  cl.commercant_idcommercant = '$idcom'
////                       AND  comm.client_idclient = '$idclient'
////                       AND comm.commercant_idcommercant = '$idcom'
////                     ORDER by cl.dateenvoi, comm.dateenvoi
////                     
////                        ";
////               $query = $this->db->query($sql);
////   
////                   return $query->result_array();
//        //////////
//
//        $this->db->order_by('dateenvoi', 'desc');
//        $this->db->where('client_idclient', $idclient);
//        $this->db->where('commercant_idcommercant', $idcom);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgclient');
//    }
//
////    function getmsgbyid($idmsg) {
////        $this->db->where('idMsgClient', $idmsg);
////        return $this->db->get('msgclient');
////    }
//
////    function reponsecommclient($idclient) {
////        $data = array('contenu' => $this->input->post('contenu'),
////            'sujet' => $this->input->post('sujet'),
////            'vue' => 0,
////            'dateenvoi' => date("y-m-d"),
////            'client_idclient' => $idclient,
////            'commercant_idcommercant' => getsessionhelper()['id']
////        );
////        $this->db->insert('msgcommclient', $data);
////    }
//
////    function get_list_client() {
////        $idcom = getsessionhelper()['id'];
////        $sql = "select DISTINCT  client_idclient
////                      from msgcommclient com
////                      where com.commercant_idcommercant = '$idcom'
////                     
////                        ";
////        $query = $this->db->query($sql);
////
////        $chaine = '';
////        $nb_resultat = 0;
////        $i = 1;
////        $numrows = $query->num_rows;
////        if ($numrows > 0) {
////            foreach ($query->result_array() as $row) {
////                if ($i < $numrows) {
////                    $chaine .= $row['client_idclient'] . ', ';
////                } else {
////                    $chaine .= $row['client_idclient'];
////                }
////                $i++;
////            }
////            return $chaine;
////        } else {
////            return $chaine;
////        }
////    }
//
//    function get_id_client_pers($chaine) {
//        $sql = "select personne_idpersonne
//                      from client c
//                      where c.idclient  IN ($chaine)
//                     
//                        ";
//        $query = $this->db->query($sql);
//
//        $chaine = '';
//        $nb_resultat = 0;
//        $i = 1;
//        $numrows = $query->num_rows;
//        if ($numrows > 0) {
//            foreach ($query->result_array() as $row) {
//                if ($i < $numrows) {
//                    $chaine .= $row['personne_idpersonne'] . ', ';
//                } else {
//                    $chaine .= $row['personne_idpersonne'];
//                }
//                $i++;
//            }
//
//            return $chaine;
//        } else {
//
//            return $chaine;
//        }
//    }
//
//    function get_login_pers($idpersonne_client) {
//        $sql = "select *
//                      from personne p
//                      where p.idpersonne  IN ($idpersonne_client)
//                     
//                      ";
//        $query = $this->db->query($sql);
//
//
//        return $query->result_array();
//    }
//
//    function getmsgclientsent($idclient, $idcom) {
//
//        $this->db->where('client_idclient', $idclient);
//        $this->db->where('commercant_idcommercant', $idcom);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgcommclient');
//    }
//
//    function getmsg_sent_byid($idmsg) {
//        $this->db->where('idMsgCommClient', $idmsg);
//        return $this->db->get('msgcommclient');
//    }
//

//

//
//    function get_list_comm_sent($idadmin) {
//        $sql = "select DISTINCT  commercant_idcommercant
//                      from msgadmin a
//                      where a.admin_idadmin = '$idadmin'
//                     
//                        ";
//        $query = $this->db->query($sql);
//
//        $chaine = '';
//        $nb_resultat = 0;
//        $i = 1;
//        $numrows = $query->num_rows;
//        if ($numrows > 0) {
//            foreach ($query->result_array() as $row) {
//                if ($i < $numrows) {
//                    $chaine .= $row['commercant_idcommercant'] . ', ';
//                } else {
//                    $chaine .= $row['commercant_idcommercant'];
//                }
//                $i++;
//            }
//            return $chaine;
//        } else {
//            return $chaine;
//        }
//    }
//
//    function get_id_comm_pers($chaine) {
//        $sql = "select personne_idpersonne
//                      from commercant c
//                      where c.idcommercant  IN ($chaine)
//                     
//                        ";
//        $query = $this->db->query($sql);
//
//        $chaine = '';
//        $nb_resultat = 0;
//        $i = 1;
//        $numrows = $query->num_rows;
//        if ($numrows > 0) {
//            foreach ($query->result_array() as $row) {
//                if ($i < $numrows) {
//                    $chaine .= $row['personne_idpersonne'] . ', ';
//                } else {
//                    $chaine .= $row['personne_idpersonne'];
//                }
//                $i++;
//            }
//
//            return $chaine;
//        } else {
//
//            return $chaine;
//        }
//    }
//
//    function getidcom($id) {
//        $this->db->select('idcommercant');
//        $this->db->from('commercant');
//        $this->db->where('personne_idpersonne', $id);
//        $this->db->limit(1);
//        return $this->db->get();
//    }
//
//    function getmsgcomm($idcomm, $idadmin) {
//        ////////::
////         $sql = "select cl.contenu, cl.sujet, comm.contenu, comm.sujet, 
////                      from msgclient cl, msgcommclient comm 
////                      where cl.client_idclient = '$idclient'
////                       AND  cl.commercant_idcommercant = '$idcom'
////                       AND  comm.client_idclient = '$idclient'
////                       AND comm.commercant_idcommercant = '$idcom'
////                     ORDER by cl.dateenvoi, comm.dateenvoi
////                     
////                        ";
////               $query = $this->db->query($sql);
////   
////                   return $query->result_array();
//        //////////
//
//        $this->db->order_by('dateenvoi', 'desc');
//        $this->db->where('admin_idadmin', $idadmin);
//        $this->db->where('commercant_idcommercant', $idcomm);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgadmin');
//    }
//
//    function getmsgcombyid($idmsg) {
//        $this->db->where('idMsgAdmin', $idmsg);
//        return $this->db->get('msgadmin');
//    }
//
//    function reponseadmincomm($idcom, $idadmin) {
//        $data = array('contenu' => $this->input->post('contenu'),
//            'sujet' => $this->input->post('sujet'),
//            'vue' => 0,
//            'dateenvoi' => date("y-m-d"),
//            'admin_idadmin' => $idadmin,
//            'commercant_idcommercant' => $idcom
//        );
//        $this->db->insert('msgadmin', $data);
//    }
//
//    function get_list_comm_recu($idadmin) {
//        $idcom = getsessionhelper()['id'];
//        $sql = "select DISTINCT  commercant_idcommercant
//                      from msgcommadmin a
//                      where a.admin_idadmin = '$idadmin'
//                     
//                        ";
//        $query = $this->db->query($sql);
//
//        $chaine = '';
//        $nb_resultat = 0;
//        $i = 1;
//        $numrows = $query->num_rows;
//        if ($numrows > 0) {
//            foreach ($query->result_array() as $row) {
//                if ($i < $numrows) {
//                    $chaine .= $row['commercant_idcommercant'] . ', ';
//                } else {
//                    $chaine .= $row['commercant_idcommercant'];
//                }
//                $i++;
//            }
//            return $chaine;
//        } else {
//            return $chaine;
//        }
//    }
//
//    function getmsgreceivedcom($idcomm, $idadmin) {
//
//
//        $this->db->where('commercant_idcommercant', $idcomm);
//        $this->db->where('admin_idadmin', $idadmin);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgcommadmin');
//    }
//
//    function getmsgcomreceiv_byid($idmsg) {
//        $this->db->where('idMsgCommAdmin', $idmsg);
//        return $this->db->get('msgcommadmin');
//    }
//
//    function getmsgadmin($idcom) {
//        $this->db->where('commercant_idcommercant', $idcom);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgadmin');
//    }
//
////    function reponsecommadmin($idcom, $idadmin) {
////        $data = array('contenu' => $this->input->post('contenu'),
////            'sujet' => $this->input->post('sujet'),
////            'vue' => 0,
////            'dateenvoi' => date("y-m-d"),
////            'admin_idadmin' => $idadmin,
////            'commercant_idcommercant' => $idcom
////        );
////        $this->db->insert('msgcommadmin', $data);
////    }
//
//    function getmsgadmin_sent($idcom) {
//        $this->db->where('commercant_idcommercant', $idcom);
//        $this->db->order_by('dateenvoi', 'desc');
//        return $this->db->get('msgcommadmin');
//    }

}

