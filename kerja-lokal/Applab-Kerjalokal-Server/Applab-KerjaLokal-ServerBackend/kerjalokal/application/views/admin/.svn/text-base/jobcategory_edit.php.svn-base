
<div style="display:table; width:100%;">
<?php
	echo form_open($form_action);
	//$this->form_validation->set_message('required', 'Error Message');
	//$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	//echo form_error('name', '<div class="error">', '</div>');
	echo "<input type='hidden' value='".$jobcat_id."' name='id' id='id' />";
	echo "<div class='row'><div class='cell_key'>".form_label('Job Category', 'jobcat_title')."</div>\n";
	echo "<div class='cell_val'>".form_input("jobcat_title", $jobcat_title ,"size=40 maxlength=100").form_error('jobcat_title', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Description', 'description')."</div>\n";
	echo "<div class='cell_val'>".form_input("description", $description ,"size=40 maxlength=100").form_error('description', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div>";
	echo form_close();
?>
</div>
<script>
</script>