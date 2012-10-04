<?php
/*
 * Created on 14.02.2010
 * file ClientSwitcher.php
 * part of phpMp-reloaded
 * 
 * by tswaehn (http://sourceforge.net/users/tswaehn/)
 */
 
/* 
 define('__CLIENT_SWITCHER_STYLE__', 'margin:0px;padding:5px;border:thin solid grey;position:fixed;bottom:1px;right:1px;background-color:white; ' );
 define('__CLIENT_SWITCHER_LOGO__', 'background-image:url(../../themes/default/speaker_icon.png);background-repeat:no-repeat;height:32px;width:32px' );
 define('__CLIENT_SWITCHER_FONT__', 'text-align:center;font-size:x-small;text-align:center;color:darkblue;	text-decoration:none;font-weight:bold; font-family:sans-serif;' );
*/ 

echo '<style type="text/css">';
echo '

#switcher_box {

	width:100px;
	margin:0px;
	border:thin solid grey;
	position:fixed;
	bottom:1px;
	right:1px;
	background-color:white; 

	padding:5px;
	-moz-border-radius: 4px;	
	
}

#clientswitcher {
	display:block;
	text-align:center;
	font-size:x-small;
	color:darkblue;
	text-decoration:none;
	font-weight:bold; 
	font-family:sans-serif;

}

#switcher_logo {
	
	float:left;
	border:none;
}';


echo '</style>';

echo '<div id="switcher_box"  >';
	echo '<a href="../../index.php" target="_parent" id="clientswitcher" >';
		
		//echo '<div id="switcher_logo">';
		echo '<img src="../../themes/default/speaker_icon.png" id="switcher_logo"/>';
		//echo '</div>';
		
		//echo '<div id="switcher_text">';
		echo 'switch<br>web client';
		///echo '</div>';
	 
	 echo '</a>';
 echo '</div>';
 
?>
