<div style="display:table; width:100%;">
	<div class="row">
	<?php 
		$filter = "";
		echo "<div class=\"cell table_cell\" style=\"border:0px\">".form_open($form_submit);
		/*
		echo "Filter by ".form_dropdown("status", $list_status , $status)." "; 
		echo form_hidden("subscriber_name", $search["subscriber_name"]);
		echo form_hidden("gender", $search["gender"]);
		echo form_hidden("loc_name", $search["loc_name"]);
		echo form_hidden("salary", $search["salary"]);
		echo form_hidden("date_add", $search["date_add"]);
		echo form_submit("submit", "Submit");
		echo form_close()."</div>";
		echo "<div class=\"cell table_cell\" style=\"border:0px; text-align:right\"><a href=\"$search_link\" id=\"search_form\" rel=\"simpleDialog\">Search</a></div>";
		*/
		//print_r($search);
		
		foreach($search as $key => $val)
		{ $filter .= ($val != "") ? $val."/" : "_/"; }
		
		/*
		$filter = "";
			foreach($search as $key => $val) { 
			$filter .= (empty($val)) ? 'null/' : $val ."/";
		}
		*/
		//echo form_open($form_submit);
	?>
	</div>
</div>
<?
//echo $_SESSION["userlevel"]."<hr>";
$useraccess = $_SESSION["userlevel"];
?>
<script type="text/javascript">
function result_row_default(a) {
	
	var urlpath = '<?php echo base_url(); ?>admin/jobseeker/manage/1/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	$(location).attr('href', urlpath);	
}
</script>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
</div>
<?
echo "Export to &nbsp;&nbsp;<a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">CSV</a>";
?>
<div style="display:table; width:100%; clear:both">
<div class="row">
<?php
	echo form_open($form_submit);
