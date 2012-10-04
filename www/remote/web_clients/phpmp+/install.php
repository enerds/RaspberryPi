<?php
// PhpMusicPlayer+
// Installation
// (Thanks to nosferat)

//Autodetect URL of phpMp+ file. (@@@hackish)
$sidebar_url = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);

// Un-comment following line if you have some problems with autodetection
#$sidebar_url = "http://127.0.0.1/phpmpp";	//URL of the phpMp+ playlist.php file.

$no_connect = TRUE;
include_once("functions.inc.php");

$sidebar_title = $userConfig['title'];

function sidebar_link_Opera($title, $url, $text)
{
    echo '<a href="playlist.php" title="'.$title.'" rel="sidebar">'.$text.'</a>';
}

function sidebar_link_Netscape($title, $url, $text)
{
    echo '
    <script type="text/javascript">
        function addNetscapePanel()
        {
            if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function")) {
                window.sidebar.addPanel ("'.$title.'","'.$url.'","");
            }
            else
            {
                var rv = window.confirm ("This page is enhanced for use with Mozilla.  " + "Would you like to upgrade now?");
                if (rv) {
                    document.location.href = "http://www.mozilla.org";
                }
            }
        }
    </script>
    <a href="javascript:addNetscapePanel();">'.$text.'</a>';
}

header("Content-Type: text/html; charset=UTF-8");

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/install.css" />
    <title>'.$userConfig['title'].'</title>
</head>
<body>
<p>';

$browser = $_SERVER['HTTP_USER_AGENT'];
if(strchr($browser,"Opera")) $browser = "Opera";
elseif(!strchr($browser,"MSIE") && strchr($browser,"Mozilla")) $browser = "Mozilla";

switch ($browser)
{
    case "Opera":
        sidebar_link_Opera($sidebar_title, $sidebar_url, _lang("Add phpMp+ to your sidebar"));
        break;
    case "Mozilla":
        sidebar_link_Netscape($sidebar_title, $sidebar_url, _lang("Add phpMp+ to your sidebar"));
        break;
    default:
        echo _lang("We were unable to determine what browser you are using. Please choose one of the following to add phpMp+ to your sidebar");
        echo ':<br />';
        echo sidebar_link_Opera($sidebar_title, $sidebar_url, "Opera");
        echo '<br />';
        echo sidebar_link_Netscape($sidebar_title, $sidebar_url, "Netscape");
        break;
}

echo '</p>
<p><a href="playlist.php">'._lang("Open phpMp+ in this window").'</a></p>
</body>
</html>';

?>