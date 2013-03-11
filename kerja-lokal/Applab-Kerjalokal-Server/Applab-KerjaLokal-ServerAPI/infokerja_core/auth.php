<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/


if($_REQUEST['submitLogin']=='login')
{


//mysql conf and connect
require ("conf.php");



	$DATA = array();
	// Will hold our errors

	// Checking whether the Login form has been submitted
	if(!$_REQUEST['username'] || !$_REQUEST['userpin'])
		$DATA['result'] = 'FAILED. All the fields must be filled in!';

	if(!count($DATA))
	{
		$_REQUEST['username'] = mysql_real_escape_string($_REQUEST['username']);
		$_REQUEST['userpin'] = mysql_real_escape_string($_REQUEST['userpin']);
		//$_REQUEST['rememberMe'] = (int)$_REQUEST['rememberMe'];
		
		if ($_REQUEST['md5']==1) {
			$query = "SELECT subscriber_id,name,loc_id,pos_lat,pos_lng  FROM subscribers WHERE mdn='{$_REQUEST['username']}' AND MD5(pinweb)='".$_REQUEST['userpin']."'";
		} else {
			$query = "SELECT subscriber_id,name,loc_id,pos_lat,pos_lng  FROM subscribers WHERE mdn='{$_REQUEST['username']}' AND pinweb='".$_REQUEST['userpin']."'";
		}
		
		//echo $query;
		$result = mysql_query($query);

		
		$row = mysql_fetch_assoc($result);

		if($row['subscriber_id'])
		{
			$DATA['loginTime']=time();
			$DATA['subscriber_id']=$row['subscriber_id'];
			$DATA['name'] = $row['name'];
			$DATA['loc_id'] = $row['loc_id'];
			$DATA['pos_lat'] = $row['pos_lat'];
			$DATA['pos_lng'] = $row['pos_lng'];
			
			$DATA['result'] = "OK";
			

		}
		else $DATA['result']='FAILED. Wrong username and/or password! ';
	}


		// Save the error messages in the session
echo json_encode($DATA);
exit;	
}


$DATA = array();
$DATA['result']='FAILED!';
echo json_encode($DATA);

?>