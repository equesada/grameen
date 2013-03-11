<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Country extends CI_Controller {

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
			//$data = $this->check_var();
			if (sizeof($_POST) > 0)
			{
				$activate = $this->input->post("activate");
				//echo "<pre>"; print_r($del); echo "</pre>";
				$ndelete = 0;
				if ($activate != "")
				{
					$json = CORE_URL."set_curr_country.php?tx_id=$tx_id&id=$activate";
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

			$data["form_submit"] = base_url()."admin/country/add";
			$this->template->write("title", "Manage Internationalization");
			$this->template->write_view("content", "admin/country_manage", $data);
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
					$data["date_add"] = date("Y-m-d H:i:s");
					$data["status"] = "1";
					$json = CORE_URL."add_country.php?tx_id=$tx_id&country_id=".urlencode($data["country_id"])."&country_name=".urlencode($data["country_name"])."&date_add=".urlencode($data["date_add"])."&status=".$data["status"];
					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "1")
					{
						unset($data["country_name"]);
						unset($data["list_format_date"]);
						unset($data["list_format_time"]);
						unset($data["list_format_number"]);
						
						foreach ($data as $key => $value)
						{ $var .= "&".$key."=".urlencode($value); }
						$var = substr($var,1);
						
						$json = CORE_URL."add_country_setting.php?tx_id=$tx_id&$var";
						//echo $json;
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
							$this->template->write("msg", "<div class=msg>1 country has been added.</div>");
					}
					else
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
					$data = $this->check_var();
				}
			}
			$data["form_submit"] = base_url()."admin/country/add";
//			echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", "Add Country");
			$this->template->write_view("content", "admin/country_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	
	function edit($country_id=0)
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
					$json = CORE_URL."update_country.php?tx_id=$tx_id&country_id=".$data["country_id"]."&country_name=".$data["country_name"]."&date_update=".$data["date_update"]."&status=".$data["status"];
					$json = $this->Madmin->get_data($json);
					
					unset($data["country_name"]);
					unset($data["list_format_date"]);
					unset($data["list_format_time"]);
					unset($data["list_format_number"]);
					//echo "<pre>"; print_r($data); echo "</pre>";
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					
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
					$data = $this->check_var();
				}
			}
			else
			{
				$json = CORE_URL."get_country_setting_by_country_id.php?tx_id=$tx_id&country_id=$country_id";
				$json = $this->Madmin->get_data($json);
				foreach($json as $key => $value)
				{ $data[$key] = $value; }
				
			}
			$data["form_submit"] = base_url()."admin/country/edit/$country_id";
			
			//echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", "Edit Country");
			$this->template->write_view("content", "admin/country_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	
	function check_var()
	{
		$list_format_date = array(
			'n/j/Y' => "m/d/yy (6/30/2001)",
			'm/d/Y' => "mm/dd/yy (06/30/2001)",
			'j/n/Y' => "d/m/yy (30/6/2001)",
			'd/m/Y' => "dd/mm/yy (30/06/2001)",
			'd-M-Y' => "dd-Mmm-yyyy (30-June-2001)",
			'F j, Y' => "June 30, 2001"
		);
		$list_format_time = array(
			'H:m' => "hh:mm (13:30)",
			'g:m A' => "h:mm AM/PM (1:30 PM)"
		);
		$list_format_number = array(
			'1' => "9.999,99",
			'2' => "9,999.99"
		);
	
		$country_id			= !is_null($this->input->post("country_id")) ? ($this->input->post("country_id")) : "";
		$country_name		= !is_null($this->input->post("country_name")) ? ($this->input->post("country_name")) : "";
		$format_date		= !is_null($this->input->post("format_date")) ? ($this->input->post("format_date")) : "";
		$format_time		= !is_null($this->input->post("format_time")) ? ($this->input->post("format_time")) : "";
		$format_number		= !is_null($this->input->post("format_number")) ? ($this->input->post("format_number")) : "";
		$format_currency	= !is_null($this->input->post("format_currency")) ? ($this->input->post("format_currency")) : "";
		$lang				= !is_null($this->input->post("lang")) ? ($this->input->post("lang")) : "";
		$status				= !is_null($this->input->post("status")) ? ($this->input->post("status")) : "";
		
		return array(
			"country_id" 		=> $country_id,
			"country_name" 		=> $country_name,
			"format_date" 		=> $format_date,
			"format_time"		=> $format_time,
			"format_number" 	=> $format_number,
			"format_currency" 	=> $format_currency,
			"lang" 				=> $lang,
			"status" 			=> $status,
			"list_format_date" 	=> $list_format_date,
			"list_format_time" 	=> $list_format_time,
			"list_format_number" => $list_format_number
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
		$this->form_validation->set_rules('country_id', 'ID', 'required');
		$this->form_validation->set_rules('country_name', 'Country Name', 'required');
		$this->form_validation->set_rules('format_currency', 'Currency', 'required');
	}
	

}
?>