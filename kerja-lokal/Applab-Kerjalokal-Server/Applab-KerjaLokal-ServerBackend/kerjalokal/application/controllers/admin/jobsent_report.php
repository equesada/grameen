<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobsent_report extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
		
		$this->load->helper('date');
	}

	function manage($page=1, $order="a_jobsend_id", $default_row=20, $date_send1="", $date_send2="", $status="", $jobseeker_name="", $jobcat_id="", $jobcat_id2="", $comp_id="", $mdn="", $sms_status="",$type="", $job_title="", $seekerkodya="", $jobkodya="", $rel_id="", $no_njfu=""){ //$loc_id="", $loc_title="", $company_id="", 
		$useraccess = array("superadmin", "admin", "company");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Job Sent Report";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{			
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = (!empty($default_row) || !empty($search["default_row"])) ? $default_row : 20;			
			if (sizeof($_POST) > 0)
			{
				$date_send1 					= $this->input->post('date_send1');
				$date_send2 					= $this->input->post('date_send2');
				$location_id	 				= $this->input->post('loc_id');
				$mdn	 						= $this->input->post('mdn');				
				$search["date_send1"] 			= ($date_send1['from_date'] && $date_send1['to_date'] != "") ? implode(':',$_POST["date_send1"]) : "_";
				$search["date_send2"] 			= ($date_send2['from_date'] && $date_send2['to_date'] != "") ? implode(':',$_POST["date_send2"]) : "_";
				$search["status"] 				= $this->input->post("status") != 0 ? $_POST["status"] : "";
				$search["jobseeker_name"] 		= $this->input->post("jobseeker_name") != "" ? $_POST["jobseeker_name"] : "_";
				$search["jobcat_id"] 			= $this->input->post("jobcat_id") != "" ? $_POST["jobcat_id"] : "_";
				$search["jobcat_id2"] 			= $this->input->post("jobcat_id2") != "" ? $_POST["jobcat_id2"] : "_";
			//	$search["loc_id"] 				= $this->input->post("loc_id") != 0 ? $_POST["loc_id"] : "_";
			//	$search["loc_title"] 			= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "_";
				$search["comp_id"] 				= $this->input->post("comp_id") != 0 ? $_POST["comp_id"] : "_";
			//	$search["company_id"] 			= $this->input->post("company_id") != 0 ? $_POST["company_id"] : "_";
				$search["mdn"] 					= $this->input->post("mdn") != "" ? $_POST["mdn"] : "_";
				$search["sms_status"]			= $this->input->post("sms_status") != "" ? $_POST["sms_status"] : "_";
				$search["type"]					= $this->input->post("type") != "" ? $_POST["type"] : "_";
				$search["job_title"]			= $this->input->post("job_title") != "" ? $_POST["job_title"] : "_";
				$search["seekerkodya"]			= $this->input->post("seekerkodya") != "" ? $_POST["seekerkodya"] : "_";
				$search["jobkodya"]				= $this->input->post("jobkodya") != "" ? $_POST["jobkodya"] : "_";
				$search["rel_id"] 				= $this->input->post("rel_id") != "" ? $_POST["rel_id"] : "_";
				$search["no_njfu"] 				= $this->input->post("no_njfu") != "" ? $_POST["no_njfu"] : "_";
				//echo "<pre>"; print_r($search); echo "</pre>";
				//$search["rel_id"] 				= $this->input->post("rel_id") != null ? $_POST["rel_id"] : null;				
			}else{
				$search["date_send1"] 			= (!empty($_GET['date_send1'])) ? $date_send1 : $date_send1; //($date_send1 == null) ? "" : $date_send1; ($date_send2 == null) ? "" : $date_send2;
				$search["date_send2"] 			= (!empty($_GET['date_send2'])) ? $date_send2 : $date_send2; 
				$search["status"] 				= (!empty($_GET['status'])) ? $status : $status;
				$search["jobseeker_name"] 		= (!empty($_GET['jobseeker_name'])) ? $jobseeker_name : $jobseeker_name;
				$search["jobcat_id"] 			= (!empty($_GET['jobcat_id'])) ? $jobcat_id : $jobcat_id;
				$search["jobcat_id2"] 			= (!empty($_GET['jobcat_id2'])) ? $jobcat_id2 : $jobcat_id2;
			//	$search["loc_id"] 				= (!empty($_GET['loc_id'])) ? $loc_id : $loc_id;	
			//	$search["loc_title"] 			= (!empty($_GET['loc_title'])) ? $loc_title : $loc_title;	
				$search["comp_id"] 				= (!empty($_GET['comp_id'])) ? $comp_id : $comp_id;
			//	$search["company_id"] 			= (!empty($_GET['company_id'])) ? $company_id : $company_id;
				$search["mdn"]			 		= (!empty($_GET['mdn'])) ? $mdn : $mdn; 
				$search["sms_status"]			= (!empty($_GET['sms_status'])) ? $_GET['sms_status'] : $sms_status;	
				$search["type"]					= (!empty($_GET['type'])) ? $_GET['type'] : $type;	
				$search["job_title"]			= (!empty($_GET['job_title'])) ? $_GET['job_title'] : $job_title;	
				$search["seekerkodya"]			= (!empty($_GET['seekerkodya'])) ? $seekerkodya : $seekerkodya;	
				$search["jobkodya"]				= (!empty($_GET['jobkodya'])) ? $jobkodya : $jobkodya;	
				$search["rel_id"] 				= (!empty($_GET['rel_id'])) ? $_GET['rel_id'] : $rel_id;	
				$search["no_njfu"] 				= (!empty($_GET['no_njfu'])) ? $_GET['no_njfu'] : $no_njfu;	
			}
			
			$search_uri 						= http_build_query($search,'','&');
			//echo "<pre>"; print_r($search); echo "</pre>"; //echo $search_uri."<BR>";
			$jobsent = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;				
			
			//echo $jobsent;
			$data = $this->Madmin->get_data($jobsent);
			//echo "<pre>"; print_r($data); echo "</pre>";exit();
									
			$data["list_status"] = array(
				"0" => "ALL",
				"1" => "Active",
				"2" => "Inactive",
				"3" => "Draft",
				"4" => "Waiting for Approval",
				"5" => "Rejected"
			);
			$data["status"] = $status;
			
			$data["form_submit"] = current_url();
			//echo "<pre>awal"; print_r($search); echo "</pre><hr>";
			$data["page"] = $page;
			$data["search"] = $search;
			//$data["search_val"] = (isset($search_val) ? $search_val : '');
			//print_r($search);
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			//echo "<pre>"; print_r($data); echo "</pre>";

			//$data["search_link"] = base_url()."admin/jobpost/search_form/".$search["status"]."/";
			$this->template->write("header", "			
			<script src=\"".base_url()."js/jquery-1.6.2.min.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".base_url()."css/jquery-ui-1.8.16.custom.css\">
			");
			
			$data["result_row"] = array(20 => 20, 50 => 50, 90 => 90);
			$data["default_row"] = (!empty($_POST['result_row'])) ? $_POST['result_row'] : $ndata;
			$data['filter'] = $this->search_form_adv($status=1);
			$data['filter']['search'] = $data['search'];
			$data['filter']['form_submit'] = base_url()."admin/jobsent_report/manage/1/d_jobsend_id";
			//echo "<pre>awal"; print_r($data['search']); echo "</pre><hr>";
			$data['search_form'] = $this->load->view('admin/jobsent_report_searchform_adv',$data['filter'],TRUE);
			
			$this->template->write("title", $title);
			
			if($this->session->flashdata('msg')) $this->template->write("msg", "<div class=msg>".$this->session->flashdata('msg')."</div>");
			$this->template->write_view("content", "admin/jobsent_report_view", $data);			
			//$this->template->render();
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function search_form_adv($status=1, $loc_title="", $date_send1="", $mdn="")
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
		//$data["status"]		= $status;		
	
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
		
/*		// query for job locations
		$sql3 = "SELECT * FROM location WHERE loc_type = 'KOTAMADYA'";		
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
*/		
		// list job status, see status table
		$data["list_status"] = array(
			"0" => "ALL",
			"1" => "Active",
			"2" => "Inactive",
			"3" => "Draft",
			"4" => "Waiting for Approval",
			"5" => "Rejected"
		);
		
		$data["location"] = $location;
				
		$data["status"] = $status;		
		$data['locations'] = $rows_location;
		$data['companies'] = $rows_company;
		$data["mdn"] = $mdn;	
		return $data;
	}
	
	function str_csv($str)
	{
		return str_replace(",", ".", $str);
	}
	
	function search_form($status=1)
	{
		$industry = CORE_URL."get_industries.php?order=industry_title";
		$data["industry"] = $this->Madmin->get_data($industry);
		$data["status"] = $status;
		$this->load->view("admin/jobsent_report_search", $data);
	}
	function save_csv($order="a_jobsend_id",$date_send1="", $date_send2="", $status="", $jobseeker_name="", $jobcat_id="", $jobcat_id2="", $comp_id="", $mdn="", $sms_status="",$type="", $job_title="", $seekerkodya="", $jobkodya="", $rel_id="", $no_njfu="") //{ //$loc_id="", $loc_title="", $company_id="", 	
	//function save_csv($order="a_jobseeker_name", $status="", $date_send1="", $sent_time1="", $jobseeker_name="", $job_kotamadya="", $date_send2="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Company as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobsent_report.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = 0;
			$search["date_send1"] = $date_send1;
			$search["date_send2"] = $date_send2;
			$search["status"] = $status;
			$search["jobseeker_name"] = $jobseeker_name;
			$search["jobcat_id"] = $jobcat_id; 
			$search["jobcat_id2"] = $jobcat_id2; 
			$search["comp_id"] = $comp_id; 
			$search["mdn"] = $mdn; 
			$search["sms_status"] = $sms_status;
			$search["type"] = $type; 
			$search["job_title"] = $job_title; 
			$search["seekerkodya"] = $seekerkodya; 
			$search["jobkodya"] = $jobkodya;
			$search["rel_id"] = $rel_id; 
			$search["no_njfu"] = $no_njfu;						
			if ( ($search["date_send1"] != "_") || ($search["date_send2"] != "_") || ($search["status"] != "_") || ($search["jobseeker_name"] != "_") || ($search["jobcat_id"]!= "_") || ($search["jobcat_id2"]!= "_") ||($search["comp_id"]!= "_") ||($search["mdn"]!= "_") ||($search["sms_status"]!= "_") ||	($search["type"]!= "_") || ($search["job_title"]!= "_") || ($search["seekerkodya"]!= "_") || ($search["jobkodya"]!= "_") || ($search["rel_id"] != "_") || ($search["no_njfu"]!= "_") )
			{
				//echo "test";
				$var = "";
			//	foreach($search as $key => $val)
			//	{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$search_uri = http_build_query($search,'','&');				
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter")){
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id$var&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;
					//echo"1<pre>";print_r($json);echo"</pre>";exit();
				}else{
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc".'&'.$search_uri;
					//echo"2<pre>";print_r($json);echo"</pre>";exit();
				}
			}
			else
			{
				//echo "jajal";
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc";
			}

			$data = $this->Madmin->get_data($json);
			//print_r($data);
			$output = "jobsend_id, send_date, sent_time, subscription_id, subscription_category, subscriber_id, mdn, jobseeker_name, jobseeker_kodya, job_id, job_title, job_category, job_kodya, company_name, distance, status, send_date2, sent_time2 \n";
			foreach($data["results"] as $job)
			{									
				$output .= $this->str_csv($job["jobsend_id"]).",".
					
					$this->str_csv($job["date_send1"]).",".					
					$this->str_csv($job["sent_time1"]).",".					
					$this->str_csv($job["rel_id"]).",".					
					$this->str_csv($job["jobcat_key"]).",".
					$this->str_csv($job["subscriber_id"]).",".															
					$this->str_csv($job["mdn"]).",".					
					$this->str_csv($job["jobseeker_name"]).",".
					$this->str_csv($job["seeker_kodya"]).",". //$this->str_csv($job["loc_title"]).",".					
					$this->str_csv($job["job_id"]).",".
					$this->str_csv($job["title"]).",".
					$this->str_csv($job["jobcat_key"]).",".
					$this->str_csv($job["job_kodya"]).",".
					$this->str_csv($job["company_name"]).",".
					$this->str_csv($job["dis"]).",";
//					.$this->str_csv($job["status"]).",";					
				switch ($job["status"])
				{
					case 1 : $output .= "Success"; break;
					case 2 : $output .= "DMS"; break;
					default : $output .= " ";
				}				
				$output .= ",".$this->str_csv($job["date_send2"]).",".
					$this->str_csv($job["sent_time2"])."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	
	
	function save_xls($order="a_jobseeker_name", $status="", $jobseeker_name="", $job_kotamadya="", $date_send2="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Company as XLS";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{

			header("Content-type: application/xls");
			header("Content-Disposition: attachment; filename=jobsent.xls");
			header("Pragma: no-cache");
			header("Expires: 0");

			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = 0;
			$search["status"] = $status;
			$search["jobseeker_name"] = $jobseeker_name;
			$search["job_kotamadya"] = $job_kotamadya;
			$search["date_send2"] = $date_send2;
			if (($search["status"] != "_") || ($search["jobseeker_name"] != "_") || ($search["job_kotamadya"] != "_") || ($search["date_send2"] != "_" || ($search["date_send2"] == ""))) 
			{
				//echo "test";
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter")){
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}else{
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc";
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}
			}
			else
			{
				//echo "jajal";
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_all_jobsent.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";
			}

			$data = $this->Madmin->get_data($json);
			//print_r($data);
			$output = "jobsend_id, jobcat_key, mdn, jobseeker_name, loc_title, company_name, company_name, status, date_send2,  sent_time2 \n";
			foreach($data["results"] as $job)
			{
				$output .= $this->str_csv($job["jobsend_id"]).",".
					$this->str_csv($job["jobcat_key"]).",".
					
					$this->str_csv($job["jobseeker_name"]).",".
					$this->str_csv($job["loc_title"]).",".
					$this->str_csv($job["company_name"]).",".
					$this->str_csv($job["status"]).",";
					
				switch ($job["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}
				
				$output .= ",".$this->str_csv($job["date_send2"]).",".
					$this->str_csv($job["sent_time2"])."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	
	
	
	function create_header($lat="", $lng="", $kelurahan=0)
	{
			
		$header = "<script src=\"".base_url()."js/calendar.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/calendar.css\">
			</script>		
		";
		$this->template->write("header", $header);
		
	}
	
	
	
	
	
}

?>