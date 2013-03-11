<?php echo form_open($form_submit);?>
<table class="table_style">
<tr>
	<td class="table_head" style="width:50px"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_help_id">ID</a></td>
	<td class="table_head"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_help_title">Title</a></td>
    <td class="table_head"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_description">Description</a></td>
	<td class="table_head"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_date_add">Date Register</a></td>
	<td class="table_head"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_date_update">Last Activity</a></td>
	<td class="table_head"><a href="<?php echo base_url()."admin/help/manage/$page/$next_order"; ?>_status">Status</a></td>
</tr>
<?php
	$a = "";
	foreach ($results as $help)
	{
		echo "<tr>\n";
		echo "<td class=\"table_cell\">".form_checkbox("del[]",$help["help_id"],($help["status"]=="2") ? TRUE : "")." ".$help["help_id"]."</td>\n";
		echo "<td class=\"table_cell\"><a href=\"".base_url()."admin/help/edit/".$help["help_id"]."\">". $help["help_title"]."</a></td>\n";
		echo "<td class=\"table_cell\">".$help["description"]."</td>\n";
		echo "<td class=\"table_cell\">".$help["date_add"]."</td>\n";
		echo "<td class=\"table_cell\">".$help["date_update"]."</td>\n";
		if ($help["status"] == "1")
			echo "<td class=\"table_cell\">Active</td>\n";
		else
			echo "<td class=\"table_cell\">Inactive</td>\n";
		echo "</tr>\n";
		$a .= $help["help_id"].",";
	}
	echo "</table>";	
	$a = substr($a, 0, strlen($a)-1);
	
	//echo "</td>"; // CLOSE TABLE
	echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) $paging .= "<a href=\"".base_url()."admin/help/manage/$a/".$this->session->userdata('order')."\">$a</a> - ";
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	echo form_submit("submit", "Disabled selected help");
	echo form_close();
?>
