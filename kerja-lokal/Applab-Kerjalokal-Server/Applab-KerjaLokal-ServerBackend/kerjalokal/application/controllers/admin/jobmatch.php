<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobmatch extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function manage($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->check_var();
			if (sizeof($_POST) > 0)
			{
				$activate = $this->input->post("activate");
				//echo "<pre>"; print_r($del); echo "</pre>";
				$ndelete = 0;
				if ($activate != "")
				{
					$json = CORE_URL."set_jobmatch.php?tx_id=$tx_id&id=$activate";
					//die($json);
					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>Job Matching has been set.</div>");
				}
				else
					$this->template->write("msg", "<div class=msg>No Job Matching has been set.</div>");

			}
			
			$ndata = 20;
			$json = CORE_URL."get_jobmatches.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=jobmatch_id&country_id=".$_SESSION["curr_country"];
			$data["jobmatches"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/jobmatch/add";
			$this->template->write("title", "Add Job Matching");
			$this->template->write_view("content", "admin/jobmatch_edit", $data);
			$this->template->write_view("content", "admin/jobmatch_manage", $data);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}

	
	function add()
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->check_var();
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				if ($this->form_validation->run() == TRUE)
				{
					$var = "";
					$data["status"] = "1";
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var .= "&country_id=".urlencode($_SESSION["curr_country"]);
					$var .= "&date_add=".urlencode(date("Y-m-d H:i:s"));
					$var .= "&date_update=".urlencode(date("Y-m-d H:i:s"));
					$var = substr($var,1);
					$json = CORE_URL."add_jobmatch.php?tx_id=$tx_id&$var";
					//echo $json;

					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Job Matching has been added.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			
			$ndata = 20;
			$json = CORE_URL."get_jobmatches.php?tx_id=$tx_id&ndata=$ndata&page=1&order=jobmatch_id&country_id=".$_SESSION["curr_country"];
			$data["jobmatches"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/jobmatch/add";
			$this->template->write("title", "Add Job Matching");
			$this->template->write_view("content", "admin/jobmatch_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		
		$this->template->render();
	}
	
	
	function edit($id=0)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->check_var();
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				if ($this->form_validation->run() == TRUE)
				{
					$var = "";
					$data["date_update"] = date("Y-m-d H:i:s");
					$data["status"] = "1";
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					
					$json = CORE_URL."update_jobmatch.php?tx_id=$tx_id&$var";
					//echo $json;
					$json = $this->Madmin->get_data($json);
					//echo "<pre>"; print_r($json); echo "</pre>";
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Job Matching has been updated.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			else
			{
				$data = CORE_URL."get_jobmatch_by_jobmatch_id.php?tx_id=$tx_id&jobmatch_id=$id";
				$data = $this->Madmin->get_data($data);
			}
			$data["form_submit"] = base_url()."admin/jobmatch/edit/$id";

			//echo "<pre>"; print_r($arr); echo "</pre>";

			$this->template->write("title", "Edit Job Mathching");
			$this->template->write_view("content", "admin/jobmatch_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	
	function check_var()
	{
		$jobmatch_id	= !is_null($this->input->post("jobmatch_id")) ? ($this->input->post("jobmatch_id")) : "";
		$title			= !is_null($this->input->post("title")) ? (strtolower($this->input->post("title"))) : "";
		$description	= !is_null($this->input->post("description")) ? ($this->input->post("description")) : "";
		$max_dis		= !is_null($this->input->post("max_dis")) ? ($this->input->post("max_dis")) : "";
		$max_nsend		= !is_null($this->input->post("max_nsend")) ? ($this->input->post("max_nsend")) : "";
		$dis			= !is_null($this->input->post("dis")) ? ($this->input->post("dis")) : "";
		$nsend			= !is_null($this->input->post("nsend")) ? ($this->input->post("nsend")) : "";
		$expired		= !is_null($this->input->post("expired")) ? ($this->input->post("expired")) : "";
		$status			= !is_null($this->input->post("status")) ? ($this->input->post("status")) : "";
		
		return array(
			"jobmatch_id" 	=> $jobmatch_id,
			"title" 		=> $title,
			"description" 	=> $description,
			"max_dis" 		=> $max_dis,
			"max_nsend"		=> $max_nsend,
			"dis" 			=> $dis,
			"nsend" 		=> $nsend,
			"expired" 		=> $expired,
			"status" 		=> $status
		);
	}
	
	
	function custom_err_msg($str)
	{
		if ($str == '0')
		{ $this->form_validation->set_message('custom_err_msg', 'Please choose a valid %s.'); return FALSE; }
		else
			return TRUE;
	}
	
	
	function check_form()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');
	}
	

}
?>