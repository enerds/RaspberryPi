<?php
// PhpMusicPlayer+
// Debug dialog

include_once("functions.inc.php");

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/debug.css" />
    <title>'._("Debug").'</title>
</head>
<body>
<h1>'._("Debug").'</h1>
<form method="post" action="debug.php">
<p id="cmd">
    <label for="f_cmd">Command to MPD</label>
    <textarea name="command" cols="30" rows="3">'.(isset($_POST['command']) ? $_POST['command'] : '').'</textarea>
    <input type="submit" value="Send" />
</p>
<pre id="result">';

if(isset($_POST['command'])) {
    fputs($mpd->mpd_sock,$_POST['command']."\n");
    while(!feof($mpd->mpd_sock)) {
        $response = fgets($mpd->mpd_sock,1024);
        print_r(htmlentities($response));
        if (eregi("(ERR|OK)",$response))
            break;
    }
}
echo '</pre>
</form>
<noscript><p><a href="./">'._lang("Back").'</a></p></noscript>
</body>
</html>';

?>