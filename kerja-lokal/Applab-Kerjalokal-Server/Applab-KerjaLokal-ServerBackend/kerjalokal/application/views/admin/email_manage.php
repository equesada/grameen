<?php echo form_open(base_url()."admin/broadcast/manage/".$countries["page"]); ?>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:30px">Active</div>
	<div class="cell table_head" style="width:50px">ID</div>
	<div class="cell table_head">Country</div>
	<div class="cell table_head">Date Format</div>
	<div class="cell table_head">Time Format</div>
	<div class="cell table_head">Number Format</div>
	<div class="cell table_head">Currency</div>
	<div class="cell table_head">Language</div>
</div>
<?php
	
	foreach ($countries["results"] as $country)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".form_radio("activate",$country["country_id"], $country["is_current"] == "1" ? "TRUE" : "")."</div>\n";
		echo "<div class=\"cell table_cell\">".$country["country_id"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/country/edit/".$country["country_id"]."\">".$country["country_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$country["format_date"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$country["format_time"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$country["format_number"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$country["format_currency"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$country["lang"]."</div>\n";
		echo "</div>\n";
	}
	$paging = "";
	
	if ($countries["npage"] > 1)
		for ($a = 1; $a <=$countries["npage"]; $a++) 
			if ($a == $countries["page"])
				$paging .= "<b>$a</b> - ";
			else
				$paging .= "<a href=\"".base_url()."admin/country/manage/$a\">$a</a> - ";
		
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
	echo form_submit("submit", "Set active country");
	echo form_close();
?>
</div>