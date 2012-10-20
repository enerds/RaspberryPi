<?php
// PhpMusicPlayer+
// Main File

include_once("functions.inc.php");

// Some actions to do
if(array_key_exists("action",$_GET)) switch($_GET['action']) {
    case "play":
        if(is_null($mpd->Play())) show_error($mpd->errStr);
        break;
    case "stop":
        if(is_null($mpd->Stop())) show_error($mpd->errStr);
        break;
    case "pause":
        if(is_null($mpd->Pause())) show_error($mpd->errStr);
        break;
    case "next":
        if(is_null($mpd->Next())) show_error($mpd->errStr);
        break;
    case "previous":
        if(is_null($mpd->Previous())) show_error($mpd->errStr);
        break;
    case "seekto":
        $t = explode(" ",urldecode($_GET['value']));
        if(is_null($mpd->SeekTo($t[1],$t[0]))) show_error($mpd->errStr);
        break;
    case "shuffle":
        if(is_null($mpd->PLShuffle())) show_error($mpd->errStr);
        break;
    case "clear":
        if(is_null($mpd->PLClear())) show_error($mpd->errStr);
        header("location: playlist.php");
        break;
    case "repeat":
        if(is_null($mpd->SetRepeat($_GET['value']))) show_error($mpd->errStr);
        break;
    case "random":
        if(is_null($mpd->SetRandom($_GET['value']))) show_error($mpd->errStr);
        break;
    case "crossfade":
        if(is_null($mpd->SetCrossfade($_GET['value']))) show_error($mpd->errStr);
        break;
    case "volume":
        if(is_null($mpd->SetVolume($_GET['value']))) show_error($mpd->errStr);
        break;
    case "remove":
        if(is_null($mpd->PLRemove($_GET['value']))) show_error($mpd->errStr);
        break;
    case "save":
        if(is_null($mpd->PLSave($_GET['value']))) show_error($mpd->errStr);
        break;
    case "update":
        header("Content-Type: text/html; charset=UTF-8");
        echo "<p>"._lang("Updating DB, please wait")."...</p>";
        flush();
        if(is_null($mpd->DBRefresh())) show_error($mpd->errStr);
        echo "<p>"._lang("Database updated").'.</p>
        <script type="text/javascript">
        window.location.href="playlist.php";
        </script>
        <noscript><p><a href="./">'._lang("Back").'</a></p></noscript>';
        break;
}


// Sending some headers to browser
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="REFRESH" content="'.$userConfig['refresh'].';URL=playlist.php" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="../../clientswitcher.css">    		
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/main.css" />
    <script type="text/javascript" src="javascript.php"></script>
    <title>'.$userConfig['title'].'</title>
</head>
<body>';



include ('../../lib/ClientSwitcher.php');

echo '<h1>'.$userConfig['title']."</h1>\n\n".'
<div id="header">
    <h2>';

switch($mpd->state) {
    case MPD_STATE_PLAYING:
        echo _lang("Playing");
        break;
    case MPD_STATE_PAUSED:
        echo _lang("Paused");
        break;
    case MPD_STATE_STOPPED:
        echo _lang("Stopped");
        break;
    default:
        echo _lang("Unknow state");
        break;
}

echo '</h2>
    <ul id="menu">
        <li id="refresh"><a href="playlist.php">'._lang("Refresh").'</a></li>
        <li id="config"><a href="userconfig.php" onclick="ShowConfig(); return false;">'._lang("Configure").'</a></li>
        <li id="update"><a href="playlist.php?action=update">'._lang("Update DB").'</a></li>
        <li id="about"><a href="about.php" onclick="ShowAbout(); return false;">'._lang("About").'</a></li>
        <li id="about"><a href="debug.php" onclick="ShowDebug(); return false;">'._lang("Debug").'</a></li>
    </ul>
';

$vol = $mpd->volume;
$repeat = $mpd->repeat;
$random = $mpd->random;
$crossfade = $mpd->xfade;

