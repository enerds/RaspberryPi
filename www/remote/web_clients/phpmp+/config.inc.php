<?php

/* some helpers for phpMpReloaded */
define('__PHPMPRELOADED_MPD_SETTINGS__', '../../config/mpd_config.php' );
define('__PHPMPRELOADED_CLIENT_SWITCHER__', '../../lib/ClientSwitcher.php');
if (file_exists( __PHPMPRELOADED_MPD_SETTINGS__ )){
	include(__PHPMPRELOADED_MPD_SETTINGS__);
}


$cfg["host"] = $mpd_host;
$cfg["port"] = $mpd_port;
$cfg["style"] = "default";
$cfg["title"] = "Music Player";
$cfg["refresh"] = 30;
$cfg["playlist_length"] = 20;
$cfg["lang"] = "fr";
$cfg["client_password"] = "abcd";
$cfg["song_display"] = "(artist) title??filename";

?>