<?php
ob_start();
require "info.php";
require "info2html.php";
require "config.php";
require "utils.php";

/* I have not yet figured out why $_REQUEST is required here, but it is */
$server =	isset( $_REQUEST["server"] )	?	$_REQUEST["server"]	:	"0";
$feature =	isset($_GET["feature"])		?	$_GET["feature"]	:	"";

// This will load the data for a streamurl
if( ! empty( $feature ) && ( strcmp( $feature, "stream-icy" ) == "0" || strcmp( $feature, "stream-shout" ) == "0" ))
{
	require "xml-parse.php";
}

if( sizeof( $servers ) > 1 && $config["server_in_title"] === true )
{
	$config["title"] .= strlen( $servers[$server][2] ) > "0" ? " ({$servers[$server][2]})" : " ({$servers[$server][0]})";
}

$host = $servers[$server][0];
$port = $servers[$server][1];

// This variable is a argument to $_COOKIE[*] to make it where your cookies
// won't go any old place, but only to the host/port that you are speaking to
$hostport = $host . ":" . $port;

// This will extract the needed GET/POST variables
$arg =		isset( $_GET["arg"])		?	$_GET["arg"]		:	"";
$arg2 =		isset( $_GET["arg2"])		?	$_GET["arg2"]		:	"";
$body =		isset( $_GET["body"])		?	$_GET["body"]		:	"";
$command =	isset( $_GET["command"])	?	$_GET["command"]	:	"";
$feature =	isset( $_GET["feature"] )	?	$_GET["feature"]	:	"";
$remember =	isset( $_REQUEST["remember"] )	?	$_REQUEST["remember"]	:	"";
$passarg =	isset( $_REQUEST["passarg"] )	?	$_REQUEST["passarg"]	:	"";
$stream =	isset( $_GET["stream"] )	?	$_GET["stream"]		:	"";
$streamurl =	isset( $_GET["streamurl"] )	?	$_GET["streamurl"]	:	"";
$inline =	isset( $_GET["inline"] )	?	$_GET["inline"]		:	"";
$streamfilter =	isset( $_GET["streamfilter"] )	?	$_GET["streamfilter"]	:	"";
$sfa =		isset($_GET["sfa"])		?	$_GET["sfa"]		:	"";

if(isset($_POST["body"])) {
	$body = $_POST["body"];
	$add_all = isset( $_POST["add_all"] ) ? $_POST["add_all"] : "" ;
}

// $inline is a simple searcher
if( isset($inline)) {
	$search = isset($_GET["search"]) ? $_GET["search"] : "";
}

if( ! empty( $body ))
{
	if( strcmp( $body, "main" ) == "0" )
	{
		if( ! empty($inline)) {
			$arg = $inline;
			$feature = "search";
			if(empty($search) || ! isset($search)) {
				$search = "any";
			}
			$search_fields = $config["display_fields"];
		}
		else if( ! empty( $feature ))
		{
			if( strcmp( $feature, "search" ) == "0" )
			{
				$search = isset( $_GET["search"] ) ? $_GET["search"] : "";
				$find = isset( $_GET["find"] ) ? $_GET["find"] : "";
				$search_fields = $config["display_fields"];
			}
		}
		$delete = isset( $_GET["delete"] ) ? $_GET["delete"] : "no";
		$dir = isset( $_GET["dir"] ) ? $_GET["dir"] : "";
		$save = isset( $_GET["save"] ) ? $_GET["save"] : "";
		$ordered = isset( $_GET["ordered"] ) ? $_GET["ordered"] : "";
	}
	else if( strcmp( $body, "playlist" ) == "0" )
	{
		// Playlist Hiding Stuff
		if( isset( $_GET["hide"] ))
		{
			$hide = $_GET["hide"];
		}
		else if( $config["use_cookies"] === true && isset( $_COOKIE["phpMp_playlist_hide"][$hostport] ))
		{
			$hide = $_COOKIE["phpMp_playlist_hide"][$hostport];
		}

		if( isset( $hide ) && $config["use_cookies"] === true )
		{
			setcookie("phpMp_playlist_hide[$hostport]", $hide);
		}
		else
		{
			$hide = 1;
		}

		$show_options = isset( $_GET["show_options"] ) ? $_GET["show_options"] : "0";
	}
}
else
{
	$logout = isset( $_GET["logout"] ) ? $_GET["logout"] : "";
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=UTF-8");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">";
echo "<html><head><link rel=\"shortcut icon\" href=\"mpd-favicon.ico\">";

// Open the connection
$fp = fsockopen( $host, $port, $errno, $errstr, 10 );

// If there's no connection, and servers exist goto the server menu
if(! is_resource( $fp ))
{
	if(isset($servers) && (sizeof($servers) > 1))
	{
		include "features.php";
		server( $servers, $host, $port, $colors["server"] );
	}
	die("{$errstr} ({$errno})<br>");
}

// Lets go ahead and get the MPD version while we can
$MPDversion = initialConnect($fp);
$MPDversion = trim($MPDversion);

// Password stuff
if( !empty( $logout ))
{
	setcookie( "phpMp_password[$hostport]", "" );
}
else if( isset( $_COOKIE["phpMp_password"][$hostport] ))
{
	$passarg = $_COOKIE["phpMp_password"][$hostport];
}

if( strlen( $passarg ) > "0" )
{
	$has_password = true;
	fputs( $fp, "password \"$passarg\"\n" );
	while ( ! feof( $fp ))
	{
		$got = fgets( $fp, 1024 );
		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			if( isset( $remember ) && strcmp( $remember, "true" ) == "0" )
			{
				setcookie( "phpMp_password[$hostport]", $passarg, time()+60*60*24*30 );
			}
			else if( ! $_COOKIE["phpMp_password"][$hostport] )
			{
				setcookie( "phpMp_password[$hostport]", $passarg );
			}
			break;
		}
		if( strncmp( "ACK", $got, strlen( "ACK" )) == "0" )
		{
			echo "Password Incorrect, press back button";
			break;
		}
	}
}

