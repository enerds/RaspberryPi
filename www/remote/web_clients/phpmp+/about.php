<?php
// PhpMusicPlayer+
// about dialog

include_once("functions.inc.php");

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/about.css" />
    <title>'._lang("About").'</title>
</head>
<body>
<h1>'._lang("About").'</h1>
<div id="stats">
  <h3>'._lang("Statistics").'</h3>
  <ul>
    <li id="artists">'._lang("Number of artists").': <span>'.$mpd->num_artists.'</span></li>
    <li id="albums">'._lang("Number of albums").': <span>'.$mpd->num_albums.'</span></li>
    <li id="songs">'._lang("Number of songs").': <span>'.$mpd->num_songs.'</span></li>
    <li id="uptime">'._lang("Uptime").': <span>'.secToTimeStr($mpd->uptime).'</span></li>
    <li id="playtime">'._lang("Playtime").': <span>'.secToTimeStr($mpd->playtime).'</span></li>
    <li id="db_playtime">'._lang("DB Playtime").': <span>'.secToTimeStr($mpd->db_playtime).'</span></li>
  </ul>
</div>
<div id="about">
    <p>PHPMusicPlayer+ Version '.$version.'</p>
    <p>Copyleft &copy; 2004 BohwaZ (GNU/GPL License)</p>
    <p>'._lang("Includes MPD Class by Benjamin Carlisle").' ('.$mpd->mpd_class_version.')</p>
    <p>'._lang("Using MPD version").' '.$mpd->mpd_version.'</p>
    <p>'._lang("Get more info about MPD at").' <a href="http://musicpd.org/">MusicPD.org</a></p>
</div>
<noscript><p><a href="./">'._lang("Back").'</a></p></noscript>
</body>
</html>';

?>