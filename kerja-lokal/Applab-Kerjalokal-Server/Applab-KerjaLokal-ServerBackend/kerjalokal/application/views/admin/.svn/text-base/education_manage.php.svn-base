<?php echo form_open($form_submit);?>
<div style="display:table; width:100%;">
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/education/manage/$page/$next_order"; ?>_edu_id">ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/education/manage/$page/$next_order"; ?>_edu_title">Education</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/education/manage/$page/$next_order"; ?>_date_add">Date Register</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/education/manage/$page/$next_order"; ?>_date_update">Last Activity</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/education/manage/$page/$next_order"; ?>_status">Status</a></div>
</div>
<?php
	$a = "";
	foreach ($results as $education)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$education["edu_id"],($education["status"]=="2") ? TRUE : "")." ".$education["edu_id"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/education/edit/".$education["edu_id"]."\">". $education["edu_title"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$education["date_add"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$education["date_update"]."</div>\n";
		if ($education["status"] == "1")
			echo "<div class=\"cell table_cell\">Active</div>\n";
		else
			echo "<div class=\"cell table_cell\">Inactive</div>\n";
		echo "</div>\n";
		$a .= $education["edu_id"].",";
	}
	echo "</div>";	
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) $paging .= "<a href=\"".base_url()."admin/education/manage/$a/".$this->session->userdata('order')."\">$a</a> - ";
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	echo form_submit("submit", "Disabled selected education");
	echo form_close();
?>
</div>