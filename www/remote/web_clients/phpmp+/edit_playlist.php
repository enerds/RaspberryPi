<?php
// PhpMusicPlayer+
// Playlist editor

include_once("functions.inc.php");

if(isset($_GET['add_url'])) {
    $mpd->PLAdd($_GET['add_url']);
    header("location: edit_playlist.php");
}
elseif(isset($_GET['move']) && isset($_GET['newpos']))
{
    $mpd->PLMoveTrack($_GET['move'],$_GET['newpos']);
    header("location: edit_playlist.php");
}

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/edit.css" />
    <title>'._lang("Edit playlist").'</title>
    <script type="text/javascript">
    function Play(Id) {
        window.opener.location.href = "playlist.php?action=seekto&amp;value=" + Id + "%200";
        window.location.reload();
    }
    function Remove(Id) {
        window.opener.location.href = "playlist.php?action=remove&amp;value=" + Id;
        window.location.reload();
    }
    function AddURL() {
        var URL = window.prompt("'._lang("Livestream URL (the livestream will be added at the end of the playlist)").'");
        if(URL) {
            location.href="edit_playlist.php?add_url=" + URL;
        }
    }
    function Move(Id) {
        var question = new String("'._lang("This song as position %pos in playlist. Please give its new position below.").'");
        var NewPos = window.prompt(question.replace("%pos",Id));
        if(NewPos) {
            location.href="edit_playlist.php?move=" + Id + "&newpos=" + NewPos;
        }
    }
    function ShowInfo(Id) {
        window.open("info.php?id="+Id,"Info","width=200,height=160,top=250,left=50,scrollbars=1,location=false");
    }
    </script>
</head>
<body>
<h1>'._lang("Edit playlist").'</h1>
<ul id="commands">
    <li class="addstream"><a href="#" onclick="AddURL(); return false;">'._lang("Add Livestream").'</a></li>
    <li class="show">'._lang("Show").':
        <a href="edit_playlist.php?show=all">'._lang("All").'</a>
        <a href="edit_playlist.php?show=1000">1000 '._lang("songs").'</a>
        <a href="edit_playlist.php?show=500">500</a>
        <a href="edit_playlist.php?show=250">250</a>
        <a href="edit_playlist.php?show=100">100</a>
    </li>
    <li class="goto"><a href="#current">'._lang("Go to currently playing song").'</a></li>
</ul>';

if($mpd->playlist_count < 1)
    echo "<p>"._("Playlist is empty.")."</p>";
else {
    echo '<ul id="list">';
    $cl = "odd";

    isset($_GET['show']) ? $limit = $_GET['show'] : $limit = '';
    if(empty($limit)) $limit = 100;
    if($limit == "all") $limit = $mpd->playlist_count;

    $half = ceil($limit / 2);
    $begin = $mpd->current_track_id - $half;
    if($begin < 0)
        $begin = 0;


    $end = $mpd->current_track_id + $half;
    if($end > $mpd->playlist_count)
        $end = $mpd->playlist_count;

    for($i=$begin;$i<$end;$i++)
    {
        $el = $mpd->playlist[$i];
        $id = $i;
        $title = GetSongTitle($el);

        echo '
        <li class="'.$cl.(($i == $mpd->current_track_id) ? '" id="current"><a name="current"></a>' : '">').'
            <p class="song"><a href="no_js.php" onclick="Play('.$id.'); return false;">'.$title.'</a></p>
            <p class="commands">
                <a href="no_js.php" onclick="Remove('.$id.'); return false;" class="remove">'._lang("Remove").'</a>
                <a href="no_js.php" onclick="Move('.$id.'); return false;" class="move">'._lang("Move").'</a>
                <a href="no_js.php" onclick="ShowInfo('.$id.'); return false;" class="info">'._lang("Info").'</a>
            </p>
        </li>';

        if($cl == "odd") $cl = "even";
        else $cl = "odd";
    }
}

echo '</body>
</html>';

$mpd->Disconnect();

?>