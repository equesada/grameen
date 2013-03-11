<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contents extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('madmin');
		$this->template->write("title", "Contents");
   }
   
	function index()
	{
		$content = "";
		if ($this->input->post("is_current"))
		{
			$this->db->query("UPDATE themes SET is_current='0'");
			$this->db->query("UPDATE themes SET is_current='1' WHERE id='".$this->input->post("is_current")."'");
			$content .= "Themes Updated.<br><br>";
		}
		$query = $this->db->query("SELECT * FROM contents ORDER BY id");
		$content .= "<table border=1 align=center>";
		foreach ($query->result_array() as $row)
		{
			$content .= "<tr>";
			$content .= "<td>".$row["id"]."</td>";
			$content .= "<td>".$row["title"]."</td>";
			$content .= "<td>".$row["date_add"]."</td>";
			$content .= "<td>".$row["date_update"]."</td>";
			$content .= "<td><a href=\"contents/edit/".$row["id"]."\">Edit</a></td>";
			$content .= "</tr>\n";
		}
		$content .= "</table><br><br>";
		$this->template->write("content", $content);
		$this->template->render();
	}
	
	function edit($id=1)
	{
		if ($this->input->post("id"))
		{
			$id = mysql_real_escape_string($this->input->post("id"));
			$title = $this->input->post("title");
			$body = mysql_real_escape_string($this->input->post("body"));
			$date_update = date("Y-m-d H:i:s");
			$query = $this->db->query("UPDATE contents SET title=\"$title\", body=\"$body\", date_update=\"$date_update\" WHERE id=\"$id\"");
			$this->template->write("content", "Content has been updated");
		}
		else
		{
			$query = $this->db->query("SELECT * FROM contents WHERE id='".$id."'");
			$result = $query->result_array();
			$content = form_open("admin/contents/edit", "method=\"post\"")."\n";
			$content .= form_label('Title', 'title')."<br>\n";
			$content .= form_input("title", $result[0]["title"],"size=100 maxlength=255")."<br><br>\n";
			$content .= form_label('Content', 'body')."<br>\n";
			$content .= "<div style=\"width:450px; margin:0px auto;\">";
			$content .= form_textarea("body", $result[0]["body"], "style=\"width:450px; margin:0px auto;\"")."</div><br>\n";
			$content .= form_hidden('id', $id);
			$content .= form_submit('mysubmit', 'Submit!');
			$content .= form_close()."\n";
			$content .= "<br><br>\n";
			$this->template->write("header", "<script type=\"text/javascript\" src=\"".base_url()."js/nicEdit.js\"></script>\n");
			$this->template->write("header", "<script type=\"text/javascript\">bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>\n");
			$this->template->write("content", $content);
		}
		$this->template->render();
	}
   
}
?>