<?php

require "consts.php";
require "api.php";
require "functions.php";

$cmd = postget('cmd');

if (is_string($cmd)) {
    
    $cmd = stripslashes($cmd);

}

$mpd = new MPD();

if ($cmd != "") {
    //echo 11111111111;
    //dump($_GET);
    //dump($cmd);
    flush();
    echo $mpd->cmd($cmd);

}


?>