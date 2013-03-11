<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Broadcast extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function manage_email($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;			
			$json = CORE_URL."get_broadcast.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=email_id&ascdesc=asc";
			//echo $json;
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/broadcast/manage_email/$page";
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
			$this->template->write("title", "Manage Email Broadcast");
			$this->template->write_view("content", "admin/broadcast_email_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	function manage_sms($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;			
			$json = CORE_URL."get_broadcast.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=email_id&ascdesc=asc";
			//echo $json;
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/broadcast/manage_email/$page";
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
			$this->template->write("title", "Manage SMS Broadcast");
			$this->template->write_view("content", "admin/broadcast_sms_manage", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}

	
	function add_email()
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->email_check_var();
			
			//echo "<pre>"; print_r($data); echo "</pre>";
			
			$email_type = 0;
			if (sizeof($_POST) > 0)
			{
				$this->check_form();				
				if ($this->form_validation->run() == TRUE)
				{
					$all_company = $data['all_company'];				
					$all_jobmentor = $data['all_jobmentor'];							
					$email = $data['email'];				
					$email = explode(";",$email);
					
					//SENT EMAIL - DONE
					
					foreach ($email as $sent)
					{
						
						require_once('/var/www/html/Mail-1.2.0/Mail-1.2.0/Mail.php');
						
						$from = "Support Kerjalokal <support@kerjalokal.com>";						
						$to = "".$sent."";						
						$subject = "".$data['title']."";						
						$body = "".$data['msg']."";												
						
						
						$host = "ssl://smtp.gmail.com";
						$port = "465";
						$username = "support@kerjalokal.com";
						$password = "support@kerjalokal1";
						
						$headers = array ('From' => $from,
										  'To' => $to,
										  'Subject' => $subject,
										  'Reply-To' => $from);
						$smtp = Mail::factory('smtp',
						array ( 'host' => $host,
								'port' => $port,
								'auth' => true,
								'username' => $username,
								'password' => $password)
							  );
						
						$mail = $smtp->send($to, $headers, $body);						
					}
					
					
					if($all_company != NULL){
						$json = CORE_URL."get_company.php";												
						$json = $this->Madmin->get_data($json);						
						
						$email_type = "1";
						
						require_once('/var/www/html/Mail-1.2.0/Mail-1.2.0/Mail.php');						
						$from = "Support Kerjalokal <support@kerjalokal.com>";
						//$to = "".$email_subscriber."";						
						$subject = "".$data['title']."";						
						$body = "".$data['msg']."";												
						
						
						$host = "ssl://smtp.gmail.com";
						$port = "465";
						$username = "support@kerjalokal.com";
						$password = "support@kerjalokal1";
						
						$headers = array ('From' => $from,
										  'To' => $to,
										  'Subject' => $subject,
										  'Reply-To' => $from);
						$smtp = Mail::factory('smtp',
						array ( 'host' => $host,
								'port' => $port,
								'auth' => true,
								'username' => $username,
								'password' => $password)
							  );
						
						foreach ($json["results"] as $company)
						{    							
							$email_company = $company['email'];    

							//UNTUK FUNCTION INI HEADER N BODY NYA BERADA DI LUAR
							$to = "".$email_company."";							
							$mail = $smtp->send($to, $headers, $body);			
							
						}
						unset($company);
					}
					if($all_jobmentor != NULL){
						$json = CORE_URL."get_subscriber_email.php";												
						$json = $this->Madmin->get_data($json);						
						
						$email_type = "2";
						
						require_once('/var/www/html/Mail-1.2.0/Mail-1.2.0/Mail.php');						
						$from = "Support Kerjalokal <support@kerjalokal.com>";
						//$to = "".$email_subscriber."";						
						$subject = "".$data['title']."";						
						$body = "".$data['msg']."";												
						
						
						$host = "ssl://smtp.gmail.com";
						$port = "465";
						$username = "support@kerjalokal.com";
						$password = "support@kerjalokal1";
						
						$headers = array ('From' => $from,
										  'To' => $to,
										  'Subject' => $subject,
										  'Reply-To' => $from);
						$smtp = Mail::factory('smtp',
						array ( 'host' => $host,
								'port' => $port,
								'auth' => true,
								'username' => $username,
								'password' => $password)
							  );
						
						
						foreach ($json["results"] as $subscriber)
						{    							
							$email_subscriber = $subscriber['email'];        
							//echo $email_subscriber."<hr>";  
							
							//UNTUK FUNCTION INI HEADER N BODY NYA BERADA DI LUAR
							$to = "".$email_subscriber."";							
							$mail = $smtp->send($to, $headers, $body);	 	
														
						}
						unset($subscriber);
					}
					
										
					$json  = CORE_URL."add_broadcast_email.php?jobposter_id=".urlencode($_SESSION["userid"])."&sender_name=".urlencode($data['sender_name'])."&sender_email=".urlencode($data['sender_email'])."";
					$json .= "&title=".urlencode($data['title'])."&msg=".urlencode($data['msg'])."&email=".urlencode($data['email'])."";
					$json .= "&email_type=".urlencode($email_type)."&date_add=".urlencode(date("Y-m-d H:i:s"))."&date_update=".urlencode(date("Y-m-d H:i:s"))."&status=1";	
					//echo $json."<hr>";
					$json  = $this->Madmin->get_data($json);
					
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>Email has been sent.</div>");
						
						$sql = CORE_URL."get_broadcast_email_by_email.php";
						//echo $json;
						$sql = $this->Madmin->get_data($sql);
						$idEmail = $sql["email_id"];
						//echo "<pre>"; print_r($json); echo "</pre>";
						
						$json1  = CORE_URL."add_logbroadcast_email.php?tx_id=".urlencode($tx_id)."&email_id=".urlencode($idEmail)."";
						$json1 .= "&user_email=".urlencode($data['sender_email'])."&date_add=".urlencode(date("Y-m-d H:i:s"))."";
						//echo $json1."<hr>";
						$json1  = $this->Madmin->get_data($json1);
						
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			$data["form_submit"] = base_url()."admin/broadcast/add_email";
			$this->template->write("title", "Broadcast e-mail");
			$this->template->write_view("content", "admin/broadcast_email_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		
		$this->template->render();
	}
	
	function add_sms()
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->email_check_var();
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
			$data["form_submit"] = base_url()."admin/broadcast/add_sms";
			$this->template->write("title", "Broadcast e-mail");
			$this->template->write_view("content", "admin/broadcast_email_edit", $data, TRUE);
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
					
					$json = CORE_URL."update_country.php?tx_id=$tx_id&id=".$data["country_id"]."&country_name=".$data["country_name"]."&date_update=".$data["date_update"]."&status=".$data["status"];
					$json = $this->Madmin->get_data($json);
					
					unset($data["country_name"]);
					$json = CORE_URL."update_country_setting.php?tx_id=$tx_id&$var";
					//echo $json;
					$json = $this->Madmin->get_data($json);
					//echo "<pre>"; print_r($json); echo "</pre>";
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 country has been updated.</div>");
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			else
			{
				$data = CORE_URL."get_country_setting_by_country_id.php?tx_id=$tx_id&country_id=$id";
				$data = $this->Madmin->get_data($data);
			}
			$data["form_submit"] = base_url()."admin/country/edit/$id";

			//echo "<pre>"; print_r($data); echo "</pre>";

			$this->template->write("title", "Edit Country");
			$this->template->write_view("content", "admin/country_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	
	function email_check_var()
	{
		$email_id		= !is_null($this->input->post("sender_name")) ? ($this->input->post("sender_name")) : "";
		$sender_name	= !is_null($this->input->post("sender_name")) ? ($this->input->post("sender_name")) : "";				
		$sender_email	= !is_null($this->input->post("sender_email")) ? (strtolower($this->input->post("sender_email"))) : "";
		$title			= !is_null($this->input->post("title")) ? ($this->input->post("title")) : "";
		$msg			= !is_null($this->input->post("msg")) ? ($this->input->post("msg")) : "";		
		$all_company	= !is_null($this->input->post("all_company")) ? ($this->input->post("all_company")) : "";		
		$all_jobmentor	= !is_null($this->input->post("all_jobmentor")) ? ($this->input->post("all_jobmentor")) : "";		
		$email			= !is_null($this->input->post("email")) ? ($this->input->post("email")) : "";
		//echo $email."<hr>";
		
		return array(
			"email_id" 		=> $sender_name,
			"sender_name" 	=> $sender_name,
			"sender_email"	=> $sender_email,
			"title" 		=> $title,
			"msg"			=> $msg,
			"all_company" 	=> $all_company,
			"all_jobmentor" => $all_jobmentor,
			"email" 		=> $email
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
	

}
?>