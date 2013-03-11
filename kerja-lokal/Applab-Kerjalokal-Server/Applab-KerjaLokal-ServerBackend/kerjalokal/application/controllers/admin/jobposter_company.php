<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobposter_company extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
		$this->load->helper(array('captcha','url'));
	}
   
	function add()
	{
		$useraccess = array("superadmin", "admin", "company");
		$number = null;
		$title = "Create Job Poster";
		$tx_id = $this->Madmin->get_uuid(current_url());
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
					
			$data = $this->check_var();
			//print_r($data);
			
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				
				//verifikasi untuk captcha
				$key=substr($_SESSION['key'],0,5);
				$number = $_REQUEST['number'];
						
				if ($this->form_validation->run() == TRUE)
				{
					if($number!=$key)
					{
						$this->template->write("msg", "<div class=msg>Verifikasi string not valid! Please try again!</div>");
					}else{
						$var = "";
						foreach ($data as $key => $value)
						{ $var .= "&".$key."=".urlencode($value); }
						$var .= "&country_id=".urlencode($_SESSION["curr_country"]);
						$var .= "&date_add=".urlencode(date("Y-m-d H:i:s"));
						$var = substr($var,1);
						$json = CORE_URL."add_jobposter_company.php?tx_id=$tx_id&$var";
						//die($json);

						$json = $this->Madmin->get_data($json);
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
							$this->template->write("msg", "<div class=msg>1 Job Poster has been registered.</div>");
						
						foreach ($data as $key => $value)
						{ $data[$key] = ""; }
					}	
				}
			}
			
			
			$companies = CORE_URL."get_companies.php?tx_id=$tx_id&country_id=".$_SESSION["curr_country"]."&order=company_name";
			$data["companies"] = $this->Madmin->get_data($companies);

			$data['form_action'] = base_url()."admin/jobposter_company/add";
			

			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobposter_company_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	
	function manage($page = 1, $order="a_username", $status="", $username="", $phone="", $mobile="", $userlevel="", $email="", $position="", $date_add="")
	{
		$useraccess = array("superadmin", "admin", "company");
		$title = "Manage Job Poster";
		$tx_id = $this->Madmin->get_uuid(current_url());
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			if (sizeof($_POST) > 0)
			{
				$search["status"] = $this->input->post("status") != "" ? $_POST["status"] : "_";
				$search["username"] = $this->input->post("username") != "" ? $_POST["username"] : "_";
				$search["phone"] = $this->input->post("phone") != "" ? $_POST["phone"] : "_";
				$search["mobile"] = $this->input->post("mobile") != "" ? $_POST["mobile"] : "_";
				$search["userlevel"] = $this->input->post("userlevel") != "" ? $_POST["userlevel"] : "_";
				$search["email"] = $this->input->post("email") != "" ? $_POST["email"] : "_";
				$search["position"] = $this->input->post("position") != "" ? $_POST["position"] : "_";
				$search["date_add"] = $this->input->post("date_add") != "" ? $_POST["date_add"] : "_";
				
				if (($search["status"] != "_") || ($search["username"] != "_") || ($search["phone"] != "_") || ($search["mobile"] != "_") || ($search["userlevel"] != "_") || ($search["email"] != "_") || ($search["position"] != "_") || ($search["date_add"] != "_")) 

					redirect(base_url()."admin/jobposter_company/manage/$page/$order/".urlencode($search["status"])."/".urlencode($search["username"])."/".urlencode($search["phone"])."/".urlencode($search["mobile"])."/".urlencode($search["userlevel"])."/".urlencode($search["email"])."/".urlencode($search["position"])."/".urlencode($search["date_add"]));
			}
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			
			$ndata = 20;
			
			$search["status"] = $status;
			$search["username"] = $username;
			$search["phone"] = $phone;
			$search["mobile"] = $mobile;
			$search["userlevel"] = $userlevel;
			$search["email"] = $email;
			$search["position"] = $position;
			$search["date_add"] = $date_add;
			
			
			if (($search["status"] != "_") || ($search["username"] != "_") || ($search["phone"] != "_") || ($search["mobile"] != "_") || ($search["userlevel"] != "_") || ($search["email"] != "_") || ($search["position"] != "_") || ($search["date_add"] != "_")) 
			{
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$json = CORE_URL."get_jobposters.php?tx_id=$tx_id"."$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";
			}
			else
			{
				$json = CORE_URL."get_jobposters_by_jobposter_id.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
			}
			$data = $this->Madmin->get_data($json);
			
			$data["list_status"] = array(
				"0" => "ALL",
				"1" => "Active",
				"2" => "Inactive"
			);
			$data["status"] = $status;
			//$data["form_submit"] = base_url()."admin/jobpost/manage/1/$order";
			$data["form_submit"] = current_url();

			$data["page"] = $page;
			
			$data["search"] = $search;
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["search_link"] = base_url()."admin/jobposter_company/search_form/".$search["status"]."/";
			$this->template->write("header", "
			
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/jquery.simpledialog.0.1.js\"></script>
			<!-- <link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/jquery.simpledialog.0.1.css\"> -->
			
			<style>
			.sd_container{
			 font-family: arial,helvetica,sans-serif; margin:0; padding: 0px; position: absolute; background-color: #fff; border: solid 5px #ccc; text-align:center;
			}
			.sd_header{
			 font-size: 125%; font-weight:bold; margin-bottom: 10px;
			}
			.sd_content{}
			.sd_footer{
			 color: #a0a0a0; margin-top: 10px;
			}
			.sd_overlay{
			 position: absolute; margin:0; padding: 0; top: 0; left: 0; background-color: #222;
			}
			.sd_loading{
			 background: url('".base_url()."images/indicator.gif') center no-repeat; background-color: #fff; height: 60px; width: 60px;
			}
			.sd_closelabel{
			 position:absolute; width:50px; height:22px; line-height:22px; top:0; left:0; padding:5px; text-align:center; background-color:#ccc;
			}
			.sd_closelabel a {
			 text-decoration:none; color: #222; font-size: 12px;
			 font-weight:bold;
			}
			</style>

			<script>
				$(document).ready(function(){

					$('#search_form').simpleDialog({
						height: 250,
						width: 350,
						showCloseLabel: false
					});
				});
			</script>		
			
			");
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobposter_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
			
	}
	
	function search_form($status=1)
	{
		$data["status"] = $status;
		$this->load->view("admin/jobposter_searchform", $data);
	}

	function edit($id=0)
	{
		$useraccess = array("superadmin", "admin", "company");
		$title = "Edit Job Poster";
		$tx_id = $this->Madmin->get_uuid(current_url());
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$data = $this->check_var();
			
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				if ($this->form_validation->run() == TRUE)
				{
					$var = "";
					$data["date_update"] = date("Y-m-d H:i:s");
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					
					$json = CORE_URL."update_jobposter.php?$var";
					$json = $this->Madmin->get_data($json);
					//echo $json;
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Job Poster has been updated.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			else
			{
				$data = CORE_URL."get_jobposter_by_jobposter_id.php?id=$id";
				$data = $this->Madmin->get_data($data);
			}
			$companies = CORE_URL."get_companies.php?country_id=".$_SESSION["curr_country"]."&order=company_name";
			$data["companies"] = $this->Madmin->get_data($companies);

			$data['form_action'] = base_url()."admin/jobposter_company/edit/$id";

			//echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobposter_company_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function save_csv($order="a_username", $filter_key="", $filter_value="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$title = "Save Job Poster as CSV";
		$tx_id = $this->Madmin->get_uuid(current_url());
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobposter.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
				
			if (($filter_key != "") && ($filter_value != "")) 
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&order=$orderby&ascdesc=$ascdesc&id=0";
			}
			else
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&order=$orderby&ascdesc=$ascdesc&id=0";
			}
			$data = $this->Madmin->get_data($json);
			$output = "ID, Comp ID, Username, Password , Userlevel, Email, Status, Date Add, Date Update, Country ID, N_fail\n";
			foreach($data["results"] as $job)
			{
				$output .= $job["jobposter_id"].",".$job["comp_id"].",".$job["username"].",".$job["password"].",".$job["userlevel"].",".$job["email"].",";
				switch ($job["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}
				$output .= ",".$job["date_add"].",".$job["date_update"].",".$job["country_id"].",".$job["n_fail"]."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	
	function check_var()
	{
		$comp_id		= !is_null($this->input->post("comp_id")) ? ($this->input->post("comp_id")) : "";
		$username		= !is_null($this->input->post("username")) ? (strtolower($this->input->post("username"))) : "";
		$phone			= !is_null($this->input->post("phone")) ? (strtolower($this->input->post("phone"))) : "";
		$mobile			= !is_null($this->input->post("mobile")) ? (strtolower($this->input->post("mobile"))) : "";
		$email			= !is_null($this->input->post("email")) ? ($this->input->post("email")) : "";
		$position		= !is_null($this->input->post("position")) ? ($this->input->post("position")) : "";
		$password		= !is_null($this->input->post("password")) ? ($this->input->post("password")) : "";
		$jobposter_id	= !is_null($this->input->post("jobposter_id")) ? ($this->input->post("jobposter_id")) : "";
		$userlevel		= !is_null($this->input->post("userlevel")) ? ($this->input->post("userlevel")) : "";
		$status			= !is_null($this->input->post("status")) ? ($this->input->post("status")) : "";
		//captcha
		//$number			= !is_null($this->input->post("number")) ? ($this->input->post("number")) : "";
		
		return array(
			"comp_id" 		=> $comp_id,
			"username" 		=> $username,
			"phone" 		=> $phone,
			"mobile" 		=> $mobile,
			"email" 		=> $email,
			"position" 		=> $position,
			"password" 		=> $password,
			"jobposter_id" 	=> $jobposter_id,
			"userlevel" 	=> $userlevel,
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
		if ($_SESSION["userlevel"] != "superadmin")
			$this->form_validation->set_rules('comp_id', 'Company', 'callback_custom_err_msg');
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('userlevel', 'User Level', 'callback_custom_err_msg');
		$this->form_validation->set_rules('status', 'Status', 'callback_custom_err_msg');
	}
	

}
?>
