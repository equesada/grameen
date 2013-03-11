<?php
	$list_jobcat[0] = "ALL";
	foreach ($jobcats["results"] as $a)
	{ $list_jobcat[$a["jobcat_id"]] = $a["jobcat_title"]; }

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
		/*
			.datePicker({
				inline:true,
				startDate: '01/04/2011',
				endDate: (new Date()).asString()
			});
			.bind({
				'dateSelected',
				function(e, selectedDate, $td)
				{
					alert('You selected ' + selectedDate);
				}
			});
		*/
	});
</script>
</head>
<body>
<form action="<?php echo base_url(); ?>admin/jobpost/manage" method="post">
<div style="display:table; width:100%;">
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Company</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="company" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Category</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><?php echo form_dropdown("jobcat_id", $list_jobcat); ?></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Post Date</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="date_add" size="12" maxlength="50" class="date-pick"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Location</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="loc_title" size="40" maxlength="100"></div>
</div>
<div class="row">
<div class="cell table_cell" style="border:0px; text-align:right;">Salary</div>
<div class="cell table_cell" style="border:0px; text-align:left;"><input type="text" name="salary" size="40" maxlength="100"></div>
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