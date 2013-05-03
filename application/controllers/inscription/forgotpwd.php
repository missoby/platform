<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forgotpwd extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->load->model('inscription/forgotpwd_model');
        $this->twig->addFunction('getsessionhelper');

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function mail()
    {
        $data['shopping'] = $this->shopping;
        $this->twig->render('login/mailforgpwd_view', $data);
    }

    function verifmail()
    {   
        $data['shopping'] = $this->shopping;
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');
        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) // validation hasn't been passed
            $this->twig->render('login/mailforgpwd_view', $data);

        else {
            // verifier si le mail existe dans la base
            $email = $this->input->post('email');
            $resultat = $this->forgotpwd_model->checkemail($email);
            if ($resultat->num_rows == 0) { // mail inexistant
                $this->twig->render('login/mailforgpwd_view', $data);
                return false;
            } else {
                foreach ($resultat->result_array() as $row) {
                    $id = $row['idpersonne'];
                }
                $pwdcrypt = sha1($id . 'azertymedecine9513mgan');  //pwd crypté

                if ($this->forgotpwd_model->savepwd($pwdcrypt, $id) == TRUE) {
                    //envoi mail
                    $config = Array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'ssl://smtp.googlemail.com',
                        'smtp_port' => 465,
                        'smtp_user' => 'cmarwabriki@gmail.com',
                        'smtp_pass' => 'marwa26041989'
                    );

                    $this->load->library('email', $config);
                    $this->email->set_newline("\r\n");

                    $this->email->from('lovelymarwa@live.fr', 'e-commerce');
                    $this->email->to($email);
                    $this->email->subject('Mot de passe oubliée');
                    $this->email->set_mailtype("html");
                    $this->email->message('Pour ajouter Votre mot de passe, cliquez sur ce' . anchor('inscription/forgotpwd/resetpwd/' . $pwdcrypt, ' Lien'));

                    if ($this->email->send()) {
                        $this->twig->render('login/succesSendfrgtmail_view', $data);
                    } else {
                        show_error($this->email->print_debugger());
                    }
                }
            }
        }
    }

    function resetpwd() {
        $code = $this->uri->segment(4);
        //requète de recherche du code apartir de la base
        $res = $this->forgotpwd_model->existcode($code);
        if ($res->num_rows == 0) { // code inexistant
            $this->twig->render('login/login_view', $this->shopping);
            return false;
        } else {
            foreach ($res->result_array() as $row) {
                $id = $row['idpersonne'];
            }

            $resinsert = $this->forgotpwd_model->rmztemp($code);

            if ($resinsert) { // afficher form du new mot de passe  
                $data = array(
                    'code1' => $id,
                    'shopping' => $this->shopping
                );
                $this->twig->render('login/newpwd_view', $data);
            } else {
                $data = array(
                    'msg' => 'echec remise a zero',
                    'shopping' => $this->shopping
                );
                $this->twig->render('login/login_view', $data);
                return false;
            }
        }
    }

    function insertpwd($code1) {
        $data['shopping'] = $this->shopping;
        $id = $code1;
        $this->form_validation->set_rules('npwd', 'Nouveau mot de passe', 'required|trim|xss_clean|max_length[15]|matches[conpwd]');
        $this->form_validation->set_rules('conpwd', 'Confirmation mot de passe', 'trim|required');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            $this->twig->render('login/newpwd_view', $data);
        } else {
            $pwd = sha1($this->input->post('npwd'));
            $form_data = array('pwd' => $pwd,);


            if ($this->forgotpwd_model->updatepwd($form_data, $id) == TRUE) {
                // mise a jour avec succes
                $this->twig->render('login/succesNewfgrpwd_view', $data);
                echo 'mise a jour avec succes';
            } else {
                $this->twig->render('login/EchecNewfgrpwd_view', $data);
                echo 'erreur';
            }
        }
    }

}

?>
