<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscription extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
	

	function manage($page=1, $order="a_subscriber_name", $default_row=20, $status="", $jobcat_id="", $subscriber_name="", $mdn="", $mentor_name="", $date_add="", $date_expired="", $loc_title="", $loc_id="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Subscription Internal";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			
			$ndata = (!empty($default_row)) ? $default_row : 20;
			$search_uri = "";
			$search = array();
			if (sizeof($_POST) > 0)
			{
				$location_id	 				= $this->input->post('loc_id');
				$date_add	 					= $this->input->post('date_add');				
				
				$search["status"] 				= $this->input->post("status") != "" ? $_POST["status"] : "";				
				$search["jobcat_id"] 			= $this->input->post("jobcat_id") != "" ? $_POST["jobcat_id"] : "_";
				$search["subscriber_name"] 		= $this->input->post("subscriber_name") != "" ? $_POST["subscriber_name"] : "_";				
				$search["mdn"] 					= $this->input->post("mdn") != "" ? $_POST["mdn"] : "_";
				$search["mentor_name"]		 	= $this->input->post("mentor_name") != "" ? $_POST["mentor_name"] : "_";
				$search["date_add"] 			= ($date_add['from_date'] && $date_add['to_date'] != "") ? implode(':',$_POST["date_add"]) : "_";	
				$search["date_expired"]			= $this->input->post("date_expired") != "" ? $_POST["date_expired"] : "_";
				$search["loc_title"] 			= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "_";
				$search["loc_id"] 				= $this->input->post("loc_id") != 0 ? $_POST["loc_id"] : "_";		
				
				foreach($search as $key => $val)
				{ $search_uri .= "$val/"; }
				redirect(base_url()."admin/subscription/manage/$page/$order/$default_row/$search_uri");
				
			}else{
				$search["status"]				= (!empty($_GET['status'])) ? $_GET['status'] : $status;
				$search["jobcat_id"] 			= (!empty($_GET['jobcat_id'])) ? $jobcat_id : $jobcat_id;
				$search["subscriber_name"] 		= urldecode($subscriber_name);
				$search["mdn"] 					= (!empty($_GET['mdn'])) ? $mdn : $mdn;		
				$search["mentor_name"] 			= urldecode($mentor_name);
				$search["date_add"] 			= ($date_add == null) ? "" : $date_add;
				$search["date_expired"]   		= (!empty($_GET['date_expired'])) ? $date_expired : $date_expired;
				$search["loc_title"] 			= (!empty($_GET['loc_title'])) ? $loc_title : $loc_title; 	
				$search["loc_id"] 				= $loc_id;	
			}
			
			//$search_uri 						= http_build_query(@$search,'','&');
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			
			
			
			$json = CORE_URL."get_subscription.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;				
			//echo "<!-- $json -->";

			//echo "<pre>"; print_r($json); echo "</pre>";
			$data = $this->Madmin->get_data($json);			
			
			$data["status"] = $status;
			//$data["form_submit"] = current_url();
			$data['form_submit'] = base_url()."admin/subscription/manage/1/$order/$default_row/";
			
			$data["page"] = $page;
			$data["search"] = $search;
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			
			$data["result_row"] = array(20 => 20, 50 => 50, 90 => 90);
			$data["default_row"] = (!empty($_POST['result_row'])) ? $_POST['result_row'] : $ndata;

			
			$data["search_link"] = "";
			$this->template->write("header", "
			
			<link rel=\"stylesheet\" href=\"".base_url()."css/jquery.ui.all.css\">
			<link rel=\"stylesheet\" href=\"".base_url()."css/demos.css\">
			<script src=\"".base_url()."js/jquery.ui.core.js\"></script>
			<script src=\"".base_url()."js/jquery.ui.widget.js\"></script>
			<script src=\"".base_url()."js/jquery.ui.tabs.js\"></script>
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
				function unsub(id)
				{
					$.ajax({
						type: \"GET\",
						url: \"".base_url()."admin/subscription/unsub/\"+id,
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
			</script>		
			
			");
			
//									 search_form_adv($status=1, $loc_title="", $jobseeker_name="", $mdn="", $mentor="", $date_add="", $jobcat_id="")
			
			$data['filter'] = $this->search_form_adv($status=1, $search['loc_title'], $search['subscriber_name'], $search['mdn'], $search['mentor_name'], $search['date_add'], $search['jobcat_id']);
			$data['filter']['search'] = $data['search'];
			//$data['filter']['form_submit'] = base_url()."admin/subscription/manage/1/d_rel_id";
			$data['filter']['form_submit'] = base_url()."admin/subscription/manage/1/$order/$default_row/";
			
			$lokasi["kotamadya_id"] = $this->input->post("kotamadya_id") != 0 ? $_POST["kotamadya_id"] : "";
			$lokasi["kecamatan_id"] = $this->input->post("kecamatan_id") != 0 ? $_POST["kecamatan_id"] : "";
			$lokasi["kelurahan_id"] = $this->input->post("kelurahan_id") != 0 ? $_POST["kelurahan_id"] : "";
			$data["lokasi"] = $lokasi;	
						
			$data['filter']['lokasi'] = $data['lokasi'];
			
			$data['search_form'] = $this->load->view('admin/subscription_searchform_adv',$data['filter'],TRUE);
			
			
			$this->template->write("title", $title);
			$this->template->write("msg", "<div id=msg></div>");
			//echo "<!-- <pre>"; print_r($search); echo "</pre> -->";
			//if($this->session->flashdata('msg')) $this->template->write("msg", "<div class=msg>".$this->session->flashdata('msg')."</div>");
			$this->template->write_view("content", "admin/subscription_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	
	function unsub($id=0)
	{
		$jobposter_id = array("superadmin", "admin", "company", "jobposter");
		//$jobposter_id = $_SESSION["superadmin", "admin"];
		$tx_id = $this->Madmin->get_uuid(current_url());
		$data = CORE_URL."update_rel_subscriber_jobcat.php?tx_id=$tx_id&rel_id=$id&status=3&jobposter_id=$jobposter_id&date_update=".urlencode(date("Y-m-d H:i:s"));
		//die($data);
		$data = $this->Madmin->get_data($data);
		
		if ($data["status"] == "1")
			echo "<div class=msg>1 subscription has been un-subscribed</div>";
		else
			echo "<div class=msg>".$data["msg"]."</div>";
	}
	
	function search_form_adv($status=1, $loc_title="", $subscriber_name="", $mdn="", $mentor_name="", $date_add="", $jobcat_id="")
	{
		$category  = CORE_URL."get_jobcategories.php?order=jobcat_title";
		$company  = CORE_URL."get_companies.php?order=name";
		$location['KELURAHAN'] = CORE_URL."get_location_by_kotamadya.php?order=name";
		$location['KECAMATAN'] = CORE_URL."get_location_by_kecamatan.php?order=name";
		$location['KOTAMADYA'] = CORE_URL."get_location_by_kelurahan.php?order=name";
	
		$company_results				= $this->Madmin->get_data($company);
		$location_results['KELURAHAN']	= $this->Madmin->get_data($location['KELURAHAN']);
		$location_results['KECAMATAN']	= $this->Madmin->get_data($location['KECAMATAN']);
		$location_results['KOTAMADYA']	= $this->Madmin->get_data($location['KOTAMADYA']);

		//$data["list_jobcat"]	= $this->Madmin->get_data($jobcats);
		$data["jobcats_title"]	= $this->Madmin->get_data($category);		
		$data["status"]		= $status;		
	
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
		/*
		$sql3 = "SELECT * FROM location WHERE parent_id = 0";		
		$sql3 = str_replace("\r\n","",$sql3);
		$sql3 = urlencode($sql3);		
		$sql3 = CORE_URL."sql.php?sql=".$sql3;
		*/
		$sql3 = CORE_URL."get_location.php?key=parent_id&value=0";
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
		/*
		$data["list_status"] = array(
			"0" => "ALL",
			"1" => "Active",
			"2" => "Unreg by Admin",
			"3" => "Unreg by User",
			"4" => "Not Enough Balance",
			"5" => "Renew"
		);
		*/
		$data["list_status"] = array(
			"0" => "ALL",
			"1" => "Active",
			"2" => "Unreg by User",
			"3" => "Unreg by Admin",
			"4" => "Renew",
			"5" => "Not Enough Balance"
		);
		
		
		//$data["approver"] = $approver;
		//$data["jbposter"] = $jbposter;
		$data["location"] = $location;
		
		$data["status"] = $status;
		$data["subscriber_name"] = $subscriber_name;	
		$data["mdn"] = $mdn;	
		$data["mentor_name"] = $mentor_name;	
		$data['locations'] = $rows_location;
		
		//$data['jobcats']['form_submit'] = base_url()."admin/subscription/manage/1/d_subscriber_id";
		return $data;
	}
	
	
	function ajax_get_location_by_id($a) {
		/*
		$sql1 = "SELECT loc_id, parent_id, name FROM `location` WHERE `parent_id` = ".$a."";
		$sql1 = str_replace("\r\n","",$sql1);
		$sql1 = urlencode($sql1);
		$sql1 = CORE_URL."sql.php?sql=".$sql1;
		*/
		$sql1 = CORE_URL."get_location.php?key=parent_id&value=".$a."";
		$location_rows = $this->Madmin->get_data($sql1);		
		$location[0] = "All";
		foreach ($location_rows['results'] as $locations) {
				$location[$locations['loc_id']] = ucfirst($locations['name']);				
		}	
		
		echo json_encode( $location );
		//echo "<pre>"; print_r($location); echo "</pre>";
	}
	function str_csv($str)
	{
		return str_replace(",", ".", $str);
	}
	
	//function manage($page=1, $order="a_subscriber_name", $status="", $jobcat_id="", $subscriber_name="", $mdn="", $mentor_name="", $date_add="", $date_expired="", $loc_title="")
	
	function save_csv($order="a_subscriber_name", $status="", $jobcat_id="", $subscriber_name="", $mdn="", $mentor_name="", $date_add="", $date_expired="", $loc_title="", $loc_id="")	
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
			
			$search["status"] 			= $status;
			$search["jobcat_id"] 		= $jobcat_id;			
			$search["subscriber_name"] 	= $subscriber_name;			
			$search["mdn"] 				= $mdn;
			$search["mentor_name"] 		= $mentor_name;
			$search["date_add"] 		= $date_add;						
			$search["date_expired"] 	= $date_expired;
			$search["loc_title"] 		= $loc_title;
			$search["loc_id"] 			= $loc_id;
			
			$search_uri = "";
			foreach($search as $key => $val)
			{ if ($search[$key] != "_") $search_uri .= "&$key=".urlencode($val); }
			
			
			//if (($search["status"] != "_") || ($search["jobcat_id"] != "_") || ($search["subscriber_name"] != "_") || ($search["mdn"] != "_") || ($search["mentor_name"] != "_") || ($search["date_add"] != "_") || ($search["date_expired"] != "_") || ($search["loc_title"] != "_") || ($search["loc_id"] != "_")) 			
			
			//{				
				$var = "";
				//foreach($search as $key => $val)
				//{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				//$search_uri = http_build_query($search,'','&');		
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscription.php?tx_id=$tx_id$var&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;
				else					
					$json = CORE_URL."get_subscription.php?tx_id=$tx_id&order=$orderby&ascdesc=$ascdesc".'&'.$search_uri;					
					//echo $json."<hr>";
			/*
			}
			else
			{				
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_subscription.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else					
					$json = CORE_URL."get_subscription.php?tx_id=$tx_id&filter_key=$filter_key&filter_value=$filter_value&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=0";
			}
			*/
			$data = $this->Madmin->get_data($json);
			//echo"<pre>";print_r($data);echo"</pre>";exit();
			$output = "rel_id, status, date_add, date_expired, jobcat_title, mdn, subscriber_id, subscriber_name, kelurahan_name, kecamatan_name, kotamadya_name, province_name, n_njfu, n_job, n_all_job, mentor_name\n";
			foreach($data["results"] as $seeker)
			{
				$output .= $seeker["rel_id"].",";
				
				switch ($seeker["status"])
				{					
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Unreg by User"; break;
					case 3 : $output .= "Unreg by Admin"; break;
					case 4 : $output .= "Renew"; break;
					case 5 : $output .= "Not enough balance"; break;
					default : $output .= " ";
				}
				$output .= ",".$seeker["date_add"].",".
							   $seeker["date_expired"].",". 
							   $seeker["jobcat_title"].",". 
							   $seeker["mdn"].",". 
							   $seeker["subscriber_id"].",". 
							   $seeker["subscriber_name"].",". 
							   $seeker["loc_title"].",". 
							   $seeker["kecamatan_name"].",". 
							   $seeker["kotamadya_name"].",". 
							   $seeker["province_name"].",". 
							   $seeker["n_njfu"].",". 
							   $seeker["n_job"].",". 
							   $seeker["n_all_job"].",". 
							   $seeker["mentor_name"]."\n";
				
				
				
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
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