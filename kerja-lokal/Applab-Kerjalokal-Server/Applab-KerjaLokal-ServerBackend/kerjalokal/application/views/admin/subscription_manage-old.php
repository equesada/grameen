<div style="display:table; width:100%;">
<div class="row">
<?php 
	$filter = "";
	echo "<div class=\"cell table_cell\" style=\"border:0px\">".form_open($form_submit);
	/*
	echo "Filter by ".form_dropdown("status", $list_status , $status)." "; 
	echo form_hidden("subscriber_name", $search["subscriber_name"]);
	echo form_hidden("gender", $search["gender"]);
	echo form_hidden("loc_name", $search["loc_name"]);
	echo form_hidden("salary", $search["salary"]);
	echo form_hidden("date_add", $search["date_add"]);
	echo form_submit("submit", "Submit");
	echo form_close()."</div>";
	echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$search_link\" id=\"search_form\" rel=\"simpleDialog\">Search</a></div>";
	*/
	//print_r($search);
	
	foreach($search as $key => $val)
	{ 
		$filter .= $val."/"; 		
	}
	
	echo form_open($form_submit);
?>
</div>
</div>

<div style="display:table; width:100%;">
<?php echo form_open($form_submit); ?>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
</div>
<?
echo "<br><br><br>Export to &nbsp;&nbsp;<a href=\"".base_url()."admin/subscription/save_csv/$order/$filter\">CSV</a>";
?>
<div class="table" style="width:50%">
<div class="row">

<div class="cell">Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>
<div class="cell">
<?php	
echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/subscription/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
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
				echo "<li><a href=\"".base_url()."admin/subscription/manage/$page/$order/$default_row/$filter\">$page</a></li>";
			$showPage = $page;
		}
	}
	if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/subscription/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
	echo "</ul>";	
	
?>
</div>
</div>
</div>
<div class="table">
<div class="row">
	
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_rel_id/$filter"; ?>">Rel ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_status/$filter"; ?>">Status</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_jobcat_title/$filter"; ?>">Category</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_date_active/$filter"; ?>">Start Date</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_date_expired/$filter"; ?>">End Date</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_tariff/$filter"; ?>">Tariff</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_job/$filter"; ?>">Job<br>Received</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_njfu/$filter"; ?>">NJFU<br>Received</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_all_job/$filter"; ?>">Total<br>Job info</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_mdn/$filter"; ?>">Job Seeker MDN</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_subscriber_name/$filter"; ?>">Job Seeker Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_province_name/$filter"; ?>">Province</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_kotamadya_name/$filter"; ?>">Kotamadya</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_kecamatan_name/$filter"; ?>">Kecamatan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_kelurahan_name/$filter"; ?>">Kelurahan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_mentor_name/$filter"; ?>">Job Mentor Name</a></div>
    <div class="cell table_head">Manage</div>
</div>
<?php
	//echo "<pre>"; print_r($subscribers); echo "</pre>";
	//$a = (($page-1)*$ndata)+1;	//update yudha, klo dikatifkan filternya ngga jalan
	$a = "";
	foreach ($results as $data)
	{
		//echo "<pre>"; print_r($results); echo "</pre>";
		echo "<div class=\"row\">\n";
		//echo "<div class=\"cell table_cell\">".$data["no"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$data["rel_id"]."</div>\n";
		switch ($data["status"])
		{
			case "1" : echo "<div class=\"cell table_cell\">Active</div>\n"; break;
			case "2" : echo "<div class=\"cell table_cell\">Unsub by User</div>\n"; break;
			case "3" : echo "<div class=\"cell table_cell\">Unsub by Admin</div>\n"; break;
			case "4" : echo "<div class=\"cell table_cell\">Renew</div>\n"; break;
			case "5" : echo "<div class=\"cell table_cell\">Not enough balance</div>\n"; break;
		}
		echo "<div class=\"cell table_cell\">".$data["jobcat_title"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["date_add"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["date_expired"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">3000</a></div>\n";
		
		//echo "<div class=\"cell table_cell\">".$data["n_job"]."</a></div>\n";
		//echo "<div class=\"cell table_cell\">".$data["n_njfu"]."</a></div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobsent_report/manage/1/d_subscriber_id/20/_/_/_/_/_/_/_/_/_/_/_/_/_/".$data["rel_id"]."/Y/\">".$data["n_job"]."</a></div>\n";
	//	echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobsent_report/manage/1/d_subscriber_id/20/_/_/_/_/_/_/_/_/1/_/_/_/_/".$data["rel_id"]."/_/\">".$data["n_job"]."</a></div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/1/d_subscriber_id/20/_/_/_/_/_/_/_/_/_/_/_/_/_/".$data["rel_id"]."/_/\">".$data["n_njfu"]."</a></div>\n";	
	//	echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobsent_report_njfu/manage/1/d_subscriber_id/20/_/_/_/_/_/_/_/_/1/_/_/_/_/".$data["rel_id"]."/_/\">".$data["n_njfu"]."</a></div>\n";	
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobsent_report/manage/1/d_subscriber_id/20/_/_/_/_/_/_/_/_/_/_/_/_/_/".$data["rel_id"]."/_/\">".$data["n_all_job"]."</a></div>\n";				
	//	echo "<div class=\"cell table_cell\">".$data["n_all_job"]."</a></div>\n";		
		echo "<div class=\"cell table_cell\">".$data["mdn"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["subscriber_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["province_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["kotamadya_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["kecamatan_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["kelurahan_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["mentor_name"]."</a></div>\n";
		//echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/edit/".$data["rel_id"]."\">Stop</a></div>\n";
		echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"unsub(".$data["rel_id"].");\">Stop</span></div>\n";

		echo "<br />";
		echo "<div class=\"row\">";
		echo "<div id=\"jobcat_detail\"></div>";
		echo "</div>";		
		echo "</div>\n";
		$a++;
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	
	/*
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) $paging .= "<a href=\"".base_url()."admin/subscription/manage/$a/$order\">$a</a> - ";
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	//echo form_submit("submit", "Disabled selected job seeker");
	echo form_close();
	echo "<br><br><a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">Save as CSV</a>";
	*/

?>
</div>


<div class="table" style="width:50%">
<div class="row">

<div class="cell">Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>
<div class="cell">
<?php	
echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/subscription/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
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
				echo "<li><a href=\"".base_url()."admin/subscription/manage/$page/$order/$default_row/$filter\">$page</a></li>";
			$showPage = $page;
		}
	}
	if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/subscription/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
	echo "</ul>";	
	
?>
</div>
</div>