if($mpd->state == MPD_STATE_PLAYING || $mpd->state == MPD_STATE_PAUSED)
{
    echo "\n    <div id=\"song\">\n    ";

    $id = $mpd->current_track_id;
    $current = $mpd->playlist[$id];

    if(empty($current['Artist']) && empty($current['Title'])) {
        $title = $current['file'];
        $title = substr($title,strrpos($title,"/")+1);
        $title = substr($title,0,strrpos($title,"."));
    }
    elseif(empty($current['Artist'])) $title = $current['Title'];
    else $title = "({$current['Artist']}) ".$current['Title'];
    $title = GetSongTitle($current);

    $time_min1 = floor($mpd->current_track_position / 60);
    $time_sec1 = $mpd->current_track_position - ($time_min1 * 60);
    if($time_sec1 < 10) $time_sec1 = "0".$time_sec1;
    $time_min2 = floor($mpd->current_track_length / 60);
    $time_sec2 = $mpd->current_track_length - ($time_min2 * 60);
    if($time_sec2 < 10) $time_sec2 = "0".$time_sec2;

    echo '<h3>'.$title."</h3>\n    ";
    if($mpd->bitrate) echo '<span id="bitrate">'.$mpd->bitrate.'kbps</span> ';
    echo '<span id="time">(<span id="time_el_min">'.$time_min1.'</span>:<span id="time_el_sec">'.$time_sec1.
        '</span> / <span id="time_re_min">'.$time_min2.'</span>:<span id="time_re_sec">'.$time_sec2."</span>)</span>";

    if($mpd->state == MPD_STATE_PLAYING) echo '<script type="text/javascript">UpdateTime();</script>';

    echo "</div>\n    ";

    $time_perc = @round(($mpd->current_track_position/$mpd->current_track_length)*100);

    // Showing seek-bar (works also in text only browsers :)
    echo '<noscript><div id="seekbar">'."\n      <span>"._lang("Seek").": [</span>\n      ";
    $active = 'inactive';
    for($i=0; $i < 25; $i++)
    {
        if($mpd->current_track_time == 0) {
            echo '<a class="seek livestream">Livestream</a>';
            break;
        }
        $i_time = round(($i * 4 * $mpd->current_track_length) / 100);
        $i_time_perc = round(($i_time / $mpd->current_track_length) * 100);
        if(($i_time_perc - 5) <= $time_perc && $time_perc <= ($i_time_perc + 5)) $active = "active";

        echo '<a class="seek '.$active.'" href="playlist.php?action=seekto&amp;value='.$mpd->current_track_id.'%20'.$i_time.
            '">'.(($active == 'active') ? "<b>$i_time_perc%</b>" : "$i_time_perc%")."</a> ";
        $active = 'inactive';
    }
    echo "\n      <span>]</span>\n    </div></noscript>\n    ";
    echo '<script type="text/javascript"> '.
         'var trackPos = '.$mpd->current_track_position.'; var trackLength = '.$mpd->current_track_length.';'.
         'ShowSeekBar('.$mpd->current_track_id.'); </script>';

    // Shows some options
    echo "    <ul id=\"options\">\n      ".
        '<li id="repeat"><a title="'._lang("Repeat").'" href="playlist.php?action=repeat&amp;value='.(int)(!$repeat).'" class="'.
        (($repeat) ? 'enabled' : 'disabled').'">'._lang("Repeat")."<span> (".($repeat ? _lang("On") : _lang("Off")).")</span></a></li>\n      ";
    echo '<li id="random"><a title="'._lang("Random").'" href="playlist.php?action=random&amp;value='.(int)(!$random).'" class="'.
        (($random) ? 'enabled' : 'disabled').'">'._lang("Random")."<span> (".($random ? _lang("On") : _lang("Off")).")</span></a></li>\n      ";
    echo '<li id="crossfade"><a title="'._lang("Crossfade").'" href="playlist.php?action=crossfade&amp;value='.(10*(int)(!$crossfade)).'" class="'.
        (($crossfade) ? 'enabled' : 'disabled').'">'._lang("Crossfade")."<span> (".($crossfade ? _lang("On") : _lang("Off")).")</span></a></li>\n    </ul>\n    ";

    if($mpd->state == MPD_STATE_PLAYING) {
        echo '<ul id="commands">'."\n      ".
            '<li id="prev" class="enabled"><a href="playlist.php?action=previous">'._lang("Previous")."</a></li>\n      ".
            '<li id="pause" class="enabled"><a href="playlist.php?action=pause">'._lang("Pause")."</a></li>\n      ".
            '<li id="stop" class="enabled"><a href="playlist.php?action=stop">'._lang("Stop")."</a></li>\n      ".
            '<li id="next" class="enabled"><a href="playlist.php?action=next">'._lang("Next")."</a></li>\n    </ul>";
    }
    else {
        echo '<ul id="commands">'."\n      ".
            '<li id="prev" class="enabled"><a href="playlist.php?action=previous">'._lang("Previous")."</a></li>\n      ".
            '<li id="play" class="enabled"><a href="playlist.php?action=pause">'._lang("Play")."</a></li>\n      ".
            '<li id="stop" class="enabled"><a href="playlist.php?action=stop">'._lang("Stop")."</a></li>\n      ".
            '<li id="next" class="enabled"><a href="playlist.php?action=next">'._lang("Next")."</a></li>\n    </ul>";
    }

}
else {
    // Shows some options
    echo "\n      <ul id=\"options\">\n      ".
        '<li id="repeat"><a title="'._lang("Repeat").'" href="playlist.php?action=repeat&amp;value='.(int)(!$repeat).'" class="'.
        (($repeat) ? 'enabled' : 'disabled').'">'._lang("Repeat")."<span> (".($repeat ? _lang("On") : _lang("Off")).")</span></a></li>\n      ";
    echo '<li id="random"><a title="'._lang("Random").'" href="playlist.php?action=random&amp;value='.(int)(!$random).'" class="'.
        (($random) ? 'enabled' : 'disabled').'">'._lang("Random")."<span> (".($random ? _lang("On") : _lang("Off")).")</span></a></li>\n      ";
    echo '<li id="crossfade"><a title="'._lang("Crossfade").'" href="playlist.php?action=crossfade&amp;value='.(10*(int)(!$crossfade)).'" class="'.
        (($crossfade) ? 'enabled' : 'disabled').'">'._lang("Crossfade")."<span> (".($crossfade ? _lang("On") : _lang("Off")).")</span></a></li>\n    </ul>\n    ";

        echo '<ul id="commands">'."\n      ".
            '<li id="prev" class="disabled">'._lang("Previous")."</li>\n      ".
            '<li id="play" class="enabled"><a href="playlist.php?action=play">'._lang("Play")."</a></li>\n      ".
            '<li id="stop" class="disabled">'._lang("Stop")."</li>\n      ".
            '<li id="next" class="disabled">'._lang("Next")."</li>\n    </ul>";
}

