<style>
.cell {
	font-size:8pt;
}
</style>
<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px">Job Send ID</div>
	<div class="cell table_head">Job Title</div>
	<div class="cell table_head">Recepient</div>
	<div class="cell table_head">Schedule</div>
</div>
<?php
	foreach ($jobsends["results"] as $jobsend)
	{
		echo "<div class=\"row\">\n";
		echo "<div class=\"cell table_cell\">".$jobsend["jobsend_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobsend["title"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobsend["name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$jobsend["date_send"]."</div>\n";
		echo "</div>\n";
	}
	echo "</div>";
	
	$paging = "";
	
	if ($jobsends["npage"] > 1)
		for ($a = 1; $a <=$logs["npage"]; $a++) 
			if ($a == $logs["page"])
				$paging .= "<b>$a</b> - ";
			else
				$paging .= "<a href=\"".base_url()."admin/jobsend/plan/$a\">$a</a> - ";
		
	$paging = substr($paging,0,strlen($paging)-3);
	echo $paging."<br><br>";
?>
</div>