<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Broadcast_sms extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function manage_sms($page=1)
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$ndata = 20;			
			$json = CORE_URL."get_broadcast_sms.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=sms_id&ascdesc=asc";
			//echo $json;
			$data["logs"] = $this->Madmin->get_data($json);
			//echo "<pre>"; print_r($data); echo "</pre>";

			$data["form_submit"] = base_url()."admin/broadcast_sms/manage_sms/$page";
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

	
	function add_sms()
	{
		$useraccess = array("superadmin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			$data = $this->email_check_var();
			//echo "<pre>"; print_r($data); echo "</pre>";
			if (sizeof($_POST) > 0)
			{
				$this->check_form();
				if ($this->form_validation->run() == TRUE)
				{
					
					$mdn_type = 0;	
					$type_mdn = $data['mdn_type'];
					$msg = $data['msg'];
					$mdn = $data['mdn'];				
					$mdn = explode(";",$mdn);
					
					//SEND SMS
					foreach ($mdn as $sent)
					{
						//echo $sent."<hr>";	
						//02193415830
						$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$sent&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 			
					}
					
					//SEND ALL SUBSCRIBER
					if($type_mdn != NULL){
						$json = CORE_URL."get_subscriber_sms.php";												
						$json = $this->Madmin->get_data($json);						
						
						$email_type = "1";
						
						
						foreach ($json["results"] as $subscriber)
						{    							
							$number_mdn = $subscriber['mdn'];    
							//echo $number_mdn."<hr>";	
							$a = file_get_contents("http://10.99.1.5:8085/sendsms.php?msisdn=$number_mdn&message=".urlencode($msg)."&appsid=GRAMEEN&msgid=".time()); 	
						}
						unset($subscriber);
					}
					
				
					$json  = CORE_URL."add_broadcast_sms.php?jobposter_id=".urlencode($_SESSION["userid"])."";
					$json .= "&msg=".urlencode($data['msg'])."&mdn=".urlencode($data['mdn'])."";
					$json .= "&mdn_type=".urlencode($mdn_type)."&date_add=".urlencode(date("Y-m-d H:i:s"))."&date_update=".urlencode(date("Y-m-d H:i:s"))."&status=1";	
					//echo $json."<hr>";		

						

					$json = $this->Madmin->get_data($json);
					if ($json["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$json["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>SMS has been sent.</div>");
						
						$sql = CORE_URL."get_broadcast_sms_by_sms.php";
						//echo $json;
						$sql = $this->Madmin->get_data($sql);
						$idSms = $sql["sms_id"];
						//echo "<pre>"; print_r($json); echo "</pre>";
						
						$json1  = CORE_URL."add_logbroadcast_sms.php?tx_id=".urlencode($tx_id)."&sms_id=".urlencode($idSms)."";
						$json1 .= "&mdn=".urlencode($data['mdn'])."&msg=".urlencode($data['msg'])."&date_send=".urlencode(date("Y-m-d H:i:s"))."";
						//echo $json1."<hr>";
						$json1  = $this->Madmin->get_data($json1);
						
					
					foreach ($data as $key => $value)
					{ $data[$key] = ""; }
				}
			}
			$data["form_submit"] = base_url()."admin/broadcast_sms/add_sms";
			$this->template->write("title", "Broadcast SMS");
			$this->template->write_view("content", "admin/broadcast_sms_edit", $data, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		
		$this->template->render();
	}
	
	function email_check_var()
	{
		$msg			= !is_null($this->input->post("msg")) ? ($this->input->post("msg")) : "";		
		$mdn			= !is_null($this->input->post("mdn")) ? ($this->input->post("mdn")) : "";
		$mdn_type		= !is_null($this->input->post("mdn_type")) ? ($this->input->post("mdn_type")) : "";	
		//echo $email."<hr>";
		
		return array(
			"msg"			=> $msg,
			"mdn" 			=> $mdn,
			"mdn_type" 		=> $mdn_type
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
		$this->form_validation->set_rules('msg', 'Msg', 'required');
	}
	

}
?>