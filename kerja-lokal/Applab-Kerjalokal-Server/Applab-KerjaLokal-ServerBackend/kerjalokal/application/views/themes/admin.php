<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>themes/admin/main.css" />
	{header}
</head>
<body style="margin:0px; padding:0px;">
	<div style="background-color:#0C0; position:relative; padding:10px;">
		<div style="padding:0px; display:table; width:100%;">
			<div style="display:table-row">
				<div style="width:70%; padding:5px; border:0px solid #000; color:#FFF; text-align:left; display:table-cell; font-size:12pt;">
					<b>KERJA LOKAL</b> | Administration Site
				</div>
				<div style="width:13%; padding:5px; border:0px solid #000; text-align:right; display:table-cell;">
					{username}
				</div>
				<div style="width:1%; padding:5px; border:0px solid #000; text-align:cengter; display:table-cell;">|</div>
   				<div style="width:10%; padding:5px; border:0px solid #000; text-align:center; display:table-cell;">My Account</div>
                <div style="width:1%; padding:5px; border:0px solid #000; text-align:center; display:table-cell;">|</div>                                
  <div style="width:5%; padding:5px; border:0px solid #000; text-align:right; display:table-cell;">
					<a href="<?php echo base_url(); ?>admin/logout">logout</a>
				</div>                                
		  </div>
		</div>
	</div>
	
<!--	<div style="background-color:#ddd; border:1px solid #000; padding:10px; border:0px solid #ddd;">
	{topmenu}
	</div> -->	
   	<div style="background-color:#0C0; border:1px solid #000; padding: 5px 0; border:0px solid #ddd; position:relative">
    {headmenu}
    </div>
	<br />
	<div style="display:table;">
		<div style="display:table-row;">
  			<!--<div style="display:table-cell; padding:5px; border:0px solid #000">
				{leftmenu}
			</div>--> 
			<div style="display:table-cell; padding:5px; width:100%; border:0px solid #000">
				<h2>{title}</h2>
				{msg}
				{content}
			</div>
		</div>
	</div>
	
	
</body>
</html>