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
<form action="<?php echo base_url(); ?>admin/jobseeker/manage" method="post">
<div style="display:table; width:100%;">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Subscriber name</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="subscriber_name" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Gender</div>
<div class="cell table_cell" style="border:0px; text-align:left;">
	<select name="gender">
	<option value="M">Male</option>
	<option value="F">Female</option>
	<option value="_">Male or Female</option>
	</select>
</div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Location</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="loc_name" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Salary</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="salary" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Date Register</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_add" size="40" maxlength="50" class="date-pick"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;"></div>
<input type="hidden" name="status" value="<?php echo $status; ?>">
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="submit" name="submit" value="Submit"></div>
</div>
</div>
<center><a href=# class=close>close</a></center></div>
</body>
</html>