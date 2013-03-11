<?php 	

	$card_type = '';
	for ($i=1; $i <= $this->uri->total_segments(); $i++) {
		//if ($this->uri->segment($i) == 'null' && in_array($this->uri->segment($i+1), array('AS', 'KH', 'SP')))
		$card_type = $this->uri->segment($i+1);
	}
	
	$filter = "";
	foreach($search as $key => $val) { 
		$filter .= (empty($val)) ? "_/" : $val ."/";
	}
	
?>
<?
//echo $_SESSION["userlevel"]."<hr>";
$useraccess = $_SESSION["userlevel"];
?>
<script type="text/javascript">
function result_row_default(a) {
	
	var urlpath = '<?php echo base_url(); ?>admin/jobpost/manage/<?php echo $page = 1; ?>/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	$(location).attr('href', urlpath);	
}
</script>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
</div>
<?
echo "<br><a href=\"".base_url()."admin/jobpost/save_csv/$order/$filter\">Export to CSV</a>";
?>
<div style="display:table; width:100%; clear:both">
<div class="row">
<?php
	echo form_open($form_submit);
?>
</div>
</div>
<?php 
if(!empty($nopage)):
echo "<ul id=\"pagination-flickr\">";
if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobpost/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
$showPage="";
for($page = 1; $page <= $npage; $page++)
{
	if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
	{
		if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
		if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
		if ($page == $nopage)
			echo "<li class=\"active\">".$page."</li>";
		else 
			echo "<li><a href=\"".base_url()."admin/jobpost/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobpost/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
echo "</ul>";
endif;
?>
<div>Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_job_id/$default_row/$filter"; ?>">Job ID</a></div>
	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_job_status/$default_row/$filter"; ?>">Status</a></div>
	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_date_add/$default_row/$filter"; ?>">Create Date</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_username/$default_row/$filter"; ?>">Create by</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_approver_id/$default_row/$filter"; ?>">Approved by</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_approved_date/$default_row/$filter"; ?>">Approved date</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_jobcat_title/$default_row/$filter"; ?>">Category</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_province_name/$default_row/$filter"; ?>">Province</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_kotamadya_name/$default_row/$filter"; ?>">Kotamadya</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_kecamatan_name/$default_row/$filter"; ?>">Kecamatan</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_loc_title/$default_row/$filter"; ?>">Kelurahan</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_job_title/$default_row/$filter"; ?>">Title</a></div>
	<? if($useraccess != "company"){ ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_company_name/$default_row/$filter"; ?>">Company</a></div>
	<? } ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_n_send/$default_row/$filter"; ?>"># sent</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobpost/manage/$page/$next_order"."_n_applied/$default_row/$filter"; ?>"># interested</a></div>
	<div class="cell table_head">Duplicate</div>
</div>
<?php
	
	$a = "";
	foreach ($results as $job){		
		if (!empty($job['job_id'])) {
		//echo "<pre>"; print_r($job); echo "</pre>";
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".$job["job_id"]."</div>\n";
		
		switch ($job["job_status"])
		{
			case 1 : $jobstatus = "Active"; break;
			case 2 : $jobstatus = "Inactive"; break;
			case 3 : $jobstatus = "Draft"; break;
			case 4 : $jobstatus = "Waiting for Approval"; break;
			case 5 : $jobstatus = "Rejected"; break;			
			default : $jobstatus = "";
		}
		
		echo "<div class=\"cell table_cell\">".$jobstatus."</div>\n";
		echo "<div class=\"cell table_cell\">".$job["date_add"]."</div>\n";
		echo "<div class=\"cell table_cell\">".ucfirst($job["username"])."</div>\n";
		echo "<div class=\"cell table_cell\">".ucfirst($job["approver_name"])."</div>\n";
		echo "<div class=\"cell table_cell\">".$job["approved_date"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$job["jobcat_title"]."</div>\n";
				
		echo "<div class=\"cell table_cell\">".$job["province_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$job["kotamadya_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$job["kecamatan_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".str_replace('.',', ',$job["loc_title"])."</div>\n";
		if($job["job_status"]=='3')
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/edit/".$job["job_id"]."\">". $job["job_title"]."</a></div>\n";
		else
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/view/".$job["job_id"]."\">". $job["job_title"]."</a></div>\n";
		if($useraccess != "company"){	//check apakan user access bukan company
			echo "<div class=\"cell table_cell\">".$job["comp_name"]."</div>\n";		
		}
		if ($job["n_send"] != 0)
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/sent/".$job["job_id"]."\" class=\"interested\" rel=\"simpleDialog\">".$job["n_send"]."</a></div>\n";
		else
			echo "<div class=\"cell table_cell\">".$job["n_send"]."</div>\n";
		if ($job["n_applied"] != 0)
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/interested/".$job["job_id"]."\" class=\"interested\" rel=\"simpleDialog\">".$job["n_applied"]."</a></div>\n";
		else
			echo "<div class=\"cell table_cell\">".$job["n_applied"]."</div>\n";
			
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/add/".$job["comp_id"]."/".$job["job_id"]."\">Duplicate</a></div>\n";
		echo "</div>\n";
		$a .= $job["job_id"].",";
		}
	}
	
	echo "</div>"; // CLOSE TABLE

	echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobpost/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
	$showPage="";
	for($page = 1; $page <= $npage; $page++)
	{
		if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
		{
			if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
			if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
			if ($page == $nopage)
				echo "<li class=\"active\">".$page."</li>";
			else 
				echo "<li><a href=\"".base_url()."admin/jobpost/manage/$page/$order/$default_row/$filter\">$page</a></li>";
			$showPage = $page;
		}
	}
	if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobpost/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
	echo "</ul>";
	
	
?>


</div>