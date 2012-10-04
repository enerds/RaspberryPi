<?php

// sehr genial! UTF-( und ISO save, gefunden unter 
// http://de.php.net/manual/de/function.urlencode.php section comments

function save_encode($string) {
	return ( base64_encode(base64_encode($string)) );
}

function save_decode($string) {
	return ( base64_decode(base64_decode($string)) );
}




function getPlaylists() {
	// returns a array of all playlists
	global $config_port;
	global $config_host;

	$mpd = new mpd($config_host,$config_port);
	$array_entry = $mpd->GetDir();
	$array_entry = $array_entry['playlists'];
	
	$mpd->Disconnect();
	
	$array_return = array();
	
	for ($i=0;$i<count($array_entry);$i++) {		
		$array_return[] = $array_entry[$i];
	}
	
	return($array_return);
}
	


function short_title ($title,$maxlen=45) {
	//reduces a songtitle or filename to the minimum and important
	// remove filetype
	if (strtolower(substr($title,-4)) == ".mp3") { $title = substr($title,0,-4);  }
	// removes anything in () if its not a the beginning, eg Artistname
	$start = strpos($title,"("); $stop = strrpos($title,")");
	if ($start>0 && $stop >0) { $title = substr($title,0,$start).substr($title,($stop+1)); }
	// replace "_" by " " to save space
	$title = str_replace("_"," ",$title);
	
	if (strlen($title)>$maxlen) {
		$newtitle = substr($title,0,15)."... ".substr($title,-($maxlen-18) ); $title = $newtitle;
	}
	return ($title);	
}

function songInfo2Display($song_info,$format_string="",$maxlen=45) {

	global $default_song_display_conf;
	
	if (strlen($format_string)>0) 	{ $song_display_conf = $format_string; }
	else 							{ $song_display_conf = $default_song_display_conf; }		

 	if(isset($song_info["Title"]) && $song_info["Title"]) {
		if(isset($song_info["Artist"])) $artist = utf8_decode($song_info["Artist"]);
		else 							$artist = "";
		if(isset($song_info["Title"])) 	$title = utf8_decode($song_info["Title"]);
		else 							$title = "";
		if(isset($song_info["Album"])) 	$album = utf8_decode($song_info["Album"]);
		else 							$album = "";
		if(isset($song_info["Track"])) 	$track = utf8_decode($song_info["Track"]);
		else 							$track = "";
		$trans = array("artist" => $artist, "title" => $title, "album" => $album, "track" => $track);
		$song_display = strtr($song_display_conf, $trans);
	}
	else {  
		$song_display = short_title(basename($song_info["file"]),$maxlen);
 	}  
	return $song_display;
} 

// used by main.php and the search results in search.php
// 20/03/2010 -- tswaehn
function printDirs($dir,$dir_array, $file_array) {
	// prints a table with the Directories in $dir

	// th
	print('<table style="width:100%;">'."\n");
	print('<tr><td class="col_directories_title sf" colspan="2">');
	
	//path
	$array_dir = explode("/",$dir);
	// in search result this link is useless
	if ($dir == "Search Result") { 
		print($dir);
	}
	else {
		print('<a href="main.php?dir=/" class="sf">Folder: Music</a>');  
		$act_path = ""; 
	
		foreach ($array_dir as $dirname) {
			if (strlen($act_path)>0 ) 	{ $act_path.="/".	$dirname; }
			else 						{ $act_path.=		$dirname; }
	
			if (strlen($dirname)>0) { print(' / <a href="main.php?dir='.rawurlencode($act_path).'" class="sf" onClick="bg(this,1)">'.utf8_decode($dirname).'</a>'); }
		}
	}	
	print('</td></tr>'."\n");
		
	// directories
	for ($i=0;$i<count($dir_array);$i++) {
		
		$directory_name = $dir_array[$i];

		// 20/03/2010 -- tswaehn
		print('<tr class="col_directories_body_'.($i%2).'"><td class="big"><a href="javascript:command(\'command=add_dir&sarg='.save_encode($directory_name).'\',\'control_body.php\',\'chase\')"><img src="images/addall2currentplaylist.gif" class="button_bg" alt="add all" title="add all" onClick="bg(this,1)"></a></td>');
		print('<td><a href="main.php?dir='.rawurlencode($directory_name).'" class="sf">'.utf8_decode(basename($directory_name) ).'</a></td></tr>'."\n");

		/*		
		if (isset($array_entry[$i]["directory"])) {
			print('<tr class="col_directories_body_'.($i%2).'"><td class="big"><a href="javascript:command(\'command=add_dir&sarg='.save_encode($array_entry[$i]["directory"]).'\',\'control_body.php\',\'chase\')"><img src="images/addall2currentplaylist.gif" class="button_bg" alt="add all" title="add all" onClick="bg(this,1)"></a></td>');
			print('<td><a href="main.php?dir='.rawurlencode($array_entry[$i]["directory"]).'" class="sf">'.utf8_decode(basename($array_entry[$i]["directory"]) ).'</a></td></tr>'."\n");
		}
		*/
	}	
	
	// songs
	$header_song = FALSE;
	
	// in search result this button is useless
	if ($dir == "Search Result") { $header_song = TRUE; }

	for ($i=0;$i<count($file_array);$i++) {
		
		// 20/03/2010 -- tswaehn
		$file = $file_array[$i];
		if (!$header_song) { // and the header ist not printed yet
			// th
			print('<table style="width:100%;">'."\n");
			print('<tr class="col_music_title"><td><a href="javascript:command(\'command=add_dir&sarg='.save_encode($act_path).'\',\'control_body.php\',\'chase\')"><img src="images/addall2currentplaylist.gif" class="button_bg" alt="add all" title="add all" onClick="bg(this,1)"></a></td>');
			print('<td>Songs </td></tr>'."\n");
			$header_song = TRUE;
		}
		// print the song
		print('<tr class="col_music_body_'.($i%2).'"><td class="big"><a href="javascript:command(\'command=add&sarg='.save_encode($file['file']).'\',\'control_body.php\',\'chase\')"><img src="images/add2currentplaylist.gif" class="button_bg" alt="add" title="add" onClick="bg(this,1)"></a></td>');
		print('<td class="sf">'.songInfo2Display($file).'</td></tr>'."\n");

		
		/*
		if (isset($array_entry[$i]["file"])) { // this entry is a song
			if (!$header_song) { // and the header ist not printed yet
				// th
				print('<table style="width:100%;">'."\n");
				print('<tr class="col_music_title"><td><a href="javascript:command(\'command=add_dir&sarg='.save_encode($act_path).'\',\'control_body.php\',\'chase\')"><img src="images/addall2currentplaylist.gif" class="button_bg" alt="add all" title="add all" onClick="bg(this,1)"></a></td>');
				print('<td>Songs </td></tr>'."\n");
				$header_song = TRUE;
			}
			// print the song
			print('<tr class="col_music_body_'.($i%2).'"><td class="big"><a href="javascript:command(\'command=add&sarg='.save_encode($array_entry[$i]["file"]).'\',\'control_body.php\',\'chase\')"><img src="images/add2currentplaylist.gif" class="button_bg" alt="add" title="add" onClick="bg(this,1)"></a></td>');
			print('<td class="sf">'.songInfo2Display($array_entry[$i]).'</td></tr>'."\n");
		}
		*/
	}	
	print('</table>'."\n");

}
?>