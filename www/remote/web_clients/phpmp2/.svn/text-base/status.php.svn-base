<div id="status">
<?php
echo "<h2><a name=\"status\"></a>";
switch($mpd_status["state"]) {
case "play":
	echo "Playing";
	break;
case "pause":
	echo "Paused";
	break;
case "stop":
	echo "Stopped";
	break;
}
echo " <small>[";
make_link("index.php", "status", "refresh");
echo "]</small></h2>";
if((array_key_exists("command", $_REQUEST) && $_REQUEST["command"])) {
	if($command_successful !== true)
		echo "Error on command ".$_REQUEST["command"]." with arguments: ".$_REQUEST["arg"]."<br />\n";
}


switch($mpd_status["state"]) {
case "play":
case "pause":
	$songinfo = do_mpd_command($connection, "playlistid ".$mpd_status["songid"], null, true);
	if($configuration["filenames_only"] == true) {
		echo $songinfo["file"];
	} else {
		echo format_song_title($configuration["song_display_format"], $songinfo, $mpd_status["song"]);
	}
	$elapsed_time = floatval(trim(strtok($mpd_status["time"], ":")));
	$total_time = floatval(trim(strtok("\0")));
	$remaining_time = $total_time - $elapsed_time;
	echo "<br />\n(";
	if($configuration["time_elapsed"] == true) {
		make_link("", "status", format_time($elapsed_time), array("time_elapsed" => "false"));
		echo ")/";
	} else {
		echo "-";
		make_link("", "status", format_time($remaining_time), array("time_elapsed" => "true"));
		echo ") ";
	}
	echo "[".format_time($total_time)."] ";
	echo $mpd_status["bitrate"]." kbps";
	break;
}
?>
</div>
