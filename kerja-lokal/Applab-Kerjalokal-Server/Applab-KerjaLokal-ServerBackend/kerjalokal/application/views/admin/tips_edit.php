
<div style="display:table; width:100%;">
<?php
	echo form_open($form_action);
	echo "<input type='hidden' value='".$tips_id."' name='id' id='id' />";
	echo "<div class='row'><div class='cell_key'>".form_label('Title', 'tips_title')."</div>\n";
	echo "<div class='cell_val'>".form_input("tips_title", $tips_title ,"size=40 maxlength=100").form_error('tips_title', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key' style='vertical-align:top'>".form_label('Description', 'description')."</div>\n";
	echo "<div class='cell_val'>".form_textarea("description", $description ,"rows=60 cols=6").form_error('description', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div>";
	echo form_close();
?>
</div>
