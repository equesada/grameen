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
/*
if (is_array($_SESSION)) {
	echo "Session global";
}else {
	echo "No Session global";
}
*/
	// Checking whether the Login form has been submitted

	$err = array();
	// Will hold our errors

	if(!$_REQUEST['username'] || !$_REQUEST['userpin'])
		$err[] = 'All the fields must be filled in!';

	if(!count($err))
	{
		$_REQUEST['username'] = mysql_real_escape_string($_REQUEST['username']);
		$_REQUEST['userpin'] = mysql_real_escape_string($_REQUEST['userpin']);
		//$_REQUEST['rememberMe'] = (int)$_REQUEST['rememberMe'];


		$url = $CORE_URL."auth.php?username=".$_REQUEST['username']."&userpin=".$_REQUEST['userpin']."&submitLogin=login";
		
		  $ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CURL_TIMEOUT);
$jsonData = curl_exec($ch);
$data = json_decode($jsonData, TRUE);


		if(trim($data['result']) == "OK")
		{
			// If everything is OK login
//			echo $query;
//			print_r($row);
			$_SESSION['loginTime']=time();
			$_SESSION['subscriber_id']=$data['subscriber_id'];
			$_SESSION['name'] = $data['name'];
			$_SESSION['loc_id'] = $data['loc_id'];
			$_SESSION['pos_lat'] = $data['pos_lat'];
			$_SESSION['pos_lng'] = $data['pos_lng'];
			
//echo $_SESSION['loginTime']."sub id:".$_SESSION['subscriber_id'];
			// Store some data in the session

			//setcookie('tzRemember',$_REQUEST['rememberMe']);
			// We create the tzRemember cookie
		}
		else $err[]='Wrong username and/or password!';
	}

	if($err)
		$_SESSION['msg']['login-err'] = implode('<br />',$err);
		// Save the error messages in the session

	header("Location: request.php");
	exit;
} else {
 
 ?>

	<div data-role="page" id="page7">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                

                <h3 style="margin-left:14px">
                    Login
                </h3>
            </div>
			<form action="request.php" method=post>
            <div data-role="content">
                <div data-role="fieldcontain">
				<?=$_SESSION['msg']['login-err']?>
                    <fieldset data-role="controlgroup">
					<input type="hidden" name="act" value="applogin">
                        <label for="textinput1">
                            Login
                        </label>
                        <input placeholder="Please type your BTEL Number i.e 021xxxxxx" name="username" id="textinput1" value="" type="text" />
						
                        <label for="textinput2">
                            Password
                        </label>
                        <input name="userpin" id="textinput2" placeholder="Please type your PIN Number" value="" type="password" />
  
				<input type="hidden" name="submitLogin" value="login"  />
				<input type="submit" name="submit" value="Login"  />
				              </fieldset>
                </div>
				<table width=100%><tr><td align=center><table><tr><td>
				<b>Demo User:</b><br/>
				Jakarta User: username: 021100 password: 100<br>
				Bogor User: username: 021200 password: 200<br>
				Depok User: username: 021300 password: 300<br>
				Tangerang User: username: 021400 password: 400<br>
				Bekasi User: username: 021500 password: 500<br>
				
				</td></tr></table></td></tr></table>
				<br>
                    New User? Please register first.<br/>
                <a data-role="button" data-transition="fade" href="request.php?act=appregister">
                    Register
                </a>
				
            </div>
			</form>
        </div>



		
		
		
		
		
		
<!--DUMMY FOR REGISTERATION -->		
        <div data-role="page" id="page62">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                
                <h3 style="margin-left:14px">
                    Registration
                </h3>
            </div>
			<form action="request.php?act=appregister" method=post>
            <div data-role="content">
                <div data-role="fieldcontain">
 <h3>Thank you for registering KerjaLokal service. We will notify you soon to your mobile number</h3>
 <br/>
 <br/>
 
 (Underconstruction)
            </div>
			</form>
		</div>
</div>
 
		
		
<?
}
?>


