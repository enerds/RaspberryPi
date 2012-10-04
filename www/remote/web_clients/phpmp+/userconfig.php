<?php
// PhpMusicPlayer+
// User Configuration dialog

if(array_key_exists("del_cookie",$_GET)) {
    setcookie("phpmpplus_userconfig","",time()+(3600*24*365));
    header("location: userconfig.php?reset=1");
}

if($_POST) $save_userconf = TRUE;
include_once("functions.inc.php");

if($_POST) {
    SaveUserConfig($_POST);
    header("location: userconfig.php?ok=1");
}

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/userconfig.css" />
    <title>'._lang("User configuration").'</title>';

if(array_key_exists("ok",$_GET) || array_key_exists("reset",$_GET)) {
    echo '<script type="text/javascript">';
    if($_GET["ok"]) echo 'alert("'._lang("Configuration has been saved").'.");';
    else echo 'alert("'._lang("Configuration has been reset (the cookie has been deleted)").'.");';
    echo '
        if(window.opener) {  window.opener.location.href="playlist.php"; }
        else { window.location.href="playlist.php"; }
    </script>';
}
echo '
</head>
<body>
<h1>'._lang("User configuration").'</h1>
<form method="post" action="userconfig.php">
<label for="style">
<strong>'._lang("Style").'</strong>
<select name="style" id="style">';

$dir = opendir("styles");
while($file = readdir($dir)) {
    if($file != "." && $file != "..") echo '
        <option value="'.$file.'"'.(($file == $userConfig['style']) ? ' selected="selected"' : '').'>'.ucfirst($file).'</option>';
}
closedir($dir);

echo '</select>
</label>
<label for="title">
    <strong>'._lang("Title").'</strong>
    <input type="text" name="title" value="'.$userConfig['title'].'" id="title" />
</label>
<label for="refresh">
    <strong>'._lang("Refresh frequency (in seconds)").'</strong>
    <input type="text" name="refresh" value="'.$userConfig['refresh'].'" id="refresh" />
</label>
<label for="length">
    <strong>'._lang("Playlist length").'</strong>
    <input type="text" name="playlist_length" value="'.$userConfig['playlist_length'].'" id="length" />
</label>
<label for="lang">
    <strong>'._lang("Lang").'</strong>
    <select name="lang" id="lang">';

foreach($langs as $code=>$name)
    echo '<option value="'.$code.'" '.(($code == $userConfig['lang']) ? ' selected="selected"' : '').'>'.htmlentities($name).'</option>';

echo '</select>
</label>
<label for="password">
    <strong>'._lang("MPD password").'</strong>
    <input type="text" name="password" id="password" value="'.$userConfig['password'].'" />
</label>
<label for="song_display">
    <strong>'._lang("Song display format").'</strong>
    <input type="text" name="song_display" id="song_display" value="'.$userConfig['song_display'].'" />
</label>
<p>
    <input type="submit" value="'._lang("Save configuration").'" />
</p>';

echo '
</form>
<ul>
    <li><a href="userconfig.php?del_cookie=1">'._lang("Reset configuration (delete cookie)").'</a></li>
    <li><a href="config.php">'._lang("Client configuration").'</a></li>
    <noscript><li><a href="./">'._lang("Back").'</a></li></noscript>
</ul>
</body>
</html>';

?>