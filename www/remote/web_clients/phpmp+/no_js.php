<?php
// PhpMusicPlayer+ v0.2.0 beta
// No Javascript support message

$no_connect = TRUE;
include_once("functions.inc.php");

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/error.css" />
    <title>'._lang("Error").'</title>
</head>
<body>
<h2>'._lang("Error").'</h2>
<p id="error">'._lang("Please switch javascript on in order to use this function").'.</p>
<p><a href="./">'._lang("Back").'</a></p>
</body>
</html>';

?>