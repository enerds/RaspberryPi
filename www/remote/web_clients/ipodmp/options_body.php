<?php

include ('../../lib/ClientSwitcher.php');

$mpd = new mpd($config_host,$config_port);

if($mpd->repeat) 	{ print('<a href="options.php?command=repeat&arg=0"><img src="images/repeat.gif" class="active_control_bg" alt="repeat on/off" title="repeat on/off" onClick="bg(this,1)"></a>'); }
else				{ print('<a href="options.php?command=repeat&arg=1"><img src="images/repeat.gif" class=""                  alt="repeat on/off" title="repeat on/off" onClick="bg(this,1)"></a>'); }

print('<a href="options.php?command=update"><img src="images/options.gif" class="" alt="scan for new tracks" title="scan for new tracks" onClick="bg(this,1)"></a>'); 
print('<a href="options.php?command=shuffle"><img src="images/shuffle.gif" class="" alt="shuffle playlist" title="shuffle playlist" onClick="bg(this,1)"></a>'); 
print('<br /><br /><div style="width:100%;" class="col_directories_title sf">Status</div>');
print('<br /><table style="width:200px;">');
foreach ($mpd->stats as $key=>$value) {

	print('<tr><td>'.$key.'</td><td>');
	if ($key == 'uptime' OR $key == 'playtime' OR $key == 'db_playtime' ) 	{ print( d_h_m_s($value) ); }
	elseif ( $key == 'db_update' ) 											{ print(date("d.m.Y",$value)); }
	else 																	{ print ($value); }
	
	print('</td></tr>');
}
print('</table>');
print('<br /><div style="width:100%;" class="col_directories_title sf"><b>&copy; Hendrik Stoetter</b></div><br />');
print('Based on <a href="http://www.musicpd.org/phpMp.shtml" target="_blank">phpMP</a> and <a href="http://mpd.24oz.com/" target="_blank">mpd.class</a><br />');
print('Please send Comments, Questions... to: <b>iPodMp (at) itrium.de</b><br />');
print('<br />This program is <b>free software</b>; you can redistribute it and/or modify
			it under the terms of the <b>GNU General Public License</b> as published by
			the Free Software Foundation; either version 2 of the License, or
			(at your option) any later version.
			');

function d_h_m_s($seconds) {
	$d = (int) ($seconds/86400); 	$seconds = $seconds % 86400;
	$h = (int) ($seconds/3600); 	$seconds = $seconds % 3600;
	$m = (int) ($seconds/60); 		$seconds = $seconds % 60;
	$s = (int) ($seconds); 		 
	
	return("$d d $h:$m:$s");
}
 
 
$mpd->Disconnect();
?>