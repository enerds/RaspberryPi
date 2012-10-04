<?php

/* some helpers for phpMpReloaded */
define('__PHPMPRELOADED_MPD_SETTINGS__', '../../config/mpd_config.php' );
define('__PHPMPRELOADED_CLIENT_SWITCHER__', '../../lib/ClientSwitcher.php');
if (file_exists( __PHPMPRELOADED_MPD_SETTINGS__ )){
	include(__PHPMPRELOADED_MPD_SETTINGS__);
}

// NEED TO SET THESE!
$config_host = $mpd_host;
$config_port = $mpd_port;

// OPTIONAL
$title = "iPodMp";
$default_song_display_conf = "artist - title";
//$default_sort = "file,Artist,Album,Track,Title";
$default_sort = "file";
$filenames_only = "yes";
 

// VOLUME OPTIONS
$volume_incr = "10";

 


?>