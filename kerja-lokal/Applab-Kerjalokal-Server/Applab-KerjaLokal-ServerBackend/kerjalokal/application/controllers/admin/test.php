<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('Madmin');
		
	}
   
	function main($level="superadmin")
	{
		$tx_id = $this->Madmin->get_uuid(current_url());
		$this->template->write("title", "Master");
		$json = CORE_URL."get_menu.php?tx_id=$tx_id&level=$level";
		$json = $this->Madmin->get_data($json);
		//echo "<pre>"; print_r($json["kepala"]); echo "</pre>";
		

		$lokasi=$_SERVER['REQUEST_URI'];
		$lokasi="/infokerja/admin/master/area";
		//$lokasi="/infokerja/admin/jobposter/manage/1/d_jobposter_id";
		echo $lokasi."<br>";
		if(substr($lokasi,-5)=='admin')   $lokasi.="/jobpost/add";
		echo $lokasi."<br>";

		$posawal=strpos($lokasi, "admin");	
		$lokasi=substr($lokasi,$posawal);
		echo $lokasi."<br>";
		$fixloc = $lokasi;
		
		//explode trus ambil hanya tiga
		$tmploc=explode("/", $lokasi);
		// by henry $fixloc=$tmploc[0];
		// by henry $fixloc.="/".$tmploc[1];
		//print_r($tmploc);

		//if($tmploc[2]=='edit' || $tmploc[2]=='view' )
		//	$fixloc.="/manage";
		//else
		// by henry $fixloc.="/".$tmploc[2];
			
		// by henry if($tmploc[1]=='master') $fixloc.="/".$tmploc[3];
		//$fixloc="admin/jobposter/manage/1/d_jobposter_id";
		// by henry echo $fixloc;
		echo strpos($fixloc,"admin/jobpost/view")."<br>";
		if (strpos($fixloc,"country/edit") !== false)
			$fixloc = "admin/country/manage";
		else if (strpos($fixloc, "jobpost/edit") !== false)
			$fixloc = "admin/jobpost/manage/1/d_job_id";
		else if (strpos($fixloc, "jobpost/view") !== false)
			$fixloc = "admin/jobpost/manage/1/d_job_id";
		else if (strpos($fixloc, "jobpost/manage") !== false)
			$fixloc = "admin/jobpost/manage/1/d_job_id";
		else if (strpos($fixloc, "company/edit") !== false)
			$fixloc = "admin/jobpost/company/1/d_comp_id";
		else if (strpos($fixloc, "jobpost/manage") !== false)
			$fixloc = "admin/jobpost/company/1/d_comp_id";
		else if (strpos($fixloc, "jobseeker/edit") !== false)
			$fixloc = "admin/jobseeker/manage/1/d_subscriber_id";
		else if (strpos($fixloc, "jobseeker/manage") !== false)
			$fixloc = "admin/jobseeker/manage/1/d_subscriber_id";
		else if (strpos($fixloc, "jobmentor/edit") !== false)
			$fixloc = "admin/jobmentor/manage";
			
		$needle=array('url'=>$fixloc);
		
		echo "<pre>"; print_r($needle); echo "</pre>";       
		foreach ($json["results"] as $key => $value) {
			$exists = 0;
			foreach ($needle as $nkey => $nvalue) {
				if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
					$exists = 1;
				} else {
					$exists = 0;
				}
			}
		echo $exists."<br>";
			if ($exists) {
				$parents=$json["results"][$key];		
				$parent=$parents['kode'];
			}
		}
		$parent=isset($parent)?$parent:"1";
		echo "<pre>"; print_r($parents); echo "</pre>"; 
		echo $parent."<br>";
		echo "<div id=\"atas\" style=\"position:relative; text-align:center\">";
		foreach ($json["kepala"] as $menuatas)
		{	
			if(substr($menuatas['kode'],0,1)==substr($parent,0,1))
				echo "<div style=\"display: inline; margin:auto; border: 1px solid white; background-color:#030; padding: 5px 10px; width: auto;\" \><b><a href=\"".base_url()."$menuatas[url]\" style=\"color:#FFF;\">$menuatas[nama]</a></b></div>\n";	
			else
				echo "<div style=\"display: inline; margin:auto; border: 1px solid white; background-color:#030; padding: 5px 10px; width: auto;\" \><a href=\"".base_url()."$menuatas[url]\" style=\"color:#FFF;\">$menuatas[nama]</a></div>\n";	
		}
?>
</div>
</div>
<div id="bawah" style="background-color: rgb(204, 204, 204); padding: 8px 0pt; border-bottom: 1px solid rgb(0, 0, 0); position: relative;">
<?
		$atas="";
		foreach ($json["results"] as $menu)
		{	
			if(substr($menu['kode'],0,1)==substr($parent,0,1))
			{
				if(strlen($menu['kode'])==1) {
					if ($atas!="") echo "</div>\n";
					$atas=$menu['kode'];
				} else {	
					echo "<div id=\"$menu[kode]\" style=\"display: inline; margin:auto; padding: 5px 10px; width: auto;\" \>";
					if($fixloc==$menu['url']) 
						echo "<b><a href=\"".base_url()."$menu[url]\" style=\"color:#000;\">$menu[nama]</a></b></div>\n";
					else
						echo "<a href=\"".base_url()."$menu[url]\" style=\"color:#000;\">$menu[nama]</a></div>\n";
				}	
			}
		}
		echo "</div>";
		$this->template->render();
		
	}
	


}
?>



