<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobseeker extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
	function index(){
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Job Seeker, Mentor & Subscription";
		//$this->template->write("content", "Menu Job Seeker, Mentor & Subscription");
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
		
	}
	function add()
	{
		$useraccess = array("superadmin", "admin", "company");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Add Job Seeker";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$dtime = date("Y-m-d H:i:s");
			$data = $this->check_var();
			//echo "<pre>"; print_r($data); echo "</pre>";
			if (sizeof($_POST) > 0)
			{
				$this->check_form($data["region"]);
				$var = "";
				if ($this->form_validation->run() == TRUE)
				{
					unset($data["region"]);
					unset($data["province"]);
					unset($data["kotamadya"]);
					unset($data["kecamatan"]);
					unset($data["kelurahan"]);
					unset($data["zip"]);

					$pin = "";
					$jobmentor_err = "";
					if ($data["mentor_id"] == "0")
					{
						$json = CORE_URL."add_jobmentor.php?tx_id=$tx_id&mdn=".$data["mdn"]."&status=".$data["status"]."&date_add=$dtime&country_id=".$_SESSION["curr_country"];
						//die($json);
						$json = $this->Madmin->get_data($json);
						if ($json["status"] == "0")
							$jobmentor_err = $json["msg"];
						else
						{
							$mentor_id = $json["subscriber_id"];
							$pin = $json["pin"];
						}
					}
					
					if ($jobmentor_err == "")
					{
						foreach ($data as $key => $value)
						{ $var .= "&".$key."=".urlencode($value); }
						$var .= "&tx_id=$tx_id";
						$var .= "&date_add=".urlencode(date("Y-m-d H:i:s"));
						$var .= "&country_id=".$_SESSION["curr_country"];
						$var = substr($var,1);
						$json = CORE_URL."add_subscriber.php?$var";
						//die($json);

						/*
						$var = explode("&", $var);
						echo "<pre>"; print_r($var); echo "</pre>";
						$var2 = array();
						foreach ($var as $a)
						{ 
							$a = explode("=",$a);
							$data[$a[0]] = urldecode($a[1]); 
						}
						echo "<pre>"; print_r($data); echo "</pre>";
						*/
						
						$json = $this->Madmin->get_data($json);
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
						{
							if ($pin != "")
							{
								$json2 = CORE_URL."update_jobmentor.php?tx_id=$tx_id&mentor_id=$mentor_id&subscriber_id=".$json["subscriber_id"];
								$json2 = $this->Madmin->get_data($json2);
								if ($json2["status"] == "1")
									$this->template->write("msg", "<div class=msg>1 Job Seeker/Job Mentor has been registered. His/her PIN is $pin</div>");
								else
									$this->template->write("msg", "<div class=msg>".$json2["msg"]."</div>");
							}
							else
								$this->template->write("msg", "<div class=msg>1 Job Seeker has been registered.</div>");
						}
					}
					else
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");

					$data["region"] = "";
					$data["province"] = "";
					$data["kotamadya"] = "";
					$data["kecamatan"] = "";
					$data["kelurahan"] = "";
					$data["zip"] = "";

					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
					//echo "<pre>"; print_r($data); echo "</pre>";
				}
				//else
				//	echo "<pre>"; print_r($_POST); echo "</pre>";
			}
			
			foreach ($this->get_lists($tx_id) as $key => $value)
			{ $data[$key] = $value; }
			
			if ($data["kelurahan"] != "0")
			{
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

			$data["form_submit"] = base_url()."admin/jobseeker/add";
			
			$this->create_header(0,0);
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobseeker_edit", $data);
			$this->template->render();
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}

	function manage($page=1, $order="a_subscriber_name", $default_row=20, $status="", $jobcat_id="", $loc_title="", $gender="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="", $submit_date="", $edit_date="", $loc_id="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Job Seeker";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			
			$ndata = (!empty($default_row)) ? $default_row : 20;
			$search_uri = "";
			if (sizeof($_POST) > 0)
			{
				$submit_date 					= $this->input->post('submit_date');
				$edit_date 						= $this->input->post('edit_date');
				$birthday 						= $this->input->post('birthday');
				$location_id	 				= $this->input->post('loc_id');
				
				$search["status"] 				= $this->input->post("status") != "" ? $_POST["status"] : "_";
				$search["jobcat_id"] 			= $this->input->post("jobcat_id") != null ? $_POST["jobcat_id"] : "_";	//DEFAULT null
				$search["loc_title"] 			= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "_";
				$search["gender"] 				= $this->input->post("gender") != "" ? $_POST["gender"] : "_";
				$search["education"] 			= $this->input->post("education") != "" ? $_POST["education"] : "_";
				$search["jobseeker_name"] 		= $this->input->post("jobseeker_name") != null ? $_POST["jobseeker_name"] : "_";
				$search["mdn"] 					= $this->input->post("mdn") != null ? $_POST["mdn"] : "_";
				$search["mentor"]		 		= $this->input->post("mentor") != null ? $_POST["mentor"] : "_";
				//$search["birthday"] 			= ($birthday['from_date'] && $birthday['to_date'] != "") ? implode(':',$_POST["birthday"]) : "_";
				$search["birthday"] 			= ($birthday['from_date'] && $birthday['to_date'] != "") ? $birthday['from_date']."-01-01:".$birthday['to_date']."-12-31" : "_";
				$search["submit_date"] 			= ($submit_date['from_date'] && $submit_date['to_date'] != "") ? implode(':',$_POST["submit_date"]) : "_";
				$search["edit_date"] 			= ($edit_date['from_date'] && $edit_date['to_date'] != "") ? implode(':',$_POST["edit_date"]) : "_";
				//$search["loc_title"] 			= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "_";
				$search["loc_id"] 				= $this->input->post("loc_id") != 0 ? $_POST["loc_id"] : "_";
				
				foreach($search as $key => $val)
				{ $search_uri .= "$val/"; }
				redirect(base_url()."admin/jobseeker/manage/$page/$order/$default_row/$search_uri");
			}
			else
			{
				$search["status"] 				= $status;
				$search["jobcat_id"] 			= $jobcat_id;
				$search["loc_title"] 			= $loc_title;
				$search["gender"] 				= $gender;
				$search["education"] 			= $education;
				$search["jobseeker_name"] 		= urldecode($jobseeker_name);
				$search["mdn"] 					= $mdn;
				$search["mentor"]		 		= urldecode($mentor);
				$search["birthday"] 			= $birthday;
				$search["submit_date"] 			= $submit_date;
				$search["edit_date"] 			= $edit_date;
				//$search["loc_title"] 			= $loc_title;
				$search["loc_id"] 				= $loc_id;
				
			}
			//echo "<pre>"; print_r($search); echo "</pre>";
			//echo (base_url()."admin/jobseeker/manage/$page/$order/$default_row/$search_uri");
			//$search_uri 						= http_build_query(@$search,'','&');
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			
			$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].$search_uri;				
			echo "<!-- $json -->";

			//echo "<pre>"; print_r($edit_date); echo "</pre>";
			$data = $this->Madmin->get_data($json);
			
			//$data["status"] = $status;
			//$data["form_submit"] = current_url();
			$data["form_submit"] = base_url()."admin/jobseeker/manage/1/$order/$default_row/";
			
			
			
			$data["page"] = $page;
			$data["search"] = $search;
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			
			$data["result_row"] = array(20 => 20, 50 => 50, 90 => 90);
			$data["default_row"] = (!empty($_POST['result_row'])) ? $_POST['result_row'] : $ndata;
			
			$data["search_link"] = base_url()."admin/jobseeker/search_form/".$search["status"]."/";
			$this->template->write("header", "
			
			<script src=\"".base_url()."js/jquery.js\"></script>
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

			<script>
				$(document).ready(function(){

					$('#search_form').simpleDialog({
						height: 300,
						width: 350,
						showCloseLabel: false
					});
				});
			</script>		
			
			");
			
			
			//$data['jobcats'] = $this->search_form_adv($status=1);
			//disini set value filternya.
			$data['jobcats'] = $this->search_form_adv($search['status'], $search['gender'], $search['education'], $search['jobseeker_name'], $search['mdn'], $search['mentor'], $search['birthday'], $search['submit_date'], $search['edit_date'], $search['loc_title']);
			$data['jobcats']['search'] = $data['search'];
			
			$data['jobcats']['form_submit'] = base_url()."admin/jobsent_report/manage/1/d_jobsend_id";
			$lokasi["kotamadya_id"] = $this->input->post("kotamadya_id") != 0 ? $_POST["kotamadya_id"] : "";
				$lokasi["kecamatan_id"] = $this->input->post("kecamatan_id") != 0 ? $_POST["kecamatan_id"] : "";
				$lokasi["kelurahan_id"] = $this->input->post("kelurahan_id") != 0 ? $_POST["kelurahan_id"] : "";
				$data["lokasi"] = $lokasi;	
			$data['jobcats']['lokasi'] = $data['lokasi'];	
			
			
			
			$data['search_form'] = $this->load->view('admin/jobseeker_searchform_adv',$data['jobcats'],TRUE);
			$this->template->write("title", $title);
			//echo "<pre>"; print_r($data[search]); echo "</pre>";
			if($this->session->flashdata('msg')) $this->template->write("msg", "<div class=msg>".$this->session->flashdata('msg')."</div>");
			$this->template->write_view("content", "admin/jobseeker_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function search_form_adv($status=1, $gender="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="", $submit_date="", $edit_date="", $loc_title="")
	//function search_form_adv($status=1, $loc_title="", $gender="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="")
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
		
		
		
		// query for job approver
		$sql1 = "SELECT edu_id, edu_title FROM `education` where status = 1";
		$sql1 = str_replace("\r\n","",$sql1);
		$sql1 = urlencode($sql1);
		$sql1 = CORE_URL."sql.php?sql=".$sql1;
		//echo $sql1."<hr>";
		
		// list job post approver
		$education_rows = $this->Madmin->get_data($sql1);
		$education = array(0 => "All");
		foreach ($education_rows['results'] as $educations) {
				$education[$educations['edu_id']] = ucfirst($educations['edu_title']);
		}		
		
		// list job status, see status table
		$data["list_status"] = array(
			"0" => "ALL",
			"1" => "Active",
			"2" => "Inactive",
			"3" => "Draft",
			"4" => "Waiting for Approval",
			"5" => "Rejected"
		);
		
		//list gender
		$data["list_gender"] = array(
			"0" => "ALL",
			"M" => "Male",
			"F" => "Female"
		);
		$data["list_year"] = array("" => "ALL");
		for ($a = date("Y"); $a > (date("Y")-100); $a--)
		{ $data["list_year"][$a] = $a; }
		
		
		//$data["approver"] = $approver;
		//$data["jbposter"] = $jbposter;
		$data["location"] = $location;
		
		$data["status"] = $status;
		$data["gender"] = $gender;	
		$data["education"] = $education;	
		$data["jobseeker_name"] = $jobseeker_name;	
		$data["mdn"] = $mdn;	
		$data["mentor"] = $mentor;	
		$data["birthday"] = $birthday;			
		$data['locations'] = $rows_location;
		$data['companies'] = $rows_company;
		return $data;
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
	function str_csv($str)
	{
		return str_replace(",", ".", $str);
	}
	
	function save_csv($order="a_subscriber_name", $status="", $jobcat_id="", $loc_title="", $gender="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="", $submit_date="", $edit_date="", $loc_id="")
	//function save_csv($order="a_subscriber_name", $status="", $loc_title="", $jobcat_id="", $subscriber_name="", $loc_id="", $gender="", $salary="", $date_add="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="", $submit_date="", $edit_date="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Job Seeker as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobseeker.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
/*			
			$search["status"] = $status;
			$search["loc_title"] = $loc_title;
			$search["jobcat_id"] = $jobcat_id;			
			$search["subscriber_name"] = $subscriber_name;
			$search["loc_id"] = $loc_id;
			$search["gender"] = $gender;
			$search["salary"] = $salary;
			$search["date_add"] = $date_add;
			$search["education"] = $education;
			$search["jobseeker_name"] = $jobseeker_name;
			$search["mdn"] = $mdn;
			$search["mentor"] = $mentor;
			$search["birthday"] = $birthday;
			$search["submit_date"] = $submit_date;
			$search["edit_date"] = $edit_date;
*/			
			$search["status"] 				= $status;
			$search["jobcat_id"] 			= $jobcat_id;			
			$search["loc_title"] 			= $loc_title;
			$search["gender"] 				= $gender;
			$search["education"] 			= $education;
			$search["jobseeker_name"] 		= $jobseeker_name;
			$search["mdn"] 					= $mdn;
			$search["mentor"]		 		= $mentor;
			$search["birthday"] 			= $birthday;
			$search["submit_date"] 			= $submit_date;
			$search["edit_date"] 			= $edit_date;
			
			$search["loc_id"] 				= $loc_id;
			
			$search_uri = "";
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			//if (($search["status"] != "_") || ($search["loc_title"] != "_") || ($search["jobcat_id"] != "_") || ($search["subscriber_name"] != "_") || ($search["loc_id"] != "_") || ($search["gender"] != "_") || ($search["salary"] != "_") || ($search["date_add"] != "_") || ($search["education"] != "_") || ($search["jobseeker_name"] != "_") || ($search["mdn"] != "_") || ($search["mentor"] != "_") || ($search["birthday"] != "_") || ($search["submit_date"] != "_") || ($search["edit_date"] != "_")) 
			
			{
				//echo "11";
				//tanpa paging
				$var = "";
				//foreach($search as $key => $val)
				//{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				//$search_uri = http_build_query($search,'','&');								
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id$var&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;					
				else					
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc".'&'.$search_uri;
					//echo $json."<hr>";	
			}
			/*
			else
			{	
				
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];					
				else					
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc&";					
				//echo $json;		
			}
			*/
			//echo $json;
			$data = $this->Madmin->get_data($json);
			//echo"<pre>";print_r($data);echo"</pre>";exit();
			
			$output = "ID, Status, Name, Gender, MDN, Education, Province, Kotamadya, Kecamatan, Kelurahan, Mentor, Date of Birth, # Active Subscription, Register Date, Last Activity Date\n";
			
			foreach($data["results"] as $job)
			{									
				$output .= $this->str_csv($job["subscriber_id"]).",";
					
				switch ($job["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}		
				$output .= ", ";
				$output .= 
					$this->str_csv($job["subscriber_name"]).",".					
					$this->str_csv($job["gender"]).",".					
					$this->str_csv($job["mdn"]).",".					
					$this->str_csv($job["edu_title"]).",".
					$this->str_csv($job["province_name"]).",".															
					$this->str_csv($job["kotamadya_name"]).",".					
					$this->str_csv($job["kecamatan_name"]).",".
					$this->str_csv($job["loc_title"]).",". //$this->str_csv($job["loc_title"]).",".					
					$this->str_csv($job["name"]).",".
					$this->str_csv($job["birthday"]).",".
					$this->str_csv($job["n_active_sub"]).",";
//					$this->str_csv($job["status"]).",";					
					$output .= $this->str_csv($job["subscriber_date_add"]).",".
					$this->str_csv($job["subscriber_date_update"])."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	/*reference save csv
	function save_csv($order="a_subscriber_name", $status="", $jobcat_id="", $loc_title="", $subscriber_name="", $gender="", $salary="", $date_add="", $education="", $jobseeker_name="", $mdn="", $mentor="", $birthday="", $submit_date="", $edit_date="", $loc_id="")	
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Job Seeker as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobseeker.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$search["status"] = $status;
			$search["jobcat_id"] = $jobcat_id;
			$search["loc_title"] = $loc_title;
			$search["subscriber_name"] = $subscriber_name;
			$search["gender"] = $gender;
			$search["salary"] = $salary;
			$search["date_add"] = $date_add;
			$search["education"] = $education;
			$search["jobseeker_name"] = $jobseeker_name;
			$search["mdn"] = $mdn;
			$search["mentor"] = $mentor;
			$search["birthday"] = $birthday;
			$search["submit_date"] = $submit_date;
			$search["loc_id"] = $loc_id;
			
			
			if (($search["status"] != "_") || ($search["jobcat_id"] != "_") || ($search["loc_title"] != "_") || ($search["subscriber_name"] != "_") || ($search["gender"] != "_") || ($search["salary"] != "_") || ($search["date_add"] != "_") || ($search["education"] != "_") || ($search["jobseeker_name"] != "_") || ($search["mdn"] != "_") || ($search["mentor"] != "_") || ($search["birthday"] != "_") || ($search["submit_date"] != "_") || ($search["loc_id"] != "_")) 
			{
				$var = "";
				$search_uri = http_build_query($search,'','&');				
				//foreach($search as $key => $val)
				//{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;				
				else					
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc".'&'.$search_uri;
					//$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc".'&'.$search_uri;
					//echo "get_subscriber_by_subscriber_id.php?tx_id=$tx_id"."$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=0"; die;
			}
			else
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];				
				else					
					$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc";						
			}
			echo $json."<hr>";
			$data = $this->Madmin->get_data($json);
			echo"<pre>";print_r($data);echo"</pre>";exit();
			
			$output = "ID, Status, Name, gender, mdn,edu_id, province_name, kotamadya_name, kecamatan_name, loc_title, mentor_name, birthday,  Date Register, Last Activity\n";
			foreach($data['results'] as $seeker)
			{
				$output .= $seeker["subscriber_id"].",";
				switch ($seeker["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}		   
				$output .= $seeker["subscriber_name"].",".
						   $seeker["gender"].",".
						   $seeker["mdn"].",".
						   $seeker["edu_id"].",".
						   $seeker["province_name"].",".
						   $seeker["kotamadya_name"].",".
						   $seeker["kecamatan_name"].",".
						   $seeker["loc_title"].",".
						   $seeker["name"].",".
						   $seeker["birthday"].",".
						   $seeker["subscriber_date_add"].",".
						   $seeker["subscriber_date_update"]."\n";
				
				//$output .= ",".$seeker["subscriber_date_add"].",".$seeker["subscriber_date_update"]."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	
	
	*/
	
	function edit($id=1)
	{
		$useraccess = array("superadmin", "admin", "company");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Edit Job Seeker";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$data = $this->check_var();
			//echo "<pre>"; print_r($data); echo "</pre>";
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				if ($this->form_validation->run() == TRUE)
				{
					unset($data["province"]);
					unset($data["kotamadya"]);
					unset($data["kecamatan"]);
					unset($data["kelurahan"]);
					unset($data["zip"]);
					$var = "";
					$msg = "";
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					
					if ($data["mentor_id"] == "0")
					{
						/*
						if ($data["pin"] != "")						
							$json = CORE_URL."update_jobmentor.php?tx_id=$tx_id&mentor_id=".$data["mentor_id"]."&mdn=".$data["mdn"]."&pin=".$data["pin"];
						else							
							$json = CORE_URL."update_jobmentor.php?tx_id=$tx_id&mentor_id=".$data["mentor_id"]."&mdn=".$data["mdn"];
						*/					
						$json = CORE_URL."update_jobmentor.php?tx_id=$tx_id&mentor_id=".$data["mentor_id"]."&mdn=".$data["mdn"];
						
						$json = $this->Madmin->get_data($json);
						if ($json["status"] == "0") $msg = $json["msg"];
					}
					$json = CORE_URL."update_subscriber.php?tx_id=$tx_id&$var";
					//echo $json;
					$json = $this->Madmin->get_data($json);
					
					if ($json["status"] == "0")
						$msg .= "<br>".$json["msg"];
					else
						$msg = "1 Job Seeker has been updated.";
					$this->template->write("msg", "<div class=msg>$msg</div>");
					
					$data["province"] = "";
					$data["kotamadya"] = "";
					$data["kecamatan"] = "";
					$data["kelurahan"] = "";
					$data["zip"] = "";
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			foreach ($this->get_lists($tx_id) as $key => $value)
			{ $data[$key] = $value; }
			
			$json = CORE_URL."get_subscriber_by_subscriber_id.php?tx_id=$tx_id&subscriber_id=$id";

			$data["subscriber"] = $this->Madmin->get_data($json);
			foreach ($data["subscriber"] as $key => $value)
			{ 
				if ($key == "pos_lat") $key = "lat";
				if ($key == "pos_lng") $key = "lng";
				$data[$key] = $value; 
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

			if ($data["mentor_id"] == "0")
			{
				$json = CORE_URL."get_mentor_by_mdn.php?tx_id=$tx_id&mdn=".$data["mdn"];
				$json = $this->Madmin->get_data($json);
				$data["pin"] = $json["pin"];
				//echo "<pre>"; print_r($json); echo "</pre>";
			}
			else
				$data["pin"] = "";
			
			$data["birthday"] = str_replace("-","/",$data["birthday"]);
			$data["form_submit"] = base_url()."admin/jobseeker/edit/$id";
			
			unset($data["subscriber"]);
			if (($data["lat"] == "") || ($data["lat"] == "0")) 
				$data["region"] = "1";
			else
				$data["region"] = "3";

			$this->create_header($data["lat"],$data["lng"], $data["loc_id"]);
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobseeker_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function view($subscriber_id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "View Job Seeker";
		$is_allow = "0";
		if ($_SESSION["comp_id"] != "0")
		{
			$json = CORE_URL."check_subscriber_and_company.php?tx_id=$tx_id&subscriber_id=$subscriber_id&comp_id=$comp_id";
			$json = $this->Madmin->get_data($json);
			if ($json["status"] == "1") $is_allow = "1";
		}
		else
			$is_allow = "1";
		
		if ($is_allow == "0")
			$this->template->write("msg", "You are not allowed to access subscriber's data"); 
		else
		{
			$json = CORE_URL."get_subscriber_by_subscriber_id.php?tx_id=$tx_id&subscriber_id=$subscriber_id";
			$data = $this->Madmin->get_data($json);
			
			$json = CORE_URL."get_education_by_edu_id.php?tx_id=$tx_id&id=".$data["edu_id"];
			$json = $this->Madmin->get_data($json);
			$data["edu_title"] = $json["edu_title"];
			//echo "<pre>"; print_r($data); echo "</pre>";
			
			//$this->create_header($data["pos_lat"],$data["pos_lng"], $data["loc_id"]);
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobseeker_view", $data, TRUE);
		}
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
			
		
	}
	

	function addjob()
	{
		$useraccess = array("superadmin", "admin");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Add Job Subscription";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$subscriber_id	= !is_null($this->input->post("subscriber_id")) ? ($this->input->post("subscriber_id")) : "";
			$jobcat_id		= !is_null($this->input->post("jobcat_id")) ? ($this->input->post("jobcat_id")) : "";
			$date_add		= date("Y-m-d H:i:s");
			$date_send		= $date_add;
			$date_active	= date("Y-m-d", (time() + 1*86400));
			$date_expired	= date("Y-m-d", (time() + 7*86400));
			
			$data = array(
				"subscriber_id" => $subscriber_id,
				"jobcat_id" => $jobcat_id,
				"date_add" => $date_add,
				"date_send"	=> $date_send,
				"date_active" => $date_active,
				"date_expired" => $date_expired,
				"status" => "1"
			);

			if (sizeof($_POST) > 0)
			{
				$this->form_validation->set_rules('subscriber_id', 'Subscriber', 'callback_custom_err_msg');
				$this->form_validation->set_rules('jobcat_id', 'Job Category', 'callback_custom_err_msg');
				if ($this->form_validation->run() == TRUE)
				{
					$var = "";
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					/*
					$json = CORE_URL."potong_pulsa.php?tx_id=$tx_id&subscriber_id=$subscriber_id&pulsa=3000";
					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
					*/
					{
		/*
        $json1 = CORE_URL."add_subscriber_jobcat.php?tx_id=".urlencode($tx_id)."&subscriber_id=$jobseeker_subscriber_id&jobcat_id=$jobseeker_category_id&date_add=".urlencode($date_add)."&date_send=".urlencode($date_send)."&date_active=".urlencode($date_active)."&date_expired=".urlencode($date_expired);
        $json2 = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=$jobseeker_category_id&rel_id=".$rel_id."&date_add=".urlencode($date_add)."&date_send=".urlencode($date_send)."&subscriber_id=$jobseeker_subscriber_id";
        $json3 = BASE_URL."send_sms_by_jobsend_id.php?tx_id=".urlencode($tx_id)."&jobsend_id=$job_id";
		*/
						//$json = CORE_URL."add_subscriber_jobcat.php?tx_id=$tx_id&$var";
						$json = CORE_URL."add_subscriber_jobcat.php?tx_id=".urlencode($tx_id)."&subscriber_id=$subscriber_id&jobcat_id=$jobcat_id&date_add=".urlencode($date_add)."&date_send=".urlencode($date_send)."&date_active=".urlencode($date_active)."&date_expired=".urlencode($date_expired);
						//echo $json."<hr>";
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						$rel_id = $json["rel_id"];
						if ($json["status"] == "0")
							$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
						else
						{
							
							//$json = CORE_URL."add_jobsend.php?tx_id=$tx_id&rel_id=".$rel_id."&$var";
							$json = CORE_URL."add_jobsend.php?tx_id=".urlencode($tx_id)."&jobcat_id=$jobcat_id&rel_id=".$rel_id."&date_add=".urlencode($date_add)."&date_send=".urlencode($date_send)."&subscriber_id=$subscriber_id";
							//echo $json."<hr>";
							$json = $this->Madmin->get_data($json);
							//echo "<pre>"; print_r($json); echo "</pre>";
							$job_id = $json["job_id"];
							$jobsend_id = $json["jobsend_id"];
							
							//echo "http://br14.esia.co.id/grameen/infokerja/send_sms_by_jobsend_id.php?jobsend_id=$jobsend_id";
							$sms_status = "1"; // SEND SMS SUCCESS
							
							if ($sms_status == "1")
							{
								$json = APP_URL."send_sms_by_jobsend_id.php?tx_id=".urlencode($tx_id)."&jobsend_id=$jobsend_id";
								//echo $json."<hr>";
								$json = file_get_contents($json);
								//echo "<pre>"; print_r($json); echo "</pre>";
								/*
								$job = CORE_URL."get_jobpost_by_job_id.php?tx_id=$tx_id&id=$job_id";
								$job = $this->Madmin->get_data($job);
								$msg = $job["comp_name"].",".$job["title"].", ".$job["description"].", hub.".$job["comp_cp"]." ".$job["comp_tel"];
								
								$subscriber = CORE_URL."get_subscriber_by_subscriber_id.php?tx_id=$tx_id&subscriber_id=$subscriber_id";
								$subscriber = $this->Madmin->get_data($subscriber);
								$mdn = $subscriber["mdn"];
								
								$writelog = CORE_URL."add_logsms.php?tx_id=$tx_id&job_id=$job_id&rel_id=$rel_id&subscriber_id=$subscriber_id&msg=".urlencode($msg)."&mdn=$mdn&status=1&date_send=".urlencode($date_add);
								$writelog = $this->Madmin->get_data($writelog);
								
								$is_mentor = CORE_URL."check_mdn.php?tx_id=$tx_id&mdn=$mdn&type=mentor";
								$is_mentor = $this->Madmin->get_data($is_mentor);
								if ($is_mentor["status"] == "1")
								{
									$writelog = CORE_URL."add_logsms.php?tx_id=$tx_id&job_id=$job_id&rel_id=$rel_id&subscriber_id=$subscriber_id&msg=".urlencode($msg)."&mdn=$mdn&status=2&date_send=".urlencode($date_add);
									echo $writelog."<Br>";
									$writelog = $this->Madmin->get_data($writelog);
								}
								$json2 = CORE_URL."update_jobsend.php?tx_id=$tx_id&jobsend_id=2";
								$json2 = $this->Madmin->get_data($json2);
								*/

							}
							$this->template->write("msg", "<div class=msg>1 job subscription has been added.</div>");
						}
						foreach ($data as $key => $value)
						{ $data[$key] = ""; }
					}
				}
			}
			
			$subscribers = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=subscriber_name";
			$subscribers = $this->Madmin->get_data($subscribers);
			$jobcat = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_title";
			$jobcat = $this->Madmin->get_data($jobcat);
			
			$data["form_submit"] = base_url()."admin/jobseeker/addjob";
			$data["subtitle"] = "ADD JOB SUBSCRIPTION";
			$data["subscribers"] = $subscribers["results"];
			$data["jobcats"] = $jobcat["results"];
			
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobseeker_addjobcat", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
		
	}

	
	function editjob($id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$jobcat_id		= !is_null($this->input->post("jobcat_id")) ? ($this->input->post("jobcat_id")) : "";
		$title = "Manage Job Subscription";
		$data = array(
			"rel_id" => $id,
			"jobcat_id" => $jobcat_id,
		);

		if (sizeof($_POST) > 0)
		{
			$this->form_validation->set_rules('jobcat_id', 'Job Category', 'callback_custom_err_msg');
			if ($this->form_validation->run() == TRUE)
			{
				$var = "";
				foreach ($data as $key => $value)
				{ $var .= "&".$key."=".urlencode($value); }
				$var = substr($var,1);
				
				$json = CORE_URL."update_rel_subscriber_jobcat.php?tx_id=$tx_id&$var";
				//die($json);
				$json = $this->Madmin->get_data($json);
				if ($json["status"] == "0")
					$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
				else
					$this->template->write("msg", "<div class=msg>1 job subscription has been updated.</div>");
				
				foreach ($data as $key => $value)
				{ $data[$key] = ""; }
			}
		}
		
		$jobcat = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_title";
		$jobcat = $this->Madmin->get_data($jobcat);
		$rel = CORE_URL."get_rel_subscriber_jobcat_by_rel_id.php?tx_id=$tx_id&id=$id";
		$rel = $this->Madmin->get_data($rel);
		//echo "<pre>"; print_r($rel); echo "</pre>";
		
		$data["jobcat_id"]		= $rel["jobcat_id"];
		$data["subscriber_id"]	= $rel["subscriber_id"];
		$data["subscriber_name"]	= $rel["name"];

		$data["form_submit"] = base_url()."admin/jobseeker/editjob/$id";
		$data["subtitle"] = "EDIT JOB SUBSCRIPTION";
	
		$data["jobcats"] = $jobcat["results"];
		
		$this->template->write("title", $title);
		$this->template->write("header", 
		"
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/date.js\"></script>
			<script src=\"".base_url()."js/jquery.datePicker.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/datePicker.css\">

			<script>
				function get_jobcat(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/get_jobcats/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							/*
							a = jQuery.parseJSON(result);
							c = '';
							//alert(a.results.length);
							for (b=0; b<=a.results.length-1; b++)
							{
								c = c + a.results[b].jobcat_id + ' ';
								//alert(b);
							}
							//alert(c);
							*/
							$(\"#jobcats\").html(result);
						}
					});
				};
				
				$(document).ready(function(){

					Date.firstDayOfWeek = 1;
					Date.format = 'yyyy/mm/dd';
					$('.date-pick').datePicker().val(new Date().asString()).trigger('change');

					$('#btn').css('cursor','pointer').click(function()
					{
						get_jobcat($(this).prev().val());
						//alert($(this).prev().val());
					});

				});
				
				
			</script>		
		");
		$this->template->write_view("content", "admin/jobseeker_addjobcat", $data, TRUE);
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	

	function updatejob($page=1, $order="a_date_add", $mdn="")
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Job Subscription";
		/*
		if (sizeof($_POST) > 0)
		{
			$unsub = !is_null($this->input->post("unsub")) ? ($this->input->post("unsub")) : "";
			$all_id = !is_null($this->input->post("all_id")) ? ($this->input->post("all_id")) : "";
			$all_id = explode(",",$all_id);
			//echo "<pre>"; print_r($all_id); echo "</pre>";
			
			foreach ($all_id as $a)
			{
				if(!empty($unsub)){
					if (in_array($a, $unsub))
						$status = 2;
					else
						$status = 1;
					$json = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=$a&tx_id=$tx_id&status=$status";
					$json = $this->Madmin->get_data($json);
				}
				else
				{
					$json = CORE_URL."update_rel_subscriber_jobcat.php?rel_id=$a&tx_id=$tx_id&status=1";
					$json = $this->Madmin->get_data($json);
				}
			}
			$this->template->write("msg", "<div class=msg>".sizeof($all_id)." Job Subscription has been updated.</div>");
		}
 		$subscribers = CORE_URL."get_subscribers.php?tx_id=$tx_id&order=subscriber_name";
		$subscribers = $this->Madmin->get_data($subscribers);
		$data["subscribers"] = $subscribers["results"];
 */		
		$this->template->write("title", $title);
		$this->template->write("header", 
		"
			<script src=\"".base_url()."js/jquery.js\"></script>
			<link rel=\"stylesheet\" href=\"".base_url()."css/jquery.ui.all.css\">
			<link rel=\"stylesheet\" href=\"".base_url()."css/demos.css\">
			<script src=\"".base_url()."js/jquery.ui.core.js\"></script>
			<script src=\"".base_url()."js/jquery.ui.widget.js\"></script>
			<script src=\"".base_url()."js/jquery.ui.tabs.js\"></script>
	
			<script>
				$(function() {
					$(\"#tabs\" ).tabs();
				});
				
				function get_jobcat(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/get_jobcats/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							/*
							a = jQuery.parseJSON(result);
							c = '';
							//alert(a.results.length);
							for (b=0; b<=a.results.length-1; b++)
							{
								c = c + a.results[b].jobcat_id + ' ';
								//alert(b);
							}
							//alert(c);
							*/
							$(\"#msg\").html(\"\");
							$(\"#jobcats\").html(result);
							$(\"#jobcat_detail\").html(\"\");
						}
					});
				};
				
				function unsub(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/unsub/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							$(\"#msg\").html(result);
							$(\"#jobcat_detail\").html(\"\");
						}
					});
					return false;
				};
				
				function unsub2(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/unsub2/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							$(\"#msg\").html(result);
							$(\"#jobcat_detail\").html(\"\");
						}
					});
					return false;
				};
				
				function blank_detail()
				{
					$(\"#msg\").html('');
					$(\"#jobcat_detail\").html('');
					$(\"#jobcat_detail2\").html('');
					
				};
				
				function get_jobcat_detail(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/get_jobcat_detail/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							$(\"#msg\").html(\"\");
							$(\"#jobcat_detail\").html(result);
						}
					});
				};
				
				function get_jobcat_detail2(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/jobseeker/get_jobcat_detail/\"+id,
						//data: \"id=\"+id,
						success: function(result){
							$(\"#msg\").html(\"\");
							$(\"#jobcat_detail2\").html(result);
						}
					});
				};
				
				
				$(document).ready(function(){
					$('#btn').css('cursor','pointer').click(function()
					{
						get_jobcat($(this).prev().val());
						//alert($(this).prev().val());
					});
					/*
					$('.link_jobcat_detail').click(function()
					{
						get_jobcat_detail($(this).prev().val());
						//alert($(this).prev().val());
					});
					*/
				});
			</script>		
		");
		
		$tx_id = $this->Madmin->get_uuid(current_url());
/* 		$curr_date = date("Y-m-d");
		$jobcategories = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_title";
		$jobcategories = $this->Madmin->get_data($jobcategories);
		$list_jobcat = array("0" => "--");
		foreach ($jobcategories["results"] as $a)
		{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
 */		
		$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
		$orderby = substr($order,2);
		$ndata = 20;
		$data['npage'] = 0;
		
		$mdn = ($_POST) ? $this->input->post('mdn') : $this->uri->segment(6);
			
		$active_subscription = CORE_URL."get_jobcat_by_mdn.php?tx_id=$tx_id&mdn=$mdn&category=active&order=$orderby&ascdesc=$ascdesc";
		echo "<!-- ".$active_subscription." -->\n";
		$data['active_subscription'] = $this->Madmin->get_data($active_subscription);
		//echo "<pre>"; print_r($data['active_subscription']); echo "</pre>";
		
		$data["page"] = $page;
		$data["order"] = $order;
		$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
		
		$history = CORE_URL."get_jobcat_by_mdn.php?tx_id=$tx_id&mdn=$mdn&category=history&order=$orderby&ascdesc=$ascdesc";
		echo "<!-- ".$history." -->\n";
		$data['history'] = $this->Madmin->get_data($history);
		//echo "<pre>"; print_r($active_subscription); echo "</pre>";
		$data["mdn"] = $mdn;
		$this->template->write("msg", "<div id=msg></div>");
		$this->template->write_view("content", "admin/jobseeker_updatejobcat", $data, TRUE);
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}


	function get_jobcats($id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$curr_date = date("Y-m-d");
		$jobcategories = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_title";
		$jobcategories = $this->Madmin->get_data($jobcategories);
		$list_jobcat = array("0" => "--");
		foreach ($jobcategories["results"] as $a)
		{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
		
		$jobcats = CORE_URL."get_jobcat_by_subscriber_id.php?tx_id=$tx_id&id=$id";
		echo $jobcats."<hr>";
		$jobcats = $this->Madmin->get_data($jobcats);
		echo "<pre>"; print_r($jobcats); echo "</pre>";
		$this->load->view("jobseeker_updatejobcat", $jobcats);
		/*
		echo "<html><head>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."themes/admin/main.css\">
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/date.js\"></script>
			<script src=\"".base_url()."js/jquery.datePicker.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/datePicker.css\">
			<script>
				$(document).ready(function(){

				
					Date.format = 'yyyy/mm/dd';
					$('.date-pick').datePicker({
						
					});
				});
			</script></head><body>";
		*/
		//echo form_open(base_url()."admin/jobseeker/update_jobcats")."\n";
		/*
		echo "<div style=\"display:table;\">";
		echo "<div class=\"row\">";
		echo "<div class=\"cell table_head\">Job Category</div>";
		echo "<div class=\"cell table_head\">Date Add</div>";
		echo "<div class=\"cell table_head\">Date Expired</div>";
		echo "<div class=\"cell table_head\">Job Post</div>";
		echo "<div class=\"cell table_head\"></div>";
		echo "</div>";
		$a = "";
		foreach ($jobcats["results"] as $jobcat)
		{
			echo "<div class=\"row\">";
			echo "<div class=\"cell table_cell\">".$jobcat["jobcat_title"]."</div>";
			echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_add"])."</div>";
			echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_expired"])."</div>";
			
			//if (($curr_date < $jobcat["date_expired"]) && ($curr_date > $jobcat["date_add"]))
			//	echo "<div class=\"cell table_cell\">".form_checkbox("unsub[]", $jobcat["rel_id"], ($jobcat["status"]=="2") ? TRUE : "")."</div>";
			//else
			//	echo "<div class=\"cell table_cell\"></div>";
			
			//echo "<div class=\"cell table_cell\"><a href=".base_url()."admin/jobseeker/editjob/".$jobcat["rel_id"].">Edit</a></div>";
			//echo "<div class=\"cell table_cell\"><a href=".base_url()."admin/jobseeker/jobcat_detail/".$jobcat["rel_id"]." class=\"link_jobcat_detail\">Detail</a></div>";
			echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"get_jobcat_detail(".$jobcat["rel_id"].");\">Detail</span></div>";
			if ($jobcat["status"] == "1")
				echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"unsub(".$jobcat["rel_id"].");\">Unsubscribe</span></div>";
			else
				echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/addjob\">Renew</a></div>";
			echo "</div>";
			$a .= $jobcat["rel_id"].",";
		}
		$a = substr($a, 0, strlen($a)-1);
		
		echo "</div>"; // CLOSE TABLE
		*/
		//echo form_hidden("all_id", $a);
		//echo form_close();
	}
	
	
	function get_jobcat_detail($id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$jobs = CORE_URL."get_jobposts_by_rel_id.php?tx_id=$tx_id&rel_id=$id";
		$jobs = $this->Madmin->get_data($jobs);
		//echo "<pre>"; print_r($jobs); echo "</pre>";
		
		echo "<div style=\"display:table;\">";
		echo "<div class=\"row\">";
		echo "<div class=\"cell table_head\">Job ID</div>";
		echo "<div class=\"cell table_head\">Title</div>";
		echo "<div class=\"cell table_head\">Company</div>";
		echo "<div class=\"cell table_head\">Status</div>";
		echo "<div class=\"cell table_head\">Date Send</div>";
		echo "<div class=\"cell table_head\">Location</div>";
		echo "<div class=\"cell table_head\"># sent</div>";
		echo "</div>";
		$a = "";
		foreach ($jobs["results"] as $job)
		{
			echo "<div class=\"row\">";
			echo "<div class=\"cell table_cell\">".$job["job_id"]."</div>";
			echo "<div class=\"cell table_cell\">".$job["title"]."</div>";
			echo "<div class=\"cell table_cell\">".$job["comp_name"]."</div>";
			switch ($job["status"])
			{
				case "1" : $status = "Active"; break;
				case "2" : $status = "Sent"; break;
				case "3" : $status = "Failed"; break;
			}
			echo "<div class=\"cell table_cell\">$status</div>";
			echo "<div class=\"cell table_cell\">".$job["date_send"]."</div>";
			echo "<div class=\"cell table_cell\">".$job["loc_name"]."</div>";
			echo "<div class=\"cell table_cell\">".$job["n_send"]."</div>";
			echo "</div>";
			$a .= $job["rel_id"].",";
		}
		$a = substr($a, 0, strlen($a)-1);
		
		echo "</div>"; // CLOSE TABLE
	}
	
	
	function unsub($id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$data = CORE_URL."update_rel_subscriber_jobcat.php?tx_id=$tx_id&rel_id=$id&status=3";
		$data = $this->Madmin->get_data($data);
		
		if ($data["status"] == "1")
			echo "<div class=msg>1 subscription has been un-subscribed</div>";
		else
			echo "<div class=msg>".$json["msg"]."</div>";
	}
	
	function unsub2($id=0)
	{
		$jobposter_id = $_SESSION["jobposter_id"];
		$tx_id = $this->Madmin->get_uuid(current_url());
		$data = CORE_URL."update_rel_subscriber_jobcat.php?tx_id=$tx_id&rel_id=$id&status=3&jobposter_id=$jobposter_id&date_update=".urlencode(date("Y-m-d H:i:s"));
		$data = $this->Madmin->get_data($data);
		
		if ($data["status"] == "1")
			echo "<div class=msg>1 subscription has been un-subscribed</div>";
		else
			echo "<div class=msg>".$data["msg"]."</div>";
	}
	
	function search_form($status=1)
	{
		$data["status"] = $status;
		$this->load->view("admin/jobseeker_searchform", $data);
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
		$name 			= ($this->input->post("name")!="") ? ($this->input->post("name")) : "";
		$mentor_id 		= ($this->input->post("mentor_id")!="") ? ($this->input->post("mentor_id")) : 1;
		$status 		= ($this->input->post("status")!="") ? ($this->input->post("status")) : "";
		//$pin	 		= ($this->input->post("pin")!="") ? ($this->input->post("pin")) : "";
		$gender 		= ($this->input->post("gender")!="") ? ($this->input->post("gender")) : "";
		$edu_id 		= ($this->input->post("edu_id")!="") ? ($this->input->post("edu_id")) : "";
		$idcard 		= ($this->input->post("idcard")!="") ? ($this->input->post("idcard")) : "";
		$place_birth 	= ($this->input->post("place_birth")!="") ? ($this->input->post("place_birth")) : "";
		$birthday 		= ($this->input->post("birthday")!="") ? ($this->input->post("birthday")) : "1970/01/01";
		$mdn 			= ($this->input->post("mdn")!="") ? ($this->input->post("mdn")) : "";
		$salary 		= ($this->input->post("salary")!="") ? ($this->input->post("salary")) : "";
		$address1 		= ($this->input->post("address1")!="") ? ($this->input->post("address1")) : "";
		$address2 		= ($this->input->post("address2")!="") ? ($this->input->post("address2")) : "";
		$rt 			= ($this->input->post("rt")!="") ? ($this->input->post("rt")) : "";
		$rw 			= ($this->input->post("rw")!="") ? ($this->input->post("rw")) : "";
		$region 		= ($this->input->post("region")!="") ? ($this->input->post("region")) : 1;
		if ($region == "1")
			$loc_id 	= ($this->input->post("kelurahan")!="") ? ($this->input->post("kelurahan")) : "";
		else if ($region == "2")
			$loc_id 	= ($this->input->post("zip")!="") ? ($this->input->post("zip")) : "";
		$province 		= ($this->input->post("province") != "") ? ($this->input->post("province")) : 0;
		$kotamadya 		= ($this->input->post("kotamadya")!="") ? ($this->input->post("kotamadya")) : 0;
		$kecamatan 		= ($this->input->post("kecamatan")!="") ? ($this->input->post("kecamatan")) : 0;
		$kelurahan 		= ($this->input->post("kelurahan")!="") ? ($this->input->post("kelurahan")) : 0;
		$lat			= ($this->input->post("lat")!="") ? ($this->input->post("lat")) : "";
		$lng			= ($this->input->post("lng")!="") ? ($this->input->post("lng")) : "";
		$zip	 		= ($this->input->post("zip")!="") ? ($this->input->post("zip")) : 0;
		$subscriber_id 	= !is_null($this->input->post("subscriber_id")) ? ($this->input->post("subscriber_id")) : "";
		
		return array(
			"name" => $name,
			"mentor_id" => $mentor_id,
			"status" => $status,
			//"pin" => $pin,
			"gender" => $gender,
			"edu_id" => $edu_id,
			"idcard" => $idcard,
			"place_birth" => $place_birth,
			"birthday" => $birthday,
			"mdn" => $mdn,
			"salary" => $salary,
			"address1" => $address1,
			"address2" => $address2,
			"rt" => $rt,
			"rw" => $rw,
			"region" => $region,
			"province" => $province,
			"kotamadya" => $kotamadya,
			"kecamatan" => $kecamatan,
			"kelurahan" => $kelurahan,
			"loc_id" => $loc_id,
			"zip" => $zip,
			"lat" => $lat,
			"lng" => $lng,
			"subscriber_id" => $subscriber_id
		);
	}

	function get_lists($tx_id)
	{
		$educations = CORE_URL."get_educations.php?tx_id=$tx_id&order=edu_id";
		$educations = $this->Madmin->get_data($educations);
		$provinces = CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=0";
		$provinces = $this->Madmin->get_data($provinces);
		$mentors = CORE_URL."get_mentors.php?tx_id=$tx_id&order=name";
		$mentors = $this->Madmin->get_data($mentors);
		$zips = CORE_URL."get_location_by_kelurahan.php?tx_id=$tx_id";
		$zips = $this->Madmin->get_data($zips);
		
		return array(
			"educations" => $educations["results"],
			"provinces" => $provinces,
			"mentors" => $mentors["results"],
			"zips" => $zips["results"]
		);
	}
	
	function create_header($lat="", $lng="", $kelurahan=0)
	{
		$header = "<script src=\"".base_url()."js/jquery.js\"></script>";
		
		$header .= "<script src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>
			<script src=\"http://code.google.com/apis/gears/gears_init.js\"></script>";
		if ($lat != "")
			$header .= "<script src=\"".base_url()."js/gmap_nogeolocation.js\"></script>";
		else
			$header .= "<script src=\"".base_url()."js/geolocation2.js\"></script>";
		
		$header .= "<script src=\"".base_url()."js/date.js\"></script>
			<script src=\"".base_url()."js/jquery.datepicker.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/datepicker.css\">

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
							
							$(\"#name\").val(a.name);
							$(\"#cp\").val(a.cp);
							$(\"#tel\").val(a.tel);
							$(\"#fax\").val(a.fax);
							$(\"#email\").val(a.email);
						}
					});
				};
				
				$(document).ready(function(){

					$('#province').chainSelect('#kotamadya','".base_url()."func/get_location_by_parent_id.php');
					$('#kotamadya').chainSelect('#kecamatan','".base_url()."func/get_location_by_parent_id.php');
					$('#kecamatan').chainSelect('#kelurahan','".base_url()."func/get_location_by_parent_id.php','".$kelurahan."');
					
					Date.firstDayOfWeek = 1;
					Date.format = 'yyyy/mm/dd';
					$('.date-pick').datePicker({
						startDate: '01/01/1920',
						endDate: (new Date()).asString()
					});
					
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
					else if ($('#region').val() == '2')
					{
						//$('#lat').val('');
						//$('#lng').val('');
						$('#kelurahan').val('');
						$('.by').css('display','none');
						$('#byZip').css('display','block');
					}

					$('#region').change(function(){
						if($(this).val() == '3')
						{
							$('.by').css('display','none');
							$('#byMap').css('display','block');
							$('#kelurahan').val('-');
							$('#zip').val('-');";
							if ($lat == "")
								$header .= "initialize(); ";
							else
								$header .= "initialize($lat, $lng); ";
						$header .= "
						}
						if($(this).val() == '1')
						{
							$('.by').css('display','none');
							//$('#lat').val('');
							//$('#lng').val('');
							$('#zip').val('-');
							$('#byRegion').css('display','block');
						}
						if($(this).val() == '2')
						{
							//$('#lat').val('');
							//$('#lng').val('');
							$('#kelurahan').val('');
							$('.by').css('display','none');
							$('#byZip').css('display','block');
						}
					});
					
					$('#zip').change(function()
					{
						$('#hdnZip').val($('#zip :selected').text());
					});
				});
			</script>		
		";
		$this->template->write("header", $header);
		
	}
	

	function check_form()
	{
		$this->form_validation->set_rules('name', 'Full name', 'required');
		$this->form_validation->set_rules('status', 'Status', 'callback_custom_err_msg');
		$this->form_validation->set_rules('gender', 'Gender', 'callback_custom_err_msg');
		$this->form_validation->set_rules('edu_id', 'Latest Education', 'callback_custom_err_msg');
		$this->form_validation->set_rules('birthday', 'Date of birth', 'required');
		$this->form_validation->set_rules('mdn', 'Phone', 'required');
		$this->form_validation->set_rules('province','Province','required');
		$this->form_validation->set_rules('kotamadya','Kotamadya','required');
		$this->form_validation->set_rules('kecamatan','Kecamatan','required');
		
		if ($_POST["region"] == "1") 
		{
			$this->form_validation->set_rules('province','Province','callback_custom_err_msg');
			$this->form_validation->set_rules('kotamadya','Kotamadya','callback_custom_err_msg');
			$this->form_validation->set_rules('kecamatan','Kecamatan','callback_custom_err_msg');
			$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'callback_custom_err_msg');
		}
		else if ($_POST["region"] == "2") $this->form_validation->set_rules('zip', 'Zip code', 'callback_custom_err_msg');
			
	}
	
	
}
?>