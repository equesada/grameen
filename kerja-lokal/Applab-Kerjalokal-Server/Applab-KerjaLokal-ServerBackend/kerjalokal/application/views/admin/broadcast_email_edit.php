<?php
	echo form_open($form_submit);
	//echo "<input type='hidden' value='".$help_id."' name='id' id='id' />";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Sender name', 'sender_name')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("sender_name", $sender_name ,"size=40 maxlength=100").form_error("sender_name", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Sender e-mail', 'sender_email')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("sender_email", $sender_email ,"size=40 maxlength=100").form_error("sender_email", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Title', 'title')."</div>\n";	
	echo "<div class=\"cell_val\">".form_input("title", $title ,"size=40 maxlength=100").form_error("title", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Message', 'msg')."</div>\n";
	echo "<div class='cell_val'>".form_textarea("msg", $msg ,"rows=5 cols=60").form_error('msg', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Recepient type', 'email_type')."</div>\n";
	
	$all_company = array(
		'name'        => 'all_company',
		'id'          => 'all_company',
		'value'       => 'all_company'
    );
	
	$all_jobmentor = array(
		'name'        => 'all_jobmentor',
		'id'          => 'all_jobmentor',
		'value'       => 'all_jobmentor'		
    );
	
	echo "<div class=\"cell_val\">".form_checkbox($all_company)." All Company<br>";	
	echo form_checkbox($all_jobmentor)." All Job Mentor<br>";
	//echo form_checkbox("email_type", $email_type)." All Job Seeker";
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key' style='vertical-align:top'>".form_label('Other Email', 'email')."</div>\n";
	echo "<div class='cell_val'>".form_textarea("email", $email ,"rows=5 cols=60").form_error('email', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Submit")."</div>";
	echo form_close();
?>