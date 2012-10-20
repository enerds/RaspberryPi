<?php

print ('<div class="">');

include "command_body.php";
$mpd = new mpd($config_host,$config_port);

//display buttons
$array_modules = array('main','playlist','options');
foreach ($array_modules as $value) {
	if ($module == $value.".php") 	{ print('<a href="'.$value.'.php"><img src="images/'.$value.'.gif" class="active_control_bg" onClick="bg(this,1)"></a>'); }
	else 							{ print('<a href="'.$value.'.php"><img src="images/'.$value.'.gif" class="control_bg" onClick="bg(this,1)"></a>'); }
}
 

print('<a href="javascript:command(\'command=previous\',\'control_body.php\',\'chase\')"><img class="control_bg" src="images/back.gif" onClick="bg(this,1)"></a>');

if ($mpd->state != "play")	{ print('<a href="javascript:command(\'command=play\',\'control_body.php\',\'chase\')"><img class="control_bg" src="images/play.gif" onClick="bg(this,1)"></a>'); }
else						{ print('<a href="javascript:command(\'command=pause\',\'control_body.php\',\'chase\')"><img class="control_bg" src="images/pause.gif" onClick="bg(this,1)"></a>'); }

print ('<a href="javascript:command(\'command=next\',\'control_body.php\',\'chase\')"><img class="control_bg" src="images/ff.gif"></a>');
print ('<a href="javascript:command(\'command=volume%20-'.$volume_incr.'\',\'control_body.php\',\'chase\')"><img src="images/down.gif" class="control_bg" onClick="bg(this,1)"></a>');
print ('<a href="javascript:command(\'command=volume%20'.$volume_incr.'\',\'control_body.php\',\'chase\')"><img src="images/up.gif" class="control_bg" onClick="bg(this,1)"></a>');
print('<a href="javascript:window.scrollTo(1,1);"><img class="control_bg" src="images/top.gif" onClick="bg(this,1)"></a>');
print ('</div>');

if($mpd->state=="play" || $mpd->state=="pause") {

	// song slider
	
	$vol 	= $mpd->volume;
	$repeat = $mpd->repeat;
	$random = $mpd->random;
	$xfade 	= $mpd->xfade;

	$act_title = songInfo2Display($mpd->playlist[$mpd->current_track_id],"",999);

	$current_track_position		= (int) $mpd->current_track_position;	
	$current_track_length		= (int) $mpd->current_track_length;
	$current_track_remaining 	= $current_track_length - $mpd->current_track_position;

	$num_ticks = 50;
	$time_per_tick = $current_track_length/$num_ticks;

	//$col = 1;
	//print actual title	
	print('<div class="vsf col_playlist_title" style="width:100%;position:relative; top:5px;">Vol '.$mpd->volume.'% | '.$act_title.'</div>');
	
	print '<table border="0" cellspacing="0" cellpadding="0" height="8" width="100%" style="position:relative; top:-10px;border-collapse:collapse;">';
	print '<tr style="">';
	for($i=0; $i<$num_ticks; $i++) {
		/* is now made by javascript:song_slider_init
		if( 		($i+1)*$time_per_tick>=$current_track_position 
				&& 	($i)  *$time_per_tick<=$current_track_position) 	{ $col = 'class="col_time_foreground"'; }
		else 															{ $col = ''; }
		*/
		$seek 	= round($i*$time_per_tick);		
		$min 	= (int)($seek/60);
		$sec	= $seek%60;
		if($sec<10) $sec = "0$sec";$col=$i;		
		print '<td width="'.round(100/$num_ticks).'%" '.$col.' style="border:none;" id="song_slider_'.$i.'"><a href="javascript:command(\'command=seekid%20'.$mpd->playlist[$mpd->current_track_id]["Id"].'%20'.$seek.'\',\'control_body.php\',\'chase\')" ><img style="border:none;" height="5" width="100%" src="transparent.gif"></a></td>';  // title="'.$min.':'.$sec.'"
	}
	print '</tr></table>'."\n";

	// start js song_slider	
	print('<span id="js">song_slider_init('.$current_track_length.','.$current_track_position.',"'.$mpd->state.'")</span>');

}

$mpd->Disconnect(); 
?>
