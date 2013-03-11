<?php
	$list_industry[0] = "ALL";
	foreach ($industry["results"] as $a)
	{ $list_industry[$a["industry_id"]] = $a["industry_title"]; }

?>
<html>
<head>
<script src="<?php echo base_url(); ?>js/date.js"></script>
<script src="<?php echo base_url(); ?>js/jquery.datePicker.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/datePicker.css">
<script>
	$(document).ready(function(){
		
		Date.firstDayOfWeek = 1;
		Date.format = 'yyyy/mm/dd';
		$('.date-pick')
		.datePicker({inline:true})
		.bind(
			'dateSelected',
			function(e, selectedDate, $td)
			{
				console.log('You selected ' + selectedDate);
			}
		);
		
	});
</script>
</head>
<body>
<form action="<?php echo base_url(); ?>admin/company/manage" method="post">
<div style="display:table; width:100%;">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Company Name</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="name" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Industry</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("industry_id", $list_industry); ?></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Date Add</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_add" size="12" maxlength="50" class="date-pick"></div>
</div>
<input type="hidden" name="status" value="<?php echo $status; ?>">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;"></div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="submit" name="submit" value="Submit"></div>
</div>
</div>
<center><a href=# class=close>close</a></center></div>
</body>
</html>