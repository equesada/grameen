<?php  if ( ! defined('BASEPATH')) exit('No direct access allowed'); ?>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>.:Infokerja:.</title>
<script type="text/javascript" src="<?php echo base_url() . "themes/theme4/js/jquery.js"; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . "themes/theme4/js/easySlider1.7.js"; ?>"></script>

<script type="text/javascript" src="<?php echo base_url() . "themes/theme4/javascript/dropdown.js"; ?>"></script>
<script type="text/javascript">
	var timeout         = 500;
	var closetimer		= 0;
	var ddmenuitem      = 0;
	
	function jsddm_open()
	{	jsddm_canceltimer();
		jsddm_close();
		ddmenuitem = $(this).find('ul').eq(0).css('visibility', 'visible');}
	
	function jsddm_close()
	{	if(ddmenuitem) ddmenuitem.css('visibility', 'hidden');}
	
	function jsddm_timer()
	{	closetimer = window.setTimeout(jsddm_close, timeout);}
	
	function jsddm_canceltimer()
	{	if(closetimer)
		{	window.clearTimeout(closetimer);
			closetimer = null;}}
	
	$(document).ready(function()
	{	$('#jsddm > li').bind('mouseover', jsddm_open);
		$('#jsddm > li').bind('mouseout',  jsddm_timer);});
	
	document.onclick = jsddm_close;
</script>
<link href="<?php echo base_url() . "themes/theme4/css/style.css"; ?>" rel="stylesheet" type="text/css" media="screen" />	
</head>

<body>

<div id="wrapper">

	<!--	HEADER TOP	-->
	<div id="header">    		
        <div class="menu">
            <ul>
                <li><a href="#">RSS</a></li>
                <li><a href="#">Contact US</a></li>
                <li><a href="#">Patnership</a></li>
                <li><a href="#">Sitemap</a></li>
                <li><a href="#">Bookmark & Share</a></li>
                <li id="float-r">
                <form>
                    <label class="text">Search</label>
                    <input class="searchbox" value="Search" type="text">
                    <button class="searchbtn" title="Search">go</button>
                </form>
                </li>
            </ul>
            <br style="clear:left"/>  
        </div>        
	</div><!-- #header-->
    
    <!--	HEADER TITLE	-->
    <div class="top-header">
    	INFO KERJA
        <ul>
        	<li><a href="#">Bakrie Telecom</a></li>
            <li><a href="#">Grameen Fondation</a></li>
        </ul>
        <ul>
        	<li><a href="#">Qualcomm</a></li>
            <li><a href="#">RUMA</a></li>
        </ul>
    </div>
    
    <!--	HEADER MENU	-->
	<div class="navbar">
		<ul id="jsddm">
            <li><a href="#">JavaScript</a>
                <ul>
                    <li><a href="#">Drop Down Menu</a></li>
                    <li><a href="#">jQuery Plugin</a></li>
                    <li><a href="#">Ajax Navigation</a></li>
                </ul>
            </li>
            <li><a href="#">Effect</a>
                <ul>
                    <li><a href="#">Slide Effect</a></li>
                    <li><a href="#">Fade Effect</a></li>
                    <li><a href="#">Opacity Mode</a></li>
                    <li><a href="#">Drop Shadow</a></li>
                    <li><a href="#">Semitransparent</a></li>
                </ul>
            </li>
            <li><a href="#">Navigation</a></li>
            <li><a href="#">HTML/CSS</a></li>
            <li><a href="#">Help</a></li>
        </ul>
	</div>
    <div class="clear"> </div>
    <!--	LOGIN FRONT	-->
	<div class="login-header">
    	<ul>
        	<li id="login-form">
                <form>
                    <label class="text">Employer</label>
                    <input class="loginbox" value="User Name" type="text">
                    <input class="loginbox" value="Password" type="text">
                    <button class="searchbtn" title="Search">Login</button>
                    <a href="#">Sign Up Free!</a> ||
                    <a href="#">Take a Tour</a>
                </form>
                </li>
        </ul>
    </div>
	

