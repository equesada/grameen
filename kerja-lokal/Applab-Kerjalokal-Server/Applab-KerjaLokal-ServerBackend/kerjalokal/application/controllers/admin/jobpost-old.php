<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobpost extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function add($comp_id=0, $job_id=0)
	{   //echo $comp_id;
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Add Job Post";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			$data = $this->check_var();
			
			if (sizeof($_POST) > 0)
			{
				//echo "<pre>"; print_r($data); echo "</pre>";
				$this->check_form($data["region"]);
				
				$var = "";
				if ($this->form_validation->run() == TRUE)
				{					
					//if ($data["aksi"]=='asdraft' || $data["aksi"]=='asubmit') {						
					if ($data["aksi"]=='asdraft' || $data["aksi"]=='asubmit' || $data["aksi"]=='approve') {		//NEW YUDHA				
						
						if ($data["aksi"]=='asubmit') {		
							$data["status"] = "4";
						} else if ($data["aksi"]=='approve') {
							$data["status"] = "1";
							$data["approver_id"] = $_SESSION["userid"];
							$data["approved_date"] = date("Y-m-d H:i:s");
						} else if ($data["aksi"]=='inactive') {
								$data["status"] = "2";								
						} else if ($data["aksi"]=='asdraft') {
								$data["status"] = "3";								
						} else {
							$data["status"] = $data["status"] == "" ? "4" : $data["status"];						
						}
						
						// by HENRY -- untuk sementara $data["status"] = "1";
						//$data["status"] = "1";
							
						$data["revision"] = "0";
						//$data["approver_id"] = null;
						$data["date_add"] = date("Y-m-d H:i:s");
						//$data["date_active"] = $data["status"] == "1" ? $data["date_add"] : "";
						$data["date_active"] = $data["date_add"];
						if ($data["date_active"] != "")
						{
							if ($data["expire_days"] != "0")
								$data["date_expired"] = date("Y-m-d", (strtotime($data["date_add"])) + ($data["expire_days"] * 86400)); 
							else
								$data["date_expired"] = date("Y-m-d", (strtotime($data["date_add"])) + (30 * 86400)); 
						}
						else
							$data["date_expired"] = "";
						unset($data["expire_days"]);
						
						unset($data["region"]);
						//unset($data["comp_id"]);
						unset($data["province"]);
						unset($data["kotamadya"]);
						unset($data["kecamatan"]);
						unset($data["kelurahan"]);
						unset($data["zip"]);						
						$aksi=$data["aksi"];
						unset($data["aksi"]);
						foreach ($data as $key => $value)
						{ 
							if ($key == "pos_lat") $key = "lat";
							if ($key == "pos_lng") $key = "lng";
							$var .= "&".$key."=".urlencode($value); 
						}
						$var .= "&tx_id=$tx_id";	
						$var .= "&country_id=".$_SESSION["curr_country"];
						$var = substr($var,1);
						//echo "<pre>"; print_r($var); echo "</pre>";
						
						$json = CORE_URL."add_jobpost.php?$var";
						//echo $json;
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
							$this->template->write("msg", "<div class=msg>1 Job Post has been added.</div>");
						
						$data["region"] = "";
						//$data["comp_id"] = "";
						$data["province"] = "";
						$data["kotamadya"] = "";
						$data["kecamatan"] = "";
						$data["kelurahan"] = "";
						$data["zip"] = "";
						$data["expire_days"] = "";
						foreach ($data as $key => $value)
						{ $data[$key] = ""; }
					}
				}
				//else
					//{ echo "<pre>"; print_r($_POST); echo "</pre>"; echo validation_errors(); }
				
			}
			
			/*
			if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
				switch ($data["status"])
				{
					case 1 : $data["jobstatus"] = "Active"; break;
					case 2 : $data["jobstatus"] = "Inactive"; break;
					case 3 : $data["jobstatus"] = "Draft"; break;
					case 4 : $data["jobstatus"] = "Waiting for Approval"; break;
					default : $data["jobstatus"] = "Waiting for Approval";
				}
			else
				$data["status"] == "0" ? "3" : "4";
			*/
			
			foreach ($this->get_lists($tx_id) as $key => $value)
			{ $data[$key] = $value; }
			
			if ($data["kelurahan"] != "0")
			{
				//$data["region"] = 1;
				//$data["zip"] = $data["kelurahan"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
				$data["kecamatan"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
				$data["kotamadya"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kotamadya']);
				$data["province"] = $json["parent_id"];
				
				$data["kotamadyas"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["province"]);
				$data["kecamatans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kotamadya"]);
				$data["kelurahans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
			}
			else if ($data["zip"] != "0")
			{
				//$data["region"] = 1;
				$data["kelurahan"] = $data["zip"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
				$data["kecamatan"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
				$data["kotamadya"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kotamadya']);
				$data["province"] = $json["parent_id"];
				
				$data["kotamadyas"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["province"]);
				$data["kecamatans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kotamadya"]);
				$data["kelurahans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
			}
			else
			{
				$data["region"] = 1;
				$data["kelurahan"] = "";
				$data["zip"] = 0;
				$data["loc_id"] = 0;
				$data["kecamatan"] = "";
				$data["kotamadya"] = "";
				$data["province"] = "";
				$data["kotamadyas"] = "";
				$data["kecamatans"] = "";
				$data["kelurahans"] = "";
			}
			//echo "<pre>"; print_r($data); echo "</pre>";
			$aksinya= isset($data["aksi"]) ? $data["aksi"]:$aksi;
			if ($aksinya=='asnext' && $this->form_validation->run() == TRUE) {				
				$data["status"] = "3";
				$data["form_submit"] = base_url()."admin/jobpost/add";
				$this->create_header($data["lat"],$data["lng"], $data["kelurahan"]);
				//echo "<pre>"; print_r($data); echo "</pre>";
				$this->template->write_view("content", "admin/jobpost_view", $data);
				$this->template->render();
			} else {

			if ($job_id != "0")
			{
				$json = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$job_id";
				//echo $json;
				$data["job"] = $this->Madmin->get_data($json);

				foreach ($data["job"] as $key => $value)
				{ 
					if ($key == "pos_lat") $key = "lat";
					if ($key == "pos_lng") $key = "lng";
					$data[$key] = $value; 
				}
				unset($data["job"]);
				unset($data["job_id"]);
				
				$data["kelurahan"] = $data["loc_id"];
				/*
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
				if (($data["lat"] == "0") || ($data["lat"] == ""))
				{
					$data["lat"] = $json["loc_lat"];
					$data["lng"] = $json["loc_lng"];
				}
				*/
				$data["lat"] = 0;
				$data["lng"] = 0;
				$data["kecamatan"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
				$data["kotamadya"] = $json["parent_id"];
				$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kotamadya']);
				$data["province"] = $json["parent_id"];
				
				$data["kotamadyas"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["province"]);
				$data["kecamatans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kotamadya"]);
				$data["kelurahans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
			}
			if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
			{
				$companies = CORE_URL."get_companies.php?order=company_name&tx_id=$tx_id&country_id=".$_SESSION["curr_country"];				
                $companies = $this->Madmin->get_data($companies);
				$data["companies"] = $companies["results"];
                
                if ($comp_id != "0")
				{
					$comp = CORE_URL."get_company_by_comp_id.php?tx_id=$tx_id&id=$comp_id";
					
					$comp = $this->Madmin->get_data($comp);
					$data["comp_id"] = $comp_id;
					$data["comp_name"] = substr($comp["name"],0,30);
					$data["comp_cp"] = substr($comp["cp"],0,30);
					$data["comp_tel"] = substr($comp["tel"],0,30);
					$data["comp_fax"] = $comp["fax"];
					$data["comp_email"] = $comp["email"];
				}
				/*
                foreach ($data["companies"] as $a)
            	{ 
            	   //$company_list[$a["comp_id"]] = $a["company_name"]; 
                   $comp_id = $a["comp_id"];
                   echo $comp_id;
                   return;
                   $comp_name = $a["company_name"];
                   $comp_cp = substr($a["cp"],0,10);       
                   $comp_tel = substr($a["tel"],0,10);
                   $comp_fax = $a["fax"];
                   $comp_email = $a["email"];
                          
                }
                */				
			}
			else if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
			{
				$company = CORE_URL."get_company_by_comp_id.php?id=".$_SESSION["comp_id"]."&tx_id=$tx_id&country_id=".$_SESSION["curr_country"];
				$company = $this->Madmin->get_data($company);
				$data["company_name"] = $company["name"];                                                
				
                if (!(($data["comp_name"] != "") ||($data["comp_cp"] != "") ||($data["comp_tel"] != "") ||($data["comp_fax"] != "") ||($data["comp_email"] != "")))
				{
					$data["comp_name"] = substr($company["name"],0,30);
					$data["comp_cp"] = substr($company["cp"],0,30);
					$data["comp_tel"] = substr($company["tel"],0,30);
					$data["comp_fax"] = $company["fax"];
					$data["comp_email"] = $company["email"];
				}
                                
			}
			
			
			$data["form_submit"] = base_url()."admin/jobpost/add";
			$this->create_header($data["lat"],$data["lng"], $data["kelurahan"]);

			$this->template->write("title", $title);
				$this->template->write_view("content", "admin/jobpost_new", $data);
				$this->template->render();
			}
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}


	function view($id=0)
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "View Job Post";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$is_allowed = 0;
			$data = $this->check_var();
			if ($id != 0)
			{
				$data["job_id"] = $id;
				$json = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$id";
				//echo $json;
				$data["job"] = $this->Madmin->get_data($json);

				if ($_SESSION["userlevel"] == "jobposter")
				{ if ($data["job"]["jobposter_id"] == $_SESSION["userid"]) $is_allowed = 1; }
				else if ($_SESSION["userlevel"] == "company")
				{
					$json = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$id";
					$json = $this->Madmin->get_data($json);
					$jobposter_id = $json["jobposter_id"];
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&id=$jobposter_id";
					$json = $this->Madmin->get_data($json);
					$job_comp_id = $json["comp_id"];
					$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&id=".$_SESSION["userid"];
					$json = $this->Madmin->get_data($json);
					$my_comp_id = $json["comp_id"];
					if ($job_comp_id == $my_comp_id) $is_allowed = 1;
				}
				else if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
					$is_allowed = 1;
				
				if ($is_allowed == 1)
				{
					if (sizeof($_POST) > 0)
					{
						$this->check_form($data["region"]);
						if (isSet($_POST["del"]))
						{
							$json = CORE_URL."delete_jobpost.php?tx_id=$tx_id&id=".$_POST["del"];
							//echo $json."<br>";
							$json = $this->Madmin->get_data($json);
							if ($json["status"] == "0")
								$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
							else
								$this->template->write("msg", "<div class=msg>1 Job post has been deleted.</div>");
						}
						else
						{
							if ($this->form_validation->run() == TRUE)
							{
								unset($data["job"]);
								unset($data["region"]);
								//unset($data["comp_id"]);
								unset($data["province"]);
								unset($data["kotamadya"]);
								unset($data["kecamatan"]);
								unset($data["kelurahan"]);
								unset($data["zip"]);

								$data["status"] = $data["status"] == "0" ? "4" : $data["status"];
								$data["revision"]++;
								$data["date_update"] = date("Y-m-d H:i:s");
								if ($data["status"] == "1")
								{
									if (($data["date_active"] == "") || ($data["date_active"] == "0000-00-00"))
									{
										$data["date_active"] = $data["date_update"];
										if ($data["expire_days"] != "0")
											$data["date_expired"] = date("Y-m-d", (time() + ($data["expire_days"] * 86400))); 
										else
											$data["date_expired"] = date("Y-m-d", (time() + (30 * 86400))); 
									}
									else
									{
										if ($data["expire_days"] != "0")
											$data["date_expired"] = date("Y-m-d", (strtotime($data["date_add"])) + ($data["expire_days"] * 86400)); 
										else
											$data["date_expired"] = date("Y-m-d", (strtotime($data["date_add"])) + (30 * 86400)); 
									}
								}
								unset($data["expire_days"]);
								
								$var = "";
								foreach ($data as $key => $value)
								{ 
									if ($key == "pos_lat") $key = "lat";
									if ($key == "pos_lng") $key = "lng";
									$var .= "&".$key."=".urlencode($value); 
								}
								$var .= "&tx_id=$tx_id";
								$var = substr($var,1);
								
								$json = CORE_URL."update_jobpost.php?$var";
								//echo $json."<br>";
								
								$json = $this->Madmin->get_data($json);
								if ($json["status"] == "0")
									$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
								else
									$this->template->write("msg", "<div class=msg>1 Job post has been updated.</div>");
								
								$data["region"] = "";
								//$data["comp_id"] = "";
								$data["province"] = "";
								$data["kotamadya"] = "";
								$data["kecamatan"] = "";
								$data["kelurahan"] = "";
								$data["zip"] = "";
								$data["expire_days"] = "";
								foreach ($data as $key => $value)
								{ $data[$key] = ""; }
							}
						}
					}
					if (isset($data["job"]))
					{
						foreach ($data["job"] as $key => $value)
						{ 
							if ($key == "pos_lat") $key = "lat";
							if ($key == "pos_lng") $key = "lng";
							$data[$key] = $value; 
						}
						unset($data["job"]);
					}
					
					switch ($data["gender"])
					{
						case "M" : $data["gender"] = "Male Only"; break;
						case "F" : $data["gender"] = "Female Only"; break;
						case "1" : $data["gender"] = "Male or Female"; break;
					}
					
					switch ($data["jobtype_id"])
					{
						case "1" : $data["jobtype_id"] = "Full Time"; break;
						case "2" : $data["jobtype_id"] = "Part Time"; break;
					}
					

					if ($data["loc_id"] != "0")
					{
						$data["kelurahan"] = $data["loc_id"];
						$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
						if (($data["lat"] == "0") || ($data["lat"] == ""))
						{
							$data["lat"] = $json["loc_lat"];
							$data["lng"] = $json["loc_lng"];
						}
						$data["kelurahan"] = $json["name"];
						$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$json["parent_id"]);
						$data["kecamatan"] = $json["name"];
						$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$json["parent_id"]);
						$data["kotamadya"] = $json["name"];
						$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$json["parent_id"]);
						$data["province"] = $json["name"];
					}
					
					if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
					{
						$companies = CORE_URL."get_companies.php?tx_id=$tx_id&order=company_name";
						$companies = $this->Madmin->get_data($companies);
						$data["companies"] = $companies["results"];
						
					}
					else if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					{
						$company = CORE_URL."get_company_by_comp_id.php?tx_id=$tx_id&id=".$_SESSION["comp_id"];
						$company = $this->Madmin->get_data($company);
						$data["company_name"] = $company["name"];
						$data["comp_name"] = substr($company["name"],0,30);
						$data["comp_cp"] = substr($company["cp"],0,30);
						$data["comp_tel"] = substr($company["tel"],0,30);
						$data["comp_fax"] = $company["fax"];
						$data["comp_email"] = $company["email"];
					}
					
					foreach ($this->get_lists($tx_id) as $key => $value)
					{ $data[$key] = $value; }
					foreach ($data["educations"] as $education)
					{ if ($data["edu_min"] == $education["edu_id"]) $data["edu_min"] = $education["edu_title"]; }
					foreach ($data["jobcats"] as $jobcat)
					{ if ($data["jobcat_id"] == $jobcat["jobcat_id"]) $data["jobcat_id"] = $jobcat["jobcat_title"]; }
					unset ($data["educations"]);
					unset ($data["jobcats"]);
					unset ($data["provinces"]);
					unset ($data["zips"]);
					
					
					if ($data["date_active"] != "")
						$data["expire_days"] = floor((strtotime($data["date_expired"]) - strtotime(date("Y-m-d", strtotime($data["date_add"])))) / 86400);
						
					$data["form_submit"] = base_url()."admin/jobpost/edit/$id";
					
					$this->create_header($data["lat"],$data["lng"], $data["loc_id"]);
					//echo "<pre>"; print_r($data); echo "</pre>";
			//add ali
			if($data["status"]=='4') {
				$data["reject_button"] = base_url()."admin/jobpost/reject_form/".$data["job_id"]."/".$data["jobposter_id"]."/";
				$this->template->write("header", "
				
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

						$('#reject_form').simpleDialog({
							height: 200,
							width: 300,
							showCloseLabel: false
						});
					});
				</script>					
				"); 
			}
			
			//NEW YUDHA
			if($data["status"]=='1') {
				$data["reject_button_status"] = base_url()."admin/jobpost/reject_form_status/".$data["job_id"]."/".$data["jobposter_id"]."/";
				$this->template->write("header", "
				
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

						$('#reject_form_status').simpleDialog({
							height: 200,
							width: 300,
							showCloseLabel: false
						});
					});
				</script>					
				"); 
			}
			
//
					$this->template->write_view("content", "admin/jobpost_view", $data);
					$data["form_type"] = "edit_preview";
					$this->template->render();
				
				}
					else
					{ $this->template->write("msg", "<div class=msg>You are not allowed to preview the Job Post</div>"); }
				
				$this->template->write("title", $title);
			}
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function manage($page=1, $order="a_job_title", $default_row=20, $status="", $jobcat_id="", $loc_title="", $comp_id="", $job_title="",  $submit_date="", $approved_date="", $approver_id="", $jobposter_id="", $loc_id="", $date_expired="")	
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Job Post";
		$search_uri 							= '';
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = (!empty($default_row) || !empty($search["default_row"])) ? $default_row : 20;
			
			if (sizeof($_POST) > 0)
			{				
				$submit_date 				= $this->input->post('submit_date');
				$approved_date 				= $this->input->post('approved_date');
				$location_id	 				= $this->input->post('loc_id');
				
				$search["status"] 				= $this->input->post("status") != 0 ? $_POST["status"] : "";
				$search["jobcat_id"] 			= $this->input->post("jobcat_id") != null ? $_POST["jobcat_id"] : "";	//DEFAULT null
				$search["loc_title"] 			= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "";	//DEFAULT null				
				$search["comp_id"] 				= $this->input->post("comp_id") != "" ? $_POST["comp_id"] : "";
				$search["job_title"] 			= $this->input->post("job_title") != "" ? $_POST["job_title"] : "";				
				$search["submit_date"] 			= ($submit_date['from_date'] && $submit_date['to_date'] != "") ? implode(':',$_POST["submit_date"]) : "";
				$search["approved_date"] 		= ($approved_date['from_date'] && $approved_date['to_date'] != "") ? implode(':',$_POST["approved_date"]) : "";
				$search["approver_id"] 			= $this->input->post("approver_id") != "" ? $_POST["approver_id"] : "";
				$search["jobposter_id"] 		= $this->input->post("jobposter_id") != "" ? $_POST["jobposter_id"] : "";
				$search["loc_id"] 				= $this->input->post("loc_id") != "" ? $_POST["loc_id"] : "";				
				$search["date_expired"] 		= $this->input->post("date_expired") != "" ? $_POST["date_expired"] : "";	
				
			} else {
				
				$search["status"] 					= (!empty($_GET['status'])) ? $status : $status;
				$search["jobcat_id"] 				= (!empty($_GET['jobcat_id'])) ? $jobcat_id : $jobcat_id;
				$search["loc_title"] 				= (!empty($_GET['loc_title'])) ? $loc_title : $loc_title;					
				$search["comp_id"] 					= (!empty($_GET['comp_id'])) ? $comp_id : $comp_id;
				$search["job_title"] 				= (!empty($_GET['job_title'])) ? $job_title : $job_title;				
				$search["submit_date"] 				= ($submit_date == null) ? "" : $submit_date;
				$search["approved_date"] 			= ($approved_date == null) ? "" : $approved_date;
				$search["approver_id"] 				= (!empty($_GET['approver_id'])) ? $approver_id : $approver_id;
				$search["jobposter_id"] 				= (!empty($_GET['jobposter_id'])) ? $jobposter_id : $jobposter_id;
				$search["loc_id"] 					= (!empty($_GET['loc_id'])) ? $loc_id : $loc_id;	
				$search["date_expired"] 			= (!empty($_GET['date_expired'])) ? $date_expired : $date_expired;	
				
			}
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			
			$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;
			echo "<!-- ".$json." -->";
			//echo $json;
			$data = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($search); echo "</pre>";
			
			if ($data):
			$i = 0;
			foreach ($data['results'] as $row):
				//$data['results'][$i]['approver_id'] = !empty($data['results'][$i]['approver_id']) ? $this->get_approver_name($tx_id, $row['approver_id']) : '--';
				$data['results'][$i]['approver_name'] = !empty($data['results'][$i]['approver_name']) ? $data['results'][$i]['approver_name'] : '--';
				$data['results'][$i]['approved_date'] = !empty($data['results'][$i]['approved_date']) ? $data['results'][$i]['approved_date'] : '--';
				$data['results'][$i]['status_id'] = !empty($data['results'][$i]['status_id']) ? $this->get_jobstatus_list($tx_id, $row['status_id']) : '';
				$i++;
			endforeach;
			endif;
			if ($data) $data = $data;
			
			$data["list_status"] = array(
				"0" => "ALL",
				"1" => "Active",
				"2" => "Inactive",
				"3" => "Draft",
				"4" => "Waiting for Approval",
				"5" => "Rejected"
			);
			
			$data["status"] = $status;			
			$data["form_submit"] = base_url()."admin/jobpost/manage/1/d_job_id";
			$data["page"] = $page;
			$data["search"] = $search;
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			//echo "<pre>"; print_r($data); echo "</pre>";
			//$data["search_link"] = base_url()."admin/jobpost/search_form/".$search["status"]."/";
			$this->template->write("header", "
			
			<script src=\"".base_url()."js/jquery-1.6.2.min.js\"></script>
			<script src=\"".base_url()."js/jquery.simpledialog.0.1.js\"></script>
			<!-- <link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/jquery.simpledialog.0.1.css\"> -->
			<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".base_url()."css/jquery-ui-1.8.16.custom.css\">
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
			<!--
			<script>
				$(document).ready(function(){
					$('#search_form').simpleDialog({
						height: 250,
						width: 450,
						showCloseLabel: false
					});
					$('.interested').simpleDialog({
						height: 600,
						width: 450,
						showCloseLabel: false
					});
				});
			</script>		
			-->
			");
			
			$data["result_row"] = array(20 => 20, 50 => 50, 90 => 90);
			$data["default_row"] = (!empty($_POST['result_row'])) ? $_POST['result_row'] : $ndata;			
			$data['jobcats'] = $this->search_form_adv($search['status'],$search['jobcat_id'],$search['comp_id'],$search['job_title'],$search['jobposter_id'],$search['approver_id']);
			$data['jobcats']['search'] = $data['search'];
			
			$lokasi["kotamadya_id"] = $this->input->post("kotamadya_id") != 0 ? $_POST["kotamadya_id"] : "";
				$lokasi["kecamatan_id"] = $this->input->post("kecamatan_id") != 0 ? $_POST["kecamatan_id"] : "";
				$lokasi["kelurahan_id"] = $this->input->post("kelurahan_id") != 0 ? $_POST["kelurahan_id"] : "";
				$data["lokasi"] = $lokasi;	
			$data['jobcats']['lokasi'] = $data['lokasi'];	
			
			$data['jobcats']["form_submit"] = base_url()."admin/jobpost/manage/1/d_job_id";
			$data['search_form'] = $this->load->view('admin/jobpost_searchform_adv',$data['jobcats'],TRUE);
			$this->template->write("title", $title);
			//print_r($data);
			if($this->session->flashdata('msg')) $this->template->write("msg", "<div class=msg>".$this->session->flashdata('msg')."</div>");
			$this->template->write_view("content", "admin/jobpost_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function str_csv($str)
	{
		return str_replace(",", ".", $str);
	}
			
	function save_csv($order="a_job_title", $status="", $jobcat_id="", $loc_title="", $comp_id="", $job_title="",  $submit_date="", $approved_date="", $approver_id="", $jobposter_id="", $loc_id="", $date_expired="")	
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Job Post as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{			

			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobpost.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			//$ndata = 0; 
			$search["status"] = $status;
			$search["jobcat_id"] = $jobcat_id;
			$search["loc_title"] = $loc_title;			
			$search["comp_id"] = $comp_id;
			$search["job_title"] = $job_title;			
			$search["submit_date"] = $submit_date;
			$search["approved_date"] = $approved_date;
			$search["approver_id"] = $approver_id;
			$search["jobposter_id"] = $jobposter_id;
			$search["loc_id"] = $loc_id;
			$search["date_expired"] = $date_expired;  
			

			$search_uri = "";
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			//if (($search["status"] != "_") || ($search["jobcat_id"] != "_") || ($search["loc_title"] != "_") || ($search["comp_id"] != "_") || ($search["job_title"] != "_") || ($search["submit_date"] != "_") || ($search["approved_date"] != "_") || ($search["approver_id"] != "_") || ($search["jobposter_id"] != "_") || ($search["loc_id"] != "_") || ($search["date_expired"] != "_")) 
			{
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id"."$var&ndata=0&page=0&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];					
				else
					$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id"."$var&ndata=0&page=0&order=$orderby&ascdesc=$ascdesc&id=0";					
			}
			/*
			else
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id&ndata=0&page=0&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];					
				else
					$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id&ndata=0&page=0&order=$orderby&ascdesc=$ascdesc&id=0";					
			}
			*/
			//$json = CORE_URL."get_jobposts_by_search.php?tx_id=$tx_id&ndata=0&page=0&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;			
			//echo $json;
			$data = $this->Madmin->get_data($json);			
			
			//$output = "ID, Title, Company, Category, Status, Post Date, Location, # sent\n";			
			$output = "Job ID,Status,Create Date,Create by,Approved by,Approved date,Category,Province,Kotamadya,Kecamatan,Kelurahan,Title,Company,# sent,# interested\n";
			foreach($data["results"] as $job)
			{
				//echo "<pre>"; print_r($job); echo "</pre><hr>";
				$output .= $job["job_id"].",";
				switch ($job["job_status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " "; 
				}
				$output .= ",".$job["date_add"].",".$job["username"].",".$job["approver_name"].",".$job["approved_date"].",".$job["jobcat_title"].
					",".$job["province_name"].",".$job["kotamadya_name"].",".$job["kecamatan_name"].",".$job["loc_title"].",".$job["job_title"].
					",".$job["comp_name"].",".$job["n_send"].",".$job["n_applied"]."\n";
				
				/*
				$output .= $this->str_csv($job["job_id"]).",";
					
				switch ($job["job_status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}		
				$output .= ", ";
				$output .= 
					$this->str_csv($job["jobcat_id"]).",".					
					$this->str_csv($job["loc_title"]).",".										
					$this->str_csv($job["comp_id"]).",".
					$this->str_csv($job["job_title"]).",".															
					$this->str_csv($job["submit_date"]).",".					
					$this->str_csv($job["approved_date"]).",".
					$this->str_csv($job["approver_id"]).",". //$this->str_csv($job["loc_title"]).",".					
					$this->str_csv($job["jobposter_id"]).",".
					$this->str_csv($job["loc_id"]).",".		
					$this->str_csv($job["date_expired"])."\n";							
				*/	
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}

	function get_approver_name($tx_id, $id) {
		$json = CORE_URL."get_approver_by_approver_id.php?tx_id=$tx_id&approver_id=$id";
		//echo $json."<hr>";
		$approver_name = $this->Madmin->get_data($json);
		return ($approver_name['username']);
	}
	
	function get_jobstatus_list($tx_id, $stat_id) {
		$json = CORE_URL."get_all_jobstatus_by_statusid.php?tx_id=$tx_id&stat_id=$stat_id";
		$job_status = $this->Madmin->get_data($json);
		//print_r($job_status['title']);
		return ($job_status['title']);
	}
	
	function edit($id=1)
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Edit Job Post";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$is_allowed = 0;
			$data = $this->check_var();
			$data["job_id"] = $id;
			$json = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$id";
			//echo $json;
			$data["job"] = $this->Madmin->get_data($json);

			if ($_SESSION["userlevel"] == "jobposter")
			{ if ($data["job"]["jobposter_id"] == $_SESSION["userid"]) $is_allowed = 1; }
			else if ($_SESSION["userlevel"] == "company")
			{
				$json = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$id";
				$json = $this->Madmin->get_data($json);
				$jobposter_id = $json["jobposter_id"];
				$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&id=$jobposter_id";
				$json = $this->Madmin->get_data($json);
				$job_comp_id = $json["comp_id"];
				$json = CORE_URL."get_jobposter_by_jobposter_id.php?tx_id=$tx_id&id=".$_SESSION["userid"];
				$json = $this->Madmin->get_data($json);
				$my_comp_id = $json["comp_id"];
				if ($job_comp_id == $my_comp_id) $is_allowed = 1;
			}
			else if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
				$is_allowed = 1;
			if ($is_allowed == 1)
			{
				if (sizeof($_POST) > 0)
				{
					$this->check_form($data["region"]);
					//if (isSet($_POST["del"]))
					if ($data["aksi"]=='ashapus')					
					{
						//$json = CORE_URL."delete_jobpost.php?tx_id=$tx_id&id=".$_POST["del"];
						$json = CORE_URL."delete_jobpost.php?tx_id=$tx_id&id=$id";
						//echo $json."<br>";
						$json = $this->Madmin->get_data($json);
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
							$this->template->write("msg", "<div class=msg>1 Job post has been deleted.</div>");
					}
					else if ($data["aksi"]=='asnext')
					{
						foreach ($this->get_lists($tx_id) as $key => $value)
						{ $data[$key] = $value; }

						if ($data["loc_id"] != "0")
						{
							$data["kelurahan"] = $data["loc_id"];
							$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
							/*
							if (($data["lat"] == "0") || ($data["lat"] == ""))
							{
								$data["lat"] = $json["loc_lat"];
								$data["lng"] = $json["loc_lng"];
							}
							*/
							$data["kecamatan"] = $json["parent_id"];
							$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
							$data["kotamadya"] = $json["parent_id"];
							$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kotamadya']);
							$data["province"] = $json["parent_id"];
							
							$data["kotamadyas"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["province"]);
							$data["kecamatans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kotamadya"]);
							$data["kelurahans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
						}

						$data["form_submit"] = base_url()."admin/jobpost/edit/$id";
						$title = "View Job Post";
						$this->template->write_view("content", "admin/jobpost_view", $data);
					}
					else
					{
						if ($this->form_validation->run() == TRUE)
						{
							unset($data["job"]);
							unset($data["region"]);
							//unset($data["comp_id"]);
							unset($data["province"]);
							unset($data["kotamadya"]);
							unset($data["kecamatan"]);
							unset($data["kelurahan"]);
							unset($data["zip"]);
							$aksi=$data["aksi"];
							unset($data["aksi"]);
							//echo "<pre>"; print_r($data); echo "</pre><hr>";exit;
							if ($aksi=='asubmit') {
								$data["status"] = "4";
							} else if ($aksi=='approve') {
								$data["status"] = "1";
							} else if ($aksi=='inactive') {
								$data["status"] = "2";								
							} else {
								$data["status"] = $data["status"] == "" ? "4" : $data["status"];
								
								// by henry - Untuk sementara $data["status"] diset menjadi 1
								//$data["status"] = "1";
							}
							$data["revision"]++;
							$data["date_update"] = date("Y-m-d H:i:s");							
							//if ($data["status"] == "1")
							{
								if (($data["date_active"] == "") || ($data["date_active"] == "0000-00-00"))
								{
									$data["date_active"] = $data["date_update"];
									if ($data["expire_days"] != "0")
										$data["date_expired"] = date("Y-m-d", (time() + ($data["expire_days"] * 86400))); 
									else
										$data["date_expired"] = date("Y-m-d", (time() + (30 * 86400))); 
								}
								else
								{
									if ($data["expire_days"] != "0")
										$data["date_expired"] = date("Y-m-d", (strtotime($data["date_active"])) + ($data["expire_days"] * 86400)); 
									else
										$data["date_expired"] = date("Y-m-d", (strtotime($data["date_active"])) + (30 * 86400)); 
								}
							}
							unset($data["expire_days"]);
							//echo "<pre>"; print_r($data); echo "</pre>";
							$var = "";
							foreach ($data as $key => $value)
							{ 
								if ($key == "pos_lat") $key = "lat";
								if ($key == "pos_lng") $key = "lng";
								$var .= "&".$key."=".urlencode($value); 
							}
							$var .= "&tx_id=$tx_id";
							$var = substr($var,1);
							
							if($aksi=='approve') {
								$var = "status=$data[status]&job_id=$data[job_id]";
								$var .= "&date_active=".urlencode($data["date_active"]);
								$var .= "&date_update=".urlencode($data["date_update"]);
								$var .= "&approved_date=".urlencode(date("Y-m-d H:i:s"));
								$var .= "&approver_id=$data[jobposter_id]";
								$var .= "&date_expired=".urlencode(date("Y-m-d", (strtotime($data["date_active"])) + (30 * 86400)));
								$var .= "&tx_id=$tx_id";
								//echo "<pre>$aksi"; print_r($var); echo "atas</pre>";
								$json = CORE_URL."update_jobpost.php?$var";
							} else if ($aksi=='inactive') {
								$var = "status=$data[status]&job_id=$data[job_id]";
								$var .= "&date_update=".urlencode($data["date_update"]);
								$var .= "&tx_id=$tx_id";
								//echo "<pre>$aksi"; print_r($var); echo "atas</pre>";
								$json = CORE_URL."update_jobpost.php?$var";
							} else {
								//echo "<pre>$aksi"; print_r($var); echo "bawah</pre>";
								$json = CORE_URL."update_jobpost.php?$var";
							}
							
							$json = CORE_URL."update_jobpost.php?$var";
							$json = $this->Madmin->get_data($json);
							if ($json["status"] == "0")
								$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
							else
								$this->template->write("msg", "<div class=msg>1 Job post has been updated.</div>");
							
							$data["region"] = "";
							//$data["comp_id"] = "";
							$data["province"] = "";
							$data["kotamadya"] = "";
							$data["kecamatan"] = "";
							$data["kelurahan"] = "";
							$data["zip"] = "";
							$data["expire_days"] = "";
							foreach ($data as $key => $value)
							{ $data[$key] = ""; }
						}
					}
				}
				if (isset($data["job"]))
				{
					foreach ($data["job"] as $key => $value)
					{ 
						if ($key == "pos_lat") $key = "lat";
						if ($key == "pos_lng") $key = "lng";
						$data[$key] = $value; 
					}
					unset($data["job"]);
				}
				
				if ($data["loc_id"] != "0")
				{
					$data["kelurahan"] = $data["loc_id"];
					$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kelurahan']);
					/*
					if (($data["lat"] == "0") || ($data["lat"] == ""))
					{
						$data["lat"] = $json["loc_lat"];
						$data["lng"] = $json["loc_lng"];
					}
					*/
					$data["kecamatan"] = $json["parent_id"];
					$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
					$data["kotamadya"] = $json["parent_id"];
					$json = $this->Madmin->get_data(CORE_URL."get_location_by_loc_id.php?tx_id=$tx_id&id=".$data['kotamadya']);
					$data["province"] = $json["parent_id"];
					
					$data["kotamadyas"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["province"]);
					$data["kecamatans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kotamadya"]);
					$data["kelurahans"] = $this->Madmin->get_data(CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=".$data["kecamatan"]);
				}
				else
				{
					$data["region"] = 1;
					$data["kelurahan"] = "";
					$data["zip"] = 0;
					$data["loc_id"] = 0;
					$data["kecamatan"] = "";
					$data["kotamadya"] = "";
					$data["province"] = "";
					$data["kotamadyas"] = "";
					$data["kecamatans"] = "";
					$data["kelurahans"] = "";
				}
				
				if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
				{
					$companies = CORE_URL."get_companies.php?tx_id=$tx_id&order=company_name";
					$companies = $this->Madmin->get_data($companies);
					$data["companies"] = $companies["results"];
					
				}
				else if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
				{
					$company = CORE_URL."get_company_by_comp_id.php?tx_id=$tx_id&id=".$_SESSION["comp_id"];
					$company = $this->Madmin->get_data($company);
					$data["company_name"] = $company["name"];
					$data["comp_name"] = substr($company["name"],0,30);
					$data["comp_cp"] = substr($company["cp"],0,30);
					$data["comp_tel"] = substr($company["tel"],0,30);
					$data["comp_fax"] = $company["fax"];
					$data["comp_email"] = $company["email"];
				}
				
				foreach ($this->get_lists($tx_id) as $key => $value)
				{ $data[$key] = $value; }
				
				
				
				$tglAwal = date('Y-m-d h:i:s', strtotime($data["date_expired"]));
				$tglAkhir = date('Y-m-d', strtotime($data["date_add"]));
				
				if ($data["date_active"] != "" && $data["date_active"] != "0000-00-00" && $data["date_expired"] != "0000-00-00")
					//$data["expire_days"] = strtotime(date('Y-m-d h:i:s', strtotime($data["date_expired"]))) - strtotime($data["date_add"]);
					//$data["expire_days"] = strtotime($tglAwal) - strtotime($tglAkhir) / 86400;
					$data["expire_days"] = floor((strtotime($tglAwal) - strtotime($tglAkhir)) / 86400);
				
				//echo $data["expire_days"];
				
				
				//echo date('Y-m-d',$data["expire_days"]);
				//$data["expire_days"] = ((strtotime($data["date_expired"]) - strtotime(substr($data["date_add"],0,strpos($data["date_add"]," ")))));
/*
				echo $data["date_expired"]." - ";
				echo $data["date_add"]." - ";
				echo strtotime($data["date_expired"])." - ";
				echo strtotime(date("Y-m-d", strtotime($data["date_add"])))." - ";
				echo $data["expire_days"];
				echo "<pre>"; print_r($data); echo "</pre>";
*/				$data["form_submit"] = base_url()."admin/jobpost/edit/$id";
				
				$this->create_header($data["lat"],$data["lng"], $data["loc_id"]);
				$aksinya= isset($data["aksi"]) ? $data["aksi"]:$aksi;
				if ($aksinya=='ashapus') {				
					unset($data["aksi"]);
				} else if ($aksinya=='asnext') {	
					$this->template->render();
				} else if ($aksinya=='inactive') {	
					$this->session->set_flashdata("msg", "1 Job post has been Inactivated");
					redirect(base_url()."admin/jobpost/manage/1/d_date_add");					
				} else if ($aksinya=='approve') {	
					$this->session->set_flashdata("msg", "1 Job post has been Approved");
					redirect(base_url()."admin/jobpost/manage/1/d_date_add");
					//$this->template->write_view("content", "admin/jobpost_edit", $data);
					//$this->template->render();					
				} else {
					//$this->session->set_flashdata("msg", "1 Job post has been edited");
					//redirect(base_url()."admin/jobpost/manage/1/d_date_add");
					$this->template->write_view("content", "admin/jobpost_edit", $data);
					$this->template->render();
				}
			}
			else
			{ $this->template->write("msg", "<div class=msg>You are not allowed to edit the Job Post</div>"); }
			$this->template->write("title", $title);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function reject_form_status($job_id=0,$jobposter_id=0)
	{		
		$data["job_id"] = $job_id;
		$data["jobposter_id"] = $jobposter_id;
		$data["form_submit"] = base_url()."admin/jobpost/reject_form_status/$job_id/$jobposter_id";
		$this->load->view("admin/jobpost_rejectform_status", $data);	
		if (sizeof($_POST) > 0) {
			$tx_id = $this->Madmin->get_uuid(current_url());
			$jobposter_id = $this->input->post("jobposter_id");
			$reason	= $this->input->post("reason");			
			$var="status=2&reason=".urlencode($reason)."&job_id=$data[job_id]"; //reason=$reason&
			$var .= "&tx_id=$tx_id";
			$json = CORE_URL."update_jobpost.php?$var";			
			//echo "<pre>"; print_r($json); echo "</pre>";
			$json = $this->Madmin->get_data($json);	
			//echo "<pre>"; print_r($json); echo "</pre>";
			if ($json["status"] == "0") {
				$this->session->set_flashdata("msg", $json["msg"]);
				//$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
			} else {				
				$data = CORE_URL."get_jobposter_by_jobposter_id.php?id=$jobposter_id";
				$data = $this->Madmin->get_data($data);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$this->load->library('email');		
				$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'mail.altermyth.com',
					'smtp_port' => 25,
					'smtp_user' => 'infokerja@altermyth.com',
					'smtp_pass' => '123456',
					'mailtype'  => 'html',
					'charset'   => 'iso-8859-1'
				);
				$this->email->initialize($config);
				$this->email->from('infokerja@altermyth.com', 'Info Kerja');
				$this->email->to($data["email"]);
				$this->email->subject('Rejected Jobpost');
				$this->email->message("Job post dengan nomor ID: $job_id , telah di reject oleh admin.\nDengan alasan $reason");	
				//$this->email->send();
				//echo $this->email->print_debugger();
				//$this->template->write("msg", "<div class=msg>1 Job post has been rejected.</div>");
				$this->session->set_flashdata("msg", "1 Job post has been inactive");
			}
			redirect(base_url()."admin/jobpost/manage/1/d_date_add");			
		}
	}
	
	function reject_form($job_id=0,$jobposter_id=0)
	{		
		$data["job_id"] = $job_id;
		$data["jobposter_id"] = $jobposter_id;
		$data["form_submit"] = base_url()."admin/jobpost/reject_form/$job_id/$jobposter_id";
		$this->load->view("admin/jobpost_rejectform", $data);	
		if (sizeof($_POST) > 0) {
			$tx_id = $this->Madmin->get_uuid(current_url());
			$jobposter_id = $this->input->post("jobposter_id");
			$reason	= $this->input->post("reason");			
			$var="status=5&reason=".urlencode($reason)."&job_id=$data[job_id]"; //reason=$reason&
			$var .= "&tx_id=$tx_id";
			$json = CORE_URL."update_jobpost.php?$var";			
			//echo "<pre>"; print_r($json); echo "</pre>";
			$json = $this->Madmin->get_data($json);	
			//echo "<pre>"; print_r($json); echo "</pre>";
			if ($json["status"] == "0") {
				$this->session->set_flashdata("msg", $json["msg"]);
				//$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
			} else {				
				$data = CORE_URL."get_jobposter_by_jobposter_id.php?id=$jobposter_id";
				$data = $this->Madmin->get_data($data);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$this->load->library('email');		
				$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'mail.altermyth.com',
					'smtp_port' => 25,
					'smtp_user' => 'infokerja@altermyth.com',
					'smtp_pass' => '123456',
					'mailtype'  => 'html',
					'charset'   => 'iso-8859-1'
				);
				$this->email->initialize($config);
				$this->email->from('infokerja@altermyth.com', 'Info Kerja');
				$this->email->to($data["email"]);
				$this->email->subject('Rejected Jobpost');
				$this->email->message("Job post dengan nomor ID: $job_id , telah di reject oleh admin.\nDengan alasan $reason");	
				//$this->email->send();
				//echo $this->email->print_debugger();
				//$this->template->write("msg", "<div class=msg>1 Job post has been rejected.</div>");
				$this->session->set_flashdata("msg", "1 Job post has been rejected");
			}
			redirect(base_url()."admin/jobpost/manage/1/d_date_add");			
		}
	}
	/*
	function search_form($status=1)
	{
		$jobcats = CORE_URL."get_jobcategories.php?order=jobcat_title";
		$data["jobcats"] = $this->Madmin->get_data($jobcats);
		$data["status"] = $status;
		$this->load->view("admin/jobpost_searchform", $data);
	}
	*/
	function search_form_adv($status=1, $loc_title="")
	{
		$jobcats  = CORE_URL."get_jobcategories.php?order=jobcat_title";
		$company  = CORE_URL."get_companies.php?order=name";
		$location['KELURAHAN'] = CORE_URL."get_location_by_kotamadya.php?order=name";
		$location['KECAMATAN'] = CORE_URL."get_location_by_kecamatan.php?order=name";
		$location['KOTAMADYA'] = CORE_URL."get_location_by_kelurahan.php?order=name";
	
		$company_results				= $this->Madmin->get_data($company);
		$location_results['KELURAHAN']	= $this->Madmin->get_data($location['KELURAHAN']);
		$location_results['KECAMATAN']	= $this->Madmin->get_data($location['KECAMATAN']);
		$location_results['KOTAMADYA']	= $this->Madmin->get_data($location['KOTAMADYA']);

		$data["jobcats"]	= $this->Madmin->get_data($jobcats);
		$data["status"]		= $status;		
	
		$i = 1;
		$rows_company = array();
		foreach ($company_results['results'] as $company) {
			$rows_company[$i]['comp_id'] = $company['comp_id'];
			$rows_company[$i]['company_name'] = $company['company_name'];
			$i++;
		}
		
		$j = 1;
		$rows_location['KELURAHAN'] = array();
		$rows_location['KECAMATAN'] = array();
		$rows_location['KOTAMADYA'] = array();
		foreach ($location_results as $location => $locations) {			
			foreach($locations['results'] as $loc) {
				if ($loc['loc_type'] == 'KELURAHAN') {
					$rows_location['KELURAHAN'][$j]['loc_id'] = $loc['loc_id'];
					$rows_location['KELURAHAN'][$j]['name'] = $loc['name'];
					$rows_location['KELURAHAN'][$j]['loc_type'] = $loc['loc_type'];
					//$j++;
				} 
				if ($loc['loc_type'] == 'KECAMATAN') {
					$rows_location['KECAMATAN'][$j]['loc_id'] = $loc['loc_id'];
					$rows_location['KECAMATAN'][$j]['name'] = $loc['name'];
					$rows_location['KECAMATAN'][$j]['loc_type'] = $loc['loc_type'];
					//$j++;
				} 
				if ($loc['loc_type'] == 'KOTAMADYA') {
					$rows_location['KOTAMADYA'][$j]['loc_id'] = $loc['loc_id'];
					$rows_location['KOTAMADYA'][$j]['name'] = $loc['name'];
					$rows_location['KOTAMADYA'][$j]['loc_type'] = $loc['loc_type'];
					//$j++;
				}
				$j++;
			}
		}
		
		// query for job approver
		$sql1 = "SELECT jobposter_id, username FROM `job_posters`";
		$sql1 = str_replace("\r\n","",$sql1);
		$sql1 = urlencode($sql1);
		$sql1 = CORE_URL."sql.php?sql=".$sql1;
		//echo $sql1."<hr>";
		
		// list job post approver
		$approver_rows = $this->Madmin->get_data($sql1);
		$approver = array(0 => "All");
		foreach ($approver_rows['results'] as $approvers) {
				$approver[$approvers['jobposter_id']] = ucfirst($approvers['username']);
		}		
		
		// query for job poster
		$sql2 = "SELECT jobposter_id, username FROM `job_posters` where status = 1";
		$sql2 = str_replace("\r\n","",$sql2);
		$sql2 = urlencode($sql2);
		$sql2 = CORE_URL."sql.php?sql=".$sql2;
		
		// list job poster users
		$jbposter_rows = $this->Madmin->get_data($sql2);
		$jbposter = array(0 => "All");
		foreach ($jbposter_rows['results'] as $jbposters) {
				$jbposter[$jbposters['jobposter_id']] = ucfirst($jbposters['username']);
		}		
		
		// query for job locations
		$sql3 = "SELECT * FROM location WHERE parent_id = 0";		
		$sql3 = str_replace("\r\n","",$sql3);
		$sql3 = urlencode($sql3);
		//echo $sql3."<hr>";
		$sql3 = CORE_URL."sql.php?sql=".$sql3;
		//echo $sql3."<hr>";
		// list job location
		$location_rows = $this->Madmin->get_data($sql3);
		//echo "<pre>"; print_r($location_rows); echo "</pre><hr>";exit;
		
		
		$location = array();
		$location['0'] = "All";
		foreach ($location_rows['results'] as $locations) {			
			//$location[$locations['loc_id']] = ucfirst($locations['name']);
			$location[$locations['loc_id']] = $locations['name'];
			//echo "<pre>"; print_r($locations); echo "</pre><hr>";exit;
		}
		//echo json_encode( $location );
		//echo "<pre>"; print_r($location_rows); echo "</pre><hr>";exit;
		
		
		
		
		// list job status, see status table
		$data["list_status"] = array(
			"0" => "ALL",
			"1" => "Active",
			"2" => "Inactive",
			"3" => "Draft",
			"4" => "Waiting for Approval",
			"5" => "Rejected"
		);
		
		$data["approver"] = $approver;
		$data["jbposter"] = $jbposter;
		$data["location"] = $location;
		
		$data["status"] = $status;		
		$data['locations'] = $rows_location;
		$data['companies'] = $rows_company;
		return $data;
	}
	
	function interested($job_id=1)
	{
		$sql = "SELECT *, subscribers.name AS name, location.name AS loc_name FROM jobs 
			INNER JOIN jobs_send ON jobs.job_id = jobs_send.job_id 
			INNER JOIN subscribers ON jobs_send.subscriber_id = subscribers.`subscriber_id`
			INNER JOIN location ON location.loc_id = subscribers.loc_id
			WHERE jobs_send.status = '3' AND jobs.job_id='$job_id'";
		//echo $subscribers."<hr>";
		
		$sql = str_replace("\r\n","",$sql);
		$sql = urlencode($sql);
		$sql = CORE_URL."sql.php?sql=".$sql;
		$subscribers = $this->Madmin->get_data($sql);
		
		echo "Interested Subscriber<br><br>";
		echo "<div style='text-align:left; overflow:scroll; height:525px;'><div class='table'>";
		foreach($subscribers["results"] as $subscriber)
		{
			echo "<div class=\"row\">\n";
			echo "<div class=\"cell table_cell\">".$subscriber["name"]."</div><div class=\"cell table_cell\">".$subscriber["loc_name"]."</div>";
			echo "</div>";
		}
		//echo "<pre>"; print_r($_SESSION); echo "</pre>";
		echo "</div></div>";
		echo "<br><a href=# class=close>close</a>";
	}


	function sent($job_id=1)
	{
		$sql = "SELECT *, subscribers.name AS name, location.name AS loc_name FROM jobs 
			INNER JOIN log_sms ON jobs.job_id = log_sms.job_id 
			INNER JOIN subscribers ON log_sms.subscriber_id = subscribers.`subscriber_id`
			INNER JOIN location ON location.loc_id = subscribers.loc_id
			WHERE log_sms.status = '1' AND jobs.job_id='$job_id'";
		//echo $subscribers."<hr>";
		$sql = str_replace("\r\n","",$sql);
		$sql = urlencode($sql);
		$sql = CORE_URL."sql.php?sql=".$sql;
		$subscribers = $this->Madmin->get_data($sql);
		
		echo "Interested Subscriber<br><br>";
		echo "<div style='text-align:left; overflow:scroll; height:525px;'><div class='table'>";
		foreach($subscribers["results"] as $subscriber)
		{
			echo "<div class=\"row\">\n";
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/view/".$subscriber["subscriber_id"]."\">".$subscriber["name"]."</a></div><div class=\"cell table_cell\">".$subscriber["loc_name"]."</div>";
			echo "</div>";
		}
		//echo "<pre>"; print_r($_SESSION); echo "</pre>";
		echo "</div></div>";
		echo "<br><a href=# class=close>close</a>";
	}
	
	
	function sent2()
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$jobs = CORE_URL."get_jobposts_by_id2.php?tx_id=$tx_id&id=0";
		$jobs = $this->Madmin->get_data($jobs);
		//echo "<pre>"; print_r($jobs); echo "</pre>";

		foreach($jobs["results"] as $job)
		{
			//echo $job["job_id"]."<br>";
			//$job_id = $jobs["results"][0]["job_id"];
			//echo $job_id;
			$job_id = $job["job_id"];
			$sql = "SELECT *, subscribers.name AS name, location.name AS loc_name FROM jobs 
				INNER JOIN log_sms ON jobs.job_id = log_sms.job_id 
				INNER JOIN subscribers ON log_sms.subscriber_id = subscribers.`subscriber_id`
				INNER JOIN location ON location.loc_id = subscribers.loc_id
				WHERE log_sms.status = '1' AND jobs.job_id='$job_id'";
			//echo $subscribers."<hr>";
			$sql = str_replace("\r\n","",$sql);
			$sql = urlencode($sql);
			$sql = CORE_URL."sql.php?sql=".$sql;
			$sql = $this->Madmin->get_data($sql);
			//echo "<pre>"; print_r($sql); echo "</pre>";
			$nrows1 = $job["n_send"];
			$nrows2 = $sql["nrows"];
			echo $job_id." - ".$nrows1." - ".$nrows2."<br>";
			if ($nrows1 != $nrows2)
			{
				
				$sql = "UPDATE jobs SET n_send='$nrows2' WHERE job_id='$job_id'";
				//echo $sql."<br>";
				/*
				//echo $subscribers."<hr>";
				$sql = str_replace("\r\n","",$sql);
				$sql = urlencode($sql);
				$sql = CORE_URL."sql.php?sql=".$sql;
				$sql = $this->Madmin->get_data($sql);
				*/
			}
			

		}
		
	}
	
	function ajax_get_location_by_id($a) {
		$sql1 = "SELECT loc_id, parent_id, name FROM `location` WHERE `parent_id` = ".$a."";
		//$sql1 = "SELECT loc_id, parent_id, name FROM `location` WHERE `name` = '".$a."'";
		//die($sql1);
		$sql1 = str_replace("\r\n","",$sql1);
		$sql1 = urlencode($sql1);
		$sql1 = CORE_URL."sql.php?sql=".$sql1;
		//echo $sql1."<hr>";
		$location_rows = $this->Madmin->get_data($sql1);		
		$location[0] = "All";
		foreach ($location_rows['results'] as $locations) {
				$location[$locations['loc_id']] = ucfirst($locations['name']);
				//$location[$locations['loc_title']] = ucfirst($locations['id']);
				//echo "<pre>"; print_r($locations); echo "</pre><hr>";exit;
		}	
		
		echo json_encode( $location );
		//echo "<pre>"; print_r($location); echo "</pre>";
	}
	
	

	function custom_err_msg($str)
	{
		if ($str == '0')
		{ $this->form_validation->set_message('custom_err_msg', 'Please choose a valid %s.'); return FALSE; }
		else
			return TRUE;
	}
	

	function check_var()
	{
		$status 		= ($this->input->post("status")!="") ? ($this->input->post("status")) : "0";
		$jobcat_id		= ($this->input->post("jobcat_id")!="") ? ($this->input->post("jobcat_id")) : "";
		$title 			= ($this->input->post("title")!="") ? ($this->input->post("title")) : "";
		$jobtype_id 	= ($this->input->post("jobtype_id")!="") ? ($this->input->post("jobtype_id")) : "";
		$salary_min		= ($this->input->post("salary_min")!="") ? ($this->input->post("salary_min")) : "";
		$salary_max		= ($this->input->post("salary_max")!="") ? ($this->input->post("salary_max")) : "";
		$description	= ($this->input->post("description")!="") ? ($this->input->post("description")) : "";
		$gender 		= ($this->input->post("gender")!="") ? ($this->input->post("gender")) : "";
		$edu_min 		= ($this->input->post("edu_min")!="") ? ($this->input->post("edu_min")) : "";
		$exp_min 		= ($this->input->post("exp_min")!="") ? ($this->input->post("exp_min")) : "";
		$age_min		= ($this->input->post("age_min")!="") ? ($this->input->post("age_min")) : "";
		$age_max		= ($this->input->post("age_max")!="") ? ($this->input->post("age_max")) : "";
		$expire_days	= ($this->input->post("expire_days")!="") ? ($this->input->post("expire_days")) : "";
		$sms			= ($this->input->post("sms")!="") ? ($this->input->post("sms")) : "";
		$comp_id 		= ($this->input->post("comp_id")!="") ? ($this->input->post("comp_id")) : "";
		$comp_name 		= ($this->input->post("comp_name")!="") ? ($this->input->post("comp_name")) : "";
		$comp_cp		= ($this->input->post("comp_cp")!="") ? ($this->input->post("comp_cp")) : "";
		$comp_tel		= ($this->input->post("comp_tel")!="") ? ($this->input->post("comp_tel")) : "";
		$comp_fax		= ($this->input->post("comp_fax")!="") ? ($this->input->post("comp_fax")) : "";
		$comp_email		= ($this->input->post("comp_email")!="") ? ($this->input->post("comp_email")) : "";
		$region 		= ($this->input->post("region")!="") ? ($this->input->post("region")) : 1;
		if ($region == "1")
			$loc_id 	= ($this->input->post("kelurahan")!="") ? ($this->input->post("kelurahan")) : "";
		else if ($region == "2")
			$loc_id 	= ($this->input->post("zip")!="") ? ($this->input->post("zip")) : "";
			//$loc_id 	= ($this->input->post("kelurahan")!="") ? ($this->input->post("kelurahan")) : "";
		
		$loc_title 		= ($this->input->post("loc_title") != "") ? ($this->input->post("loc_title")) : 0;
		
		$province 		= ($this->input->post("province") != "") ? ($this->input->post("province")) : 0;
		$kotamadya 		= ($this->input->post("kotamadya")!="") ? ($this->input->post("kotamadya")) : 0;
		$kecamatan 		= ($this->input->post("kecamatan")!="") ? ($this->input->post("kecamatan")) : 0;
		$kelurahan 		= ($this->input->post("kelurahan")!="") ? ($this->input->post("kelurahan")) : 0;
		$lat			= ($this->input->post("lat")!="") ? ($this->input->post("lat")) : "";
		$lng			= ($this->input->post("lng")!="") ? ($this->input->post("lng")) : "";
		$zip	 		= ($this->input->post("zip")!="") ? ($this->input->post("zip")) : 0;
		$job_id			= ($this->input->post("job_id")!="") ? ($this->input->post("job_id")) : "";
		$revision		= ($this->input->post("revision")!="") ? ($this->input->post("revision")) : 0;
		$date_add		= ($this->input->post("date_add")!="") ? ($this->input->post("date_add")) : "";
		$date_active	= ($this->input->post("date_active")!="") ? ($this->input->post("date_active")) : "";
		$jobposter_id	= ($this->input->post("jobposter_id")!="") ? ($this->input->post("jobposter_id")) : "";
		$country_id	 	= ($this->input->post("country_id")!="") ? ($this->input->post("country_id")) : "";
		$aksi			= ($this->input->post("aksi")!="") ? ($this->input->post("aksi")) : "";
		return array(
			"status" => $status,
			"jobcat_id" => $jobcat_id,
			"title" => $title,
			"jobtype_id" => $jobtype_id,
			"salary_min" => $salary_min,
			"salary_max" => $salary_max,
			"description" => $description,
			"gender" => $gender,
			"edu_min" => $edu_min,
			"exp_min" => $exp_min,
			"age_min" => $age_min,
			"age_max" => $age_max,
			"expire_days" => $expire_days,
			"sms" => $sms,
			"comp_id" => $comp_id,
			"comp_name" => $comp_name,
			"comp_cp" => $comp_cp,
			"comp_tel" => $comp_tel,
			"comp_fax" => $comp_fax,
			"comp_email" => $comp_email,
			"region" => $region,
			"province" => $province,
			"kotamadya" => $kotamadya,
			"kecamatan" => $kecamatan,
			"kelurahan" => $kelurahan,
			"loc_id" => $loc_id,
			"zip" => $zip,
			"lat" => $lat,
			"lng" => $lng,
			"job_id" => $job_id,
			"revision" => $revision,
			"date_add" => $date_add,
			"date_active" => $date_active,
			"jobposter_id" => $jobposter_id,
			"country_id" => $country_id,
			"aksi" => $aksi
		);
	}

	
	function get_lists($tx_id)
	{
		$educations = CORE_URL."get_educations.php?tx_id=$tx_id&order=edu_id&status=1";
		$educations = $this->Madmin->get_data($educations);
		$provinces = CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=0";
		$provinces = $this->Madmin->get_data($provinces);
		$jobcats = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_title";
		$jobcats = $this->Madmin->get_data($jobcats);
		$zips = CORE_URL."get_location_by_kelurahan.php?tx_id=$tx_id&order=zipcode";
		$zips = $this->Madmin->get_data($zips);
		
		return array(
			"jobposter_id" => $_SESSION["userid"],
			"educations" => $educations["results"],
			"provinces" => $provinces,
			"jobcats" => $jobcats["results"],
			"zips" => $zips["results"]
		);
	}
	
	
	function check_form($region=1)
	{
		$this->form_validation->set_rules('jobcat_id', 'Job Category', 'callback_custom_err_msg');
		$this->form_validation->set_rules('title', 'Job Title', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		$this->form_validation->set_rules('gender', 'Gender', 'callback_custom_err_msg');
		$this->form_validation->set_rules('comp_id', 'Company group', 'callback_custom_err_msg');
		$this->form_validation->set_rules('comp_name', 'Company Name', 'required');
		$this->form_validation->set_rules('comp_cp', 'Contact Person', 'required');
		$this->form_validation->set_rules('comp_tel', 'Contact Phone', 'required');
		
		if ($_POST["region"] == "1") 
		{
			$this->form_validation->set_rules('province','Province','callback_custom_err_msg');
			$this->form_validation->set_rules('kotamadya','Kotamadya','callback_custom_err_msg');
			$this->form_validation->set_rules('kecamatan','Kecamatan','callback_custom_err_msg');
			$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'callback_custom_err_msg');
		}
		else if ($_POST["region"] == "2") $this->form_validation->set_rules('zip', 'Zip code', 'callback_custom_err_msg');
		
		/*
		if ($region == "1")
			$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'callback_custom_err_msg');
		else if ($region == "2")
			$this->form_validation->set_rules('zip', 'Zip code', 'required');
		*/
	}
	
	
	function create_header($lat="", $lng="", $kelurahan=0)
	{
		$header = "
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>
			<script src=\"http://code.google.com/apis/gears/gears_init.js\"></script>";
		if ($lat != "")
			$header .= "<script src=\"".base_url()."js/gmap_nogeolocation.js\"></script>";
		else
			$header .= "<script src=\"".base_url()."js/geolocation2.js\"></script>";
		
		$header .= "
			<!--
			<script src=\"".base_url()."js/date.js\"></script>
			<script src=\"".base_url()."js/jquery.datePicker.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/datePicker.css\">
			-->
			<script src=\"".base_url()."js/jquery.chainedSelects.js\"></script>
			<script>
				function get_company_by_comp_id(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".CORE_URL."get_company_by_comp_id.php\",
						data: \"country_id=".CID."&id=\"+id,
						success: function(result){							
							a = jQuery.parseJSON(result);
							
							$(\"#comp_name\").val(a.name.substr(0,30));
							$(\"#comp_cp\").val(a.cp.substr(0,30));
							$(\"#comp_tel\").val(a.tel.substr(0,30));
							$(\"#comp_fax\").val(a.fax);
							$(\"#comp_email\").val(a.email);
							$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
						}
					});
				};
				
				$(document).ready(function(){

					$('#province').chainSelect('#kotamadya','".base_url()."func/get_location_by_parent_id.php');
					$('#kotamadya').chainSelect('#kecamatan','".base_url()."func/get_location_by_parent_id.php');
					$('#kecamatan').chainSelect('#kelurahan','".base_url()."func/get_location_by_parent_id.php','".$kelurahan."');
					/*
					Date.firstDayOfWeek = 1;
					Date.format = 'yyyy/mm/dd';
					$('.date-pick').datePicker({
						startDate: '01/01/1920',
						endDate: (new Date()).asString()
					});
					*/
					
					//$('#byMap').css('display','block');
					//$('#kelurahan').val('-');
					//$('#zip').val('-');
					$('#lng').val('$lng');
					$('#lat').val('$lat');";
					if ($lat == "")
						$header .= "initialize(); ";
					else
						$header .= "initialize($lat, $lng); ";
					$header .= "
					
					$('#btn').css('cursor','pointer').click(function()
					{
						get_company_by_comp_id($(this).prev().val());
						//alert($(this).prev().val());
					});

/*
					if ($('#region').val() == '3')
					{
						$('.by').css('display','none');
						$('#byMap').css('display','block');
						$('#kelurahan').val('-');
						$('#zip').val('-');
						$('#lng').val('$lng');
						$('#lat').val('$lat');";
						if ($lat == "")
							$header .= "initialize(); ";
						else
							$header .= "initialize($lat, $lng); ";
					$header .= "

					}
*/
					if ($('#region').val() == '2')
					{
						//$('#lat').val('');
						//$('#lng').val('');
						//$('#kelurahan').val('');
						$('.by').css('display','none');
						$('#byZip').css('display','block');
					}

					$('#region').change(function(){
						if($(this).val() == '1')
						{
							//$('#lat').val('');
							//$('#lng').val('');
							//$('#zip').val('-');
							$('.by').css('display','none');
							$('#byRegion').css('display','block');
						}
						if($(this).val() == '2')
						{
							//$('#lat').val('');
							//$('#lng').val('');
							//$('#kelurahan').val('');
							$('.by').css('display','none');
							$('#byZip').css('display','block');
						}
					});
					
					$('#comp_name').keyup(function() {
						$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
					}).keyup();
					
					$('#comp_cp').keyup(function() {
						$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
					}).keyup();
					
					$('#comp_tel').keyup(function() {
						$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
					}).keyup();
					
					$('#title').keyup(function() {
						$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
					}).keyup();
					
					$('#description').keyup(function() {
						$('#default_sms').text($('#comp_name').val() + ', ' + $('#title').val() + ', ' + $('#description').val() + ', hub. ' + $('#comp_cp').val() + ' ' + $('#comp_tel').val());
					}).keyup();
					
					$('#zip').change(function()
					{
						$('#hdnZip').val($('#zip :selected').text());
					});					
				});
			</script>		
		";
		$this->template->write("header", $header);
	}
	

}
?>