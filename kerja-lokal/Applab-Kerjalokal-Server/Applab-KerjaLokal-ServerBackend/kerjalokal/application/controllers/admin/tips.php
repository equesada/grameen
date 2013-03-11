<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tips extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
	}
   
	function add()
	{
		
		$useraccess = array("superadmin", "admin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{			
			$arr = array(
				"tips_id" => "",
				"tips_title" => "",
				"description" => "",
				"status" => "",
				"date_add" => "",
				"date_update" => "",
				"country_id" => ""
			);
			$arr["msg"] = "";
			$arr['form_action'] = base_url()."admin/tips/insertInto";
			$this->template->write("title", "Add Tips dan Trik");
			$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess)); 
		$this->template->render();
	}
	
	
	function manage($page = 1, $order="a_tips_id")
	{
		$useraccess = array("superadmin", "admin");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$tx_id = $this->Madmin->get_uuid(current_url());
			if (sizeof($_POST) > 0)
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
						$json = CORE_URL."update_tips.php?tips_id=$a&tx_id=$tx_id&status=$status";
						$json = $this->Madmin->get_data($json);
					}
					else
					{
						$json = CORE_URL."update_tips.php?tips_id=$a&tx_id=$tx_id&status=1";
						//die($json);
						$json = $this->Madmin->get_data($json);

					}
				}
				$this->template->write("msg", "<div class=msg>".sizeof($all_id)." Tips has been updated.</div>");
			}
			$ascdesc = substr($order,0,1) == "a" ? "ASC" : "DESC";
			$orderby = substr($order,2);
			
			$ndata = 10;
			$json = CORE_URL."get_tips.php?tx_id=$tx_id&ndata=$ndata&page=$page&order=$orderby&ascdesc=$ascdesc&id=".$_SESSION["userid"];
			//die($json);
			$data = $this->Madmin->get_data($json);

			$data["page"] = $page;
			$data["order"] = $order;
			$data["next_order"] = $ascdesc == "ASC" ? "d" : "a";
			//echo "<pre>"; print_r($data); echo "</pre>";

		
			$data["form_submit"] = base_url()."admin/tips/manage/$page/$order";
			$this->template->write("title", "Manage Tips dan Trik");
			$this->template->write_view("content", "admin/tips_manage", $data, TRUE);
		}
		else
		{
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
		}
		$this->template->render();
	}
	
	function edit($id=0)
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$useraccess = array("superadmin", "admin", "company");
		if ($this->Madmin->check_access($_SESSION["userlevel"], $useraccess) == "1")
		{
			$is_ok = 0;
			if (($_SESSION["userlevel"] == "superadmin") || ($_SESSION["userlevel"] == "admin")) 
				$is_ok = 1;
			else
				if ($_SESSION["userlevel"] == "company") { $is_ok = 1; $id = $_SESSION["comp_id"]; }
			
			$arr = file_get_contents(CORE_URL."get_tips_by_tips_id.php?tx_id=$tx_id&id=$id");
			$arr = json_decode($arr, true);
			$arr['form_action'] = base_url()."admin/tips/update";
			if ($is_ok == 1)
			{
				//echo "<pre>"; print_r($arr); echo "</pre>";
				$this->template->write("header", "<script src=\"".base_url()."js/jquery.js\"></script>");
				$this->template->write("title", "Edit Tips");
				$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
			}
			else
				$this->template->write("msg", "You are not authorize to modify the help.");
		}
		else
			$this->template->write("msg", $this->Madmin->check_access($_SESSION["userlevel"], $useraccess));
			
		$this->template->render();
	}
	
	function update()
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$tips_id = $this->input->post('id');
		$tips_title = $this->input->post('tips_title');
		$description = $this->input->post('description');
		$arr = array(
					"tips_id" => $tips_id,
					"tips_title" => $tips_title,
					"description" => $description,					
					"status" => "",
					"date_add" => "",
					"date_update" => "",
					"country_id" => ""
				);
			
		$arr['form_action'] = base_url()."admin/tips/update";
		$this->form_validation->set_rules('tips_title', 'Tips', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		if ($this->form_validation->run() == FALSE)
		{	
			$this->template->write("header", "<script src=\"".base_url()."js/jquery.js\"></script>");
			$this->template->write("title", "Edit Tips");
			$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
		}
		else
		{
			$url = CORE_URL."update_tips.php?tx_id=$tx_id&tips_id=".urlencode($tips_id)."&tips_title=".urlencode($tips_title)."&description=".urlencode($description);
			$status = file_get_contents($url);
			$status = json_decode($status, true);
			$arr['status'] = $status['status'];
			$arr['msg'] = $status['msg'];
			$this->template->write("header", "<script src=\"".base_url()."js/jquery.js\"></script>");
			if ($arr["status"] == "0")
				$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
			else
				$this->template->write("msg", "<div class=msg>1 Tips has been updated.</div>");
				
			$this->template->write("title", "Edit Tips");
			$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
		}
		$this->template->render();
	}
	
	function insertInto()
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$tips_title = $this->input->post('tips_title');
		$description = $this->input->post('description');
		$arr = array(
				"tips_id" => "",
				"tips_title" => "",
				"description" => "",
				"status" => "",
				"date_add" => "",
				"date_update" => "",
				"country_id" => ""
			);
		$arr['form_action'] = base_url()."admin/tips/insertInto";
		$this->form_validation->set_rules('tips_title', 'Tips', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$arr = array(
				"tips_id" => "",
				"tips_title" => $tips_title,
				"description" => $description,
				"status" => "",
				"date_add" => "",
				"date_update" => "",
				"country_id" => ""
			);
			$arr['form_action'] = base_url()."admin/tips/insertInto";
			$this->template->write("header", "<script src=\"".base_url()."js/jquery.js\"></script>");

			$this->template->write("title", "Add Tips dan Trik");
			$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
		}
		else
		{
			$url = CORE_URL."add_tips.php?tx_id=$tx_id&tips_title=".urlencode($tips_title)."&description=".urlencode($description);
			//die($url);		
			$status = file_get_contents($url);
			$status = json_decode($status, true);
			$arr['status'] = $status['status'];
			$arr['msg'] = $status['msg'];
			$this->template->write("header", "
			<script src=\"".base_url()."js/jquery.js\"></script>");

			if ($arr["status"] == "0")
				$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
			else
				$this->template->write("msg", "<div class=msg>1 Tips has been registered.</div>");
				
			$this->template->write("title", "Add Tips dan Trik");
			$this->template->write_view("content", "admin/tips_edit", $arr, TRUE);
		}
		$this->template->render();
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