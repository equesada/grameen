<?
/*
This code is owned by Grameen Applab Indonesia

Copying and re-using the code is prohibited without permission from  Grameen Applab Indonesia

-April 2012-
-Ramot Lubis-

*/

session_start();

$subscriber_id = $_SESSION['subscriber_id'];
$loginTime = $_SESSION['loginTime'];



//check login
if (isset($subscriber_id) && isset($loginTime)) {

?>
	  
	  <div data-role="page" id="page2">
            <div data-theme="a" data-role="header" align="left">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>                
				<h3 style="margin-left:14px">
                    <?=$LANG['home_title']?>
                </h3>
            </div>
            <div data-role="content">
                <ul data-role="listview" data-divider-theme="b" data-inset="false">
                    <li data-theme="c">
                        <a href="request.php?act=jobcat" data-transition="slide" data-prefetch>
                            <?=$LANG['home_jobinfo']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=myprofile" data-transition="slide" data-prefetch>
                            <?=$LANG['home_myprofile']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=jobmanagesub" data-transition="slide">
                            <?=$LANG['home_managesubscriptions']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#page00" data-transition="slide">
                            <?=$LANG['home_mymember']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="request.php?act=tipstrick" data-transition="slide">
                            <?=$LANG['home_tipsntricks']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#page00" data-transition="slide">
                            <?=$LANG['home_settings']?>
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#page00" data-transition="slide">
                            <?=$LANG['home_help']?>
                        </a>
                    </li>
					<!--
                    <li data-theme="c">
                        <a href="#page01" data-transition="slide">
                            <?=$LANG['home_giveusfeedback']?>
                        </a>
                    </li>
					-->
                </ul>
               <br>
				<a data-role="button" data-transition="fade" href="request.php?act=logout">
                    <?=$LANG['home_logout']?>
                </a>	
				<div align="center">
                
                    <font size="1.5" face="arial" color="grey" align="center">
					<?
				if ($_SESSION['language'] == "english")
					echo '
					English &nbsp  I  &nbsp <a href="request.php?language=indonesia" >Bahasa</a>
					';
				else if ($_SESSION['language'] == "indonesia")
					echo '
					<a href="request.php?language=english" >English</a> &nbsp  I  &nbsp Bahasa
					';
				else
					echo '
					<a href="request.php?language=english" >English</a> &nbsp  I  &nbsp <a href="request.php?language=indonesia" >Bahasa</a>
					';
					
					
					?>
                    
                    </font>
               
                </div>
				
            </div>
        </div>

		       
        
        <div data-role="page" id="page00">
            <div data-theme="a" data-role="header">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>       			
                <h3>
                    Kerja Lokal-Underconstruction
                </h3>
            </div>
            <div data-role="content">
 
			<?=$LANG['home_content_underconstruction']?>
			
 
            </div>
        </div>
 

        <div data-role="page" id="page01">
            <div data-theme="a" data-role="header">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>       			
                <h3>
                    <?=$LANG['home_giveusfeedback']?>
                </h3>
            </div>
            <div data-role="content">
 
			<?=$LANG['home_content_giveusfeedback']?>
					<fieldset data-role="controlgroup">
                        <label for="textinput91">
                            <?=$LANG['home_content_giveusfeedback_yourname']?>
                        </label>
                        <input id="textinput91" placeholder="" value="" type="text" name="jobtitle"/>
                    </fieldset>
					<fieldset data-role="controlgroup">
                        <label for="textarea1">
                            <?=$LANG['home_content_giveusfeedback_idea']?>
                        </label>
                        <textarea id="textarea1" placeholder=""  name="jobtwitfb"></textarea>
                    </fieldset>			
				<a data-role="button" data-transition="fade" data-theme="a" href="#page02">
                    <?=$LANG['home_content_giveusfeedback_send']?>
                </a>

            </div>
        </div>
 
 
        <div data-role="page" id="page02">
            <div data-theme="a" data-role="header">
<div>
    <a href="request.php" data-transition="slide"><img border="0" src="images/kerjalokal.gif" alt="Kerja Lokal" style="float:left; display:inline; margin-left:14px; margin-top:11px;" /></a>
</div>       			
                <h3>
                    <?=$LANG['home_giveusfeedback']?>
                </h3>
            </div>
            <div data-role="content">
 
			<?=$LANG['home_content_giveusfeedback_response']?>

				<a data-role="button" data-transition="fade" data-theme="a" href="#page2">
                    <?=$LANG['home_content_giveusfeedback_backhome']?>
                </a>

            </div>
        </div>
 
		
<?
}

else {


include("content_login.php");


}








?>