
<div style="display:table; width:100%;">
<?php
	echo form_open($form_action);
	$comp_ids = array("0"=>"--");

	foreach ($companies["results"] as $a)
	{ $comp_ids[$a["comp_id"]] = $a["company_name"]; }
	
	$list_status = array("0" => "--", "1" => "Active", "2" => "Inactive");
	
	if ($_SESSION["userlevel"] != "superadmin")
		$list_userlevels = array(
				'-' => "-",
				'company' => "Company",
				'jobposter' => "Jobposter"
		);
	else
		$list_userlevels = array(
				'-' => "-",
				'superadmin' => "Superadmin",
				'admin' => "Admin",
				'company' => "Company",
				'jobposter' => "Jobposter"
		);
	
	

	echo "<div class='row'><div class='cell_key'>".form_label('Company', 'comp_id')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("comp_id", $comp_ids, $comp_id,"id=\"comp_dd\"")." </div></div>\n";
	echo "<input type='hidden' value='".$jobposter_id."' name='jobposter_id' id='id' />";
	echo "<div class='row'><div class='cell_key'>".form_label("Username", "username")."</div>\n";
	echo "<div class='cell_val'>".form_input("username", $username ,"size=40 maxlength=20 id=username").form_error('username', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label("Phone", "phone")."</div>\n";
	echo "<div class='cell_val'>".form_input("phone", $phone ,"size=40 maxlength=20 id=phone").form_error('phone', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label("Mobile", "mobile")."</div>\n";
	echo "<div class='cell_val'>".form_input("mobile", $mobile ,"size=40 maxlength=20 id=mobile").form_error('mobile', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label('E-mail', 'email')."</div>\n";
	echo "<div class='cell_val'>".form_input("email", $email ,"size=40 maxlength=100 id=email").form_error('email', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label("Position", "position")."</div>\n";
	echo "<div class='cell_val'>".form_input("position", $position ,"size=40 maxlength=20 id=position").form_error('position', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label('Password', 'password')."</div>\n";
	echo "<div class='cell_val'>".form_input("password", $password ,"size=40 maxlength=100 id=email").form_error('password', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('User Level', 'userlevel')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("userlevel", $list_userlevels ,$userlevel, "id=userlevel")."</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Status', 'status')."</div>\n";
	echo "<div class='cell_val'>".form_dropdown("status", $list_status, $status )."</div></div>\n";
	
	echo "<div class='cell_captcha'><img src=\"".base_url()."captcha/php_captcha.php\"></div>\n";
	
	echo "<div class='row'><div class='cell_key'>".form_label("Verifikasi", "number")."</div>\n";
	echo "<div class='cell_val'>".form_input("number", "", "size=40 maxlength=20 id=number").form_error('number', '<div class="form_error" style="color:red">', '</div>')."</div></div>\n";
	
	
	echo "<div class='row'><div class='cell_key'></div><div class='cell_val'>".form_submit("submit", "Daftar")."</div>";
	echo form_close();
?>
</div>