// volume display
if($vol)
{
    echo '<div id="volume">
    <h4>'._lang("Volume").'</h4>';

    echo '<noscript><a href="playlist.php?action=volume&amp;value='.($mpd->volume - 10).'" id="decrease">'._lang("Decrease")."</a>\n    ";
    echo '<span id="volumebar"><span style="width: '.$vol.'%;"></span></span>';
    echo '<a href="playlist.php?action=volume&amp;value='.($mpd->volume + 10).'" id="increase">'._lang("Increase").'</a></noscript>
    <script type="text/javascript"> ShowVolumeBar('.$mpd->volume.'); </script></div>';
}

// display playlist
echo "</div><div id=\"playlist\">\n      <h4>"._lang("Playlist")."</h4>\n      ";

if(count($mpd->playlist) > 0)
{
    echo "<ul id=\"list_commands\">\n        ";
    echo '<li id="suffle"><a href="playlist.php?action=shuffle">'._lang("Shuffle")."</a></li>\n         ".
    '<li id="save"><a href="no_js.php" onclick="SavePlayList(); return false;">'._lang("Save")."</a></li>\n         ".
    '<li id="clear"><a href="playlist.php?action=clear">'._lang("Clear")."</a></li>\n       ".
    '<li id="editpl"><a href="edit_playlist.php" onclick="EditPlayList(); return false;">'._lang("Edit")."</a></li>\n       </ul>\n      ";
    echo '<a id="browse" href="files.php" onclick="ShowFiles(); return false;">'._lang("Music browser")."</a>";

    $start = $mpd->current_track_id - floor($userConfig['playlist_length'] / 2);
    $end = $mpd->current_track_id + ceil($userConfig['playlist_length'] / 2);;
    if($start < 0) {
        $end -= $start;
        $start = 0;
    }
    if($end >= $mpd->playlist_count) {
        $start -= $end - $mpd->playlist_count + 1;
        if($start < 0) $start = 0;
        $end = $mpd->playlist_count - 1;
    }

    echo '<ul id="list">';
    $cl = "odd";
    for($i=$start;$i<=$end;$i++)
    {
        $el = $mpd->playlist[$i];

        echo '<li'.(($i == $mpd->current_track_id) ? ' id="current"' : '').' class="'.$cl.'"><a name="'.$i.'" href="playlist.php?action=seekto&amp;value='.$i.'%200">';
        echo GetSongTitle($el);
        echo "</a></li>\n      ";

        if($cl == "odd") $cl = "even";
        else $cl = "odd";
    }
    echo '</ul></div>';
}
else echo '<a id="browse" href="files.php" onclick="ShowFiles(); return false;">'._lang("Music browser")."</a></div>";


$mpd->Disconnect();

echo '
</body>
</html>';

?>