/* no longer used
function detectUTF8($string)
{
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);
}

function cp1251_utf8( $sInput )
{
    $sOutput = "";

    for ( $i = 0; $i < strlen( $sInput ); $i++ )
    {
        $iAscii = ord( $sInput[$i] );

        if ( $iAscii >= 192 && $iAscii <= 255 )
            $sOutput .=  "&#".( 1040 + ( $iAscii - 192 ) ).";";
        else if ( $iAscii == 168 )
            $sOutput .= "&#".( 1025 ).";";
        else if ( $iAscii == 184 )
            $sOutput .= "&#".( 1105 ).";";
        else
            $sOutput .= $sInput[$i];
    }
    
    return $sOutput;
}

function encoding($string){
    if (function_exists('iconv')) {    
        if (@!iconv('utf-8', 'cp1251', $string)) {
            $string = iconv('cp1251', 'utf-8', $string);
        }
        return $string;
    } else {
        if (detectUTF8($string)) {
            return $string;        
        } else {
            return cp1251_utf8($string);
        }
    }
}

function setNotSetSongFields($song) {
	if(isset($song["Title"])) {
		if(!isset($song["Track"])) $song["Track"] = "";
		if(!isset($song["Album"])) $song["Album"] = "";
		if(!isset($song["Artist"])) $song["Artist"] = "";
	}

	return $song;
}


function getStatusInfo(&$mpd) {

	$return = $mpd->SendCommand($command);
	$array_return = explode("\n",$return);	
	foreach ($array_return as $got) {

		$got = preg_replace("/\n/","",$got);
		$el = strtok($got,":");
		$ret["$el"] = strtok("\0");
		$ret["$el"] = preg_replace("/^ /","",$ret["$el"]);
	}
	if(!isset($ret)) $ret = array();
	return $ret;
}


function decodeHTML($string) {
	$string = preg_replace("/\%26/","&",$string);
	$string = preg_replace("/\%20/"," ",$string);
	$string = preg_replace("/\%2D/","-",$string);
	$string = preg_replace("/\%2B/","+",$string);
	$string = preg_replace("/\%23/","#",$string);
	$string = preg_replace("/\%27/","'",$string);
	$string = preg_replace("/\%22/","\"",$string);
	$strng = addSlashes($string);
	return $string;
}

function sanitizeForURL($str) {
	$url = stripslashes($str);
	$url = preg_replace("/\&/","%26",$url);
	$url = preg_replace("/ /","%20",$url);
	$url = preg_replace("/-/","%2D",$url);
	$url = preg_replace("/\+/","%2B",$url);
	$url = preg_replace("/#/","%23",$url);
	$url = preg_replace("/\'/","%27",$url);
	$url = preg_replace("/\"/","%22",$url);
	return $url;
}

function sanitizeForPost($str) {
	$url = $str;
	$url = preg_replace("/\&/","%26",$url);
	$url = preg_replace("/ /","%20",$url);
	$url = preg_replace("/-/","%2D",$url);
	$url = preg_replace("/\+/","%2B",$url);
	$url = preg_replace("/#/","%23",$url);
	$url = preg_replace("/\'/","%27",$url);
	$url = preg_replace("/\"/","%22",$url);
	return $url;
}


if(strlen($dir)>0) 	{ $lsinfo = getLsInfo($mpd,"lsinfo \"$dir\"\n"); }
else 				{ $lsinfo = getLsInfo($mpd,"lsinfo\n"); }


// lsinfo2musicTable should start here

$dcount = count($lsinfo["dir"]);
if($dcount) usort($lsinfo["dir"],"strcasecmp");
$dic = 0;
for($i=0;$i<$dcount;$i++) {
	$dirent = $lsinfo["dir"][$i];
	$dirstr = $dirent;
	$dirss = split("/",$dirstr);
	if(count($dirss)==0) 
	$dirss[0] = $dirstr;
	$dirss[0] = $dirss[count($dirss)-1];
	$dirstr = sanitizeForURL($dirstr);
	$dcol = $colors["directories"]["body"][$i%2];
	$dprint[$i] = "<tr bgcolor=\"$dcol\"><td>";
	$fc = strtoupper(mbFirstChar($dirss[0]));
	if($dic==0 || $dindex[$dic-1]!=$fc) {
		$dindex[$dic] = $fc;
		$foo = $dindex[$dic];
		$dic++;
		$dprint[$i].="<a name=d$foo />";
	}
	$dprint[$i].="<a href=\"playlist.php?add_dir=$dirstr\" ><img class=\"big\" src=\"images/playlist.gif\" alt=\"add to playlist\" title=\"add to playlist\"></a> <a href=\"main.php?sort=$sort&amp;dir=$dirstr\">".short_title($dirss[0],30)."</a></td></tr>\n";
	
}
if(!isset($dindex)) $dindex = array();

//var_dump($mpd->SendCommand("lsinfo \"$dir\"\n") );

list($mprint,$mindex,$add_all) 	= lsinfo2musicTable($lsinfo,$sort,$dir_url);
displayDirectory($dir,$sort,"Current Directory",count($mprint),count($pprint));
  

 
// begin printDirectoryTable
// dcount -> is the number of directories
// dprint -> array, has dcount elements, just do print $dprint[$i]
//           to print output for that directory, it was formatted
//	    in lsinfo2directoryTable (this parses input from 
//           lsinfo and make the $dprint's for output)
// dindex -> these are the links etc for the index elements point too
// printIndex -> function that takes $dinex and prints all the links
//               for the indexes

if($dcount) {
	//print "<br>\n";
	print "<table border=0 cellspacing=1 bgcolor=\"".$colors["directories"]["title"]."\">\n";
	//print "<tr><td nowrap><b>Directories</b>\n";
	//printIndex($dindex,"","d");
	//print "</td></tr>\n";
	//print "<tr><td><table border=0 cellspacing=1 bgcolor=\"".$colors["directories"]["body"][1]."\">\n";
	for($i=0;$i<$dcount;$i++) {print($dprint[$i]); }
	//print "</table></td></tr>";
	print "</table>\n";
}

// end of printDirectoryTable

printMusicTable($mprint,"main.php?dir=$dir_url",$add_all,$mindex);

// you get the playlists only when you use lsinfo without a dir
$lsinfo 				= getLsInfo($mpd,"lsinfo\n");
list($pprint,$pindex) 	= lsinfo2playlistTable($lsinfo,$sort);
printPlaylistTable($pprint,$pindex);


//displayStats($dir,$sort);
displayUpdate($dir,$sort);


function displayDirectory($dir,$sort,$title,$music,$playlists) {
	global $colors,$has_password;
	$dir_url = sanitizeForURL($dir);
	print "<table border=0 cellspacing=1 bgcolor=\"".$colors["directories"]["title"]."\" width=\"100%\">\n";
	print "<tr><td>";
	//print "<b>$title</b>\n";
	//if($music) print "(<a href=\"#music\">Music</a>) ";
	if($music) print "Folder ";
	//if($playlists) print "(<a href=\"#playlists\">Playlists</a>) ";
	if($playlists) print "Playlists";
	print "</td><td align=right>";
	//print "[<a href=\"login.php?dir=$dir_url&amp;sort=$sort\">";
	//if($has_password) 
	//	print "Logout</a>]\n";
	//else
	//	print "Login</a>]\n";
	//
	//print "[<a href=\"stream.php?dir=$dir_url&amp;sort=$sort\">Stream</a>]\n";
	//print "[<a href=\"search.php?dir=$dir_url&amp;sort=$sort\">";
	//print "Search</a>]
	print "</td></tr>\n";
	print "<tr bgcolor=\"";
	print $colors["directories"]["body"][0];
	print "\"><td colspan=2>";
	$dirs = split("/",$dir);
	print "<a href=\"main.php?sort=$sort&amp;dir=/\">Music</a>";
	$build_dir = "";
	for($i=0;$i<count($dirs)-1;$i++) {
		if($i>0 && $i<(count($dirs)-1)) $build_dir.="/";
		$dirs[$i] = stripslashes($dirs[$i]);
		$build_dir.="$dirs[$i]";
		$build_dir = sanitizeForURL($build_dir);
		print " / <a href=\"main.php?sort=$sort&amp;dir=$build_dir\">$dirs[$i]</a>";
	}
	if($i>0) $build_dir.="/";
	if(strlen($dir)>0) {
		$dirs[$i] = stripslashes($dirs[$i]);
		$build_dir.="$dirs[$i]";
		$build_dir = sanitizeForURL($build_dir);
		print " / <a href=\"main.php?sort=$sort&amp;dir=$build_dir\">$dirs[$i]</a>";
	}
	print "</td></tr></table>\n";
}

function displayUpdate($dir,$sort) {
	$dir_url = sanitizeForURL($dir);
	print "<table width=\"100%\"><tr><td><small>[";
	print "<a href=\"update.php?dir=$dir_url&amp;sort=$sort\">";
	print "Update</a>] - Update Music Database (scans music directory for changes)";
	print "</small></td></tr></table>\n";
}

function displayStats($dir,$sort) {
	$dir_url = sanitizeForURL($dir);
	print "<br><table width=\"100%\"><tr><td><small>[";
	print "<a href=\"stats.php?dir=$dir_url&amp;sort=$sort\">";
	print "Stats</a>] - Display MPD Stats";
	print "</small></td></tr></table>\n";
}


function mbFirstChar($str) {
	$i = 1;
	$ret = "$str[0]";
	while($i < strlen($str) && ord($str[$i]) >= 128  && ord($str[$i]) < 192) {
		$ret.=$str[$i];
		$i++;
	}
	return $ret;
}

function readM3uFile($fp) {
	$add = array();
	$i = 0;

	while(!feof($fp)) {
		$url = fgets($fp,4096);
		$url = preg_replace("/\n$/","",$url);
		if(preg_match("/^[a-z]*:\/\//",$url)) {
			$add[$i] = $url;
			$i++;
		}
	}

	return $add;
}

function readPlsFile($fp) {
	$add = array();
	$i = 0;

	while(!feof($fp)) {
		$line = fgets($fp,4096);
		if(preg_match("/File[0-9]*=/",$line)) {
			$url = preg_replace("/^File[0-9]*=/","",$line);
			$url = preg_replace("/\n$/","",$url);
			if(preg_match("/^[a-z]*:\/\//",$url)) {
				$add[$i] = $url;
				$i++;
			}
		}
	}

	return $add;
}

// obsolete (old code)
 

function lsinfo2playlistTable($lsinfo,$sort) {
	$pic = 0;
	$pcount = count($lsinfo["playlist"]);
	if($pcount) usort($lsinfo["playlist"],"strcasecmp");
	for($i=0;$i<$pcount;$i++) {
		$dirent = $lsinfo["playlist"][$i];
		$dirstr = $dirent;
		$dirss = split("/",$dirstr);
		if(count($dirss)==0) 
		$dirss[0] = $dirstr;
		$dirss[0] = $dirss[count($dirss)-1];
		$dirstr = sanitizeForURL($dirstr);
		$fc = strtoupper(mbFirstChar($dirss[0]));
		if($pic==0 || $pindex[$pic-1]!=$fc) {
			$pindex[$pic] = $fc;
			$foo = $pindex[$pic];
			$pic++;
			$pprint[$i] = "<a name=\"p$foo\" id=\"p$foo\" />";
		}
		else {
			$pprint[$i] = "";
		}
		$pprint[$i].="<tr bgcolor=\"".$colors["playlist"]["body"]."\"><td class=\"big\"><a href=\"playlist.php?command=load&amp;arg=$dirstr\"><img src=\"images/load.gif\" class=\"big\" alt=\"load\" title=\"load\"></a></td><td> $dirss[0] </td><td class=\"big\"><a href=\"main.php?sort=$sort&amp;command=rm&amp;arg=$dirstr\"><img src=\"images/delete.gif\" class=\"big\" alt=\"delete\" title=\"delete\"></a></td></tr>\n";
	}
	if(!isset($pprint)) $pprint = array();
	if(!isset($pindex)) $pindex = array();
	return array($pprint,$pindex);
}

function lsinfo2musicTable($lsinfo,$sort,$dir_url) {
	global $sort_array, $song_seperator, $filenames_only,$colors;
	global $unknown_string;
	$color = $colors["music"]["body"];
	$mic = 0;
	$mcount = count($lsinfo["music"]);
	if($mcount) usort($lsinfo["music"],"msort");
	$add_all = "";
	for($i=0;$i<$mcount;$i++) {
		$dirent = $lsinfo["music"][$i]["file"];
		$dirstr = $dirent;
		$dirss = split("/",$dirstr);
		if(count($dirss)==0) 
			$dirss[0] = $dirstr;
		$dirss[0] = $dirss[count($dirss)-1];
		if($i<$mcount-1) $add_all .= addslashes($dirstr) . $song_seperator;
		else $add_all .= $dirstr;
		$dirstr = sanitizeForURL($dirstr);
		$col = $color[$i%2];
		if($filenames_only!="yes" && isset($lsinfo["music"][$i]["Title"]) && $lsinfo["music"][$i]["Title"]) {
			if(strcmp($sort_array[0],"Track")) {
				if(isset($lsinfo["music"][$i][$sort_array[0]]) && strlen($lsinfo["music"][$i][$sort_array[0]]) && ($mic==0 || $mindex[$mic-1]!=strtoupper(mbFirstChar($lsinfo["music"][$i][$sort_array[0]])))) {
					$mindex[$mic] = strtoupper(mbFirstChar($lsinfo["music"][$i][$sort_array[0]]));
					$foo = $mindex[$mic];
					$mic++;
					$mprint[$i] = "<a name=\"m$foo\" id=\"m$foo\" />";
				}
				else {
					$mprint[$i] = "";
				}
			}
			else {
				if(isset($foo)) unset($foo);
				if(isset($lsinfo["music"][$i][$sort_array[0]])) {
					$foo = strtok($lsinfo["music"][$i][$sort_array[0]],"/");
				}
				if(isset($foo) && ($mic==0 || 0!=strcmp($mindex[$mic-1],$foo))) {
					$mindex[$mic] = $foo;
					$mic++;
					$mprint[$i] = "<a name=\"m$foo\" id=\"m$foo\" />";
				}
				else {
					$mprint[$i] = "";
				}
			}
			//$mprint[$i] = "<tr bgcolor=$col><td width=0>$mprint[$i][<a href=\"playlist.php?command=add&amp;arg=$dirstr\">add</a>]</td><td>";
			$mprint[$i] = "<tr bgcolor=$col><td>$mprint[$i]<a href=\"playlist.php?command=add&amp;arg=$dirstr\"><img src=\"images/playlist.gif\" class=\"small\"></a></td><td>";
			if(!isset($lsinfo["music"][$i]["Artist"])) {
				$mprint[$i].= $unknown_string . "</td><td>";
			}
			else {
				$artist_url = sanitizeForURL($lsinfo["music"][$i]["Artist"]);
				$mprint[$i].= "<a href=\"find.php?find=artist&amp;arg=$artist_url&amp;sort=$sort&amp;dir=$dir_url\">";
				$mprint[$i].= substr($lsinfo["music"][$i]["Artist"],0,15) . "</a></td><td>";
			}
			$mprint[$i].= $lsinfo["music"][$i]["Title"] . "</td><td>";
			if(!isset($lsinfo["music"][$i]["Album"])) {
				$mprint[$i].= $unknown_string . "</td><td>";
			}
			else {
				$album_url = sanitizeForURL($lsinfo["music"][$i]["Album"]);
				$mprint[$i].= "<a href=\"find.php?find=album&amp;arg=$album_url&amp;sort=$sort&amp;dir=$dir_url\">";
				$mprint[$i].= substr($lsinfo["music"][$i]["Album"],0,15) . "</a></td><td>";
			}
			if(!isset($lsinfo["music"][$i]["Track"])) {
				$mprint[$i].= $unknown_string . "</td></tr>";
			}
			else {
				$mprint[$i].= substr($lsinfo["music"][$i]["Track"],0,15) . "</td></tr>\n";
			}
		}
		else {
			if($mic==0 || $mindex[$mic-1]!=strtoupper($dirss[0][0])) {
				$mindex[$mic] = strtoupper($dirss[0][0]);
				$foo = $mindex[$mic];
				$mic++;
				$mprint[$i] = "<a name=\"m$foo\" id=\"m$foo\" />";
			}
			else {
				$mprint[$i] = "";
			}
			$short_name = short_title($dirss[0]);
			$mprint[$i] = "<tr bgcolor=\"$col\"><td>$mprint[$i]<a href=\"playlist.php?command=add&amp;arg=$dirstr\"><img class=\"small\" src=\"images/playlist.gif\"></a></td><td><span class=\"sf\">".$short_name."</span></td></tr>\n";
		}
	}
	if(!isset($mprint)) $mprint = array();
	if(!isset($mindex)) $mindex = array();
	return array($mprint,$mindex,$add_all);
}

function printIndex($index,$title,$anc) {
	if(count($index)) {
		print "$title: [ ";
		for($i=0;$i<count($index);$i++) {
			$foo = $index[$i];
			print "<a href=\"#$anc$foo\">$foo</a>\n";
		}
		print "]<br>\n";
	}
}

function printMusicTable($mprint,$url,$add_all,$mindex) {
	global $filenames_only, $colors, $use_javascript_add_all,$sort_array;
	if(count($mprint)>0) {
		print "<br>\n";
 		if($use_javascript_add_all=="yes") {
			$add_all = sanitizeForPost($add_all);
			print "<form style=\"padding:0;margin:0;\" name=\"add_all\" method=\"post\" action=\"playlist.php\">";
			print "<input type=hidden name=\"add_all\" value=\"$add_all\">";
			print "<table border=0 cellspacing=1 bgcolor=\"".$colors["music"]["title"]."\" width=\"100%\">\n";
			print "<tr><a name=\"music\" id=\"music\" /><td colspan=4 nowrap>\"><b>Songs</b>\n";
			print "(<a href=\"javascript:document.add_all.submit()\">";
			print "add all</a>)\n";
			//printIndex($mindex,"","m");
			print "</td></tr>\n";
		}
		else {
			$add_all = sanitizeForUrl($add_all);
			print "<table border=0 cellspacing=1 bgcolor=\"".$colors["music"]["title"]."\" >\n";
			print "<tr><td>\n";
			print " <a href=\"".$_SERVER["PHP_SELF"]."?add_all=$add_all\">";
			//print "add all</a>)\n";
			print "<img src=\"images/playlist.gif\" class=\"small\" title=\"add all\" alt=\"add all\"></a> \n";
			//printIndex($mindex,"","m");
			print "<td><b>Songs</b></td></tr>\n";
		}
		//print "<tr><td>\n"; 
		//print "<table border=0 cellspacing=1 bgcolor=\"".$colors["music"]["body"][1]."\" width=\"100%\">\n";
		if($filenames_only!="yes") {
			print "<tr bgcolor=\"".$colors["music"]["sort"]."\">";
			$cols[0] = "Artist";
			$cols[1] = "Title";
			$cols[2] = "Album";
			$cols[3] = "Track";
			for($i=0;$i<count($cols);$i++) {
				$new_sort = pickSort("$cols[$i]");
				if($cols[$i]==$sort_array[0])
					$cols[$i] = "<b>$cols[$i]</b>";
				print "<td><a href=\"$url&amp;sort=$new_sort\">$cols[$i]</a></td>";
			}
			print "</tr>\n";
		}
		for($i=0;$i<count($mprint);$i++) print $mprint[$i];
		//print "</td></tr></table>\n";
		print "</table>\n";
		if($use_javascript_add_all=="yes")
			print "</form>";
	}
}

function printPlaylistTable($pprint,$pindex) {
	if(count($pprint)) {
		print "<br>\n";
		print "<table border=0 cellspacing=1 bgcolor=\"".$colors["playlist"]["title"]."\" >\n";

		print "<tr><td nowrap colspan=\"3\"><a name=playlists /><b>Playlists</b>";
		//printIndex($pindex,"","p");
		print "</td></tr>\n";
		//print "<tr bgcolor=\"".$colors["playlist"]["body"]."\"><td>\n";
		for($i=0;$i<count($pprint);$i++) print $pprint[$i];
		//print "</td></tr></table>\n";
		print "</table>\n";
	}
}




///////////////////////////// OLD CODE 
/*

if(!$fp) {
	echo "$errstr ($errno)<br>\n";
}
else {
*/
	// have no idea, what this is for...
	/*while(!feof($fp)) {
		$got =  fgets($fp,1024);
		if(strncmp("OK",$got,strlen("OK"))==0) 
			break;
		print "$got<br>";
		if(strncmp("ACK",$got,strlen("ACK"))==0) 
			break;
	}*/
	// password (we dont use one)
	/*if(isset($password)) {
		fputs($fp,"password \"$password\"\n");
		while(!feof($fp)) {
			$got =  fgets($fp,1024);
			if(strncmp("OK",$got,strlen("OK"))==0)
				break;
			print "$got<br>";
			if(strncmp("ACK",$got,strlen("ACK"))==0) 
				break;
		}
	}
	*/
	// playlist file upload
	/*
	if(isset($HTTP_POST_FILES['playlist_file']['name'])) {
		$name = $HTTP_POST_FILES['playlist_file']['name'];
		$file = $HTTP_POST_FILES['playlist_file']['tmp_name'];
		if(!is_uploaded_file($file)) {
			print "Problems uploading file<br>";
		}
		else if(!($pls_fp = fopen($file, "r"))) {
			print "Problems opening file<br>";
		}
		else if(preg_match("/\.m3u/",$name)) {
			$add = readM3uFile($pls_fp);
		}
		else if(preg_match("/\.pls/",$name)) {
			$add = readPlsFile($pls_fp);
                }
		else {
			print "NOT a m3u or pls file!<br>";
		}
	}
	*/
	// streams
	/*
	if(isset($stream)) {
		if(preg_match("/^[a-z]*:\/\//",$stream) && !preg_match("/^file:/",$stream)) {
			if(preg_match("/\.m3u/",$stream)) {
				$pls_fp = fopen($stream,"r");
				$add = readM3uFile($pls_fp);
			}
			else if(preg_match("/\.pls/",$stream)) {
				$pls_fp = fopen($stream,"r");
				$add = readPlsFile($pls_fp);
                	}
			else {
				$command = "add";
				$arg = $stream;
			}
		}
		else {
			print "Doesn't appear to be a url<br>";
		}
	}
	*/
 