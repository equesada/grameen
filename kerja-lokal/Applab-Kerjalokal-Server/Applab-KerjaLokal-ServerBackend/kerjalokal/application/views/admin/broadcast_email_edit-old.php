<?php
	$list_percent = array();
	for ($a = 0; $a <=100; $a++)
	{ $list_percent[$a] = $a; }
	
	echo "<div class=\"table\">";
	echo form_open($form_submit);
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Sender name', 'sender_name')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("sender_name", $sender_name ,"size=40 maxlength=100").form_error("sender_name", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Sender e-mail', 'sender_email')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("sender_email", $sender_email ,"size=40 maxlength=100").form_error("sender_email", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Title', 'title')."</div>\n";
	echo "<div class=\"cell_val\">".form_input("title", $title ,"size=40 maxlength=100").form_error("title", "<div class='form_error'>", "</div>")."</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Message', 'msg')."</div>\n";
	echo "<div class=\"cell_val\"><textarea cols=60 rows=5>$msg</textarea></div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Recepient type', 'email_type')."</div>\n";
	echo "<div class=\"cell_val\">".form_checkbox("email_type", $email_type)." All Company<br>";
	echo form_checkbox("email_type", $email_type)." All Job Mentor<br>";
	echo form_checkbox("email_type", $email_type)." All Job Seeker";
	echo "</div></div>\n";
	echo "<div class=\"row\"><div class=\"cell_key\">".form_label('Other e-mail', 'msg')."</div>\n";
	echo "<div class=\"cell_val\"><textarea cols=60 rows=5>$msg</textarea><br>separated by ;</div></div>\n";
	echo form_hidden("email_id", $email_id);
	echo form_submit("submit", "Submit");
	echo form_close();
	echo "</div>";
?>


<hr>
