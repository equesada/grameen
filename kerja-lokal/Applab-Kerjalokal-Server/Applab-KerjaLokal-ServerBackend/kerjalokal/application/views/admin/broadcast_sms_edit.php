<?php
	echo form_open($form_submit);
	//echo "<input type='hidden' value='".$help_id."' name='id' id='id' />";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Recepient type', 'mdn_type')."</div>\n";
	
	$mdn_type = array(
		'name'        => 'mdn_type',
		'id'          => 'mdn_type',
		'value'       => 'mdn_type'
    );
	
	echo "<div class=\"cell_val\">".form_checkbox($mdn_type)." All Subscriber<br>";	
	
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key' style='vertical-align:top'>".form_label('Other MDN', 'mdn')."</div>\n";
	echo "<div class='cell_val'>".form_textarea("mdn", $mdn ,"rows=5 cols=60").form_error('mdn', '<div class="form_error" style="color:red">', '</div>')."<br>separated by ;</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Message', 'msg')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("msg", $msg ,"size=40 maxlength=159").form_error("msg", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div>";
	echo form_close();
?>