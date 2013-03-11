<?php
$lokasi=$_SERVER['REQUEST_URI'];
if(substr($lokasi,-5)=='admin')   $lokasi.="/jobpost/add";

$posawal=strpos($lokasi, "admin");	
$lokasi=substr($lokasi,$posawal);
//explode trus ambil hanya tiga
$tmploc=explode("/", $lokasi);
$fixloc=$tmploc[0];
$fixloc.="/".$tmploc[1];
//echo $fixloc."<br>";
//print_r($tmploc);

//if($tmploc[2]=='edit' || $tmploc[2]=='view' )
//	$fixloc.="/manage";
//else
	$fixloc.="/".$tmploc[2];
	
// by henry if($tmploc[1]=='master') $fixloc.="/".$tmploc[3];


 
// by henry
$fixloc = $lokasi;
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
	
else if (strpos($fixloc, "subscription/manage") !== false)		//subscription internal
	$fixloc = "admin/subscription/manage/1/d_rel_id";	
	
else if (strpos($fixloc, "jobmentor/edit") !== false)
	$fixloc = "admin/jobmentor/manage";
else if (strpos($fixloc, "jobposter/edit") !== false)
	$fixloc = "admin/jobposter/manage/1/d_jobposter_id";

//DASHBOARD	
else if (strpos($fixloc, "jobsent_report/manage") !== false)
	$fixloc = "admin/jobsent_report/manage/1/d_jobsend_id";			//JOB SENT REPORT
else if (strpos($fixloc, "jobsent_report_njfu/manage") !== false)
	$fixloc = "admin/jobsent_report_njfu/manage/1/d_jobsend_id";	//JOB SENT REPORT
else if (strpos($fixloc, "admin/log/sms") !== false)				
	$fixloc = "admin/log/sms";										//LOG SMS	
	
	
//MASTER SETTING
else if (strpos($fixloc, "master/industry/manage") !== false || strpos($fixloc, "master/industry/edit") !== false || strpos($fixloc, "master/industry/update") !== false)								//MANAGE INDUSTRI
	$fixloc = "admin/master/industry/manage/1/d_industry_id";
else if (strpos($fixloc, "master/industry/add") !== false)			//ADD INDUSTRI
	$fixloc = "admin/master/industry/insert";
else if (strpos($fixloc, "education/insertInto") !== false)			//ADD EDUCATION
	$fixloc = "admin/education/add";
else if (strpos($fixloc, "education/manage") !== false || strpos($fixloc, "education/edit") !== false || strpos($fixloc, "education/update") !== false)																//MANAGE EDUCATION
	$fixloc = "admin/education/manage/1/d_edu_id";	
else if (strpos($fixloc, "jobcategory/insertInto") !== false)		//ADD JOB CATEGORY
	$fixloc = "admin/jobcategory/add";	
else if (strpos($fixloc, "jobcategory/manage") !== false || strpos($fixloc, "jobcategory/edit") !== false || strpos($fixloc, "jobcategory/update") !== false)															//MANAGE JOB CATEGORY
	$fixloc = "admin/jobcategory/manage/1/d_jobcat_id";	
else if (strpos($fixloc, "master/location/add_location") !== false)	//ADD LOCATION
	$fixloc = "admin/master/location/add";	
else if (strpos($fixloc, "master/location/manage") !== false || strpos($fixloc, "master/location/edit") !== false || strpos($fixloc, "master/location/update") !== false)								//MANAGE LOCATION
	$fixloc = "admin/master/location/manage/1/0/d_loc_id";	


//USER ACCOUNT
else if (strpos($fixloc, "jobposter/manage") !== false || strpos($fixloc, "admin/jobposter/edit") !== false)	//MANAGE INDUSTRI
	$fixloc = "admin/jobposter/manage/1/d_jobposter_id";	
	
//HELP & TOOLS
else if (strpos($fixloc, "help/manage") !== false || strpos($fixloc, "help/edit") !== false || strpos($fixloc, "help/update") !== false) //MANAGE BANTUAN
	$fixloc = "admin/help/manage/1/d_help_id";
else if (strpos($fixloc, "help/insertInto") !== false)	//ADD HELP
	$fixloc = "admin/help/add";	
else if (strpos($fixloc, "tips/manage") !== false || strpos($fixloc, "tips/edit") !== false || strpos($fixloc, "tips/update") !== false) //MANAGE BANTUAN
	$fixloc = "admin/tips/manage/1/d_tips_id";
else if (strpos($fixloc, "tips/insertInto") !== false)	//ADD TIPS & TRIK
	$fixloc = "admin/tips/add";		
	
	
// ================



//echo $fixloc;
$needle=array('url'=>$fixloc);
//echo "<pre>"; print_r($results); echo "</pre>";       
foreach ($results as $key => $value) {
	$exists = 0;
	foreach ($needle as $nkey => $nvalue) {
		if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
			$exists = 1;
		} else {
			$exists = 0;
		}
	}
	if ($exists) {
		$parents=$results[$key];		
		$parent=$parents['kode'];
	}
}
$parent=isset($parent)?$parent:"1";
?>
<div id="atas" style="position:relative; text-align:center">
<?php
foreach ($kepala as $menuatas)
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
foreach ($results as $menu)
{	
	if(substr($menu['kode'],0,1)==substr($parent,0,1))
	{
		if(strlen($menu['kode'])==1) {
			if ($atas!="") echo "</div>\n";
			$atas=$menu['kode'];
		} else {	
			echo "<div id=\"$menu[kode]\" style=\"display: inline; margin:auto; padding: 5px 5px; width: auto; font-size:8pt;\" \>";
			if($fixloc==$menu['url']) 
				echo "<b><a href=\"".base_url()."$menu[url]\" style=\"color:#000;\">$menu[nama]</a></b></div>\n";
			else
				echo "<a href=\"".base_url()."$menu[url]\" style=\"color:#000;\">$menu[nama]</a></div>\n";
		}	
	}
}?>
</div>



