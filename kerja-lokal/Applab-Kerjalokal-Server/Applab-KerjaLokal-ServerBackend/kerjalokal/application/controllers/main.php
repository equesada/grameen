<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		/* NEW FOR PAGING*/
		$this->load->helper('url'); //load helper URL untuk memanggil function base_url()		
        $this->load->library('pagination');	//load library pagination

		$this->load->model('Mmain');
	}


	function index($a=0)
	{
		//$this->template->write_view('header', 'themes/theme4/inc/header');		
		//$this->template->write_view('footer', 'themes/theme4/inc/footer');
		
		$result = file_get_contents(CORE_URL."get_theme_current.php");
		$result = json_decode($result, true);
		
		$this->template->write_view('header', 'themes/'.$result["folder"].'/inc/header');		
		$this->template->write_view('footer', 'themes/'.$result["folder"].'/inc/footer');
		
		
		//$this->template->write_view('content', 'themes/theme4/inc/mainmenu', FALSE, 'themes/theme4/inc/mainmenu/posts');
		$this->template->write_view('content', 'themes/'.$result["folder"].'/inc/mainmenu', FALSE, 'themes/'.$result["folder"].'/inc/mainmenu/posts');

		$this->template->render();

	}
	
	
	function index2($a=0)
	{
		$this->template->write("content", "henry 2");
		$this->template->render();
		//echo "<pre>";print_r($this->template->regions);echo "</pre>";
		
	}
	
	function index3($a=0)
	{
		$this->template->write("content", "Yudha");
		$this->template->render();
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */