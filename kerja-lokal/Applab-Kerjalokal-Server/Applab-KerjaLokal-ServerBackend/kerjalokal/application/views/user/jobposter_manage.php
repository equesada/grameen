<div style="display:table; width:100%;">
<div class="row">
<?php 
	$filter = "";
	echo "<div class=\"cell table_cell\" style=\"border:0px\">".form_open($form_submit);
	
	echo "Filter by ".form_dropdown("status", $list_status , $status)." "; 
	echo form_hidden("username", $search["username"]);
	echo form_hidden("phone", $search["phone"]);
	echo form_hidden("mobile", $search["mobile"]);
	echo form_hidden("userlevel", $search["userlevel"]);
	echo form_hidden("email", $search["email"]);
	echo form_hidden("date_add", $search["date_add"]);
	echo form_submit("submit", "Submit");
	echo form_close()."</div>";
	//echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$search_link\" id=\"search_form\" rel=\"simpleDialog\">Search</a></div>";

	foreach($search as $key => $val)
	{ $filter .= urlencode($val)."/"; }
	
	echo form_open($form_submit);
?>
</div>
</div>

<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_jobposter_id/$filter"; ?>">ID</a></div>	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_username/$filter"; ?>">Username</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_password/$filter"; ?>">Password</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_userlevel/$filter"; ?>">Level</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_email/$filter"; ?>">Email</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobposter/manage/$page/$next_order"."_status/$filter";?>">Status</a></div>
</div>
<?php
	$a = "";
	foreach ($results as $jobposter)
	{
		echo "<div class=\"row\">\n";
		//echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$jobposter["jobposter_id"], ($jobposter["jobposter_status"]=="2") ? TRUE : "")." ".$jobposter["jobposter_id"];
		echo "<div class=\"cell table_cell\">".$jobposter["jobposter_id"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobposter/edit/".$jobposter["jobposter_id"]."\">". $jobposter["username"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["phone"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["mobile"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["password"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["userlevel"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["email"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobposter["position"]."</div>\n";
		if ($jobposter["jobposter_status"] == "1")
			echo "<div class=\"cell table_cell\">Active</div>\n";
		else
			echo "<div class=\"cell table_cell\">Inactive</div>\n";
		echo "</div>\n";
		$a .= $jobposter["jobposter_id"].",";
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>";
	echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) 
			if ($a == $page)
				$paging .= "<b>$a</b> - ";
			else
				$paging .= "<a href=\"".base_url()."admin/jobposter/manage/$a/$order/$filter\">$a</a> - ";
		
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	//echo form_submit("submit", "Disabled selected job post");
	echo form_close();
	echo "<br><br><a href=\"".base_url()."admin/jobposter/save_csv/$order/$filter\">Save as CSV</a>";
?>
</div>