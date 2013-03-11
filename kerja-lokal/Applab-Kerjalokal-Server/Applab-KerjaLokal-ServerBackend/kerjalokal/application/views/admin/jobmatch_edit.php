<?php
	$list_percent = array();
	for ($a = 0; $a <=100; $a++)
	{ $list_percent[$a] = $a; }
	
	echo "<div class=\"table\">";
	echo form_open($form_submit);
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Title', 'title')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("title", $title ,"size=40 maxlength=100").form_error("title", "<div class='form_error'>", "</div>")."</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Description', 'description')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("description", $description ,"size=40 maxlength=100")."</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Max distance', 'max_dis')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("max_dis", $max_dis ,"size=4 maxlength=4")." km</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Max # send', 'max_nsend')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("max_nsend", $max_nsend ,"size=4 maxlength=4")." pcs</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Distance', 'dis')."</div>\n";
	echo "<div class=\"cell_val\">".form_dropdown("dis", $list_percent, $dis)." %</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('# send', 'nsend')."</div>\n";
	echo "<div class=\"cell_val\">".form_dropdown("nsend", $list_percent, $nsend)." %</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Date to expired', 'expired')."</div>\n";
	echo "<div class=\"cell_val\">".form_dropdown("expired", $list_percent, $expired)." %</div>\n";
	echo "</div>\n";
	echo form_hidden("jobmatch_id", $jobmatch_id);
	echo form_hidden("country_id", "ID");
	echo form_submit("submit", "Submit");
	echo form_close();
	echo "</div>";
?>


<hr>
