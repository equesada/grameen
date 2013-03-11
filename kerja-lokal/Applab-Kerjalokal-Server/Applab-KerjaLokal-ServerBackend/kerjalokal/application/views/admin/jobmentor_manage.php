<div style="display:table; width:100%;">
<div class="row">
<?php 
	$filter = "";
	echo "<div class=\"cell table_cell\" style=\"border:0px\">".form_open($form_submit);

	echo "Filter by ".form_dropdown("status", $list_status , $status)." "; 
	echo form_hidden("mentor", $search["mentor"]);
	echo form_hidden("mdn", $search["mdn"]);
	echo form_hidden("pin", $search["pin"]);
	echo form_hidden("date_add", $search["date_add"]);	
	echo form_submit("submit", "Submit");
	echo form_close()."</div>";
	echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$search_link\" id=\"search_form\" rel=\"simpleDialog\">Search</a></div>";

	foreach($search as $key => $val)
	{ $filter .= urlencode($val)."/"; }
	
	//echo form_open($form_submit);
?>
</div>
</div>

<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/jobmentor/manage/$page/$next_order"; ?>_mentor_id">ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobmentor/manage/$page/$next_order"; ?>_name">Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobmentor/manage/$page/$next_order"; ?>_mdn">MDN</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobmentor/manage/$page/$next_order"; ?>_pin">PIN</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobmentor/manage/$page/$next_order"; ?>_status">Status</a></div>
</div>
<?php
	$a = "";
	foreach ($results as $jobmentor)
	{
		echo "<div class=\"row\">\n";
		//echo "<div class=\"cell table_cell\">".form_checkbox("del[]", $jobmentor["mentor_id"], ($jobmentor["mentor_status"]=="2") ? TRUE : "")." ".$jobmentor["mentor_id"]."</div>\n";;
		echo "<div class=\"cell table_cell\">".$jobmentor["mentor_id"]."</div>\n";;
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobmentor/edit/".$jobmentor["mentor_id"]."\">". $jobmentor["name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$jobmentor["mdn"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobmentor["pin"]."</div>\n";
		if ($jobmentor["status"] == "1")
			echo "<div class=\"cell table_cell\">Active</div>\n";
		else
			echo "<div class=\"cell table_cell\">Inactive</div>\n";
		echo "</div>\n";
		$a .= $jobmentor["mentor_id"].",";
	}
	//echo "</div>";
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	//echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) 
			if ($a == $page)
				$paging .= "<b>$a</b> - ";
			else
				$paging .= "<a href=\"".base_url()."admin/jobmentor/manage/$a/$order\">$a</a> - ";
		
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	//echo form_submit("submit", "Disabled selected company");
	//echo form_close();
	echo "<br><br><a href=\"".base_url()."admin/jobmentor/save_csv/$order/$filter\">Save as CSV</a>";
?>
</div>
