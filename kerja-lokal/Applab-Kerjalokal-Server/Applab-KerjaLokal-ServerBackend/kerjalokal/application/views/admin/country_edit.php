<?php
	
	echo "<div class=\"table\">";
	echo form_open($form_submit);
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('ID', 'country_id')."</div>\n";
	if (strpos(current_url(), "/country/add") != FALSE)
		echo "<div class=\"cell_val\">".form_input("country_id", $country_id ,"size=2 maxlength=2").form_error("country_id", "<div class='form_error'>", "</div>")."</div></div>\n";
	else
		echo "<div class=\"cell_val\"><b>$country_id</b>".form_hidden("country_id", $country_id)."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Name', 'country_name')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("country_name", $country_name ,"size=40 maxlength=100").form_error("country_name", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Date Format', 'format_date')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("format_date", $list_format_date ,$format_date, "id=format_date").form_error("format_date", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Time Format', 'format_time')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("format_time", $list_format_time ,$format_time, "id=format_time").form_error("format_time", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Number Format', 'format_number')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("format_number", $list_format_number ,$format_number, "id=format_number").form_error("format_number", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Currency', 'format_currency')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("format_currency", $format_currency ,"size=5 maxlength=10").form_error("format_currency", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Language', 'lang')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("lang", $lang ,"size=4 maxlength=10")."</div></div>\n";
	echo form_submit("submit", "Submit");
	echo form_close();
	echo "</div>";
?>


<hr>
