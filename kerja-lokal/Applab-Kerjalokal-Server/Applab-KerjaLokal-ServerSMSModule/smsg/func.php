<?php
function dtime_micro($a)
{
	if ($a == "") $a = date("Y-m-d H:i:s");
	$b = microtime();
	$b = substr($b,strpos($b,".")+1,8);
	return $a.".".$b;
}

function send_sms($mdn="0", $msg="", $tx_id, $shortcode="81820", $is_debug="0")
{
/*
	//$mdn = $_GET["mdn"];
	//print "<pre>";
	require_once "smpp.php";
	$tx=new SMPP('172.27.239.7',5019);
	//$tx->debug=true;
	$tx->system_type="WWW";
	$tx->addr_npi=1;
	//print "open status: ".$tx->state."\n<hr>";
	$tx->bindTransmitter("usahaku","usahak4")."<hr>";
	$tx->sms_source_addr_npi=1;
	//$tx->sms_source_addr_ton=1;
	$tx->sms_dest_addr_ton=1;
	$tx->sms_dest_addr_npi=1;
	print $tx->sendSMS("818","62".$mdn, $msg)."<hr>";
	$tx->close();
	unset($tx);
	//print "</pre>";
*/
	$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s");
	$dtime_micro = dtime_micro($dtime);
	
	//$msgid = time();
	$msgid = str_replace(" ", "||", $msg);
	
	$url = SMS_URL."msisdn=0$mdn&message=".urlencode($msg)."&shortcode=$shortcode&appsid=GRAMEEN&msgid=".urlencode($msgid); 
	
	$data = CORE_URL."add_logsmsg.php?tx_id=$tx_id&mdn=0$mdn&sms_type=MT&message=".urlencode($msg)."&dtime_add=".urlencode($dtime_micro);
	echo $data."<hr>";
	$data = get_data($data);
	
	if ($is_debug == "0") $a = file_get_contents($url); 
	return "<b>$url<br>MT: ".$msg."</b><hr>";
}

function db_input_filter($str)
{
	 if(get_magic_quotes_gpc()) 
	 {
		if(ini_get('magic_quotes_sybase')) 
		{
			$str = str_replace("''", "'", $str);			
		} 
		else 
		{
			$str = stripslashes($str);
		}
	} 

	$str = mysql_real_escape_string($str);
		
	return $str;
}


function get_data($data)
{
	$data = file_get_contents($data);
	return json_decode($data, TRUE);
    //return true;
}

function get_uuid($param="")
{
	$uuid = 
		//sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		sprintf( '%04x%04x%04x%04x%04x%04x%04x%04x',
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
	//return ($param == "") ? $uuid : $uuid."_".$param;
	$uuid = ($param == "") ? $uuid : $uuid."_".$param;
	$uuid = date("Ym").$uuid;
	return $uuid;
}

function write_logfile($mdn, $msg)
{
    $dtime_micro = dtime_micro();
	$str = $dtime_micro." >> $mdn $msg \r\n";
	
	$filepath = "logs/";
	$filename = date('Ymd');
    $fnamecheck = $filename.".txt";
	
    if(file_exists($filepath.$fnamecheck))
    {
		if (is_writable($filepath.$fnamecheck)) {
            if (!$handle = fopen($filepath.$fnamecheck, 'a')) {
                echo "0";//"Cannot open file ($fnamecheck)";
                exit;
            }
            if (fwrite($handle, $str) === FALSE) {
                 echo "0";//"Cannot write to file ($fnamecheck)";
                 exit;
            }
            //echo "Success, wrote ($logcontent) to file ($fnamecheck)";
  	        //echo "Write Log to File ".$fnamecheck." Successful";
            fclose($handle);
        } else {
            echo "0";//"The file $fnamecheck is not writable";
        }
	}
	else
	{
	    $int = file_put_contents($filepath.$fnamecheck, $str);
	}
}

?>
