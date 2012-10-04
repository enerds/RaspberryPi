<?php
// PhpMusicPlayer+
// javascript functions

$no_connect = TRUE;
include_once("functions.inc.php");

echo '
function SavePlayList()
{
    var name=window.prompt("'._lang("Playlist name").':");
    if(name) {
        location.href="playlist.php?action=save&amp;value="+name;
    }
}
function LoadPlayList()
{
    window.open("load_playlist.php","LoadPlayList","width=200,height=360,top=150,left=50,scrollbars=1,location=false");
}
function ShowFiles()
{
    window.open("files.php","Files","width=400,height=360,top=150,left=50,scrollbars=1,location=false");
}
function EditPlayList()
{
    window.open("edit_playlist.php","EditPlayList","width=500,height=300,top=4,left=3,scrollbars=1,location=false");
}
function ShowAbout()
{
    window.open("about.php","About","width=270,height=220,top=150,left=50,scrollbars=1,location=false");
}
function ShowConfig()
{
    window.open("userconfig.php","Config","width=300,height=260,top=150,left=50,scrollbars=1,location=false");
}
function ShowDebug()
{
    window.open("debug.php","Debug","width=300,height=260,top=150,left=50,scrollbars=1,location=false");
}
function UpdateTime()
{
    myTime = window.setTimeout("increaseTime()",1000);
    myTime = clearTimeout();
}
function increaseTime()
{
    var elapsedTimeMin = document.getElementById("time_el_min").innerHTML;
    var elapsedTimeSec = document.getElementById("time_el_sec").innerHTML;
    elapsedTimeSec = parseInt(elapsedTimeSec) + 1;
    if(elapsedTimeSec >= 60) {
        elapsedTimeSec = "00";
        elapsedTimeMin = parseInt(elapsedTimeMin) + 1;
    }
    if(elapsedTimeSec < 10 && elapsedTimeSec > 0) {
        elapsedTimeSec = "0" + elapsedTimeSec;
    }
    document.getElementById("time_el_min").innerHTML = elapsedTimeMin;
    document.getElementById("time_el_sec").innerHTML = elapsedTimeSec;

    remainTime = document.getElementById("time_re_min").innerHTML + document.getElementById("time_re_sec").innerHTML;
    elapsedTime = elapsedTimeMin + elapsedTimeSec;
    if(parseInt(elapsedTime) >= parseInt(remainTime)) {
        if(parseInt(remainTime) > 0 && parseInt(trackLength) > 0) {
            window.location.href="playlist.php";
        }
    }

    var t1 = trackPos;
    trackPos = (parseInt(elapsedTimeMin) * 60) + parseInt(elapsedTimeSec);

    UpdateSeekBar();
    UpdateTime();
}
function SeekTo(trackID)
{
    if(parseInt(trackLength) < 1) {
        alert("'._lang("You can't seek in a livestream").'.");
        return false;
    }
    var seekToPos = window.event.x;
    var barSize = document.getElementById("seekbar_js").offsetWidth;
    posFactor = parseInt(seekToPos) / parseInt(barSize);
    truePos = Math.round(trackLength * posFactor);
    window.location.href = "playlist.php?action=seekto&amp;value=" + trackID + "%20" + truePos;
    return false;
}
function GetSeekLength()
{
    seekPercent = Math.round((parseInt(trackPos) / parseInt(trackLength)) * 100);
    if(seekPercent == "Infinity") return 0;
    return seekPercent;
}
function ShowSeekBar(trackID)
{
    document.write("<div id=\"seekbar_js\"><div onclick=\"SeekTo(" + trackID + ");\">");
    document.write("<span id=\"seekbar_js_active\" style=\"width: " + GetSeekLength(trackPos,trackLength) + "%;\"></span>");
    document.write("</div></div>");
    UpdateSeekBar();
}
function UpdateSeekBar()
{
    var SeekActive = document.getElementById("seekbar_js_active");
    SeekActive.style.width = GetSeekLength() + "%";
}
function VolumeTo()
{
    var volumeToPos = window.event.x;
    var barSize = document.getElementById("volumebar_js").offsetWidth;
    posFactor = parseInt(volumeToPos) / parseInt(barSize);
    truePos = Math.round(100 * posFactor);
    if(truePos > 95) truePos = 100;
    if(truePos < 5) truePos = 1;
    window.location.href = "playlist.php?action=volume&amp;value=" + truePos;
    return false;
}
function ShowVolumeBar(currentPos)
{
    document.write("<div id=\"volumebar_js\"><div onclick=\"VolumeTo();\">");
    document.write("<span id=\"volumebar_js_active\" style=\"width: " + currentPos + "%;\"></span>");
    document.write("</div></div>");
}
';
?>