if( ! isset( $has_password ))
{
	$has_password = false;
}

$commands = getCommandInfo( $fp, $MPDversion );
if( $commands["status"] === true )
{
	$status = getStatusInfo( $fp );
}

if( ! empty( $command ))
{
	doCommand( $fp, $arg, $arg2, $command, $config["overwrite_playlists"], $status );
	$status = getStatusInfo( $fp ); 
}

// This needs to go down here to give the cookies, server time to load
include "theme.php";
if( $commands["status"] === true && $config["smart_updating"] === true && ! empty($status["time"])) {
	$var1 = strtok($status["time"],":");
	$var2 = strrchr($status["time"],":");
	$var2 = str_replace(":","",$var2);
	$sub = ($var2-$var1);
	if($sub<$config["refresh_freq"] && $sub>0) {
		$config["refresh_freq"] = ($var2-$var1);
	}
}

// There might be more that's would prevent phpMp from loading, rather than taking time to figure it out, we'll wait for reports.
if( $commands["listall"] === false || $commands["lsinfo"] === false || $commands["playlist"] === false || $commands["playlistinfo"] === false || $commands["stats"] === false )
{
	include "features.php";
	setcookie( "phpMp_password[$hostport]", "" );
	unset( $has_password );

	echo "<b>Error:</b> Can't load phpMp due to not having permission to the following commands: ";
	if( $commands["listall"] === false )
	{
		echo "listall ";
	}
	if( $commands["lsinfo"] === false )
	{
		echo "lsinfo ";
	}
	if( $commands["playlist"] === false )
	{
		echo "playlist ";
	}
	if( $commands["playlistinfo"] === false )
	{
		echo "playlistinfo";
	}

	echo "<br>";

	login( $fp, $config, $colors["login"], $server, $arg, $dir, $remember );
	server( $servers, $host, $port, $colors["server"], $config, $commands );
}
// This will serve as our front page if called w/o $body
else if( empty( $body ) && empty( $feature ))
{
	unset( $hostport );
	echo "<title>{$config["title"]}</title>";
	echo "</head>";

	echo "<frameset {$config["frames_layout"]}>";
	$mainurl = "index.php?body=main&amp;server=$server";
	if($inline) {
		$mainurl .= "&amp;inline=$inline&amp;search=$search";
	}

	echo "<frame name=\"main\" src=\"$mainurl\" frameborder={$config["frame_border_size"]}>";
	echo "<frame name=\"playlist\" src=\"index.php?body=playlist&amp;server=$server\" frameborder=0>";
	echo "<noframes>NO FRAMES ... try phpMp+</noframes>";
	echo "</frameset>";
}
else
{
	unset( $hostport );
	if( strcmp( $body, "playlist" ) == "0" )
	{
		echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"{$config["refresh_freq"]};URL=index.php?body=playlist&amp;hide=$hide&amp;show_options=$show_options&amp;server=$server\">";
	}
	if( isset( $status["updating_db"] ) && strcmp( $body, "main" ) == "0" )
	{
		$sort =	isset( $_GET["sort"] ) ? $_GET["sort"] : $config["default_sort"];
		echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"{$config["refresh_freq"]};URL=index.php?body=main&amp;sort=$sort&amp;dir=$dir&amp;ordered=$ordered&amp;server=$server\">";
	}
	echo "<title>{$config["title"]} - $body</title>";

	// I would _much_ rather have a php generated stylesheet
	echo "<style type=\"text/css\">";
	echo "* { font-family: {$fonts["all"]}; }";
	echo "A:link, A:visited, A:active { text-decoration: none; border-style: none none none none; }";
	echo "a.green:link, a.green:active, a.green:visited, a.green:hover {background: {$colors["playing"]["on"]}}";
	echo "table { width: 100%; border-style: none }";
	echo "form { padding: 0; margin: 0 }";
	echo "tr.noborder td { border: none; border-style: none; border-color: {$colors["directories"]["body"][0]} }";
	echo "</style>";
	echo "</head>";

	echo "<body link={$colors["links"]["link"]} ";
	echo "vlink={$colors["links"]["visual"]} "; 
	echo "alink={$colors["links"]["active"]} ";
	echo "bgcolor={$colors["background"]}>";

	echo "<!-- The Header (index.php) Ends Here, Body begins here -->";

	include $body . ".php";

	echo "</body>";
}
echo "</html>";
fclose( $fp );
ob_end_flush();
?>
