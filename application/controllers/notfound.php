<?php
class notfound extends CI_Controller
{
	public function __construct()
	{
	        parent::__construct();
	}

	public function index()
	{
		
		$this->load->view('home_page');//loading in my template
	}
}
?>