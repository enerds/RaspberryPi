<?php
// PhpMusicPlayer+
// Copyleft (c) 2004 BohwaZ, GNU/GPL
// General file

# for debugging
error_reporting(E_ALL);

$version = "0.2.3";     # Version of this client

$langs = array(         # Available languages
    "fr"    =>  "Fran�ais",
    "en"    =>  "English");

// Display a string in chosen language
function _lang($string) {
    if(empty($GLOBALS["lang_strings"][$string]))
        return htmlspecialchars($string);
    else
        return htmlspecialchars($GLOBALS["lang_strings"][$string]);
}

// Transform a seconds time in human-readable time
function secToTimeStr($secs)
{
    $minute = 60;
    $hour = $minute*60;
    $day = 24*$hour;

    $days = (int)($secs/$day);
    $secs2 = $secs;
    $secs2 -= $days * $day;

    $hours = (int)($secs2/$hour);
    $secs2 -= $hours * $hour;

    $minutes = (int)($secs2/$minute);
    $secs2 -= $minutes*$minute;
    $seconds = $secs2;
    if($seconds<10) $seconds = "0$seconds";
    if($minutes<10) $minutes = "0$minutes";
    if($hours<10) $hours = "0$hours";

    $timestring='';
    if (round($days))    $timestring .= round($days).' '._lang("days").' ';
    if (round($hours))   $timestring .= round($hours)."h ";
    if (round($minutes)) $timestring .= round($minutes)."m";
    if (!round($minutes)&&!round($hours)&&!round($days)) $timestring.=" ".round($seconds)."s";
    return $timestring;
}

function show_error($err) {
    echo "<h1>"._lang("Error")."</h1>";
    echo '<p>'.$err.'</p>';
    echo '<p><a href="playlist.php">'._lang("Back").'</a></p>';
    exit;
}

function get_pref_language_array($str_http_languages)
{
  $langs = explode(',',$str_http_languages);
  $qcandidat = 0;
  $nblang = count($langs);

  for ($i=0; $i<$nblang; $i++)
  {
    for ($j=0; $j<count($langs); $j++) {
      $lang = trim($langs[$j]);

      if (!strstr($lang, ';') && $qcandidat != 1) {
        $candidat = $lang;
        $qcandidat = 1;
        $indicecandidat = $j;
      }
      else {
        $q = ereg_replace('.*;q=(.*)', '\\1', $lang);

        if ($q > $qcandidat) {
          $candidat = ereg_replace('(.*);.*', '\\1', $lang); ;
          $qcandidat = $q;
          $indicecandidat = $j;
        }
      }
    }

    $resultat[$i] = $candidat;

    $qcandidat=0;
    unset($langs[$indicecandidat]);
    $langs = array_values($langs);
  }
  return $resultat;
}

function SaveUserConfig($conf)
{
    setcookie("phpmpplus_userconfig",base64_encode(serialize($conf)),time()+(3600*24*365));
    #print_r($conf); exit;
    return TRUE;
}

function GetUserConfig()
{
    if(!array_key_exists("phpmpplus_userconfig",$_COOKIE)) return FALSE;
    $conf = $_COOKIE["phpmpplus_userconfig"];
    $conf = base64_decode($conf);
    $conf = unserialize($conf);
    foreach($GLOBALS['cfg'] as $key=>$val) {
        if(strchr($key,"password")) continue;
        if(empty($conf[$key])) $conf[$key] = $val;
    }
    return $conf;
}

function GetSongTitle($songInfo)
{
    $display = $GLOBALS['userConfig']['song_display'];
    $songInfo['filename'] = substr($songInfo['file'],strrpos($songInfo['file'],"/")+1);
    $songInfo['filename'] = substr($songInfo['filename'],0,strrpos($songInfo['filename'],"."));
    $songInfo['filename'] = str_replace("_"," ",$songInfo['filename']);
    $replace = array(
        "artist"    =>  array_key_exists("Artist",$songInfo) ? $songInfo['Artist'] : '',
        "album"     =>  array_key_exists("Album",$songInfo) ? $songInfo['Album'] : '',
        "file"      =>  $songInfo['file'],
        "track"     =>  array_key_exists("Track",$songInfo) ? $songInfo['Track'] : '',
        "title"     =>  array_key_exists("Title",$songInfo) ? $songInfo['Title'] : '',
        "filename"  =>  $songInfo['filename']);

    $display = explode("??",$display);
    if(empty($songInfo['Artist']) && empty($songInfo['Title']))
    {
        $display = $display[1];
        if(empty($display)) $display = "file";
    }
    else
        $display = $display[0];

    $out = strtr($display,$replace);
    return $out;
}

// END OF FUNCTIONS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if(!file_exists("config.inc.php") && !isset($is_config)) header("location: config.php");

@include_once("config.inc.php");     // Including configuration
include_once("../mpd.class/mpd.class.php");      // And MPD Class

echo '<span style="color:grey">connected to '.$cfg["host"].'</span><br>';

// For previous versions
if(empty($cfg["default_lang"])) $cfg["default_lang"] = "en";

$userConfig = GetUserConfig();
#print_r($_COOKIE);
#SaveUserConfig(array("lang"=>"fr"));

// So what's your lang?
if(empty($userConfig["lang"]))
{
    $lang = get_pref_language_array($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    $lang = $lang["0"];
    if(empty($lang) || !in_array($lang,array_flip($langs)))
        $lang = $cfg["default_lang"];
    $userConfig["lang"] = $lang;
    SaveUserConfig($userConfig);
}
else $lang = $userConfig["lang"];

if(file_exists("lang/".$lang.".php"))
    include_once("lang/".$lang.".php");

if(!isset($no_connect) && isset($cfg["host"]) && isset($cfg['port']))
{
    if(!empty($userConfig['password'])) $password = $userConfig['password'];
    else $password = NULL;

    // Connecting to MPD Server
    $mpd = new mpd($cfg["host"],$cfg["port"],$password);

    // If connexion fails, tell it
    if(!$mpd->connected)
    {
        if(strchr($mpd->errStr,"incorrect password")) {
            $userConfig["password"] = "";
            SaveUserConfig($userConfig);
            $msg = '<p>'._lang("It seems your password is incorrect. It has been reset, please try another one.").'</p>';
        }
        else $msg = '<p>'.$mpd->errStr.'</p>';

        header("Content-Type: text/html; charset=UTF-8");
        echo '<p>'._lang("Connexion error. Can't connect to Music Player Daemon.").'</p>'.$msg;
        echo '<p><a href="playlist.php">'._lang("Reload").'</a> - ';
        echo '<a href="config.php" onclick="window.open(\"config.php\",\"Config\",\"width=300,height=260,top=150,left=50,scrollbars=1,location=false\"); return false;">';
        echo _lang("Configure").'</a></p>';
        exit;
    }
}
?>