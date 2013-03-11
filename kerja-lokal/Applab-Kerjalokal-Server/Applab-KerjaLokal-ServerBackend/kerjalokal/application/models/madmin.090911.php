<?php
	class Madmin extends CI_Model{
	
		function __construct()
		{
			// Call the Model constructor
			parent::__construct();
			
			$theme['template'] = 'themes/admin.php';
			$theme['parser'] = 'parser';
			$theme['parser_method'] = 'parse';
			$theme['parse_template'] = TRUE;
			$theme['regions'] = array(
				'header',
				'username',
				'title',
				'msg',
				'topmenu',
				'leftmenu',
				'content',
				'sidebar',
				'footer'
			);
			$this->template->add_template('admin', $theme, TRUE);
			$this->template->set_template('admin');
			
			if (!isSet($_SESSION["username"])) $_SESSION["username"] = "";
			if (!isSet($_SESSION["curr_country"])) $_SESSION["curr_country"] = "";
			if (!isSet($_SESSION["userlevel"])) $_SESSION["userlevel"] = "";
			if (!isSet($_SESSION["userid"])) $_SESSION["userid"] = "";
			if (!isSet($_SESSION["comp_id"])) $_SESSION["comp_id"] = "";
			
			if ($_SESSION["username"] != "")
			{
				//echo "<pre>"; print_r($_SESSION); echo "</pre>";
				$this->template->write_view("leftmenu", "admin/leftmenu_".$_SESSION["userlevel"]);
				//$this->template->write_view("leftmenu", "admin/leftmenu_superadmin");
				//$this->template->write_view("leftmenu", "admin/leftmenu_superadmin");
				if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
					$this->template->write_view("topmenu", "admin/topmenu");
				else
					$this->template->write("topmenu", "<a href=\"".base_url()."admin/logout\">Logout</a>");
				
				if (($_SESSION["curr_country"])=="")
				{
					$curr_country = file_get_contents(CORE_URL."get_country_current.php");
					$curr_country = json_decode($curr_country, true);
					$_SESSION["curr_country"] = $curr_country["country_id"];
					$_SESSION["curr_country_name"] = $curr_country["country_name"];
				}
				$this->template->write("username", "Hello ".$_SESSION["username"].",", TRUE);
			}
			
		}
		
		function get_data($data)
		{
			$data = file_get_contents($data);
			return json_decode($data, TRUE);
		}
		
		
		function check_access($userlevel, $useraccess)
		{
			if (in_array($userlevel, $useraccess))
				return "1";
			else
				return "<div class=msg>You are not authorized to be here.</div>";
		}
		
		function get_uuid($param="")
		{
			
			$uuid = 
				//sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				sprintf( '%04x%04x%04x%04x%04x%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,

				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
			//return ($param == "") ? $uuid : $uuid."_".$param;
			$uuid = ($param == "") ? $uuid : $uuid."_".$param;
			$uuid = date("Ym").$uuid;
			return $uuid;
			
		}
		
		
		function write_log($tx_id="", $title="", $response="")
		{
			$response = "";
			$json = CORE_URL."add_logweb.php?tx_id=$tx_id&jobposter_id=".$_SESSION["userid"]."&title=".urlencode($title)."&date_add=".urlencode(date("Y-m-d H:i:s"))."&response=".urlencode($response);
			$json = file_get_contents($json);
			return json_decode($json, TRUE);
		}

		
	}
?>