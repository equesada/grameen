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
	{ $filter .= $val."/"; }
	
	/*
	$filter = "";
		foreach($search as $key => $val) { 
		$filter .= (empty($val)) ? 'null/' : $val ."/";
	}
	*/
	echo form_open($form_submit);
?>
</div>
</div>

<div style="display:table; width:100%;">
<?php echo form_open($form_submit); ?>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
	<? echo "<br><a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">Save as CSV</a>"; ?>
</div>
<?php 
if(!empty($nopage)):
	echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
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
			echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
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
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_id/$filter"; ?>">ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_status/$filter"; ?>">Status</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_name/$filter"; ?>">Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_gender/$filter"; ?>">Gender</a></div>
    
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_mdn/$filter"; ?>">Tel.</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_edu_title/$filter"; ?>">Education</a></div>
	
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_province_name/$filter"; ?>">Province</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_kotamadya_name/$filter"; ?>">Kotamadya</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_kecamatan_name/$filter"; ?>">Kecamatan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_loc_title/$filter"; ?>">Kelurahan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_name/$filter"; ?>">Mentor</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_birthday/$filter"; ?>">Date of Birth</a></div>
	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_date_add/$filter"; ?>">Date Register</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_date_update/$filter"; ?>">Last Activity</a></div>
    <div class="cell table_head">Manage</div>
</div>
<?php
	//echo "<pre>"; print_r($subscribers); echo "</pre>";
	$a = "";
	foreach ($results as $subscriber)
	{
		if (!empty($subscriber['subscriber_id'])) {
			//echo "<pre>"; print_r($results); echo "</pre>";
			echo "<div class=\"row\">\n";
			//echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$subscriber["subscriber_id"],($subscriber["status"]=="2") ? TRUE : "")." ".$subscriber["subscriber_id"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["subscriber_id"]."</div>\n";
			if ($subscriber["status"] == "1")
				echo "<div class=\"cell table_cell\">Active</div>\n";
			else
				echo "<div class=\"cell table_cell\">Inactive</div>\n";
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/edit/".$subscriber["subscriber_id"]."\">". $subscriber["subscriber_name"]."</a></div>\n";
			
			if ($subscriber["gender"] == "F")
				echo "<div class=\"cell table_cell\">Female</div>\n";
			else
				echo "<div class=\"cell table_cell\">Male</div>\n";
			
			echo "<div class=\"cell table_cell\">".$subscriber["mdn"]."</div>\n";
			
			echo "<div class=\"cell table_cell\">".$subscriber["edu_title"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["province_name"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["kotamadya_name"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["kecamatan_name"]."</div>\n";
			
			echo "<div class=\"cell table_cell\">".$subscriber["loc_title"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["subscriber_name"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["birthday"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["subscriber_date_add"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["subscriber_date_update"]."</div>\n";
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/edit/".$subscriber["subscriber_id"]."\">Edit</a></div>\n";
			echo "</div>\n";
			$a .= $subscriber["subscriber_id"].",";
		}
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE	
	echo form_hidden("all_id", $a);
	
	
	
?>
</div>
<?php 
if(!empty($nopage)):
	echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
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
			echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
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

