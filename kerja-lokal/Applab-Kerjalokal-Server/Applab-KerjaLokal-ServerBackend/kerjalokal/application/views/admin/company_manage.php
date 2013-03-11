<?php 	

	$card_type = '';
	for ($i=1; $i <= $this->uri->total_segments(); $i++) {
		//if ($this->uri->segment($i) == 'null' && in_array($this->uri->segment($i+1), array('AS', 'KH', 'SP')))
		$card_type = $this->uri->segment($i+1);
	}
	//echo "<pre>"; print_r($results); echo "</pre>";
	$filter = "";
	foreach($search as $key => $val) { 
		$filter .= (empty($val)) ? '_/' : $val ."/";
	}
	
?>
<script type="text/javascript">
function result_row_default(a) {
	
	var urlpath = '<?php echo base_url(); ?>admin/company/manage/<?php echo $page = 1; ?>/<?php echo $order; ?>/'+a+'/<?php echo $filter; ?>';
	//alert(urlpath);
	$(location).attr('href', urlpath);	
}
</script>
<div class="divClearBoth">
	<?php echo $search_form; ?>
	<!--a href="<?php echo $search_link; ?>" id="search_form" rel="simpleDialog">Search</a-->
</div>
<div style="display:table; width:100%; clear:both">
<div class="row">
<?php
	echo form_open($form_submit);
?>
</div>
</div>

<div class="table" style="width:50%">
<div class="row">
<div class="cell" style="width:15%">
	<a href="<?php echo base_url()."admin/company/save_csv/$order/$filter";?>">Save as CSV</a>
</div>
<div class="cell" style="width:25%">Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
?>
</div>
<div class="cell" style="width:60%">
<?php 
if(!empty($nopage)):
echo "<ul id=\"pagination-flickr\">";
//echo $nopage;
if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/company/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
$showPage="";
	$page = 1;
for($page = 1; $page <= $npage; $page++)
{
	if ((($page >= $nopage - 3) && ($page <= $nopage + 3)) || ($page == 1) || ($page == $npage))
	{
		if (($showPage == 1) && ($page != 2))  echo "<li class=\"active2\">...</li>";
		if (($showPage != ($npage - 1)) && ($page == $npage))   echo "<li class=\"active2\">...</li>";
		if ($page == $nopage)
			echo "<li class=\"active\">".$page."</li>";
		else 
			echo "<li><a href=\"".base_url()."admin/company/manage/$page/$order/$default_row/$filter\">$page</a></li>";
		$showPage = $page;
	}
}
if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/company/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
echo "</ul>";
endif;
?>
</div>
</div>
</div>

<div class="table">
<div class="row">
    <div class="cell table_head" style="width:50px"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_comp_id/$default_row/$filter"; ?>">ID</a></div>	
	<div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_status/$default_row/$filter"; ?>">Status</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_username/$default_row/$filter"; ?>">Created By</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_date_add/$default_row/$filter"; ?>">Create Date</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_name/$default_row/$filter"; ?>">Company Name</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_industry_title/$default_row/$filter"; ?>">Industry</a></div>    
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_active/$default_row/$filter"; ?>">Active Job Post</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_inactive/$default_row/$filter"; ?>">Inactive Job Post</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_total/$default_row/$filter"; ?>">Total Job Post</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_province_name/$default_row/$filter"; ?>">Province</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_kotamadya_name/$default_row/$filter"; ?>">Kotamadya</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_kecamatan_name/$default_row/$filter"; ?>">Kecamatan</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_loc_title/$default_row/$filter"; ?>">Kelurahan</a></div>
	<div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_cp/$default_row/$filter"; ?>">Contact Person</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_tel/$default_row/$filter"; ?>">Telepon</a></div>
    <div class="cell table_head"><a href="<?php echo base_url()."admin/company/manage/$nopage/$next_order"."_email/$default_row/$filter"; ?>">Email</a></div>
    <div class="cell table_head">Manage</div>
	
</div>
<?php
	$a = "";
	foreach ($results as $company)
	{
	  if (!empty($company['comp_id'])) {
		echo "<div class=\"row\">\n";
		//echo "<div class=\"cell table_cell\">".form_checkbox("del[]",$company["comp_id"], ($company["status"]=="2") ? TRUE : "")." ".$company["comp_id"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["comp_id"]."</div>\n";
		
		if ($company["status"] == "1")
			echo "<div class=\"cell table_cell\">Active</div>\n";
		else
			echo "<div class=\"cell table_cell\">Inactive</div>\n";
			
		echo "<div class=\"cell table_cell\">".$company["username"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["date_add"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/company/edit/".$company["comp_id"]."\">". $company["company_name"]."</a></div>\n";
		echo "<div class=\"cell table_cell\">".$company["industry_title"]."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/manage/1/d_job_id/20/1/_/_/".$company["comp_id"]."/_/_/_/_/_/_/\">".$company["active"]."</a></div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/manage/1/d_job_id/20/2/_/_/".$company["comp_id"]."/_/_/_/_/_/_/\">".$company["inactive"]."</a></div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/jobpost/manage/1/d_job_id/20/_/_/_/".$company["comp_id"]."/_/_/_/_/_/_/\">".$company["total"]."</a></div>\n";
		
		echo "<div class=\"cell table_cell\">".$company["province_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["kotamadya_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["kecamatan_name"]."</div>\n";
		echo "<div class=\"cell table_cell\">".str_replace('.',', ',$company["loc_title"])."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["cp"]."</div>\n";
		echo "<div class=\"cell table_cell\">".$company["tel"]."</div>\n";
		$posat=strpos($company["email"], "@");
		if($posat>8) 
			$imel=substr_replace($company["email"], '<br>@', $posat,1);
		else
			$imel=$company["email"];
		echo "<div class=\"cell table_cell\">".$imel."</div>\n";
		echo "<div class=\"cell table_cell\"><a href=\"".base_url()."admin/company/edit/".$company["comp_id"]."\">Edit</a></div>\n";
		
		echo "</div>\n";
		$a .= $company["comp_id"].",";
	  }
	}
	$a = substr($a, 0, strlen($a)-1);
	
	echo "</div>"; // CLOSE TABLE
	//echo form_hidden("all_id", $a);	
	//$paging = "";
?>
<div class="table" style="width:50%">
<div class="row">
<div class="cell" style="width:15%">
	<a href="<?php echo base_url()."admin/company/save_csv/$order/$filter";?>">Save as CSV</a>
</div>
<div class="cell" style="width:25%">Result/Pages 
<?php 
	$js = 'id="result_row" onChange="result_row_default(this.value);"';
	echo form_dropdown('result_row',$result_row, $default_row, $js);
	echo form_close();
	$page = 1;
?>
</div>
<div class="cell" style="width:60%">
<?php	
echo "<ul id=\"pagination-flickr\">";
	if ($nopage > 1) echo "<li class=\"previous\"><a href=\"".base_url()."admin/company/manage/".($nopage - 1)."/$order/$default_row/$filter\">&lt;&lt; Previous</a></li>";
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
				echo "<li><a href=\"".base_url()."admin/company/manage/$page/$order/$default_row/$filter\">$page</a></li>";
			$showPage = $page;
		}
	}
	if ($nopage < $npage) echo "<li class=\"next\"><a href=\"".base_url()."admin/company/manage/".($nopage + 1)."/$order/$default_row/$filter\">Next &gt;&gt;</a></li>";
	echo "</ul>";	
	
?>
</div>
</div>