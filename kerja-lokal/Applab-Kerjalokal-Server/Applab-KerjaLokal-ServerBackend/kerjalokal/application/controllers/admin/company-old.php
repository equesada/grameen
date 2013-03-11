<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function add()
	{
		$useraccess = array("superadmin", "admin");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Add Company";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$data = $this->check_var();
			//echo "<pre>"; print_r($_SESSION); echo "</pre><hr>";
			
			$data["creator_id"] = $_SESSION["userid"];
			if (sizeof($_POST) > 0)
			{
			    
                //NEW YUDHA
                $comp_id 		= ($this->input->post("comp_id")!="") ? ($this->input->post("comp_id")) : "0";        		
                //END YUDHA
                 
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

					foreach ($data as $key => $value)
					{ 
						if ($key == "pos_lat") $key = "lat";
						if ($key == "pos_lng") $key = "lng";
						$var .= "&".$key."=".urlencode($value); 
					}
					$var .= "&tx_id=$tx_id";
					$var .= "&date_add=".urlencode(date("Y-m-d H:i:s"));
					$var .= "&country_id=".$_SESSION["curr_country"];
					$var = substr($var,1);
					$json = CORE_URL."add_company.php?$var";
					//echo $json;                    
                    					
					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>".$json["comp_id"]." Company has been added.</div>");
					
					$data["region"] = "";
					$data["province"] = "";
					$data["kotamadya"] = "";
					$data["kecamatan"] = "";
					$data["kelurahan"] = "";
					$data["zip"] = "";
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
                    
                    redirect('admin/jobpost/add/'.$json["comp_id"]);  //NEW FROM YUDHA
				}	
				/*
				else
				{ 
					echo "<pre>"; print_r($_POST); echo "</pre>"; 
					echo validation_errors(); 
				}
				*/
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
			
			$data["form_submit"] = base_url()."admin/company/add";
			//echo "<pre>"; print_r($data); echo "</pre>";
			$data["jobs"]["results"] = array();

            $this->create_header($data["lat"],$data["lng"], $data["kelurahan"]);

			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/company_edit", $data);
			            
            $this->template->render();
			
			
            
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	
	function manage($page = 1, $order="a_name", $default_row=20, $status="", $creator_id="", $industry_id="", $name="", $cp="", $date_add="", $loc_id="", $loc_title="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Company";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = (!empty($default_row) || !empty($search["default_row"])) ? $default_row : 20;
			if (sizeof($_POST) > 0)
			{				
				$search["status"] = $this->input->post("status") != "" ? $_POST["status"] : "";
				$search["creator_id"] = $this->input->post("creator_id") != "" ? $_POST["creator_id"] : "_";
				$search["industry_id"] = $this->input->post("industry_id") != "" ? $_POST["industry_id"] : "_";
				$search["name"] = $this->input->post("name") != "" ? $_POST["name"] : "_";
				$search["cp"] = $this->input->post("cp") != "" ? $_POST["cp"] : "_";
				$search["date_add"] = $this->input->post("date_add") != "" ? $_POST["date_add"] : "_";
				$search["loc_id"] 		= $this->input->post("loc_id") != 0 ? $_POST["loc_id"] : "_";
				$search["loc_title"]	= $this->input->post("loc_title") != "" ? $_POST["loc_title"] : "_";
				
				$search_uri 			= http_build_query($search,'','&');
			//	if (($search["status"] != "_") || ($search["name"] != "_") || ($search["industry_id"] != "_") || ($search["date_add"] != "_"))
				//	redirect(base_url()."admin/company/manage/$page/$order/".urlencode($search["status"])."/".$search["name"]."/".urlencode($search["industry_id"])."/".urlencode($search["date_add"]));
		//add ali					
			} else {
				$search["status"]	  = (!empty($_GET['status'])) ? $status : $status;
				$search["creator_id"] = (!empty($_GET['creator_id'])) ? $creator_id : $creator_id;
				$search["industry_id"]= (!empty($_GET['industry_id'])) ? $industry_id : $industry_id;
				$search["name"] 	  = (!empty($_GET['name'])) ? $name : $name;
				$search["cp"]		  = (!empty($_GET['cp'])) ? $cp : $cp;
				$search["date_add"]   = (!empty($_GET['date_add'])) ? $date_add : $date_add;
				$search["loc_id"]	  = (!empty($_GET['loc_id'])) ? $loc_id : $loc_id;	
				$search["loc_title"]  = (!empty($_GET['loc_title'])) ? $loc_title : $loc_title;					
				
			//	if (($search["status"] != "_") || ($search["name"] != "_") || ($search["industry_id"] != "_") || ($search["date_add"] != "_"))
				//	redirect(base_url()."admin/company/manage/$page/$order/".urlencode($search["status"])."/".$search["name"]."/".urlencode($search["industry_id"])."/".urlencode($search["date_add"]));				
				
			} //end add
			$search_uri 					= http_build_query($search,'','&');
			$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;
/*			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = (!empty($default_row) || !empty($search["default_row"])) ? $default_row : 20; //$ndata = 20;
			$search["status"] = $status;
			$search["creator_id"] = $creator_id;
			$search["industry_id"] = $industry_id;
			$search["name"] = $name;
			$search["cp"] = $cp;
			$search["date_add"] = $date_add;
			
			if (($search["status"] != "_") || ($search["name"] != "_") || ($search["industry_id"] != "_") || ($search["date_add"] != "_")) 
			{
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_companies.php?tx_id=$tx_id"."$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_companies.php?tx_id=$tx_id"."$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=0";
			}
			else
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=0";
			}*/			
			//echo $json;
			$data = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($search_uri); echo "</pre>";
			//die($json);
			/*
			$data["list_status"] = array(
				"0" => "ALL",
				"1" => "Active",
				"2" => "Inactive"
			);
			*/
			$data["status"] = $status;
			$data['form_submit'] = base_url()."admin/company/manage/1/d_comp_id";
			$data["page"] = $page;			
			$data["search"] = $search;			
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";

			//$data["search_link"] = base_url()."admin/company/search_form/".$search["status"]."/";
			$this->template->write("header", "			
			<script src=\"".base_url()."js/jquery.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".base_url()."css/jquery-ui-1.8.16.custom.css\">");			
			
			$data["result_row"] = array(20 => 20, 50 => 50, 90 => 90);
			$data["default_row"] = (!empty($_POST['result_row'])) ? $_POST['result_row'] : $ndata;

			$data['jobcats'] = $this->search_form_adv($search['status'],$search['creator_id'], $search['industry_id'], $search['name'], $search['cp'], $search['date_add'], $search['loc_title']);
			$data['jobcats']['form_submit'] = base_url()."admin/company/manage/1/d_comp_id";
			//$data['jobcats'] 
			//if($search["loc_title"]!="") {
				$lokasi["kotamadya_id"] = $this->input->post("kotamadya_id") != 0 ? $_POST["kotamadya_id"] : "";
				$lokasi["kecamatan_id"] = $this->input->post("kecamatan_id") != 0 ? $_POST["kecamatan_id"] : "";
				$lokasi["kelurahan_id"] = $this->input->post("kelurahan_id") != 0 ? $_POST["kelurahan_id"] : "";
				$data["lokasi"] = $lokasi;			
			//}

			$data['jobcats']['search'] = $data['search'];
			$data['jobcats']['lokasi'] = $data['lokasi'];
			//echo "<pre>"; print_r($data['search']); echo "</pre>";
			$data['search_form'] = $this->load->view('admin/company_searchform_adv',$data['jobcats'],TRUE); 
			//echo "<pre><!--"; print_r($data['jobcats']); echo "--></pre>";
			
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/company_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function search_form_adv($status=1,$creator_id="", $industry_id="", $name="", $cp="", $date_add="", $loc_title="")
	{
		//$company  = CORE_URL."get_companies.php?order=name";
		$creator  = CORE_URL."get_jobposters.php?order=username&userlevel=admin"; //&userlevel=superadmin
		$industry = CORE_URL."get_industries.php?order=industry_title";
		$location['KELURAHAN'] = CORE_URL."get_location_by_kotamadya.php?order=name";
		$location['KECAMATAN'] = CORE_URL."get_location_by_kecamatan.php?order=name";
		$location['KOTAMADYA'] = CORE_URL."get_location_by_kelurahan.php?order=name";
	
		//$company_results				= $this->Madmin->get_data($company);
		$creator_results				= $this->Madmin->get_data($creator);
		$industry_results				= $this->Madmin->get_data($industry);
		$location_results['KELURAHAN']	= $this->Madmin->get_data($location['KELURAHAN']);
		$location_results['KECAMATAN']	= $this->Madmin->get_data($location['KECAMATAN']);
		$location_results['KOTAMADYA']	= $this->Madmin->get_data($location['KOTAMADYA']);

		$data["status"]		= $status;

		$i = 1;
/*		$rows_company = array();
		foreach ($company_results['results'] as $company) {
			$rows_company[$i]['comp_id'] = $company['comp_id'];
			$rows_company[$i]['company_name'] = $company['company_name'];
			$i++;
		} */
		//echo $creator;
		$rows_creator = array();
		foreach ($creator_results['results'] as $creator) {
			$rows_creator[$i]['jobposter_id'] = $creator['jobposter_id'];
			$rows_creator[$i]['jobposter_name'] = $creator['jobposter_name'];
			$i++;
		}
		$rows_industry = array();
		foreach ($industry_results['results'] as $industry) {
			$rows_industry[$i]['industry_id'] = $industry['industry_id'];
			$rows_industry[$i]['industry_title'] = $industry['industry_title'];
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
			//$location[$locations['name']] = ucfirst($locations['name']);
			$location[$locations['loc_id']] = $locations['name'];
			//echo "<pre>"; print_r($locations); echo "</pre><hr>";exit;
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
		$data["location"] = $location; 
		
		$data['locations'] = $rows_location;
		$data['creators'] = $rows_creator;
		$data['name'] = $name;
		//$data['companies'] = $rows_company;
		$data['industries'] = $rows_industry;
	//echo "<pre>"; print_r($data); echo "</pre><hr>";exit;		
		return $data;
	}
	
	function search_form($status=1)
	{
		$industry = CORE_URL."get_industries.php?order=industry_title";
		$data["industry"] = $this->Madmin->get_data($industry);
		$data["status"] = $status;
		$this->load->view("admin/company_searchform", $data);
	}

//	function save_csv($order="a_name", $filter_key="", $filter_value="")
	function save_csv($order="a_name", $status="", $username="", $industry_id="", $name="", $cp="", $date_add="", $loc_id="", $loc_title="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Company as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=company.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = 0;
			$search["status"] = $status;
			$search["username"] = $username;			
			$search["industry_id"] = $industry_id;
			$search["name"] = $name;
			$search["cp"] = $cp;
			$search["date_add"] = $date_add;
			$search["loc_title"] = $loc_title;
			if (($search["status"] != "_") || ($search["username"] != "_") || ($search["industry_id"] != "_") || ($search["name"] != "_") || ($search["cp"] != "_") || ($search["date_add"] != "_") || ($search["loc_title"] != "_")) 
			{
				//$var = "";
				//foreach($search as $key => $val)
				//{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				
				$search_uri = http_build_query($search,'','&');				
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					//$json = CORE_URL."get_companies.php?tx_id=$tx_id"."$var&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"].'&'.$search_uri;
				else
					//$json = CORE_URL."get_companies.php?tx_id=$tx_id"."$var&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=0";
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=0&".$search_uri;
			}
			else
			{
				if (($_SESSION["userlevel"] == "company") || ($_SESSION["userlevel"] == "jobposter"))
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				else
					$json = CORE_URL."get_companies.php?tx_id=$tx_id&ndata=$ndata&order=$orderby&ascdesc=$ascdesc&id=0";
			}
			//echo $json;
			$data = $this->Madmin->get_data($json);
			//print_r($data);
			$output = "ID, Status, Creator, Create Date, Company Name, Industry, Active Job Post, Inactive Job Post, Total Job Post, Province, Kotamadya, Kecamatan, Kelurahan, Contact Person, tel, email\n";
			foreach($data["results"] as $job)
			{
				$output .= $this->str_csv($job["comp_id"]).",";
				switch ($job["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}
				$output .= ",".$this->str_csv($job["username"]).",".				
//					$this->str_csv($job["status"]).",".
					$this->str_csv($job["date_add"]).",".					
					$this->str_csv($job["company_name"]).",".
					$this->str_csv($job["industry_title"]).",".
					$this->str_csv($job["active"]).",".
					$this->str_csv($job["inactive"]).",".
					$this->str_csv($job["total"]).",".					
					$this->str_csv($job["province_name"]).",".
					$this->str_csv($job["kotamadya_name"]).",".
					$this->str_csv($job["kecamatan_name"]).",".
					$this->str_csv($job["loc_title"]).",".
					$this->str_csv($job["cp"]).",".
					$this->str_csv($job["tel"]).",".
					$this->str_csv($job["email"])."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	
	function edit($id=0)
	{
		$useraccess = array("superadmin", "admin", "company");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Edit Company";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$data = $this->check_var();
			$json = CORE_URL."get_company_by_comp_id.php?tx_id=$tx_id&id=$id";
			$data["company"] = $this->Madmin->get_data($json);
			if (sizeof($_POST) > 0)
			{
				$this->check_form($data["region"]);
				if ($this->form_validation->run() == TRUE)
				{
					unset($data["creator_id"]);
					unset($data["region"]);
					unset($data["province"]);
					unset($data["kotamadya"]);
					unset($data["kecamatan"]);
					unset($data["kelurahan"]);
					unset($data["zip"]);
					unset($data["company"]);
					unset($data["date_add"]);

					//$data["status"] = $data["status"] == "0" ? "4" : "3";
					$data["date_update"] = date("Y-m-d H:i:s");
					$data["country_id"] = $_SESSION["curr_country"];
					$var = "";
					//echo "<pre>"; print_r($data); echo "</pre>";
					foreach ($data as $key => $value)
					{ 
						if ($key == "pos_lat") $key = "lat";
						if ($key == "pos_lng") $key = "lng";
						$var .= "&".$key."=".urlencode($value); 
					}
					$var .= "&tx_id=$tx_id";
					$var = substr($var,1);
					
					$json = CORE_URL."update_company.php?$var";
					//echo $json."<br>";
					
					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Company has been updated.</div>");
					
					$data["creator_id"] = "";
					$data["region"] = "";
					$data["province"] = "";
					$data["kotamadya"] = "";
					$data["kecamatan"] = "";
					$data["kelurahan"] = "";
					$data["zip"] = "";
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			
			if (isset($data["company"]))
			{
				foreach ($data["company"] as $key => $value)
				{ 
					if ($key == "pos_lat") $key = "lat";
					if ($key == "pos_lng") $key = "lng";
					$data[$key] = $value; 
				}
				unset($data["company"]);
			}
			$data["comp_id"] = $id;
			
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
			if ($_SESSION["userid"] <= 100)
				$json = CORE_URL."get_jobposts_by_comp_id.php?tx_id=$tx_id&order=date_add&ascdesc=desc&comp_id=$id&id=0";
			else
				$json = CORE_URL."get_jobposts_by_comp_id.php?tx_id=$tx_id&order=date_add&ascdesc=desc&comp_id=$id&id=".$_SESSION["userid"];
			//echo $json;
			$data["jobs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";
			
			
			foreach ($this->get_lists($tx_id) as $key => $value)
			{ $data[$key] = $value; }
			
			$data["form_submit"] = base_url()."admin/company/edit/$id";
			
			$this->create_header($data["lat"],$data["lng"], $data["loc_id"]);

			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/company_edit", $data);
			$this->template->render();
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	
	function get_lists($tx_id)
	{
		$industries = CORE_URL."get_industries.php?tx_id=$tx_id&order=industry_title";
		$industries = $this->Madmin->get_data($industries);
		$provinces = CORE_URL."get_location_by_parent_id.php?tx_id=$tx_id&id=0";
		$provinces = $this->Madmin->get_data($provinces);
		$zips = CORE_URL."get_location_by_kelurahan.php?tx_id=$tx_id&order=zipcode";
		$zips = $this->Madmin->get_data($zips);
		
		return array(
			"industries" => $industries["results"],
			"provinces" => $provinces,
			"zips" => $zips["results"]
		);
	}
	

	function check_form($region=1)
	{
		$this->form_validation->set_rules('name', 'Company', 'required');
		$this->form_validation->set_rules('industry_id', 'Industry', 'callback_custom_err_msg');
		$this->form_validation->set_rules('address1', 'Address', 'required');
		$this->form_validation->set_rules('tel', 'Telephone', 'required');
		$this->form_validation->set_rules('gender', 'Gender', 'callback_custom_err_msg');
		$this->form_validation->set_rules('email', 'E-mail', 'required');
		
		if ($_POST["region"] == "1") 
		{
			$this->form_validation->set_rules('province','Province','callback_custom_err_msg');
			$this->form_validation->set_rules('kotamadya','Kotamadya','callback_custom_err_msg');
			$this->form_validation->set_rules('kecamatan','Kecamatan','callback_custom_err_msg');
			$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'callback_custom_err_msg');
		}
		else if ($_POST["region"] == "2") $this->form_validation->set_rules('zip', 'Zip code', 'callback_custom_err_msg');
	}


	function check_var()
	{
		$comp_id 		= ($this->input->post("comp_id")!="") ? ($this->input->post("comp_id")) : "0";
		$industry_id	= ($this->input->post("industry_id")!="") ? ($this->input->post("industry_id")) : "";
		$creator_id		= ($this->input->post("creator_id")!="") ? ($this->input->post("creator_id")) : "";
		$name 			= ($this->input->post("name")!="") ? ($this->input->post("name")) : "";
		$cp 			= ($this->input->post("cp")!="") ? ($this->input->post("cp")) : "";
		$description	= ($this->input->post("description")!="") ? ($this->input->post("description")) : "";
		$address1 		= ($this->input->post("address1")!="") ? ($this->input->post("address1")) : "";
		$address2 		= ($this->input->post("address2")!="") ? ($this->input->post("address2")) : "";
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
		$tel			= ($this->input->post("tel")!="") ? ($this->input->post("tel")) : "";
		$fax			= ($this->input->post("fax")!="") ? ($this->input->post("fax")) : "";
		$email 			= ($this->input->post("email")!="") ? ($this->input->post("email")) : "";
		$status			= ($this->input->post("status")!="") ? ($this->input->post("status")) : 1;
		$notes			= ($this->input->post("notes")!="") ? ($this->input->post("notes")) : "";
		$date_add		= ($this->input->post("date_add")!="") ? ($this->input->post("date_add")) : "";
		$date_update 	= ($this->input->post("date_update")!="") ? ($this->input->post("date_update")) : "";
		$country_id 	= ($this->input->post("country_id")!="") ? ($this->input->post("country_id")) : "";
		
		return array(
			"comp_id" => $comp_id,
			"industry_id" => $industry_id,
			"creator_id" => $creator_id,
			"name" => $name,
			"cp" =>  $cp,
			"description" => $description,
			"address1" => $address1,
			"address2" =>  $address2,
			"region" => $region,
			"loc_id" => $loc_id,
			"province" => $province,
			"kotamadya" => $kotamadya,
			"kecamatan" => $kecamatan,
			"kelurahan" => $kelurahan,
			"lat" =>  $lat,
			"lng" =>  $lng,
			"zip" =>  $zip,
			"tel" => $tel,
			"fax" => $fax,
			"email" => $email,
			"status" => $status,
			"notes" => $notes,
			"date_add" => $date_add,
			"date_update" => $date_update,
			"country_id" => $country_id
		);
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
			<script src=\"".base_url()."js/jquery.chainedSelects.js\"></script>
			<!-- <script src=\"".base_url()."js/jquery.optionTree.js\"></script> -->
			<script>
				$(document).ready(function(){
					
					
					$('#province').chainSelect('#kotamadya','".base_url()."func/get_location_by_parent_id.php');
					$('#kotamadya').chainSelect('#kecamatan','".base_url()."func/get_location_by_parent_id.php');
					$('#kecamatan').chainSelect('#kelurahan','".base_url()."func/get_location_by_parent_id.php','".$kelurahan."');
					
					
					
					";
					/*
					$header .= "var option_tree = {";
					$provinces = CORE_URL."get_location_by_parent_id.php?id=0";
					$provinces = $this->Madmin->get_data($provinces);
					//$prov["name"] = "Jakarta";
					//$prov["loc_id"] = "1013";
					foreach ($provinces as $prov)
					
					{
						$header .= "\"".$prov["name"]."\": {";
						$kotamadyas = CORE_URL."get_location_by_parent_id.php?id=".$prov["loc_id"];
						$kotamadyas = $this->Madmin->get_data($kotamadyas);
						foreach ($kotamadyas as $kotamadya)
						{
							$header .= "\"".$kotamadya["name"]."\": {";
							$kecamatans = CORE_URL."get_location_by_parent_id.php?id=".$kotamadya["loc_id"];
							$kecamatans = $this->Madmin->get_data($kecamatans);
							foreach ($kecamatans as $kecamatan)
							{
								$header .= "\"".$kecamatan["name"]."\": {";
								$kelurahans = CORE_URL."get_location_by_parent_id.php?id=".$kecamatan["loc_id"];
								$kelurahans = $this->Madmin->get_data($kelurahans);
								foreach ($kelurahans as $kelurahan)
								{
									$header .= "\"".$kelurahan["name"]."\": ".$kelurahan["loc_id"];
									if ($kelurahan != end($kelurahans)) $header .= ", ";
								}
								$header .=  "}\n";
								if ($kecamatan != end($kecamatans)) $header .= ", ";
							}
							$header .=  "}\n";
							if ($kotamadya != end($kotamadyas)) $header .= ", ";
						}
						$header .=  "}\n";
						if ($prov != end($provinces)) $header .= ", ";
					}
					*/
					
						/*
						$header .= "\"Option 1\": {\"Suboption\":200},
						\"Option 2\": {\"Suboption 2\": {\"Subsub 1\":201, \"Subsub 2\":202},
								\"Suboption 3\": {\"Subsub 3\":203, \"Subsub 4\":204, \"Subsub 5\":205}
						}
						*/
					/*
					$header .= "};

					var options = {empty_value: -1, choose: '...', preselect: {'province': [1013]}};

					$('input[name=province]').optionTree(option_tree, options)
                          .change(function() { alert('Field ' + this.name  + ' = ' + this.value )});

					*/	  
					$header .= "
					$('#lng').val('$lng');
					$('#lat').val('$lat');";
					if ($lat == "")
						$header .= "initialize(); ";
					else
						$header .= "initialize($lat, $lng); ";
					$header .= "
					
					if ($('#region').val() == '2')
					{
						$('.by').css('display','none');
						$('#byZip').css('display','block');
					}

					$('#region').change(function(){
						if($(this).val() == '1')
						{
							$('.by').css('display','none');
							$('#byRegion').css('display','block');
						}
						if($(this).val() == '2')
						{
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

	function str_csv($str)
	{
		return str_replace(",", ".", $str);
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
	



}
?>