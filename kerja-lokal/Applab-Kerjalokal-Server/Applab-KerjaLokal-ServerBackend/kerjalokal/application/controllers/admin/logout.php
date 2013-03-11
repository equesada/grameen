<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$_SESSION["username"] = "";
		$_SESSION["curr_country"] = "";
		$_SESSION["curr_country_name"] = "";
		$_SESSION["userlevel"] = "";
		$this->load->model('Madmin');
   }
   
	function index()
	{
		$this->template->write_view("content", "admin/login");
		$this->template->render();
	}
   
}
?>