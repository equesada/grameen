<?php echo form_open($form_submit);?>
<div style="display:table; width:100%;">
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_tips_id">ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_tips_title">Title</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_description">Description</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_date_add">Date Register</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_date_update">Last Activity</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/tips/manage/$page/$next_order"; ?>_status">Status</a></div>
</div>
<?php
	$a = "";
	foreach ($results as $help)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$help["tips_id"],($help["status"]=="2") ? TRUE : "")." ".$help["tips_id"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/tips/edit/".$help["tips_id"]."\">". $help["tips_title"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$help["description"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$help["date_add"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$help["date_update"]."</div>\n";
		if ($help["status"] == "1")
			echo "<div class=\"cell table_cell\">Active</div>\n";
		else
			echo "<div class=\"cell table_cell\">Inactive</div>\n";
		echo "</div>\n";
		$a .= $help["tips_id"].",";
	}
	echo "</div>";	
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) $paging .= "<a href=\"".base_url()."admin/tips/manage/$a/".$this->session->userdata('order')."\">$a</a> - ";
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	echo form_submit("submit", "Disabled selected Tips dan Trik");
	echo form_close();
?>
</div>