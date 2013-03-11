<div style="display:table; width:100%;">
<?php
	switch($gender) {
		case "0" : $gender = "--"; break;
		case "M" : $gender = "Male"; break;
		case "F" : $gender = "Female"; break;
	};
	
	switch($status) {
		case "0" : $status = "--"; break;
		case "1" : $status = "Active"; break;
		case "2" : $status = "Inactive"; break;
	};
	//echo "<pre>"; print_r($_SESSION); echo "</pre>";
	
	echo "<h3>User Information</h3>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Full Name', 'name')."</div>\n";
	echo "<div class='cell_val'>$name</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Status', 'status')."</div>\n";
	echo "<div class='cell_val'>$status</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Gender', 'gender')."</div>\n";
	echo "<div class='cell_val'>$gender</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('ID Card Number', 'idcard')."</div>\n";
	echo "<div class='cell_val'>$idcard</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Place of birth', 'place_birth')."</div>\n";
	echo "<div class='cell_val'>$place_birth</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Date of birth', 'birthday')."</div>\n";
	echo "<div class='cell_val'>$birthday</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Phone', 'mdn')."</div>\n";
	echo "<div class='cell_val'>$mdn</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('Address', 'address1')."</div>\n";
	echo "<div class='cell_val'>$address1";
	echo $address2 != "" ? "<br>$address2" : "";
	echo "</div></div>\n";
	echo "<div class='row'><div class='cell_key'>".form_label('RT', 'rt')."</div>\n";
	echo "<div class='cell_val'>$rt <img src=\"".base_url()."images/shim.gif\" width=20 height=1>".form_label('RW', 'rw')."<img src=\"".base_url()."images/shim.gif\" width=10 height=1>$rw</div>";
	echo "</div>\n";
	
	
?>
</div>
<hr />
The password for this user has been changed to <span style="font-weight: bold"><?php echo $newpassword;?></span>
<br /><br />
<a href="<?php echo $_SESSION['back_url'] ?>">Back to Manage Jobseeker</a>
