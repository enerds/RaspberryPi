<?php

include ('../../lib/ClientSwitcher.php');

$sort = $default_sort;

$dir_url = rawurldecode($dir);
$mpd = new mpd($config_host,$config_port);
$array_entry = $mpd->GetDir($dir_url,$sort);

printDirs($dir_url,$array_entry['directories'], $array_entry['files'] );
printPlaylists();

//save actual dir 
$_SESSION["dir"] = $dir;
  

$mpd->Disconnect(); 

function printPlaylists() {

	
	$array_playlist = getPlaylists();
	for ($i=0;$i<count($array_playlist);$i++) {

		if ($i==0) { // the header ist not printed yet
			// th
			print('<table>'."\n");
			print('<tr><td class="col_music_title sf" colspan="3">Playlists </td></tr>'."\n");
			$header_playlist = TRUE;
		}
		print('<tr class="col_music_body_'.($i%2).'"><td class="big"><a href="javascript:command(\'command=load&amp;arg='.rawurlencode($array_playlist[$i]).'\',\'control_body.php\',\'chase\')"><img src="images/add2currentplaylist.gif" class="button_bg" alt="add all" title="add all"></a></td>');
		print('<td class="sf">'.$array_playlist[$i].'</td>'."\n");
		print('<td class="big"><a href="main.php?command=rm&amp;arg='.rawurlencode($array_playlist[$i]).'" class="sf"><img src="images/remove.gif" class="button_bg" alt="delete" title="delete"></a></td>');
	}	
	print('</table>'."\n");
}
?>