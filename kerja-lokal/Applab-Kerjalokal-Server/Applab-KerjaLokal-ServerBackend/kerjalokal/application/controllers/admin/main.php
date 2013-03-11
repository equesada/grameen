<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function index()
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		if($_SESSION['username'] != '')
		{
			$username = $_SESSION["username"];
			$title = "Home";
			$this->template->write("content", "Hello $username".",");
			$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
			$this->template->render();
		}
		else
		{
			$_SESSION["username"] = "";
			$_SESSION["curr_country"] = "";
			$_SESSION["userlevel"] = "";
			$_SESSION["userid"] = "";
			$_SESSION["comp_id"] = "";
			$_SESSION["jobposter_id"] = "";
			
			if ($this->input->post("username") && $this->input->post("password"))
			{
				$username = $this->input->post("username");
				$password = $this->input->post("password");
				//$is_login = $this->Madmin->cek_login($username, $password);
				$data = CORE_URL."check_user.php?username=$username&password=$password";
				$data = $this->Madmin->get_data($data);
				//print_r($data);
				if ($data["userlevel"] != "0")
				{
					$_SESSION["username"] = $username;
					$_SESSION["userlevel"] = $data["userlevel"];
					$_SESSION["userid"] = $data["userid"];
					$_SESSION["comp_id"] = $data["comp_id"];
					$_SESSION["jobposter_id"] = $data["jobposter_id"];
					//echo "<pre>"; print_r($_SESSION); echo "</pre>";
					$title = "Home";
					//$this->template->write_view("top_menu", "admin/top_menu");
					//$this->template->write_view("leftmenu", "admin/leftmenu_".$_SESSION["userlevel"]);
					
					$json = CORE_URL."get_menu.php?level=".$_SESSION["userlevel"];
					//die($json);
					$data = $this->Madmin->get_data($json);
					//echo "<pre>"; print_r($data); echo "</pre>";
					if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin"))
						$this->template->write_view("topmenu", "admin/topmenu");
					else
						$this->template->write("topmenu", "<a href=\"".base_url()."admin/logout\">Logout</a>");
					$this->template->write_view("headmenu", "admin/menu_superadmin",$data,TRUE);//.$_SESSION["userlevel"]
					$this->template->write("username", $username.",");
					//$this->template->write("content", "Hello $username".",");
				}
				else
				{
					echo "Invalid username / password";
					$title = "Invalid username / password";
					$this->template->write("headmenu", "");
					$this->template->write_view("content", "admin/login");
				}
			}
			else
			{
				$title = "Login";
				$this->template->write("headmenu", "");
				$this->template->write_view("content", "admin/login");
			}
			
		}
		$this->Madmin->write_log($tx_id, $title, $this->template->render("content"));
		$this->template->render();
		
	}
	
	function forget_password()
	{
		if (sizeof($_POST) > 0)
		{
			$username = $this->input->post("username");
			$data = CORE_URL."get_jobposter_by_jobposter_email.php?username=$username";
			echo $data;
			$data = $this->Madmin->get_data($data);				
			if ($data["email"]!='') //klo ketemu emailnya
			{
				//1.generate new password
				$newpwd=$this->RandomPassword(6,'alphanum');
				//2.update database				
				$setdata['jobposter_id'] = $data["jobposter_id"];
				$setdata['password'] = $newpwd;
				$setdata["date_update"] = date("Y-m-d H:i:s");
				$setdata['lookup'] = $username;
				$var = "";
				foreach ($setdata as $key => $value)
				{ $var .= "&".$key."=".urlencode($value); }
				$var = substr($var,1);
				$json = CORE_URL."update_jobposter_passwd.php?$var";				
				//3.send mail to user
				$this->load->library('email');		
				$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'mail.altermyth.com',
					'smtp_port' => 25,
					'smtp_user' => 'infokerja@altermyth.com',
					'smtp_pass' => '123456',
					'mailtype'  => 'html',
					'charset'   => 'iso-8859-1'
				);
				$this->email->initialize($config);
				$this->email->from('infokerja@altermyth.com', 'Info Kerja');
				$this->email->to($data["email"]);
				$this->email->subject('Reset Password');
				$this->email->message("Password anda yang baru adalah $newpwd ,Silahkan login");	
				$this->email->send();
				//echo $this->email->print_debugger();				
				$this->template->write("msg", "<div class=msg>A new password has been sent to your email.</div><br>"); 
			}
			else
			{
				$this->template->write("msg", "<div class=msg>Invalid username or email.</div><br>"); 							
			}		
			
		}
		$this->template->write_view("content", "admin/forget_password");
		$this->template->render();
	}
	
	function RandomPassword($PwdLength, $PwdType)
    {
    // $PwdType can be one of these:
    //    test .. .. .. always returns the same password = "test"
    //    any  .. .. .. returns a random password, which can contain strange characters
    //    alphanum . .. returns a random password containing alphanumerics only
    //    standard . .. same as alphanum, but not including l10O (lower L, one, zero, upper O)
    //
    $Ranges='';
 
    if('test'==$PwdType)         return 'test';
    elseif('standard'==$PwdType) $Ranges='65-78,80-90,97-107,109-122,50-57';
    elseif('alphanum'==$PwdType) $Ranges='65-90,97-122,48-57';
    elseif('any'==$PwdType)      $Ranges='40-59,61-91,93-126';
 
    if($Ranges<>'')
        {
        $Range=explode(',',$Ranges);
        $NumRanges=count($Range);
        mt_srand(time()); //not required after PHP v4.2.0
        $p='';
        for ($i = 1; $i <= $PwdLength; $i++)
            {
            $r=mt_rand(0,$NumRanges-1);
            list($min,$max)=explode('-',$Range[$r]);
            $p.=chr(mt_rand($min,$max));
            }
        return $p;			
        }
    }	
}
?>