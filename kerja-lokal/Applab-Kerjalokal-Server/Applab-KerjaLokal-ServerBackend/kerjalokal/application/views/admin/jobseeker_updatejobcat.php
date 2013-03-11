<div style="display:table; width:100%;">
<?php
	echo form_open('admin/jobseeker/updatejob');
	echo "<div class='row'><div class='cell_key'>".form_label('Find Subscriber Number', 'msdn')."</div>\n";
	echo "<div class='cell_val'>".form_input("mdn", $mdn);
	echo form_submit('submit','Go');
	echo form_close();
	echo "</div></div>";
	echo "<div class='row'>";
	echo "<div id='msg'></div>";
	echo "</div>";
	echo "<div class='row'>";
		echo "<div class=\"demo\">";
			echo "<div id=\"tabs\">";
			echo "<ul>
					<li><a href=\"#tabs-1\" onClick=\"blank_detail();\">ACTIVE SUBSCRIPTION</a></li>
					<li><a href=\"#tabs-2\" onClick=\"blank_detail();\">HISTORY</a></li>
				</ul>";
				
			echo "<div id=\"tabs-1\">";
			
				echo "<div style=\"display:table;\">";
				echo "<div class=\"row\">";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_date_add/$mdn \">Date Start</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_date_expired/$mdn \">Date Expired</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_jobcat_title/$mdn \">Job Category</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_status/$mdn \">Status</a></div>";
				echo "<div class=\"cell table_head\"></div>";
				echo "</div>";
				$status = "";
				foreach ($active_subscription["results"] as $jobcat)
				{
					if($jobcat["status"]=='1') $status = "Active <span style=\"cursor:pointer; color:#00f;\" onclick=\"unsub2(".$jobcat["rel_id"].");\">UNREG</span> ";
					elseif($jobcat["status"]=='2') $status = "Unreg by user on ". $jobcat['date_update'];
					elseif($jobcat["status"]=='3') $status = "Unreg by ".$jobcat['username']." on ".$jobcat["date_update"];
					
					echo "<div class=\"row\">";
					echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_add"])."</div>";
					echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_expired"])."</div>";
					echo "<div class=\"cell table_cell\">".$jobcat["jobcat_title"]."</div>";
					echo "<div class=\"cell table_cell\">".$status."</div>";
					echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"get_jobcat_detail(".$jobcat["rel_id"].");\">View Job Post</span></div>";		
					echo "</div>";
				}
				
				echo "</div>"; // CLOSE TABLE
				echo "<br />";
				echo "<div class=\"row\">";
				echo "<div id=\"jobcat_detail\"></div>";
				echo "</div>";	

			echo "</div>";
			
			echo "<div id=\"tabs-2\">";
				echo "<div style=\"display:table;\">";
				echo "<div class=\"row\">";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_date_add/$mdn \">Date Start</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_date_expired/$mdn \">Date Expired</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_jobcat_title/$mdn \">Job Category</a></div>";
				echo "<div class=\"cell table_head\">
				<a href=\"".base_url()."admin/jobseeker/updatejob/$page/$next_order"."_status/$mdn \">Status</a></div>";
				echo "<div class=\"cell table_head\"></div>";
				echo "</div>";
				$status = "";
				foreach ($history["results"] as $jobcat)
				{
					if($jobcat["status"]=='2') $status = "Unreg by user on ". $jobcat['date_update'];
					elseif($jobcat["status"]=='3') $status = "Unreg by ".$jobcat['username']." on ".$jobcat["date_update"];
					elseif($jobcat["status"]=='4') $status = "Succesfully Renew";
					elseif($jobcat["status"]=='5') $status = "Insufficient balance";
					
					echo "<div class=\"row\">";
					echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_add"])."</div>";
					echo "<div class=\"cell table_cell\">".str_replace("-","/",$jobcat["date_expired"])."</div>";
					echo "<div class=\"cell table_cell\">".$jobcat["jobcat_title"]."</div>";
					echo "<div class=\"cell table_cell\">".$status."</div>";
					echo "<div class=\"cell table_cell\"><span style=\"cursor:pointer; color:#00f;\" onclick=\"get_jobcat_detail2(".$jobcat["rel_id"].");\">View Job Post</span></div>";			
					echo "</div>";
				}
				echo "</div>"; // CLOSE TABLE
				echo "<br />";
				echo "<div class=\"row\">";
				echo "<div id=\"jobcat_detail2\"></div>";
				echo "</div>";	
		echo "</div>";
	
	echo "</div></div>";	
	?>
</div>