<?php

include ('../../lib/ClientSwitcher.php'); 

$mpd = new mpd($config_host,$config_port);
 
// show the form for adding a song to a (existing) playlist ; add2playlist holds the file(name) of the song or 1
if(isset($add2m3u) && $add2m3u) {

 
	print '<br><form style="padding:0;margin:0;" action="playlist.php">'."\n";
	print '<table border="0" cellspacing="0">'."\n";
	if ($add2m3u == "1") {
		print '<tr><td class="sf">Save as</span></td></tr>'."\n";
	}
	else {
		print '<tr><td class="sf">Add <br><span class="vsf">'.basename(save_decode($add2m3u)).'</span> <br>to</td></tr>'."\n";
	}
	//show existing playlists
	$array_playlist = getPlaylists();
	foreach($array_playlist as $value) {
		print '<tr class="col_playlist_body"><td>&nbsp;&nbsp;<input type="radio" name="ex_playlist" value="'.$value.'" />'.$value.'</td></tr>'."\n";
	}
	// or a new one
	print '<tr><td class="sf">or create a new one</td></tr>'."\n";
	print '<tr class="col_playlist_body"><td><input name="new_playlist" size="40"></td></tr>'."\n";
	print '<tr><td><br><input type="submit" name="add2playlist_save" value="Save">&nbsp;<input type="reset" name="add2playlist_save" value="Cancel" onClick="location.replace(\'playlist.php\')" ><br>&nbsp;</td></tr>'."\n";
	print '<input type="hidden" value="'.$add2m3u.'" name="song">'."\n";
	print '</table>'."\n";
	print '</form>'."\n";
	
	 
}
// save the song in the playlist
if(isset($add2playlist_save) && $add2playlist_save) {
	if ($song == "1") 			{ $song=""; }
	if (strlen($ex_playlist)>0)	{ $pl_name = $ex_playlist; }
	else						{ $pl_name = $new_playlist; }
	
	savePlaylist($pl_name,save_decode($song));
	
	print '<br><table border="0" cellspacing="0" class="col_playlist_title" width="100%">'."\n";
	print '<tr><td>Song/Playlist saved.</td></tr>'."\n";
	print '</table>'."\n";
}
	


/* display current playlist / queue*/
print '<table border=0 cellspacing=1 class="col_playing_title">'."\n";
print '<tr valign="top" class="big"><td><b>Current Playlist</b></td>'."\n";
print '<td valign="top" align="right">';
print '<a href=playlist.php?add2m3u=1><img src="images/save_small.gif" class="button_bg" alt="shuffle" title="save"></a> ';
print '<a href="playlist.php?command=clear"><img src="images/remove_small.gif" class="button_bg" alt="clear" title="clear" onClick="bg(this,1)"></a> ';
print '</td></tr>'."\n";
print '<tr></table>'."\n";
print '<table border=0 cellspacing=0 >'."\n";
if(!isset($mpd->current_track_id)) $mpd->current_track_id = -1;
if(count($mpd->playlist)>0) {
	printPlaylistInfo($mpd,$mpd->current_track_id);
}
print "</table>\n";
$mpd->Disconnect();



function printPlaylistInfo(&$mpd,$num) {
	// in the current playlist we have no directory for orientation
	// so we have to display artist and track
	// if they are not set we have to use the (short) filename
 
 
	foreach ( $mpd->playlist as $current=>$entry) {
 			
		$artist = utf8_decode($entry["Artist"]);
		$title 	= utf8_decode($entry["Title"]);
		$file	= utf8_decode($entry["file"]);
		$id		= utf8_decode($entry["Id"]);
	
		if (strlen($artist)>1 && strlen($title)>1) 	{ $display = $artist." - ".$title; }
		else 										{ $display = basename($file); }
		 
		if ( $num==$current) 	{ print '<tr class="col_playlist_current">'; }
		else					{ print '<tr class="col_playlist_body">'; }
		
		$display = short_title($display);
		print '<td class="big"><a href="javascript:command(\'command=playid%20'.$id.'\',\'control_body.php\',\'chase\')"><img src="images/play_small.gif" class="button_bg" onClick="bg(this,1)"></a></td>';
		print '<td class="sf">'.$display.'</td>'."\n";
		print '<td valign="top" class="big"><a href="playlist.php?command=deleteid%20'.$id.'"><img src="images/remove.gif" class="button_bg" onClick="bg(this,1)"></a></td>'."\n";
		print '<td valign="top" class="big"><a href="playlist.php?add2m3u='.save_encode($file).'"><img src="images/playlist.gif" class="button_bg" onClick="bg(this,1)"></a></td></tr>'."\n";
	}


}

function savePlaylist($playlistname="",$song="") {
	// this functions saves the current playlist to $playlistname
	// OR adds $song to a (new/existing) playlist

	global $config_port;
	global $config_host;
	
	$array_playlist = getPlaylists();

	$mpd = new mpd($config_host,$config_port);

	if (strlen($playlistname)>0 && strlen($song)==0) { // save current playlist to $playlistname
		// remove playlist if exists (otherwise it wont be overwritten)		
		if (in_array($playlistname,$array_playlist)) { 
			$mpd->SendCommand("rm",$playlistname); 
		} 
		$mpd->PLSave($playlistname);
	}
	if (strlen($playlistname)>0 && strlen($song)>0) { //adds $song to a (new/existing) playlist

		$current_state 			= $mpd->state; 
		$current_track_position = $mpd->current_track_position; 
		$current_track_id		= $mpd->current_track_id;

		
		$mpd->PLSave("__temp__"); 									// save current playlist to templiste 				
		$mpd->PLClear();											// and clear it 

			
		if (in_array($playlistname,$array_playlist)) { 
			$mpd->PLLoad($playlistname); 							// load playlist if exists 				
			$mpd->SendCommand("rm",$playlistname); 					// remove playlist if exists (otherwise it wont be overwritten)
		} 
		
		$mpd->PLAdd($song); 										// add song 		 
		$mpd->PLSave($playlistname); 								// save playlist		
		$mpd->PLClear();											// and clear it 		
		$mpd->PLLoad("__temp__"); 									// restore current playlist 		
		$mpd->SeekTo($current_track_position, $current_track_id); 	// set Track & position 		
		$mpd->SendCommand($current_state); 							// restores state (Play, Stop, Pause)		
		$mpd->SendCommand("rm","__temp__"); 						// rm templist 
		
	}
	$mpd->Disconnect();		
}
?>