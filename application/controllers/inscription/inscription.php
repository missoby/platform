<?php

class Inscription extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->load->model('inscription/inscription_model');
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('produit/produit_model', '', TRUE);

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] = $this->cart->total_items();
    }

    function index() {
        $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';


        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        $data['shopping'] = $this->shopping;
        $this->twig->render('accueilinscri_view', $data);
    }

    function inscriptionClient() {
        //validation des champs du formulaire
        $this->form_validation->set_rules('errortotal', '', 'callback_verifemail');
        $this->form_validation->set_rules('nom', 'nom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('prenom', 'prenom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('civilite', 'civilite', 'trim|xss_clean|max_length[10]');
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');
        $this->form_validation->set_rules('login', 'login', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('pwd', 'mot de passe', 'required|trim|xss_clean|max_length[15]|matches[confpwd]');
        $this->form_validation->set_rules('confpwd', 'Confirmation mot de passe', 'trim|required');
        $this->form_validation->set_rules('adresse', 'adresse', 'required|trim|xss_clean|max_length[60]');
        $this->form_validation->set_rules('pays', 'pays', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('ville', 'ville', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('tel', 'Numéro telephone', 'required|trim|xss_clean|max_length[45]|integer');

        $this->form_validation->set_error_delimiters('<span class="error" name="errortotal">', '</span>');
        $data['shopping'] = $this->shopping;
        $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';


        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            $this->twig->render('client/inscription_view', $data);
        } else {
            $link = base_url('/inscription/inscription/confirmation') . '/' . sha1($this->input->post('email') . 'XkI85BtF');
            //send email
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'cmarwabriki@gmail.com',
                'smtp_pass' => 'marwa26041989',
                'mailtype' => 'html'
            );

            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");

            $this->email->from('cmarwabriki@gmail.com', 'e-commerce');
            $this->email->to($this->input->post('email'));

            $this->email->subject(' Activer Votre compte ');
            $this->email->message('Pour Activer Votre compte, cliquez sur ce ' . anchor($link, 'lien'));

            if (!$this->email->send())
                show_error($this->email->print_debugger());
            else
                $this->twig->render('successInscri_view', $data);
        }
    }

    function verifemail() {
        if (!$this->inscription_model->SaveForm()) {
            $this->form_validation->set_message('verifemail', 'Le nom utilisateur ou email choisi existe deja');
            return FALSE;
        } else
            return TRUE;
    }

    function inscriptionCommercant() {
        // validation du formulaire
        $this->form_validation->set_rules('errortotal', '', 'callback_verifemailcomm');
        $this->form_validation->set_rules('nom', 'nom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('prenom', 'prenom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('civilite', 'civilite', 'trim|xss_clean|max_length[10]');
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');
        $this->form_validation->set_rules('login', 'login', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('pwd', 'mot de passe', 'required|trim|xss_clean|max_length[15]|matches[confpwd]');
        $this->form_validation->set_rules('confpwd', 'Confirmation mot de passe', 'trim|required');
        $this->form_validation->set_rules('adresse', 'adresse', 'required|trim|xss_clean|max_length[60]');
        $this->form_validation->set_rules('pays', 'pays', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('societe', 'societe', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('ville', 'ville', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('tel', 'Numéro telephone', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('adrsoc', 'Adresse Societe', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('descsoc', 'Description Societe', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('siteweb', 'Site Web', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('telpro', ' Telephone professionnel', 'required|trim|xss_clean|max_length[45]|integer');
        $this->form_validation->set_rules('fax', 'Fax', 'required|trim|xss_clean|max_length[45]|integer');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
        $enscom = $this->produit_model->getcommercant();
        $data['comm'] = $enscom;
        $data['pathphoto'] = site_url() . 'uploads/';


        $ensproduitdate = $this->produit_model->get_product_by_date();
        $data['produitdate'] = $ensproduitdate;
        $data['comm'] = $enscom;
        $data['shopping'] = $this->shopping;
        if ($this->form_validation->run() == FALSE) {
            // validation hasn't been passed
            $this->twig->render('commercant/inscriptioncomm_view', $data);
        } else {

            $link = base_url('inscription/inscription/confirmation') . '/' . sha1($this->input->post('email') . 'XkI85BtF');
            //send email
            $config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_port' => 465,
                'smtp_user' => 'cmarwabriki@gmail.com',
                'smtp_pass' => 'marwa26041989',
                'mailtype' => 'html'
            );

            $this->load->library('email', $config);
            $this->email->set_newline("\r\n");

            $this->email->from('cmarwabriki@gmail.com', 'e-commerce');
            $this->email->to($this->input->post('email'));

            $this->email->subject(' Activer Votre compte ');
            $this->email->message('Pour Activer Votre compte, cliquez sur ce ' . anchor($link, 'lien'));

            if (!$this->email->send())
                show_error($this->email->print_debugger());
            else
                $this->twig->render('successInscri_view', $data);
        }
    }

    function verifemailcomm() {
        if (!$this->inscription_model->SaveFormcomm()) {
            $this->form_validation->set_message('verifemailcomm', 'Le nom utilisateur ou email choisi existe deja');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function confirmation($link) {
        $resultats = $this->inscription_model->get_all_desabled();
        $data['shopping'] = $this->shopping;
        foreach ($resultats as $res) {
            if (sha1($res->email . 'XkI85BtF') == $link) {
                $this->inscription_model->update_activer($res->idpersonne);
                $this->twig->render('successInscri_view', $data);
                break;
            }
        }
    }

    function NewsLetter() {

        $this->inscription_model->NewsLetter();
        redirect('home_page');
    }

}

?>