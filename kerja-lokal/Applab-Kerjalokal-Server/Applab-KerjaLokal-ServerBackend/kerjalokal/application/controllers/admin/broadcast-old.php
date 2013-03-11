<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Broadcast extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function manage_email($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			//$data = $this->check_var();
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
						$this->template->write("msg", "<div class=msg>Country has been set.</div>");
				}
				else
					$this->template->write("msg", "<div class=msg>No Country has been set.</div>");

			}
			
			$ndata = 20;
			$json = CORE_URL."get_country_setting.php?tx_id=$tx_id&ndata=$ndata&page=$page";
			$data["countries"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/email/add";
			$this->template->write("title", "Manage Email Broadcast");
			$this->template->write_view("content", "admin/email_manage", $data);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}

	
	function add_email()
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->email_check_var();
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
					$json = CORE_URL."add_broadcast_email.php?tx_id=$tx_id&$var";
					//echo $json;

					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 broadcast email has been added.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			$data["form_submit"] = base_url()."admin/broadcast/add_email";
			$this->template->write("title", "Broadcast e-mail");
			$this->template->write_view("content", "admin/broadcast_email_edit", $data, TRUE);
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
					
					$json = CORE_URL."update_country.php?tx_id=$tx_id&id=".$data["country_id"]."&country_name=".$data["country_name"]."&date_update=".$data["date_update"]."&status=".$data["status"];
					$json = $this->Madmin->get_data($json);
					
					unset($data["country_name"]);
					$json = CORE_URL."update_country_setting.php?tx_id=$tx_id&$var";
					//echo $json;
					$json = $this->Madmin->get_data($json);
					//echo "<pre>"; print_r($json); echo "</pre>";
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 country has been updated.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			else
			{
				$data = CORE_URL."get_country_setting_by_country_id.php?tx_id=$tx_id&country_id=$id";
				$data = $this->Madmin->get_data($data);
			}
			$data["form_submit"] = base_url()."admin/country/edit/$id";

			//echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", "Edit Country");
			$this->template->write_view("content", "admin/country_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	
	function email_check_var()
	{
		$email_id		= !is_null($this->input->post("sender_name")) ? ($this->input->post("sender_name")) : "";
		$sender_name	= !is_null($this->input->post("sender_name")) ? ($this->input->post("sender_name")) : "";
		$sender_email	= !is_null($this->input->post("sender_email")) ? (strtolower($this->input->post("sender_email"))) : "";
		$title			= !is_null($this->input->post("title")) ? ($this->input->post("title")) : "";
		$msg			= !is_null($this->input->post("msg")) ? ($this->input->post("msg")) : "";
		$email_type		= !is_null($this->input->post("email_type")) ? ($this->input->post("email_type")) : "";
		$email			= !is_null($this->input->post("email")) ? ($this->input->post("email")) : "";
		
		return array(
			"email_id" 		=> $sender_name,
			"sender_name" 	=> $sender_name,
			"sender_email"	=> $sender_email,
			"title" 		=> $title,
			"msg"			=> $msg,
			"email_type" 	=> $email_type,
			"email" 		=> $email
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