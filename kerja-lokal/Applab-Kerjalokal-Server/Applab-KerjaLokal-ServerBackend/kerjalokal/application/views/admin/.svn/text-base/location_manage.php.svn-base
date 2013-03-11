
<div style="display:table; width:100%;">

<?php echo form_open($form_submit); ?>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:20px"></div>
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_loc_id">ID</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_loc_type">Type</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_name">Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_loc_lat">Latitude</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_loc_lng">Longitude</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_zipcode">Zip Code</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/master/location/manage/$page/0/$next_order"; ?>_date_update">Date Update</a></div>
</div>
<?php
	$a = "";
	foreach ($results as $location)
	{
		$loc_type = $location["loc_type"];
		$loc_type = $loc_type == "KOTA" ? "PROVINCE" : $loc_type;
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".form_checkbox("del[]", $location["loc_id"], ($location["status"]=="2") ? TRUE : "")."</div>\n";
		echo "<div class=\"cell table_cell\">".$location["loc_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$loc_type."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/master/location/edit/1/".$location["loc_id"]."\">". $location["name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$location["loc_lat"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$location["loc_lng"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$location["zipcode"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$location["date_update"]."</div>\n";
		echo "</div>\n";
		$a .= $location["loc_id"].",";
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	echo form_hidden("all_id", $a);
	
	$paging = "";
	
	if ($npage > 1)
		for ($a = 1; $a <=$npage; $a++) $paging .= "<a href=\"".base_url()."admin/master/location/manage/$a\">$a</a> - ";
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	echo form_submit("submit", "Disabled selected location");
	echo form_close();
?>


</div>