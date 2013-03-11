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
	
	//foreach($search as $key => $val)
	//{ $filter .= $val."/"; }
	foreach($search as $key => $val)
		{ $filter .= ($val != "") ? $val."/" : "_/"; }
	
	echo form_open($form_submit);
?>
</div>
</div>

<div style="display:table; width:100%;">
<?php echo form_open($form_submit); ?>
<div class="divClearBoth">
<script type="text/javascript">
function result_row_default(a) {
	
	//var urlpath = 'http://110.138.141.255:8085/kerjalokal/admin/jobseeker/manage/1/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	var urlpath = '<?php echo base_url(); ?>admin/subscription/manage/1/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	$(location).attr('href', urlpath);	
}
</script>
<?php 
	//echo "<a href=\"$search_link; \" id=\"search_form\" rel=\"simpleDialog\">Search</a>
	
	//*** function paging, result page, export csv
	echo "<div class=\"divClearBoth\" style=\"border:1px solid #000\">$search_form</div>";
	echo "<div style=\"display:table;\"><div style=\"display:table-row\"><div style=\"display:table-cell; width:50%\">"; 
	echo "<a href=\"".base_url()."admin/subscription/save_csv/$order/$filter\">Export to CSV</a>";
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1> Result/Pages "; 

	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	//echo form_open($form_submit); 
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1>";
	//echo form_close();
	$page = 1;
	if(!empty($nopage)):
		echo "</div><div style=\"display:table-cell; width:50%;\">";
		echo "<ul id=\"pagination-flickr\" style=\"padding-top:10px;\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/subscription/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;Prev</a></li>";
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
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/subscription/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next&gt;</a></li>";
		echo "</ul>";
		echo "</div></div></div>";
	endif;
?>

	
</div>


<div class="table">
<div class="row">
	
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_rel_id/$default_row/$filter"; ?>">Rel ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_status/$default_row/$filter"; ?>">Status</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_jobcat_title/$default_row/$filter"; ?>">Category</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_date_add/$default_row/$filter"; ?>">Start Date</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_date_expired/$default_row/$filter"; ?>">End Date</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_tariff/$default_row/$filter"; ?>">Tariff</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_job/$default_row/$filter"; ?>">Job<br>Received</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_njfu/$default_row/$filter"; ?>">NJFU<br>Received</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_n_all_job/$default_row/$filter"; ?>">Total<br>Job info</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_mdn/$default_row/$filter"; ?>">Job Seeker MDN</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_subscriber_name/$default_row/$filter"; ?>">Job Seeker Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_province_name/$default_row/$filter"; ?>">Province</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_kotamadya_name/$default_row/$filter"; ?>">Kotamadya</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_kecamatan_name/$default_row/$filter"; ?>">Kecamatan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_loc_title/$default_row/$filter"; ?>">Kelurahan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/subscription/manage/$nopage/$next_order"."_mentor_name/$default_row/$filter"; ?>">Job Mentor Name</a></div>
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
			case "2" : echo "<div class=\"cell table_cell\">Unreg by User</div>\n"; break;
			case "3" : echo "<div class=\"cell table_cell\">Unreg by Admin</div>\n"; break;
			case "4" : echo "<div class=\"cell table_cell\">Renew</div>\n"; break;
			case "5" : echo "<div class=\"cell table_cell\">Not enough balance</div>\n"; break;
			case "6" : echo "<div class=\"cell table_cell\">Not activated yet</div>\n"; break;
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
		echo "<div class=\"cell table_cell\">".$data["loc_title"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$data["mentor_name"]."</a></div>\n";
		//echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/subscription/edit/".$data["rel_id"]."\">Stop</a></div>\n";
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
	echo "<br><br><a href=\"".base_url()."admin/subscription/save_csv/$order/$filter\">Save as CSV</a>";
	*/

?>
</div>

<?php 
	//echo "<a href=\"$search_link; \" id=\"search_form\" rel=\"simpleDialog\">Search</a>	
	echo "<div style=\"display:table;\"><div style=\"display:table-row\"><div style=\"display:table-cell; width:50%\">"; 
	echo "<a href=\"".base_url()."admin/subscription/save_csv/$order/$filter\">Export to CSV</a>";
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1> Result/Pages "; 

	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	//echo form_open($form_submit); 
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1>";
	//echo form_close();
	$page = 1;
	if(!empty($nopage)):
		echo "</div><div style=\"display:table-cell; width:50%;\">";
		echo "<ul id=\"pagination-flickr\" style=\"padding-top:10px;\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/subscription/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;Prev</a></li>";
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
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/subscription/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next&gt;</a></li>";
		echo "</ul>";
		echo "</div></div></div>";
	endif;
?>

