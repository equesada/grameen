<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobmentor extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   

	function manage($page=1, $order="a_name", $status="", $mentor="", $mdn="", $pin="", $date_add="")
	{
		$useraccess = array("superadmin", "admin", "btel");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Manage Job Mentors";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			if (sizeof($_POST) > 0)
			{	
				$search["status"] = $this->input->post("status") != "" ? $_POST["status"] : "_";
				$search["mentor"] = $this->input->post("mentor") != "" ? $_POST["mentor"] : "_";
				$search["mdn"] = $this->input->post("mdn") != "" ? $_POST["mdn"] : "_";
				$search["pin"] = $this->input->post("pin") != "" ? $_POST["pin"] : "_";				
				$search["date_add"] = $this->input->post("date_add") != "" ? $_POST["date_add"] : "_";
				//die(urlencode($search["mentor"]));
				if ($search["status"] != "_" || ($search["mentor"] != "_") || ($search["date_add"] != "_") || ($search["mdn"] != "_") || ($search["pin"] != "_"))
					//redirect(current_url()."/".urlencode($filter_key)."/".urlencode($filter_value));					
					redirect(base_url()."admin/jobmentor/manage/$page/$order/".urlencode($search["status"])."/".$search["mentor"]."/".urlencode($search["mdn"])."/".urlencode($search["pin"])."/".urlencode($search["date_add"]));
					//redirect(base_url()."admin/jobmentor/manage/$page/$order/".urlencode($search["status"]));
				else
				{	
					$del = !is_null($this->input->post("del")) ? ($this->input->post("del")) : "";
					$all_id = !is_null($this->input->post("all_id")) ? ($this->input->post("all_id")) : "";
					$all_id = explode(",",$all_id);
					//echo "<pre>"; print_r($all_id); echo "</pre>";
					
					foreach ($all_id as $a)
					{
						if(!empty($del)){
							if (in_array($a, $del))
								$status = 2;
							else
								$status = 1;
							$json = CORE_URL."update_jobmentor.php?mentor_id=$a&tx_id=$tx_id&status=$status";
							
							$json = $this->Madmin->get_data($json);
						}
						else
						{
							$json = CORE_URL."update_jobmentor.php?mentor_id=$a&tx_id=$tx_id&status=1";
							//die($json);
							$json = $this->Madmin->get_data($json);
	
						}
					}
					$this->template->write("msg", "<div class=msg>".sizeof($all_id)." Job Mentors has been updated.</div>");
				} //ini aslinya
			}
			
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);			
			$ndata = 20;
			$search["status"] = $status;			
			$search["mentor"] = $mentor;
			$search["mdn"] = $mdn;			
			$search["pin"] = $pin;
			$search["date_add"] = $date_add;
			
			if ($search["status"] != "_")  
			{
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$var=substr($var,1);
				$json = CORE_URL."get_mentors.php?$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc"; //&id=".$_SESSION["userid"]
			}
			else
			{	
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$var=substr($var,1);
				//$json = CORE_URL."get_mentors.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				$json = CORE_URL."get_mentors.php?$var&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc";

			}
			//echo($json);
			
			$data = $this->Madmin->get_data($json);
			//echo "<pre>";print_r($data); echo "</pre>";die;
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
			//sini awal
			$data["search_link"] = base_url()."admin/jobmentor/search_form/".$search["status"]."/";
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
			$data["search_link"] = base_url()."admin/jobmentor/search_form/".$search["status"]."/";
			$data["form_submit"] = base_url()."admin/jobmentor/manage/$page/$order";
			//echo "<pre>"; print_r($data); echo "</pre>";
			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobmentor_manage", $data, TRUE);
		}
		else
		{
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		}
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	function save_csv($order="a_name", $status="", $mentor="", $mdn="", $pin="", $date_add="")
	{
		$useraccess = array("superadmin", "admin", "company", "jobposter");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Save Mentor as CSV";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=jobmentor.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
				
						$search["status"] = $status;			
			$search["mentor"] = $mentor;
			$search["mdn"] = $mdn;			
			$search["pin"] = $pin;
			$search["date_add"] = $date_add;
			
			if ($search["status"] != "_")  
			{
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$var=substr($var,1);
				$json = CORE_URL."get_mentors_by_criteria.php?$var&order=$orderby&ascdesc=$ascdesc"; //&id=".$_SESSION["userid"]
			}
			else
			{	
				$var = "";
				foreach($search as $key => $val)
				{ if ($search[$key] != "_") $var .= "&$key=".urlencode($val); }
				$var=substr($var,1);
				//$json = CORE_URL."get_mentors.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
				$json = CORE_URL."get_mentors_by_criteria.php?$var&order=$orderby&ascdesc=$ascdesc";

			}
			//die($json);
			$data = $this->Madmin->get_data($json);
			$output = "ID, Name, MDN, PIN, Status, Post Date\n";
			foreach($data["results"] as $mentor)
			{
				$output .= $mentor["mentor_id"].",".$mentor["name"].",".$mentor["mdn"].",".$mentor["pin"].",";
				switch ($mentor["status"])
				{
					case 1 : $output .= "Active"; break;
					case 2 : $output .= "Inactive"; break;
					case 3 : $output .= "Draft"; break;
					case 4 : $output .= "Waiting for Approval"; break;
					default : $output .= " ";
				}
				$output .= ",".$mentor["date_add"]."\n";
			}
			echo $output;
			$this->Madmin->write_log($tx_id, $title, $output);
		}
	}
	

	function edit($id=0)
	{
		$useraccess = array("superadmin", "admin", "company");
		$tx_id = $this->Madmin->get_uuid(current_url());
		$title = "Edit Job Mentor";
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$data["mentor_id"] = !is_null($this->input->post("mentor_id")) ? ($this->input->post("mentor_id")) : "";
			$data["pin"] = !is_null($this->input->post("pin")) ? ($this->input->post("pin")) : "";
			
			if (sizeof($_POST) > 0)
			{
				$this->form_validation->set_rules('pin', 'PIN', 'numeric');
				if ($this->form_validation->run() == TRUE)
				{
					$var = "";
					$data["date_update"] = date("Y-m-d H:i:s");
					foreach ($data as $key => $value)
					{ $var .= "&".$key."=".urlencode($value); }
					$var = substr($var,1);
					
					$json = CORE_URL."update_jobmentor.php?$var";
					$json = $this->Madmin->get_data($json);
					//echo $json;
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Job Mentor has been updated.</div>");
					
				}
			}
				$data = CORE_URL."get_mentor_by_jobmentor_id.php?tx_id=$tx_id&country_id=".$_SESSION["curr_country"]."&mentor_id=$id";
				//echo $data;
				$data = $this->Madmin->get_data($data);

			$data['form_action'] = base_url()."admin/jobmentor/edit/$id";

			//echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", $title);
			$this->template->write_view("content", "admin/jobmentor_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
	}
	
	
	function search_form($status=1)
	{
		//$jobcats = CORE_URL."get_jobcategories.php?order=jobcat_title";
		//$data["jobcats"] = $this->Madmin->get_data($jobcats);
		$data["status"] = $status;
		$this->load->view("admin/jobmentor_searchform", $data);
	}
	
	function update()
	{
		$id = $this->input->post('id');		
		$name = $this->input->post('name');
		$gender = $this->input->post('gender');
		$mdn = $this->input->post('mdn');
		$city = $this->input->post('city');
		$kotamadya = $this->input->post('kotamadya');
		$kecamatan = $this->input->post('kecamatan');
		$kelurahan = $this->input->post('kelurahan');
		$pos_lat = $this->input->post('pos_lat');
		$pos_lng = $this->input->post('pos_lng');
		$loc_id_by_zip = $this->input->post('zip');
		$hdnZip = $this->input->post('hdnZip');
		
		$arr = array(
			"mentor_id" => $id,
			"gender" => $gender,
			"mdn" => $mdn,
			"name" => $name,
			"loc_id" => "",
			"pos_lat" =>  $pos_lat,
			"pos_lng" =>  $pos_lng,
			"status" => "",
			"date_add" => "",
			"date_update" => "",
			"country_id" => ""
			);
		
		$arr['form_action'] = base_url()."admin/jobmentor/update";
		if($kelurahan != '0')
		{
			$arr["loc_id"] = $kelurahan;
		}
		elseif($loc_id_by_zip != '-')
		{
			$arr["loc_id"] = $loc_id_by_zip;
		}
		else
		{
			$arr["loc_id"] = "";
		}
		if(!empty($arr['loc_id']) || $arr['loc_id'] != "")
			{
				$loc1 = file_get_contents(CORE_URL."get_location_by_loc_id.php?id=".$arr['loc_id']);
				$loc1 = json_decode($loc1, true);
				$arr['id_kelurahan'] = $arr['loc_id'];
				$loc2 = file_get_contents(CORE_URL."get_location_by_loc_id.php?id=".$loc1['parent_id']);
				$loc2 = json_decode($loc2, true);
				$arr['id_kecamatan'] = $loc2['loc_id'];
				$loc3 = file_get_contents(CORE_URL."get_location_by_loc_id.php?id=".$loc2['parent_id']);
				$loc3 = json_decode($loc3, true);
				$arr['id_kotamadya'] = $loc3['loc_id'];
				$loc4 = file_get_contents(CORE_URL."get_location_by_loc_id.php?id=".$loc3['parent_id']);
				$loc4 = json_decode($loc4, true);
				$arr['id_city'] = $loc4['loc_id'];
			}
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('mdn', 'MDN', 'required|numeric');
		//$this->form_validation->set_rules('industry_id', 'Industry', 'required');
		if ($this->form_validation->run() == FALSE)
		{	
			$this->template->write("header", "
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/jquery.chainedSelects.js\"></script>
			<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>
			<script type=\"text/javascript\" src=\"http://code.google.com/apis/gears/gears_init.js\"></script>
			<script src=\"".base_url()."js/geolocation2.js\"></script>
			<script>
				$(function()
				{
					$('#city').chainSelect('#kotamadya','".base_url()."func/get_location_by_parent_id.php');
					$('#kotamadya').chainSelect('#kecamatan','".base_url()."func/get_location_by_parent_id.php');
					$('#kecamatan').chainSelect('#kelurahan','".base_url()."func/get_location_by_parent_id.php');
					
				});
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
			</script>");

			$this->template->write("title", "Edit Jobmentor");
			$this->template->write_view("content", "admin/jobmentor_edit", $arr, TRUE);
		}
		else
		{
			//$status = file_get_contents(CORE_URL."add_company.php?name=".urlencode($comp)."&industry=".urlencode($industry_id)."&c_person=".urlencode($cp)."&desc=".urlencode($desc)."&addr=".urlencode($address1)."&addr2=".urlencode($address2)."&telp=".urlencode($tel)."&fax=".urlencode($fax)."&email=".urlencode($email)."&loc_id=".urlencode($kelurahan));
			$url = CORE_URL."update_jobmentor.php?&mentor_id=".urldecode($id)."&name=".urlencode($name)."&gender=".urlencode($gender)."&mdn=".urlencode($mdn)."&loc_id=".urlencode($kelurahan);
			if($kelurahan == "0" && $loc_id_by_zip == "-")
			{
				$url = CORE_URL."update_jobmentor.php?mentor_id=".urlencode($id)."&name=".urlencode($name)."&pos_lat=".urlencode($pos_lat)."&pos_lng=".urlencode($pos_lng)."&gender=".urlencode($gender)."&mdn=".urlencode($mdn);
			}
			elseif($kelurahan == "0" && $loc_id_by_zip != "-")
			{
				$url = CORE_URL."update_jobmentor.php?mentor_id=".urlencode($id)."&name=".urlencode($name)."&gender=".urlencode($gender)."&mdn=".urlencode($mdn)."&loc_id=".urlencode($loc_id_by_zip);
			}
			$status = file_get_contents($url);

			$status = json_decode($status, true);
			$arr['status'] = $status['status'];
			$arr['msg'] = $status['msg'];
			$this->template->write("header", "
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/jquery.chainedSelects.js\"></script>
			<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=true\"></script>
			<script type=\"text/javascript\" src=\"http://code.google.com/apis/gears/gears_init.js\"></script>
			<script src=\"".base_url()."js/geolocation2.js\"></script>
			<script>
				$(function()
				{
					$('#city').chainSelect('#kotamadya','".base_url()."func/get_location_by_parent_id.php');
					$('#kotamadya').chainSelect('#kecamatan','".base_url()."func/get_location_by_parent_id.php');
					$('#kecamatan').chainSelect('#kelurahan','".base_url()."func/get_location_by_parent_id.php');
					
				});
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
			</script>");

			if ($arr["status"] == "0")
				$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
			else
				$this->template->write("msg", "<div class=msg>1 jobmentor has been updated.</div>");
				
			$this->template->write("title", "Edit Jobmentor");
			$this->template->write_view("content", "admin/jobmentor_edit", $arr, TRUE);
		}
		$this->template->render();
	}
}
?>