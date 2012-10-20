<?php
// PhpMusicPlayer+
// Informations about one song

include_once("functions.inc.php");

$id = $_GET['id'];

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/info.css" />
    <title>'._lang("Info").'</title>
</head>
<body>
<h1>'._lang("Info").'</h1>
<ul>';

$info = $mpd->playlist[$id];
if(isset($info['file']))
    echo '<li><strong>'._lang("Filename").'</strong>: '.$info['file'].'</li>';
if(isset($info['Artist']))
    echo '<li><strong>'._lang("Artist").'</strong>: '.$info['Artist'].'</li>';
if(isset($info['Album']))
    echo '<li><strong>'._lang("Album").'</strong>: '.$info['Album'].'</li>';
if(isset($info['Track']))
    echo '<li><strong>'._lang("Track").'</strong>: '.$info['Track'].'</li>';
if(isset($info['Title']))
    echo '<li><strong>'._lang("Title").'</strong>: '.$info['Title'].'</li>';
if(isset($info['Time']))
{
    $min = floor($info['Time'] / 60);
    $secs = $info['Time'] - ($min*60);
    echo '<li><strong>'._lang("Length").'</strong>: '.$min.':'.$secs.'</li>';
}
if(isset($info['Pos']))
    echo '<li><strong>'._lang("Playlist position").'</strong>: '.$info['Pos'].'</li>';
if(isset($info['Id']))
    echo '<li><strong>'._lang("Id").'</strong>: '.$info['Id'].'</li>';

echo '</ul></body>
</html>';

$mpd->Disconnect();

?>