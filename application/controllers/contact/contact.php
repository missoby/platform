<?php

class Contact extends CI_Controller {

    private $shopping = array();

    function __construct() {
        parent::__construct();
        $this->load->model('inscription/inscription_model');
        $this->twig->addFunction('getsessionhelper');

        $this->shopping['content'] = $this->cart->contents();
        $this->shopping['total'] = $this->cart->total();
        $this->shopping['nbr'] =$this->cart->total_items();
    }

    function index() {
        $data['shopping'] = $this->shopping;
        $this->twig->render('contact/contact_view', $data);
    }
    
    function send()
    {
                $data['shopping'] = $this->shopping;
        $this->form_validation->set_rules('nom', 'nom', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('sujet', 'sujet', 'required|trim|xss_clean|max_length[45]');
        $this->form_validation->set_rules('message', 'message', 'required|trim|xss_clean|max_length[100]');               
        $this->form_validation->set_rules('email', 'email', 'required|trim|xss_clean|valid_email|max_length[60]');

   if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
           $data['shopping'] = $this->shopping;
        $this->twig->render('contact/contact_view', $data);
        } else {
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

            $this->email->from($this->input->post('email'), 'Client');
            $this->email->to('lovelymarwa@live.fr');

            $this->email->subject($this->input->post('sujet'));
            $this->email->message($this->input->post('message'));
            
            if (!$this->email->send())
                show_error($this->email->print_debugger());
            else
            $this->twig->render('contact/contact_view', $data);        
            }
        
        
        }


}

?>