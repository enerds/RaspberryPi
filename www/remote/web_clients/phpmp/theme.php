<?php
global $hide, $server, $show_options;

// Font
$fonts["all"] = "sans";

// Background Color
$colors["background"] = "#ffffff";

// Colors for Links
$colors["links"]["link"] = "#0000ff";
$colors["links"]["active"] = "#0000ff";
$colors["links"]["visual"] = "#0000ff";

// Colors for Directories
$colors["directories"]["title"] = "#bbbbff";
$colors["directories"]["body"][0] = "#eeeeff";
$colors["directories"]["body"][1] = "#ddddff";

// Colors for Music Table, with metadata
$colors["music"]["meta"]["title"] = "#88ff88";
$colors["music"]["meta"]["body"][0] = "#eeffee";
$colors["music"]["meta"]["body"][1] = "#ccffcc";
$colors["music"]["meta"]["sort"] = "#aaffaa";

// Colors for the Music Table, without metadata 
$colors["music"]["file"]["title"] = "#88ff88";
$colors["music"]["file"]["body"][0] = "#eeffee";
$colors["music"]["file"]["body"][1] = "#ccffcc";
$colors["music"]["file"]["sort"] = "#aaffaa";

// Colors for Playlist Table
$colors["playlist"]["title"] = "#ffaaaa";
$colors["playlist"]["body"][0] = "#ffdddd";
$colors["playlist"]["body"][1] = "#ffcccc"; 
$colors["playlist"]["current"] = "#88ff88";

// Colors for Login
$colors["login"]["title"] = "#cccccc";
$colors["login"]["body"] = "#eeeeee";

// Colors for Playing
$colors["playing"]["title"] = "#cccccc";
$colors["playing"]["body"] = "#eeeeee";
$colors["playing"]["on"] = "#88ff88";

// Colors for the Output Table
$colors["outputs"]["title"] = "#aaffaa";
$colors["outputs"]["body"][0] = "#ccffcc";
$colors["outputs"]["body"][1] = "#eeffee";

// Colors for Search Music Table without metadata
$colors["search"]["file"]["title"] = "#88ff88";
$colors["search"]["file"]["body"][0] = "#ccffcc";
$colors["search"]["file"]["body"][1] = "#eeffee";
$colors["search"]["file"]["sort"] = "#aaffaa";

// Colors for Search Music Table with metadata
$colors["search"]["meta"]["title"] = "#88ff88";
$colors["search"]["meta"]["body"][0] = "#ccffcc";
$colors["search"]["meta"]["body"][1] = "#eeffee";
$colors["search"]["meta"]["sort"] = "#aaffaa";

// Colors for Server Table
$colors["server"]["title"] = "#cccccc";
$colors["server"]["body"] = "#eeeeee";

// Colors for Stats
$colors["stats"]["title"] = "#cccccc";
$colors["stats"]["body"][0] = "#ffffff";
$colors["stats"]["body"][1] = "#eeeeee";

// Colors for the Steam Table
$colors["stream"]["title"] = "#88ff88";
$colors["stream"]["body"][0] = "#ccffcc";
$colors["stream"]["body"][1] = "#eeffee";
$colors["stream"]["sort"] = "#aaffaa";

// Colors for Volume
$colors["volume"]["title"] = "#cccccc";
$colors["volume"]["body"] = "#eeeeee";
$colors["volume"]["unselected"] = "#eeeeee";
$colors["volume"]["background"] = "#ffffff";
$colors["volume"]["foreground"] = "#000000";

// Colors for Time Progress Bar
$colors["time"]["background"] = "#aaaaaa";
$colors["time"]["foreground"] = "#000000";

// URL Displays
if ($config["use_images"] === true)
{
	// The following are examples of images you could use for phpMp. These are user-supplied.
	$display["playing"]["prev"]["active"] = "<a title=\"Previous\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;command=previous\"><img src=\"images/previous.gif\" border=0></a>";
	$display["playing"]["prev"]["inactive"] = "<img src=\"images/previous_inactive.gif\" border=0>";
	$display["playing"]["play"]["active"] = "<a title=\"Play\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=play\"><img src=\"images/play.gif\" border=0></a>";
	$display["playing"]["play"]["pause"] = "<a title=\"Pause\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=pause\"><img src=\"images/play.gif\" border=0></a>";
	$display["playing"]["play"]["inactive"] = "<img src=\"images/play_inactive.gif\" border=0>";
	$display["playing"]["next"]["active"] = "<a title=\"Next\"  href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=next\"><img src=\"images/next.gif\" border=0></a>";
	$display["playing"]["next"]["inactive"] = "<img src=\"images/next_inactive.gif\" border=0>";
	$display["playing"]["pause"]["active"] = "<a title=\"Pause\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=pause\"><img src=\"images/pause.gif\" border=0></a>";
	$display["playing"]["pause"]["inactive"] = "<img src=\"images/pause_inactive.gif\" border=0>";
	$display["playing"]["stop"]["active"] = "<a title=\"Stop\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=stop\"><img src=\"images/stop.gif\" border=0></a>";
	$display["playing"]["stop"]["inactive"] = "<img src=\"images/stop_inactive.gif\" border=0>";
}
else
{
	$display["playing"]["prev"]["active"] = "[<a title=\"Previous\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=previous\">&lt;&lt;</a>]";
	$display["playing"]["prev"]["inactive"] = "[&lt;&lt;]";
	$display["playing"]["play"]["active"] = "[<a title=\"Play\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=play\">Play</a>]";
	$display["playing"]["play"]["pause"] = "[<a title=\"Play\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=pause\">Play</a>]";
	$display["playing"]["play"]["inactive"] = "[Play]";
	$display["playing"]["next"]["active"] = "[<a title=\"Next\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=next\">&gt;&gt;</a>]";
	$display["playing"]["next"]["inactive"] = "[&gt;&gt;]";
	$display["playing"]["pause"]["active"] = "[<a title=\"Pause\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=pause\">| |</a>]";
	$display["playing"]["pause"]["inactive"] = "[| |]";
	$display["playing"]["stop"]["active"] = "[<a title=\"Stop\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=stop\">Stop</a>]";
	$display["playing"]["stop"]["inactive"] = "[Stop]";
}
?>
