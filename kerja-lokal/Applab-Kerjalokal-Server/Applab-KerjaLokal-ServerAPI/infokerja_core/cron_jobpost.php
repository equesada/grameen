<?
require_once("conf.php");
require_once("func.php");

$tx_id = get_uuid("sms");

echo "<pre>";
$id = isSet($_GET["id"]) ? $_GET["id"] : 0;
$dtime = isSet($_GET["dtime"]) ? ($_GET["dtime"]." ".date("H:i:s")) : date("Y-m-d H:i:s", time() + ($id * 86400));
$curr_date = date("Y-m-d", strtotime($dtime));
$date_expired = date("Y-m-d", (strtotime($dtime) + (7 * 86400)));
$next_date = date("Y-m-d", (strtotime($dtime) + 86400));
$next_date_expired = date("Y-m-d", (strtotime($next_date) + (7 * 86400)));

echo "dtime = ".$dtime."<br>";
echo "curr_date = ".$curr_date."<br>";
echo "next_date = ".$next_date."<br>";
echo "date_expired = ".$date_expired."<hr>";

$min_date_expired = strtotime ( '-1 day' , strtotime ( $date_expired ) ) ;
$min_date_expired = date ( 'Y-m-j' , $min_date_expired );
echo $min_date_expired."<hr>";

$sql = "SELECT * FROM $t_jobs";
//echo $sql;
$sql = mysql_query($sql) OR die(output(mysql_error()));
$temp = array();
$arr['nfields'] = mysql_num_fields($sql);
while($row = mysql_fetch_assoc($sql))
{
	for($j=0;$j<$arr['nfields'];$j++)
	{
		$val[mysql_field_name($sql,$j)] = $row[mysql_field_name($sql,$j)];
	}
	//array_push($arr["results"], $val);
	array_push($temp, $val);
}
foreach ($temp as $job)
{
	$date_expired = $job["date_expired"];
	
	if($date_expired < $curr_date){
		//echo $date_expired.'&nbsp;&nbsp; Status : &nbsp;&nbsp;'.$job["status"]."<br>";
		
		$sql = "UPDATE jobs SET status = '2' WHERE date_expired='$date_expired' AND job_id = ".$job["job_id"]."";
		//echo $sql."<hr>";				
		$sql = mysql_query($sql) OR die(output(mysql_error()));
	}
}
?>