?>
</div>
</div>
<?php 
if(!empty($nopage)):
echo "<ul id=\"pagination-flickr\">";
if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
$showPage="";
for($page = 1; $page <= $npage; $page++)
{
	if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
	{
		if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
		if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
		if ($page == $nopage)
			echo "<li class=\"active\">".$page."</li>";
		else 
			echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
echo "</ul>";
endif;
?>
<div>Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>
<?php 
/* DEFAULT LAMA, TIDAK BISA DIGUNAKAN UNTUK SORTINGNYA
	//echo "<a href=\"$search_link; \" id=\"search_form\" rel=\"simpleDialog\">Search</a>
	echo "<div class=\"divClearBoth\" style=\"border:1px solid #000\">$search_form</div>";
	echo "<div style=\"display:table;\"><div style=\"display:table-row\"><div style=\"display:table-cell; width:50%\">"; 
	echo "<a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">Export to CSV</a>";
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1> Result/Pages "; 

	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	//echo form_open($form_submit); 
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1>";
	//echo form_close();
	$page = 1;
	if(!empty($nopage)):
		echo "</div><div style=\"display:table-cell; width:50%;\">";
		echo "<ul id=\"pagination-flickr\" style=\"padding-top:10px;\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;Prev</a></li>";
		$showPage="";
		for($page = 1; $page <= $npage; $page++)
		{
			if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
			{
				if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
				if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
				if ($page == $nopage)
					echo "<li class=\"active\">".$page."</li>";
				else 
					echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
				$showPage = $page;
			}
		}
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next&gt;</a></li>";
		echo "</ul>";
		echo "</div></div></div>";
	endif;
*/	
?>

<div class="table">
<div class="row">
	<div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_id/$default_row/$filter"; ?>">ID</a></div>
	
	<? if($useraccess != "company"){ ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_status/$default_row/$filter"; ?>">Status</a></div>
    <? } ?>
	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_name/$default_row/$filter"; ?>">Name</a></div>
	<? if($useraccess != "company"){ ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_gender/$default_row/$filter"; ?>">Gender</a></div>
    <? } ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_mdn/$default_row/$filter"; ?>">MDN</a></div>
    <? if($useraccess != "company"){ ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_edu_title/$default_row/$filter"; ?>">Education</a></div>	
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_province_name/$default_row/$filter"; ?>">Province</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_kotamadya_name/$default_row/$filter"; ?>">Kotamadya</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_kecamatan_name/$default_row/$filter"; ?>">Kecamatan</a></div>
    <? } ?>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_loc_title/$default_row/$filter"; ?>">Kelurahan</a></div>
	<? if($useraccess != "company"){ ?>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_subscriber_name/$default_row/$filter"; ?>">Mentor</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_birthday/$default_row/$filter"; ?>">Date of Birth</a></div>	
    <div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_n_active_sub/$default_row/$filter"; ?>"># Active Subscription</a></div>	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_date_add/$default_row/$filter"; ?>">Register Date</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/jobseeker/manage/$page/$next_order"."_date_update/$default_row/$filter"; ?>">Last Activity Date</a></div>
    <div class="cell table_head">Manage</div>
	<? } ?>
</div>
<?php
	//echo "<pre>"; print_r($subscribers); echo "</pre>";
	$a = "";
	foreach ($results as $subscriber)
	{
		if (!empty($subscriber['subscriber_id'])) {
			//echo "<pre>"; print_r($results); echo "</pre>";
			echo "<div class=\"row\">\n";
			//echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$subscriber["subscriber_id"],($subscriber["status"]=="2") ? TRUE : "")." ".$subscriber["subscriber_id"]."</div>\n";
			echo "<div class=\"cell table_cell\">".$subscriber["subscriber_id"]."</div>\n";
			
			if($useraccess != "company"){	//check apakan user access bukan company
				if ($subscriber["status"] == "1")
					echo "<div class=\"cell table_cell\">Active</div>\n";
				else
					echo "<div class=\"cell table_cell\">Inactive</div>\n";
			}	
			
			echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/edit/".$subscriber["subscriber_id"]."\">". $subscriber["subscriber_name"]."</a></div>\n";
			
			if($useraccess != "company"){	//check apakan user access bukan company
				if ($subscriber["gender"] == "F")
					echo "<div class=\"cell table_cell\">Female</div>\n";
				else
					echo "<div class=\"cell table_cell\">Male</div>\n";
			}
			echo "<div class=\"cell table_cell\">".$subscriber["mdn"]."</div>\n";			
			if($useraccess != "company"){	//check apakan user access bukan company
				echo "<div class=\"cell table_cell\">".$subscriber["edu_title"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["province_name"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["kotamadya_name"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["kecamatan_name"]."</div>\n";
			}
			echo "<div class=\"cell table_cell\">".$subscriber["loc_title"]."</div>\n";
			if($useraccess != "company"){	//check apakan user access bukan company
				echo "<div class=\"cell table_cell\">".$subscriber["subscriber_name"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["birthday"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["n_active_sub"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["subscriber_date_add"]."</div>\n";
				echo "<div class=\"cell table_cell\">".$subscriber["subscriber_date_update"]."</div>\n";
				echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobseeker/view/".$subscriber["subscriber_id"]."\">Profile</a><br /> 
					<a href=\"".base_url()."admin/jobseeker/view/".$subscriber["subscriber_id"]."\">Status</a><br />
					<a href=\"".base_url()."admin/subscription/manage/1/a_jobcat_title/0/0///".$subscriber["mdn"]."\">Subscription</a><br />
					<a href=\"".base_url()."admin/jobseeker/resetpass/".$subscriber["subscriber_id"]."\" onclick=\"return confirm('Are you sure you want to reset the password for this user (".$subscriber["mdn"]." - ".$subscriber["subscriber_name"].")?')\">Reset&nbsp;Password</a></div>\n";
					
			}
			echo "</div>\n";
			$a .= $subscriber["subscriber_id"].",";
		}
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE	
	echo form_hidden("all_id", $a);
	
	
	
?>
</div>
<?
echo "Export to &nbsp;&nbsp;<a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">CSV</a>";
?>
<div style="display:table; width:100%; clear:both">
<div class="row">
<?php
	echo form_open($form_submit);
?>
</div>
</div>
<?php 
if(!empty($nopage)):
echo "<ul id=\"pagination-flickr\">";
if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
$showPage="";
for($page = 1; $page <= $npage; $page++)
{
	if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
	{
		if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
		if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
		if ($page == $nopage)
			echo "<li class=\"active\">".$page."</li>";
		else 
			echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
echo "</ul>";
endif;
?>
<?php 
/*
	//echo "<a href=\"$search_link; \" id=\"search_form\" rel=\"simpleDialog\">Search</a>
	echo "<div style=\"display:table;\"><div style=\"display:table-row\"><div style=\"display:table-cell; width:50%\">"; 
	echo "<a href=\"".base_url()."admin/jobseeker/save_csv/$order/$filter\">Export to CSV</a>";
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1> Result/Pages "; 

	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	//echo form_open($form_submit); 
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo "<img src=\"".base_url()."images/shim.gif\" width=30 height=1>";
	//echo form_close();
	$page = 1;
	if(!empty($nopage)):
		echo "</div><div style=\"display:table-cell; width:50%;\">";
		echo "<ul id=\"pagination-flickr\" style=\"padding-top:10px;\">";
		if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;Prev</a></li>";
		$showPage="";
		for($page = 1; $page <= $npage; $page++)
		{
			if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
			{
				if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
				if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
				if ($page == $nopage)
					echo "<li class=\"active\">".$page."</li>";
				else 
					echo "<li><a href=\"".base_url()."admin/jobseeker/manage/$page/$order/$default_row/$filter\">$page</a></li>";
				$showPage = $page;
			}
		}
		if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/jobseeker/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next&gt;</a></li>";
		echo "</ul>";
		echo "</div></div></div>";
	endif;
*/	
?>

