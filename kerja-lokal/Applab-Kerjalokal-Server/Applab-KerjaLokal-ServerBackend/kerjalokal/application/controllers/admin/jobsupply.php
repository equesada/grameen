<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobsupply extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
    function index(){
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Dashboard";
		$this->template->write("content", "");
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
		
	}
	function manage()
	{
		$useraccess = array("superadmin","admin","company");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->check_var();
			
			$ndata = 20;
			$jobcats = CORE_URL."get_jobcategories.php?tx_id=$tx_id&order=jobcat_id&country_id=".$_SESSION["curr_country"];
			$data["jobcats"] = $this->Madmin->get_data($jobcats);
						
			$jobs = CORE_URL."sql.php?sql=";
			$jobs .= urlencode("SELECT * FROM jobs INNER JOIN location ON SUBSTRING(jobs.loc_id,6) = location.loc_id WHERE 
				jobs.date_expired > ".date("Y-m-d")."
				AND jobs.status=1");								
			//echo $jobs;			
			
			$jobs = CORE_URL."get_jobposts_by_id2.php?tx_id=$tx_id&date_expired=".date("Y-m-d")."&ndata=0&page=1&order=job_id&ascdesc=asc&id=0";
			$jobs = $this->Madmin->get_data($jobs);
			//echo "<pre>"; print_r($jobs); echo "</pre>";
			/*
			$subscribers = CORE_URL."sql.php?sql=";
			$subscribers = "SELECT * FROM subscribers 
				INNER JOIN location ON SUBSTRING(subscribers.loc_id,6) = location.loc_id
				INNER JOIN rel_subscriber_cat ON subscribers.subscriber_id = rel_subscriber_cat.subscriber_id
				WHERE rel_subscriber_cat.date_expired > ".date("Y-m-d")."";
			*/
			/*
			$subscribers = str_replace("\r\n","",$subscribers);
			$subscribers = urlencode($subscribers);
			$subscribers = CORE_URL."sql.php?sql=".$subscribers;
			*/
			$subscribers = CORE_URL."jobsupply_get_subscriber.php";			
			
			//echo $subscribers;
			$subscribers = $this->Madmin->get_data($subscribers);
			//echo "<pre>"; print_r($subscribers); echo "</pre>";
			
			$njob = array();
			$nsubscriber = array();
			
			foreach ($data["jobcats"]["results"] as $jobcat)
			{
			
				$njob[$jobcat["jobcat_id"]][0] = 0;
				$nsubscriber[$jobcat["jobcat_id"]][0] = 0;
				for ($a = 1; $a <= 6; $a++)
				{ 
					//$njob[$jobcat["jobcat_id"]][$a]["current"] = 0; 
					$njob[$jobcat["jobcat_id"]][$a]["need"] = 0; 
					$nsubscriber[$jobcat["jobcat_id"]][$a]["current"] = 0; 
					$sql = "";
					switch ($a)
					{
						case "1" : $loc_title = ""; 
							$sql = CORE_URL."get_jobposts_by_id2.php?tx_id=$tx_id&date_expired=".date("Y-m-d")."&status=1&jobcat_id=".$jobcat["jobcat_id"]."&order=job_id&ascdesc=asc&id=0";
							break;
						case "2" : $loc_title = "jakarta"; break;
						case "4" : $loc_title = "depok"; break;
						case "3" : $loc_title = "bogor"; break;
						case "5" : $loc_title = "tangerang"; break;
						case "6" : $loc_title = "bekasi"; break;
					}
					if ($sql == "")
						$sql = CORE_URL."get_jobposts_by_id2.php?tx_id=$tx_id&date_expired=".date("Y-m-d")."&status=1&loc_title=$loc_title&jobcat_id=".$jobcat["jobcat_id"]."&order=job_id&ascdesc=asc&id=0";
					//echo $sql."<hr>";
					$sql = $this->Madmin->get_data($sql);
					//echo "<pre>"; print_r($sql); echo "</pre><hr>";
					$values = $sql["ndata"] - 7;
					$njob[$jobcat["jobcat_id"]][$a]["current"] = $sql["ndata"];
					$njob[$jobcat["jobcat_id"]][$a]["need"] = $values;
					
					
				}
			}
			
			
			//echo "<pre>"; print_r($njob); echo "</pre><hr>";
			
			/*
			$njob["TANGERANG"]["current"] = 0;
			$njob["JAKARTA"]["current"] = 0;
			$njob["DEPOK"]["current"] = 0;
			$njob["BOGOR"]["current"] = 0;
			$njob["BEKASI"]["current"] = 0;
			*/
			/*
			foreach ($jobs["results"] as $job)
			{
				if ($job["jobcat_id"] > 0)
				{
					//echo $job["jobcat_id"]."<br>";
					$njob[$job["jobcat_id"]][1]["current"]++;
					
					//switch ($job["parent_id"])
					switch ($job["kotamadya_id"])
					{
						case "10391":
						case "10392": $njob[$job["jobcat_id"]][5]["current"]++; break;
						case "10130":
						case "10129":
						case "10128":
						case "10127":
						case "10126": 
							$njob[$job["jobcat_id"]][2]["current"] = $njob[$job["jobcat_id"]][2]["current"]+1; 
							break;
						case "10094": $njob[$job["jobcat_id"]][4]["current"]++; break;
						case "10065": $njob[$job["jobcat_id"]][3]["current"]++; break;
						//case "10048": $njob[$job["jobcat_id"]][6]["current"] = $njob[$job["jobcat_id"]][6]["current"]+1; break;
					}
				}
			}
			*/
			//echo "<pre>"; print_r($njob); echo "</pre>";
			//echo "<pre>"; print_r($nsubscriber); echo "</pre>";
			//echo "<pre>"; print_r($jobs); echo "</pre>";
			
			//foreach ($subscribers["results"] as $subscriber)
			/*
			for($a = 1; $a++; $a < 16)
			{
				@$nsubscriber[$job["jobcat_id"]][1]["current"]++;
				switch ($subscriber["parent_id"])
				{
					case "10391":
					case "10392": 
						@$nsubscriber[$subscriber["jobcat_id"]][5]["current"]++; 
						//$njob[$subscriber["jobcat_id"]][5]["need"] = $njob[$subscriber["jobcat_id"]][5]["need"] + $this->count_days($subscriber["date_expired"], date("Y-m-d"));
						$json = CORE_URL."get_jobsend_list_by_subscriber_id_and_jobcat_id.php?tx_id=$tx_id&date_add=".urlencode($subscriber["date_add"])."&subscriber_id=".$subscriber["subscriber_id"]."&jobcat_id=".$subscriber["jobcat_id"];
						$json = $this->Madmin->get_data($json);
						$need = $this->count_days($subscriber["date_expired"], date("Y-m-d")) - $json["n_qualified_job"];
						if ($need > 0) 
							if ($njob[$subscriber["jobcat_id"]][5]["need"] < $need) 
								$njob[$subscriber["jobcat_id"]][5]["need"] = $need;
						break;
					case "10130":
					case "10129":
					case "10128":
					case "10127":
					case "10126": 
						@$nsubscriber[$subscriber["jobcat_id"]][2]["current"]++; 
						//$njob[$subscriber["jobcat_id"]][2]["need"] = $njob[$subscriber["jobcat_id"]][1]["need"] + $this->count_days($subscriber["date_expired"], date("Y-m-d"));
						$json = CORE_URL."get_jobsend_list_by_subscriber_id_and_jobcat_id.php?tx_id=$tx_id&date_add=".urlencode($subscriber["date_add"])."&subscriber_id=".$subscriber["subscriber_id"]."&jobcat_id=".$subscriber["jobcat_id"];
						//echo $json."<hr>";
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						$need = $this->count_days($subscriber["date_expired"], date("Y-m-d")) - $json["n_qualified_job"];
						if ($need > 0) 
							if ($njob[$subscriber["jobcat_id"]][2]["need"] < $need) 
								$njob[$subscriber["jobcat_id"]][2]["need"] = $need;
						break;
					case "10094": 
						@$nsubscriber[$subscriber["jobcat_id"]][3]["current"]++; 
						//$njob[$subscriber["jobcat_id"]][3]["need"] = $njob[$subscriber["jobcat_id"]][3]["need"] + $this->count_days($subscriber["date_expired"], date("Y-m-d"));
						$json = CORE_URL."get_jobsend_list_by_subscriber_id_and_jobcat_id.php?tx_id=$tx_id&date_add=".urlencode($subscriber["date_add"])."&subscriber_id=".$subscriber["subscriber_id"]."&jobcat_id=".$subscriber["jobcat_id"];
						$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&loc_title=depok&status=1&jobcat_id=$a";
						
						echo $json."<hr>";
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						//$need = $this->count_days($subscriber["date_expired"], date("Y-m-d")) - $json["n_qualified_job"];
						$njob[$subscriber["jobcat_id"]][3]["need"] = $json["ndata"];
						
						if ($need > 0) 
							if (@$njob[$subscriber["jobcat_id"]][3]["need"] < $need) 
								$njob[$subscriber["jobcat_id"]][3]["need"] = $need;
						
						break;
					case "10065": 
						@$nsubscriber[$subscriber["jobcat_id"]][4]["current"]++; 
						//$njob[$subscriber["jobcat_id"]][4]["need"] = $njob[$subscriber["jobcat_id"]][4]["need"] + $this->count_days($subscriber["date_expired"], date("Y-m-d"));
						$json = CORE_URL."get_jobsend_list_by_subscriber_id_and_jobcat_id.php?tx_id=$tx_id&date_add=".urlencode($subscriber["date_add"])."&subscriber_id=".$subscriber["subscriber_id"]."&jobcat_id=".$subscriber["jobcat_id"];
						//echo $json."<hr>";
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						$need = $this->count_days($subscriber["date_expired"], date("Y-m-d")) - $json["n_qualified_job"];
						if ($need > 0) 
							if ($njob[$subscriber["jobcat_id"]][4]["need"] < $need) 
								$njob[$subscriber["jobcat_id"]][4]["need"] = $need;
						break;
					case "10048": 
						@$nsubscriber[$subscriber["jobcat_id"]][6]["current"]++; 
						//$njob[$subscriber["jobcat_id"]][6]["need"] = $njob[$subscriber["jobcat_id"]][6]["need"] + $this->count_days($subscriber["date_expired"], date("Y-m-d"));
						$json = CORE_URL."get_jobsend_list_by_subscriber_id_and_jobcat_id.php?tx_id=$tx_id&date_add=".urlencode($subscriber["date_add"])."&subscriber_id=".$subscriber["subscriber_id"]."&jobcat_id=".$subscriber["jobcat_id"];
						//echo $json."<hr>";
						$json = $this->Madmin->get_data($json);
						//echo "<pre>"; print_r($json); echo "</pre>";
						$need = $this->count_days($subscriber["date_expired"], date("Y-m-d")) - $json["n_qualified_job"];
						if ($need > 0) 
							if ($njob[$subscriber["jobcat_id"]][6]["need"] < $need) 
								$njob[$subscriber["jobcat_id"]][6]["need"] = $need;
						break;
				}
			}
			*/
						
			for($a = 1; $a < 16; $a++)
			{
				$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=20&page=1&order=subscriber_id&ascdesc=asc&id=".$_SESSION["userid"]."&loc_title=depok&status=1&jobcat_id=$a";
				//echo $json."<hr>";
				$json = $this->Madmin->get_data($json);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$nsubscriber[$a][4]["current"] = $json["totaldata"];
						
				$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=20&page=1&order=subscriber_id&ascdesc=asc&id=".$_SESSION["userid"]."&loc_title=bogor&status=1&jobcat_id=$a";
				//echo $json."<hr>";
				$json = $this->Madmin->get_data($json);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$nsubscriber[$a][3]["current"] = $json["totaldata"];
						
				$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=20&page=1&order=subscriber_id&ascdesc=asc&id=".$_SESSION["userid"]."&loc_title=tangerang&status=1&jobcat_id=$a";
				//echo $json."<hr>";
				$json = $this->Madmin->get_data($json);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$nsubscriber[$a][5]["current"] = $json["totaldata"];
						
				$json = CORE_URL."get_subscribers.php?tx_id=$tx_id&ndata=20&page=1&order=subscriber_id&ascdesc=asc&id=".$_SESSION["userid"]."&loc_title=bekasi&status=1&jobcat_id=$a";
				//echo $json."<hr>";
				$json = $this->Madmin->get_data($json);
				//echo "<pre>"; print_r($json); echo "</pre>";
				$nsubscriber[$a][6]["current"] = $json["totaldata"];
					
				$total = $nsubscriber[$a][4]["current"] + $nsubscriber[$a][3]["current"] + $nsubscriber[$a][5]["current"] + $nsubscriber[$a][6]["current"];
				//echo $total."<hr>";
				$nsubscriber[$a][1]["current"] = $total;
					
			
			}

			
			
			
			
			
			
			
			//echo "<pre>"; print_r($njob); echo "</pre>";
			$data["njob"] = $njob;
			$data["nsubscriber"] = $nsubscriber;
			/*
			$kotamadyas = CORE_URL."sql.php?sql=";
			$kotamadyas .= "SELECT * FROM \$t_location WHERE loc_type='KOTAMADYA'";
			$kotamadyas = $this->Madmin->get_data($kotamadyas);
			
			foreach($kotamadyas["result"] as $kotamadya)
			{
				
			}
			*/
			

			$data["form_submit"] = base_url()."admin/jobmatch/add";
			$this->template->write("title", "Job Supply Monitoring");
			$this->template->write_view("content", "admin/jobsupply_manage", $data);
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
	
	function count_days($date1, $date2)
	{
		$a = strtotime($date1) - strtotime($date2);
		$a = $a/86400;
		return $a;
	}
	

}
?>