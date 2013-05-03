<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Note extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
        $this->load->model('note/note_model');
    }

    function index() {  }

    function savenote() {
        $note = $this->input->post('note');

        $idp = $this->input->post('id');
        $oldnote = $this->note_model->getnote($idp)->row()->note;
        $newnote = round(($note + $oldnote) / 2);
        if ($this->note_model->savenote($idp, $newnote))
            echo $newnote;
        else
            echo 'savenote erreur';
    }

}

?>