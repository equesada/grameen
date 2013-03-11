
<div style="display:table; width:100%;">
<?php
	
	$list_subscriber = array("0" => "--");
	$list_jobcat = array("0" => "--");
	
	foreach ($jobcats as $a)
	{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }
	
	echo form_open($form_submit);
	echo "<div class='right_section'><b>$subtitle</b></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Subscriber', 'subscriber_id')."</div>\n";
	
	foreach ($subscribers as $a)
	{ $list_subscriber[$a["subscriber_id"]] = $a["subscriber_name"]; }
	echo "<div class='cell_val'>".form_dropdown("subscriber_id", $list_subscriber).form_error("subscriber_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label('Job Category', 'jobcat_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("jobcat_id", $list_jobcat, $jobcat_id).form_error("jobcat_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div></div>";
	
	echo form_close();



	?>
</div>
