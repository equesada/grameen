<?php
function str_clean($a, $is_num=0) {
	$a = trim($a);
	$a = urldecode($a);
	$a = mysql_real_escape_string($a);
	if ($is_num == 1) 
		if ($a == "0")
			$check_num = true;
		else
			$check_num = (is_numeric($a)) ? true : false;
	else
		$check_num = true;
	return ($check_num) ? $a : null;
}

function json_callback($json, $callback)
{
	if ($callback) {
		return $callback."($json);"; // somefunction({data here});
	} else {
		return json_encode($json);
	}
}
function output($val, $msg=0, $callback=0) {
	$output = "";
	if (!is_array($val))
	{
		switch (strtolower($val)) {
			case "api" : $val = "invalid API key"; break;
			case "numeric" : $val = "$msg must be numeric"; break;
			case "invalid" : $val = "$msg is invalid or empty"; break;
			case "empty" : $val = "no result"; break;
		}
		
		///// XML
		
		/*
		$xml_output = "<?xml version=\"1.0\"?>\n";
		$xml_output .= "<result>\n";
		if ($val == 1) {
			$xml_output .= "\t<status>1</status>\n"; 
			$xml_output .= "\t<msg></msg>\n";
			write_log(1);
		}
		else {
			$xml_output .= "\t<status>0</status>\n"; 
			$xml_output .= "\t<msg>$a</msg>\n";
			write_log("0-$a");
		}
		$xml_output .= "</result>";
		*/
		
		
		///// JSON
		
		if ($val == 1) {
			$output = '{"status":"1","msg":""}';
			//write_log(1);
		}
		else {
			$output = '{"status":"0","msg":"'.$val.'"}';
			//write_log("0-$a");
		}	
	}
	else
	{
		/* XML */
		
		/* JSON */
		$output = json_callback($val, $callback);
		
	}
	$output_log = str_replace("\"", "\\\"", $output);
	$tx_id = isset($_GET["tx_id"]) ? str_clean($_GET["tx_id"]) : get_uuid();
	write_log($output_log, $tx_id);
	return $output;
}


function write_log($output, $tx_id) {
	$sql = "SELECT * FROM country WHERE is_current=\"1\"";
	$sql = mysql_query($sql);
	$r = mysql_fetch_assoc($sql);
	$curr_country = $r["country_id"];
	$src = substr($tx_id,strpos($tx_id,"_")+1);
	if (strpos($tx_id,"_") == true)
	{
		$page_title = substr($tx_id,strpos($tx_id,"_")+1);
		$tx_id = substr($tx_id,0,strpos($tx_id,"_"));
	}
	else
		$page_title = "";

	//$tx_id = substr($tx_id,0,strpos($tx_id,"_"));
	$sql = "INSERT INTO log_web (tx_id, date_add, src, filename, param, response, country_id) VALUES (\"$tx_id\", \"".date("Y-m-d H:i:s")."\", 
		\"$src\", \"".$_SERVER["SCRIPT_NAME"]."\", \"".$_SERVER["QUERY_STRING"]."\", \"$output\", \"$curr_country\")";
	//die($sql);
	$sql = mysql_query($sql);
	
}


function getPagingQuery($sql, $rowPerPage, $page, $order="", $ascdesc="ASC")
{
	if ($order != "") $sql = $sql." ORDER BY $order $ascdesc";
	if ($rowPerPage > 0)
	{
		if ($page > 0) $offset = ($page - 1) * $rowPerPage;
		$sql = $sql." LIMIT $offset, $rowPerPage";
	}
	return $sql;
}


function array_find($needle, $haystack, $search_keys = false, $keys_to_search="") 
{
	if(!is_array($haystack)) return false;
	foreach($haystack as $key=>$value) {
		$what = ($search_keys) ? $key : $value;
		if(strpos($what, $needle)!==false) 
		{
			if ($keys_to_search!="")
				if (strpos($keys_to_search, $key)!==false) return $key;
		}
	}
	return false;
}


function get_uuid()
{
	//return
		//sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		$a = sprintf( '%04x%04x%04x%04x%04x%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		// 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,

		// 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
	$a = date("Ym").$a;
	return $a;
}



function getPagingLink($sql, $rowPerPage, $page)
{
	$r = mysql_query($sql);
	$numRows = mysql_num_rows($r);
	$maxPage = ceil($numRows / $rowPerPage);
	$pagingLink = "";
	$numLinks      = 5;
	if($maxPage > 1)
	{
		if($page < $maxPage)
		{
			$nextPage = $page + 1;
			$next = " <div id=\"page$nextPage\" class=\"wordPage activePage\">[Next]</div> ";
			$last = " <div id=\"page$maxPage\" class=\"wordPage activePage\" >[Last]</div> ";
		}
		else
		{
			$last = '';
			$next = '';
		}
		if ($page > 1)
		{
			$prevPage = $page - 1;
			if ($prevPage > 1)
			{
				$prev = " <div class=\"wordPage activePage\" id=\"page$prevPage\">[Prev]</div> ";
			} else
			{
				$prev = " <div class=\"wordPage activePage\" id=\"page1\">[Prev]</div> ";
			}

			$first = " <div class=\"wordPage activePage\" id=\"page1\">[First]</div> ";
		} else
		{
			$prev  = ''; // we're on page one, don't show 'previous' link
			$first = ''; // nor 'first page' link
		}
		$starts = $page - ($page % $numLinks) + 1;
		$ends = $starts + $numLinks -1;
		$ends = min($maxPage, $ends);
		$pagingLink = array();
		for($i=$starts;$i <= $ends; $i++)
		{
			if($i == $page)
			{
			  $pagingLink[] = "<div id=\"page$i\" class=\"numberPage\" style=\"color:#000\">$i</div>";
			}
			else{
			   if($i == 1)
				{
					$pagingLink[] = " <div class=\"numberPage activePage\" id=\"page$i\">$i</div> ";
				}
				else
				{
					$pagingLink[] = " <div id=\"page$i\" class=\"numberPage activePage\">$i</div> ";
				}
			}

		}
		$pagingLink = implode(' <div style="float:left;margin:3px 5px 0px 5px">|</div> ', $pagingLink);

		// return the page navigation link
		$pagingLink = $first . $prev . $pagingLink . $next . $last;
	}
	return $pagingLink;
}




?>