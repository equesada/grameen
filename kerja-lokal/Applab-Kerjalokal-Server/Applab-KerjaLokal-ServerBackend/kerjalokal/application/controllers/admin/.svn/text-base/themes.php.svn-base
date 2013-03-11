<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Themes extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
		
		//New Yudha
		$this->load->library('ftp');
    }
	function index(){
		
		$this->template->write("title", "Themes");
		
		$this->template->render();	
	}
	
	function template($stat="0", $id='')
	{
		switch ($stat)
		{
			case "0" :
			{
				//$this->template->write("content", $content);
				$arr = file_get_contents(CORE_URL."get_themes.php?country_id=".$_SESSION["curr_country"]);
				$arr = json_decode($arr, true);
				//echo "<pre>"; print_r($arr); echo "</pre>";
				
				$this->template->write("header", "<script type=\"text/javascript\" src=\"".base_url()."js/nicEdit.js\"></script>\n");
				$this->template->write("header", "<script type=\"text/javascript\">bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>\n");
				
				$this->template->write_view("content", "admin/themes_edit", $arr, TRUE);
				break;
			}
			
			case "add" :
			{
				
				$title = urlencode($this->input->post("title"));
				$creator = urlencode($this->input->post("creator"));
				$is_active = urlencode($this->input->post("is_active"));
				$is_current = urlencode($this->input->post("is_current"));
				$desc = urlencode($this->input->post("desc"));
				//$dir = mkdir("".base_url()."themes/".$desc."", 0777);
				
				
				if($creator != NULL){
					 mkdir('themes\\'.$creator, 0700);
				}
				
				if ($title != "")
				{
					$arr = file_get_contents(CORE_URL."add_themes.php?title=$title&desc=$desc&creator=$creator&is_active=$is_active&is_current=$is_current&country_id=".$_SESSION["curr_country"]);
					$arr = json_decode($arr, true);
					if ($arr["status"] == "0")
						$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
					else
						$this->template->write("msg", "<div class=msg>1 Themes has been created.</div>");
				}
				else
					$this->template->write("msg", "<div class=msg>Please enter a valid Themes name.</div>");

				$arr = file_get_contents(CORE_URL."get_themes.php?country_id=".$_SESSION["curr_country"]);
				$arr = json_decode($arr, true);
				
				$this->template->write("header", "<script type=\"text/javascript\" src=\"".base_url()."js/nicEdit.js\"></script>\n");
				$this->template->write("header", "<script type=\"text/javascript\">bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>\n");
				
				$this->template->write_view("content", "admin/themes_edit", $arr, TRUE);
				break;
			
			}
			
			
			case "delete" :
			{
				$arr_id = $id;
				//echo "<pre>"; print($form); echo "</pre>";
				//die();
				$arr = CORE_URL."delete_themes.php?val=".$arr_id;
				//die($arr);
				$arr = file_get_contents($arr);
				//echo "<pre>"; print_r($arr); echo "</pre>";

				$arr = json_decode($arr, true);
				if ($arr["status"] == "0")
					$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
				else
					$this->template->write("msg", "<div class=msg>Delete has been updated.</div>");
				
				//$arr = file_get_contents(CORE_URL."get_themes.php?order=id");
				$arr = file_get_contents(CORE_URL."get_themes.php?country_id=".$_SESSION["curr_country"]);
				$arr = json_decode($arr, true);
				$this->template->write("header", "<script type=\"text/javascript\" src=\"".base_url()."js/nicEdit.js\"></script>\n");
				$this->template->write("header", "<script type=\"text/javascript\">bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>\n");
				$this->template->write_view("content", "admin/themes_edit", $arr, TRUE);
				
				break;
			}
			
			case "update" :
			{
				$form["name"] = $this->input->post("name");
				$form["del"] = $this->input->post("del");
				$form["is_current"] = $this->input->post("is_current");
				//echo "<pre>"; print_r($form); echo "</pre>";
				//die();
				$arr = CORE_URL."update_themes.php?val=".$form["is_current"];
				//$arr = CORE_URL."update_themes.php?val=".urlencode(json_encode($form));
				//die($arr);
				$arr = file_get_contents($arr);
				//echo "<pre>"; print_r($arr); echo "</pre>";

				$arr = json_decode($arr, true);
				if ($arr["status"] == "0")
					$this->template->write("msg", "<div class=msg>".$arr["msg"]."</div>");
				else
					$this->template->write("msg", "<div class=msg>Themes has been updated.</div>");
				
				$arr = file_get_contents(CORE_URL."get_themes.php?order=id");
				$arr = json_decode($arr, true);
				$this->template->write("header", "<script type=\"text/javascript\" src=\"".base_url()."js/nicEdit.js\"></script>\n");
				$this->template->write("header", "<script type=\"text/javascript\">bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>\n");
				$this->template->write_view("content", "admin/themes_edit", $arr, TRUE);
				break;
			}
						
		}
		$this->template->render();
		
	}
	
	function edit($id=1)
	{
		$content = "";
		$title = "";
		$this->template->write("title", "Edit Themes");
		//$query = $this->db->query("SELECT * FROM themes WHERE id='".$id."'");		
		$result = $query->result_array();
		$content .= form_open('admin/themes/edit');
		$content .= form_label('Posisi 1')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_1", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		//$content .= form_dropdown("pos_1", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 2')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_2", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 3')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_3", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 4')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_4", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 5')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_5", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 6')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_6", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= form_label('Posisi 7')."&nbsp;&nbsp;&nbsp;";
		$content .= form_input("pos_7", '','id="name"',"size=100 maxlength=255")."<br><br>\n";
		$content .= "<br><br>\n";
		$content .= form_submit("submit", "Edit");
		$content .= form_close();
		$this->template->write("content", $content);
		$this->template->render();
	}
	
   
}
?>