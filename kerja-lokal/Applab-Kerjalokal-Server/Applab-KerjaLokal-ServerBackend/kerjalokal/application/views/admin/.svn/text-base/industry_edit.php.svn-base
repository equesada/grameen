<div class="table">
<?php
	echo form_open($form_action);
	echo "<input type='hidden' value='".$industry_id."' name='id' id='id' />";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Industry name', 'title')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("title", $industry_title ,"size=40 maxlength=100").form_error('title', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Description', 'desc')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("desc", $description ,"size=40 maxlength=100")."</div>\n";
	echo "</div>\n";
	echo "<div class=\"row\">\n";
	echo "<div class=\"cell_key\">".form_label('Status', 'status_id')."</div>\n";
	$options = array(
		"1" => "Active",
		"2" => "Inactive"
	);	
	echo "<div class=\"cell_val\">".form_dropdown('status', $options, $status)."</div>\n";
	echo "</div>\n</div>\n";
	echo form_hidden("country_id", "ID");
	echo form_submit("submit", "Submit");
	echo form_close();
	
?>
</div>