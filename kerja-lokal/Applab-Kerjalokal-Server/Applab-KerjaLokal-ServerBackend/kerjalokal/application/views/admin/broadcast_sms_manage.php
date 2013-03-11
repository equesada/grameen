<style>
.cell {
	font-size:8pt;
}
</style>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px">SMS ID</div>
	<div class="cell table_head">Job Poster</div>
	<div class="cell table_head">Message</div>
	<div class="cell table_head">MDN</div>
	<div class="cell table_head">Date Time</div>	
</div>
<?php	
	foreach ($logs["results"] as $log)
	{
		//echo "<pre>"; print_r($logs["results"]); echo "</pre>";
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".$log["sms_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["jobposter_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["msg"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["mdn"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["date_add"]."</div>\n";
		echo "</div>\n";
	}
	echo "</div>";
	
	$paging = "";
	
	if ($logs["npage"] > 1)
		for ($a = 1; $a <=$logs["npage"]; $a++) 
			if ($a == $logs["page"])
				$paging .= "<b>$a</b> - ";
			else
				$paging .= "<a href=\"".base_url()."admin/log/web/$a\">$a</a> - ";
		
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
?>
</div>