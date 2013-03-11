<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobsend extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function plan($page=1, $date_send="")
	{
		$useraccess = array("superadmin", "admin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$date_send = $date_send == "" ? date("Y-m-d") : $date_send;
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;
			$json = CORE_URL."get_jobsends_by_date.php?tx_id=$tx_id&date_send=$date_send&ndata=$ndata&page=$page&order=log_id&ascdesc=desc";
			$data["jobsends"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/jobsend/plan/$page/$date_send";
			$this->template->write("header", 
			"
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/jquery.simpledialog.0.1.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/jquery.simpledialog.0.1.css\">

			<script>
				$(document).ready(function(){

					$('.log_detail').simpleDialog({
						height: 400,
						width: 600,
						showCloseLabel: false
					});
				});
			</script>		
			");
			$this->template->write("title", "Log Web");
			$this->template->write_view("content", "admin/jobsend_plan", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	
	function report($page=1)
	{
		$useraccess = array("superadmin", "admin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ndata = 20;
			$json = CORE_URL."get_logdms.php?ndata=$ndata&page=$page&order=log_id&ascdesc=desc";
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/log/dms/$page";
			$this->template->write("header", 
			"
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/jquery.simpledialog.0.1.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/jquery.simpledialog.0.1.css\">

			<script>
				$(document).ready(function(){

					$('.log_detail').simpleDialog({
						height: 400,
						width: 600,
						showCloseLabel: false
					});
				});
			</script>		
			");
			$this->template->write("title", "Log DMS");
			$this->template->write_view("content", "admin/log_dms_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	
	function detail($webdms="web", $id=1)
	{
		$json = CORE_URL."get_log_by_log_id.php?webdms=$webdms&country_id=".$_SESSION["curr_country"]."&id=$id";
		$data = $this->Madmin->get_data($json);
		$this->template->write_view("content", "admin/log_detail", $data, TRUE);
		$this->template->render();
	}

}
?>