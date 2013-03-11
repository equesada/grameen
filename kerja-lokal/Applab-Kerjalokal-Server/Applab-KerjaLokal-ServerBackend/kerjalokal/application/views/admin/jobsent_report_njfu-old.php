<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobsent_report_njfu extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
		
		$this->load->helper('date');
	}
		
	function manage($page=1, $order="a_jobsend_id", $list_industry1=""){
		$useraccess = array("superadmin", "admin");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Job Sent Report No Job For You";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ndata = 20;
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			
			$jobsent = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";
			//die($jobsent);
			$data = $this->Madmin->get_data($jobsent);
			//echo "<pre>"; print_r($data); echo "</pre>";exit();
						
			$this->template->write("title", $title);
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			
			$this->template->write_view("content", "admin/jobsent_report_njfu_view", $data);			
			$this->template->render();
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
	
	function search_form($status=1)
	{
		$industry = CORE_URL."get_industries.php?order=industry_title";
		$data["industry"] = $this->Madmin->get_data($industry);
		$data["status"] = $status;
		$this->load->view("admin/jobsent_report_njfu_search", $data);
	}
	
	function save_csv($order="a_jobseeker_name", $status="", $jobseeker_name="", $job_kotamadya="", $date_send2="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Company as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobsent.csv");
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
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}else{
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc";
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}
			}
			else
			{
				//echo "jajal";
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";
			}

			$data = $this->Madmin->get_data($json);
			//print_r($data);
			$output = "jobsend_id, jobcat_key, mdn, jobseeker_name, jobseeker_kotamadya, company_name, company_name, status, date_send2,  sent_time2 \n";
			foreach($data["results"] as $job)
			{
				$output .= $this->str_csv($job["jobsend_id"]).",".
					$this->str_csv($job["jobcat_key"]).",".
					
					$this->str_csv($job["jobseeker_name"]).",".
					$this->str_csv($job["jobseeker_kotamadya"]).",".
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
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}else{
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc";
					//echo"<pre>";print_r($json);echo"</pre>";exit();
				}
			}
			else
			{
				//echo "jajal";
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_all_jobsent_njfu.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";
			}

			$data = $this->Madmin->get_data($json);
			//print_r($data);
			$output = "jobsend_id, jobcat_key, mdn, jobseeker_name, jobseeker_kotamadya, company_name, company_name, status, date_send2,  sent_time2 \n";
			foreach($data["results"] as $job)
			{
				$output .= $this->str_csv($job["jobsend_id"]).",".
					$this->str_csv($job["jobcat_key"]).",".
					
					$this->str_csv($job["jobseeker_name"]).",".
					$this->str_csv($job["jobseeker_kotamadya"]).",".
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