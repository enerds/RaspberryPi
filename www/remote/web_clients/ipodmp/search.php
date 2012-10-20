<?php

include "command_body.php";
$mpd = new mpd($config_host,$config_port);
// for security & server load reasons we start a search only, if strlen($arg>1)
$search = rawurldecode($search);
if (isset($search) && strlen($search)>1) { 

	$array_entry = $mpd->search("any",$search); 
	if (count($array_entry)>0) 	{ printDirs("Search Result",null, $array_entry['files']); }
	else 						{ print("Nothing found."); }

}

$mpd->Disconnect(); 
?>
