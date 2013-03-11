<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function web($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;
			//$json = CORE_URL."get_logweb.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=log_id&ascdesc=desc&country_id=".$_SESSION["curr_country"];
			$json = CORE_URL."get_logweb.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=log_id&ascdesc=desc";
			echo $json;
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/log/manage/$page";
			$this->template->write("header", 
			"
			<script src=\"".base_url()."js/jquery.js\"></script>
			<script src=\"".base_url()."js/pngfix.js\"></script>
			<script src=\"".base_url()."js/jquery.simpledialog.0.1.js\"></script>
			<!-- <link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"".base_url()."css/jquery.simpledialog.0.1.css\"> -->
			
			<style>
			.sd_container{
			 font-family: arial,helvetica,sans-serif; margin:0; padding: 0px; position: absolute; text-align:center;
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

					$('.log_detail').simpleDialog({
						height: 634,
						width: 553,
						showCloseLabel: false
					});
				});
			</script>		
			");
			$this->template->write("title", "Log Web");
			$this->template->write_view("content", "admin/log_web_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	
	function dms($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;
			$json = CORE_URL."get_logdms.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=log_id&ascdesc=desc";
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/log/dms/$page";
			$this->template->write("header", 
			"
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

					$('.log_detail').simpleDialog({
						height: 400,
						width: 600,
						showCloseLabel: false
					});
				});
			</script>		
			");
			$this->template->write("title", "Log DMS");
			$this->template->write_view("content", "admin/log_dms_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	
	function sms($page=1, $order="a_date", $default_row=20, $date="")
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			$ndata = (!empty($default_row) || !empty($search["default_row"])) ? $default_row : 20;			
			if (sizeof($_POST) > 0)
			{
				$date 						= $this->input->post('date');				
				$search["date"] 			= ($date['from_date'] && $date['to_date'] != "") ? implode(':',$_POST["date"]) : "_";				
			}else{
				$search["date"] 			= (!empty($_GET['date'])) ? $date : $date; 				
			}
			
			$search_uri 						= http_build_query($search,'','&');
			
			$json = CORE_URL."get_logsms.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=log_id&ascdesc=desc&country_id=".$_SESSION["curr_country"].'&'.$search_uri;
			//echo $json;
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/log/manage/$page";
			
			$this->template->write("header", "			
			<script src=\"".base_url()."js/jquery-1.6.2.min.js\"></script>
			<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".base_url()."css/jquery-ui-1.8.16.custom.css\">
			");
			
			$data["search"] = $search;
			$data['filter'] = $this->search_form_adv_sms($status=1);
			$data['filter']['search'] = $data['search'];
			$data['filter']['form_submit'] = base_url()."admin/log/sms/1/d_jobsend_id";
			//echo "<pre>awal"; print_r($data['search']); echo "</pre><hr>";
			$data['search_form'] = $this->load->view('admin/log_sms_searchform_adv',$data['filter'],TRUE);
			
			
			$this->template->write("title", "Log SMS");
			$this->template->write_view("content", "admin/log_sms_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	function search_form_adv_sms($date="")
	{		
		$data["date"] = $date;				
		return $data;
	}
	
	
	function detail($webdms="web", $id=1)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		echo "<div style=\"background-color:#fff; text-align:left; width:600px; height:400px; overflow:scroll;\">
			<center><a href=\"#\" class=\"close\">close</a><br><br></center>";

		//echo "<div style=\"background: url('".base_url()."images/nokiabox.png"."'); text-align:left; width:553px; height:634px;\">
		//	<center><a href=\"#\" class=\"close\">close</a><br><br></center>";
		
		//echo $json;
		//echo "<pre>"; print_r($data); echo "</pre>";
		
		if ($webdms == "web")
		{
			$json = CORE_URL."get_log_by_log_id.php?webdms=$webdms&id=$id&tx_id=$tx_id";
			//echo $json;
			$data = $this->Madmin->get_data($json, TRUE);
			echo "<b>URL : </b><br>".$data["src"]."<hr>";
			echo "<div style=\"position:relative; margin:10px; border:1px solid #666; height:250px; overflow:scroll;\">".$data["response"]."</div>";
		}
		else if ($webdms == "dms")
		{
			$json = CORE_URL."get_log_by_tx_id.php?webdms=$webdms&country_id=".$_SESSION["curr_country"]."&tx_id=$id";
			//echo $json;
			$data = $this->Madmin->get_data($json, TRUE);
			//echo "<pre>"; print_r($data); echo "</pre>";
			//foreach ($data["results"] as $log)
			{
				echo "<b>Request : </b><br>".base_url().$data["request"]."<hr>";
				echo "<b>Response : </b><br>".$data["response"];
			}
		}
		else if ($webdms == "sms")
		{
			$json = CORE_URL."get_logsms_by_date.php?date_send=$id&tx_id=$tx_id";
			//echo $json;
			$data = $this->Madmin->get_data($json, TRUE);
			echo "<b>SMS : </b><br>";
			$no = 1;
			echo "<div class=\"table\"><div class=\"row\"><div class=\"cell table_head\" style=\"width:20px\">No.</div><div class=\"cell table_head\" style=\"width:70px\">MDN</div><div class=\"cell table_head\" style=\"width:120px\">Date</div><div class=\"cell table_head\">Message</div></div>";
			foreach ($data["results"] as $a)
			{
				if ($a["status"] == "1")
				{
					echo "<div class=\"row\"><div class=\"cell table_cell\">$no</div><div class=\"cell table_cell\">".$a["mdn"]."</div><div class=\"cell table_cell\">".$a["date_send"]."</div><div class=\"cell table_cell\">".$a["msg"]."</div></div>";
					$no++;
				}
			}
			echo "</div><br>";
			
			echo "<b>DMS : </b><br>";
			$no = 1;
			echo "<div class=\"table\"><div class=\"row\"><div class=\"cell table_head\" style=\"width:20px\">No.</div><div class=\"cell table_head\" style=\"width:70px\">MDN</div><div class=\"cell table_head\" style=\"width:120px\">Date</div><div class=\"cell table_head\">Message</div></div>";
			foreach ($data["results"] as $a)
			{
				if ($a["status"] == "2")
				{
					echo "<div class=\"row\"><div class=\"cell table_cell\">$no</div><div class=\"cell table_cell\">".$a["mdn"]."</div><div class=\"cell table_cell\">".$a["date_send"]."</div><div class=\"cell table_cell\">".$a["msg"]."</div></div>";
					$no++;
				}
			}
			echo "</div>";
			
		}
		echo "</div>";
	}

}
?>