<?php 
// 
// takes mpd command and sends them to the server. May be included by any script at the beginning. 
// at the beginning it was a own class. Now we use the mpd.class!!!
include "config.php";
include "../mpd.class/mpd.class.php";
// removed sort.php here as it is now in mpd.class.php
//
$mpd = new mpd($config_host,$config_port);
if ($mpd->errStr){
	echo $mpd->errStr;
}

include "utils.php";
include "log.class";
import_request_variables("gpc", "url_");


if(isset($url_command)) { $command	= rawurldecode($url_command); }
if(isset($url_arg)) 	{ $arg		= rawurldecode($url_arg); }

// had some trouble with filenames in different charsets and rawurlencode; double base64_encode works!
if(isset($url_sarg)) 	{ $arg		= save_decode($url_sarg); }
if(isset($url_module)) 	{ $module  = $url_module; }
if(isset($url_dir)) 	{ $dir  = $url_dir; }else{ $dir=""; }
if(isset($url_col))		{ $col = $url_col; }
if(isset($url_search)) 	{ $search = $url_search; }
	
// add complete dir to the actual playlist
if (isset($command) && $command == "add_dir" && strlen($arg)>0) {

	$add_array = array();
	$i = 0;
	$return = $mpd->SendCommand("listall \"$arg\"\n");
	$array_return = explode("\n",$return);
	
	foreach ($array_return as $got) {
		if(strncmp($got,"file: ",strlen("file: "))==0) {
			$got = preg_replace("/\n/","",$got);
			$got = preg_replace("/^file\: /","",$got);
			$add_array[$i] = addslashes($got);
			$i++;
		}
	}
	$mpd->PLAddBulk($add_array);
}
// send a command to the mpd
elseif (isset($command)) {

	if (isset($arg)){
		if(strlen($arg)>0) { $command.=' "'.$arg.'"'; }	
	}
	
	$mpd->SendCommand("$command\n");
	 
} 

if (isset($command)){
	// if we add something, we want to hear it
	if (substr($command,0,3)=="add" && $mpd->state != "play")  { $mpd->Play(); }
}

$mpd->Disconnect();	

?>