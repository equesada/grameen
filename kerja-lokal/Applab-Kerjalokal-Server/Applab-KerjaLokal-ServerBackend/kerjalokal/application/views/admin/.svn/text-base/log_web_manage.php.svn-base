<style>
.cell {
	font-size:8pt;
}
</style>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px">Log ID</div>
	<div class="cell table_head">Job Poster</div>
	<div class="cell table_head">Title</div>
	<div class="cell table_head">Source</div>
	<div class="cell table_head">Date Time</div>
	<div class="cell table_head">Response</div>
</div>
<?php
	foreach ($logs["results"] as $log)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".$log["log_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["jobposter_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["title"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["src"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$log["date_add"]."</div>\n";
		//echo "<div class=\"cell table_cell\">".substr($log["filename"], strrpos($log["filename"], "/")+1)."</div>\n";
		//echo "<div class=\"cell table_cell\"><a href=\"".base_url()."func/log_webdetail.php?webdms=web&id=".$log["log_id"]."\" class=\"log_detail\" rel=\"simpleDialog\">Details</a></div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/log/detail/web/".$log["log_id"]."\" class=\"log_detail\" rel=\"simpleDialog\">Details</a></div>\n";
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