<?php
// PhpMusicPlayer+
// Configuration dialog

$no_connect = TRUE;
$is_config = TRUE;
include_once("functions.inc.php");

$defaults = array("host"=>$cfg["host"],"port"=>$cfg["port"],"style"=>"default","title"=>"Music Player","refresh"=>30,"playlist_length"=>20,
    "lang"=>$userConfig['lang'],"client_password"=>"abcd","song_display"=>"(artist) title??filename");

if(!file_exists("config.inc.php") && isset($_POST['client_password'])) {
    $cfg['client_password'] = $_POST['client_password'];
    $_POST['password'] = $_POST['client_password'];
}

if(array_key_exists("save",$_POST) && $_POST['password'] == $cfg['client_password'])
{
    $tpl = '<?php
$cfg["host"] = "'.$_POST['host'].'";
$cfg["port"] = '.$_POST['port'].';
$cfg["style"] = "'.$_POST['style'].'";
$cfg["title"] = "'.$_POST['title'].'";
$cfg["refresh"] = '.$_POST['refresh'].';
$cfg["playlist_length"] = '.$_POST['playlist_length'].';
$cfg["lang"] = "'.$_POST['lang'].'";
$cfg["client_password"] = "'.$_POST['client_password'].'";
$cfg["song_display"] = "'.$_POST['song_display'].'";
?>';
    $fp = @fopen("config.inc.php","w");
    if(!$fp) {
        header("Content-Type: text/html; charset=UTF-8");
        echo _lang("Can't write in config.inc.php. Please set permissions or write the following content to this file.");
        echo "<hr><pre>";
        echo htmlspecialchars($tpl);
        exit;
    }

    fputs($fp,$tpl);
    fclose($fp);
    header("location: config.php?ok=1");
}

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/config.css" />
    <title>'._lang("Configure").'</title>';

if(array_key_exists("ok",$_GET))
    echo '<script type="text/javascript">
        alert("'._lang("Configuration has been saved").'.");
        if(window.opener) { window.opener.location.href="playlist.php"; }
        else { window.location.href="playlist.php"; }
    </script>';

echo '
</head>
<body>
<h1>'._lang("Configure").'</h1>
<form method="post" action="config.php">
<p>'._lang("This is the default configuration for the client, it will be applied to new users.").'</p>';

if((!array_key_exists("password",$_POST) || ($_POST['password'] != $cfg['client_password'])) && file_exists("config.inc.php"))
{
    echo '<label for="password">
        <strong>'._lang("Please enter client password to access configuration").'</strong>
        <input type="password" name="password" />
    </label>
    <p><input type="submit" value="OK" /></p>';
}
else
{
    if(!file_exists("config.inc.php")) $cfg = $defaults;
    echo '<label for="style">
    <strong>'._lang("Style").'</strong>
    <select name="style" id="style">';

    $dir = opendir("styles");
    while($file = readdir($dir)) {
        if($file != "." && $file != "..") echo '
            <option value="'.$file.'"'.(($file == $cfg['style']) ? ' selected="selected"' : '').'>'.ucfirst($file).'</option>';
    }
    closedir($dir);

    echo '</select>
    </label>
    <label for="host">
        <strong>'._lang("Host").'</strong>
        <input type="text" name="host" value="'.$cfg['host'].'" id="host" />
    </label>
    <label for="port">
        <strong>'._lang("Port").'</strong>
        <input type="text" name="port" value="'.$cfg['port'].'" id="port" />
    </label>
    <label for="title">
        <strong>'._lang("Title").'</strong>
        <input type="text" name="title" value="'.$cfg['title'].'" id="title" />
    </label>
    <label for="refresh">
        <strong>'._lang("Refresh frequency (in seconds)").'</strong>
        <input type="text" name="refresh" value="'.$cfg['refresh'].'" id="refresh" />
    </label>
    <label for="length">
        <strong>'._lang("Playlist length").'</strong>
        <input type="text" name="playlist_length" value="'.$cfg['playlist_length'].'" id="length" />
    </label>
    <label for="lang">
        <strong>'._lang("Lang").'</strong>
        <select name="lang" id="lang">';

    foreach($langs as $code=>$name)
        echo '<option value="'.$code.'" '.(($code == $cfg['lang']) ? ' selected="selected"' : '').'>'.htmlentities($name).'</option>';

    echo '</select>
    </label>
    <label for="password">
        <strong>'._lang("Client password").'</strong>
        <input type="text" name="client_password" id="password" value="'.$cfg['client_password'].'" />
    </label>
    <label for="song_display">
        <strong>'._lang("Song display format").'</strong>
        <input type="text" name="song_display" id="song_display" value="'.$cfg['song_display'].'" />
    </label>
    <p>
        <input type="submit" value="'._lang("Save configuration").'" name="save" />
        <input type="hidden" name="password" value="'.$_POST['password'].'" />
    </p>';
}

echo '
</form>
<p><a href="userconfig.php">'._lang("User configuration").'</a></p>
<p><a href="playlist.php">'._lang("Back").'<a></p>
</body>
</html>';

?>