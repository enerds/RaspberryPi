<?php

function outputs( $fp, $host, $color, $server, $commands )
{
	$i = "-1";
	fputs( $fp, "outputs\n" );
	while( ! feof( $fp ))
	{
		$got = fgets( $fp, "1024" );
		if ( strstr ( $got, ":" ))
		{
			list( $val,$arg ) = explode( ":", $got );
		}
		if ( strcmp( $val, "outputid" ) == "0")
		{
			$i++;
		}
		if ( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}
		if ( strncmp( "ACK", $got, strlen("ACK")) == "0")
		{
			print "$got<br>";
			break;
		}
		$outputs[$i]["$val"] = preg_replace( "/^ /", "", $arg );
	}	
	echo "<br>";
	echo "<table summary=\"Outputs\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td nowrap><b>Sound outputs for $host</b></td></tr>";
	echo "<tr><td>";
	echo "<table summary=\"Outputs\" border=0 cellspacing=1 bgcolor=\"{$color["body"][$i%2]}\" width=\"100%\">";
	for( $i = "0"; $i < sizeOf( $outputs ); $i++ )
	{
		echo "<tr bgcolor=\"{$color["body"][$i%2]}\"><td nowrap>";
		if( ( ( $outputs[$i]["outputenabled"]%2 ) - 1 ) && $commands["enableoutput"] === true )
		{
			echo "[<a title=\"Enable this output\"  href=index.php?body=main&amp;feature=outputs&amp;server=$server&amp;command=enableoutput&amp;arg=$i>enable</a>]";
		}
		else if( $commands["disableoutput"] === true )
		{
			echo "[<a title=\"Disable this output\" href=index.php?body=main&amp;feature=outputs&amp;server=$server&amp;command=disableoutput&amp;arg=$i>disable</a>]</td>";
		}
		echo "<td width=\"100%\">&nbsp;{$outputs[$i]["outputname"]}</td></tr>";
	}

	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
}

function login($fp, $default_sort, $color, $server, $arg, $dir, $remember)
{
	echo "<br>";
	echo "<form target=_top action=\"index.php\" method=post>";
	echo "<table summary=\"Login\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Password</b></td></tr>";
	echo "<tr bgcolor=\"{$color["body"]}\"><td>";
	echo "<input type=hidden value=\"$server\" name=server>";
	echo "<input type=password name=passarg value=\"{$arg}\" size=20>";
	echo "<input type=hidden value=\"" . rawurlencode( $dir ) . "\" name=dir>";
	echo "<input type=hidden value=\"$default_sort\" name=sort>";
	echo "<input type=submit value=login name=foo>";
	echo "<br>";
	echo "<small><input type=checkbox name=remember value=true>remember password</small>";
	echo "</td></tr></table>";
	echo "</form>";
}

function stats( $fp, $color, $MPDversion, $phpMpVersion, $host, $port )
{
	// Used to use mktime() until it became too combersome
	function secondsToDHMS( $seconds )
	{
		// Floor is not 100% accurate, patch anyone?
		$ret = "";
		$years = floor ($seconds / 31536000);
		if( $years > 1 )
		{
			$seconds -= $years * 31536000;
			$ret .= "$years years ";
		}

		$days = floor ($seconds / 86400);
		if( $days > 1)
		{
			$seconds -= $days * 86400;
			$ret .= "$days days ";
		}

		$hours = floor ($seconds / 3600);
		if ($hours > 1)
		{
			$seconds -= $hours * 3600;
			$ret .= "$hours hours ";
		}

		$minutes = floor ($seconds / 60);   
		if ($minutes > 1)
		{
			$seconds -= $minutes * 60;
			$ret .= "$minutes minutes ";
		}

		if( ! empty( $seconds ))
		{
			$ret .= "$seconds seconds";
		}

		return $ret;
	}

	fputs( $fp, "stats\n" );
	while( ! feof( $fp ))
	{
		$got = fgets( $fp, "1024" );
		$el = strtok( $got, ":");
		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}
		if( strncmp( "ACK", $got, strlen( "ACK" )) == "0")
		{
			print "$got<br>";
			break;
		}
		$got = strtok( "\0" );
		$stats[$el] = preg_replace( "/^ /", "", $got );
	}
	
	// requesting time() or date() requires a well set timezone
	date_default_timezone_set  ( "Europe/Berlin" );
	
	$statistics = array
		(
			"Artists" => $stats["artists"],
			"Albums" => $stats["albums"],
			"Songs" => $stats["songs"],
			"Uptime" => secondsToDHMS( $stats["uptime"] ),
			"Play Time" => secondsToDHMS( $stats["playtime"] ),
			"MPD Version" => $MPDversion,
			"phpMp Version" => $phpMpVersion,
			"Database Updated" => date( "F j, Y, g:i a", (int) $stats["db_update"] ),
			"Total Database Play Time" => secondsToDHMS( $stats["db_playtime"] )
		); 

	/* Begin Stats Form */
	echo "<br>";
	echo "<table summary=\"Statistics\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td nowrap><b>Statistics for $host:$port</b></td></tr>";
	echo "<tr><td>";
	echo "<table summary=\"Statistics\" border=0 cellspacing=1 bgcolor=\"{$color["body"][1]}\" width=\"100%\">";

	$j=0;
	foreach( $statistics as $key => $value )
	{
		for( $i = "0"; $i < sizeof( $statistics ); $i++ )
		{
			echo "<tr bgcolor=\"{$color["body"][$j%2]}\"><td><b>$key:</b>&nbsp;$value</td></tr>";
			break;
		}
		$j++;
	}

	echo "</table></td></tr><table>";
}

function stream( $server, $color, $stream_browser, $url_icy, $url_shout, $dir, $updating )
{
	echo "<br>";
	echo "<form action=index.php? name=1 target=playlist method=get>";
	echo "<table summary=\"Add Stream\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Add Stream</b></td></tr>";
	echo "<tr bgcolor=\"{$color["body"][0]}\"><td>";
	echo "<input type=hidden value=\"playlist\" name=body>";
	echo "<input type=hidden value=$server name=server>";
	echo "<input type=input name=stream size=40>";
	echo "<input type=submit value=Add name=foo>";
	echo "</td></tr></table></form>";

	echo "<br>";
	echo "<form action=index.php? name=2 target=playlist method=get>";
	echo "<table summary=\"Search Webpage for Streams\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Search Webpage for Streams</b></td></tr>";
	echo "<tr bgcolor=\"{$color["body"][0]}\"><td>";
	echo "<input type=hidden value=\"playlist\" name=body>";
	echo "<input type=hidden value=$server name=server>";
	echo "<input type=input name=streamurl size=40>";
	echo "<input type=submit value=Add name=foo>";
	echo "</td></tr></table></form>";

	echo "<br>";
	echo "<form enctype=\"multipart/form-data\" action=\"index.php?\" target=\"playlist\" method=\"post\">";
	echo "<table summary=\"Load Stream From Playlist\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Load Stream From Playlist</b></td></tr>";
	echo "<tr bgcolor=\"{$color["body"][0]}\"><td>";
	echo "<input type=\"hidden\" value=\"playlist\" name=\"body\">";
	echo "<input type=\"hidden\" value=\"$server\" name=\"server\">";
	echo "<input type=\"file\" name=\"playlist_file[]\" size=30>";
	echo "&nbsp;";
	echo "<input type=\"submit\" value=\"Load\" name=\"foo\"><br>";
	echo "</td></tr></table></form>";

	echo "<br>";
	$real_path = realpath(".");
	$icy_file = "{$real_path}/cache/stream-icy.xml";
	$shout_file = "{$real_path}/cache/stream-shout.xml.gz";
	if(isset($url_icy) && !empty($url_icy) && (is_file($icy_file) || $updating === true) && $stream_browser === true) {
		echo "<table summary=\"Icecast Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
		echo "<tr><td>";
		echo "<table summary=\"Icecast Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
		echo "<tr><td><b>Icecast Streams</b>";
		echo "&nbsp;<small>(<a title=\"Show a table of current Icecast Streams\" href=\"index.php?body=main&amp;server=$server&amp;dir=$dir&amp;feature=stream-icy\" target=main>show</a>)</small>";
		echo "</td></tr></table></td></tr></table>";
	}

	echo "<br>";
	if(isset($url_shout) && !empty($url_shout) && (is_file($shout_file) || $updating === true) && $stream_browser === true) {
		echo "<table summary=\"Shoutcast Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
		echo "<tr><td>";
		echo "<table summary=\"Shoutcast Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
		echo "<tr><td><b>Shoutcast Streams</b>";
		echo "&nbsp;<small>(<a title=\"Show a table of current Shoutcast streams\" href=\"index.php?body=main&amp;server=$server&amp;dir=$dir&amp;feature=stream-shout\" target=main>show</a>)</small></td>";
		echo "</tr></table></td></tr></table>";
	}
}

function sbrowser( $server, $color, $feature, $server_data, $stream_browser, $url_icy, $url_shout, $dir, $arg, $arg2, $updating, $baseline, $streamfilter, $sfa )
{
	// This should never happen
	if( $stream_browser !== true )
	{
		return -1;
	}

	/* Filter $server_data */
	if(!empty($streamfilter) && !empty($sfa)) {
		$j = 0;
		$new_server_data = array();
		for($i = 0; $i < sizeof($server_data); $i++) {
			foreach( $server_data[$i] as $key => $value ) {
				if(strcmp($sfa,"any") == 0 || strcmp($sfa,$key) == 0) {
					$pos = stripos($value,$streamfilter);
					if($pos !== false) {
						$new_server_data[$j] = $server_data[$i];
						$j++;
					}
				}
			}
		}
		$server_data = $new_server_data;
	}

	/* End $server_data filter*/
	$k=0;
	for( $i = "0"; $i < sizeOf( $server_data ); $i++ )
	{
		if( isset ( $server_data[($i+1)]["server_name"] ) &&
			strcmp( $server_data[$i]["server_name"], $server_data[($i+1)]["server_name"] ) != 0)
		{
			$k++;
		}
	}
	echo "<br><table summary=\"Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td>";
	echo "<table summary=\"Streams\" border=\"0\" cellspacing=\"1\" bgcolor=\"{$color["title"]}\" width=\"100%\">";
	if( strcmp( $feature, "stream-icy" ) == "0" )
	{ 
		echo "<tr><td><b>Icecast Streams</b>";
	}
	else if( strcmp( $feature, "stream-shout" ) == "0" )
	{
		echo "<tr><td><b>Shoutcast Streams</b>";
	}
	$baseurl = "index.php?body=main&amp;dir=$dir&amp;server=$server&amp;streamfilter=$streamfilter&amp;sfa=$sfa&amp;";
	echo "&nbsp;<small>(<a title=\"Hide the streams table\" href=\"{$baseurl}feature=stream\" target=_self>hide</a>)</small>";
	if(!(strcmp($arg,"info") == 0 && strcmp($arg2,"expandall") == 0)) {
		echo "&nbsp;<small>(<a title=\"Expand all\" href=\"{$baseurl}feature=$feature&amp;arg=info&amp;arg2=expandall \" target=_self>expand all</a>)</small>";
	}
	echo "&nbsp;<small>(<a title=\"Refresh streams table\" href=\"index.php?body=main&amp;dir=$dir&amp;server=$server&amp;feature=$feature\" target=_self>refresh</a>)</small>";
	if( $updating === true ) {
		echo "&nbsp;<small>(<a title=\"Update streams table\" href=\"{$baseurl}feature=$feature&amp;arg=update\" target=_self>update</a>)</small></td>";
	}
	if($k!=0) {
		$k++; /* Human readable */
	}
	echo "<td align=\"right\"><small><b>Found $k unique results</b></small></td></tr>";
	echo "<tr><td>";
	echo "<form action=index.php? method=get>";
	echo "<select name=\"sfa\">";
	echo "<option value=\"any\">Any</option>";
	ksort($baseline);
	foreach( $baseline as $key => $value) {
		$tmp = str_replace("_"," ",$key);
		$tmp = ucwords($tmp);
		echo "<option";
		if(strcmp($key,$sfa) == 0) {
			echo " selected ";
		}
		echo " value=\"$key\">$tmp</option>";
	}
	echo "</select>";
	echo "<input type=hidden value=\"main\" name=\"body\">";
	echo "<input type=hidden value=\"{$feature}\" name=\"feature\">";
	echo "<input type=hidden value=\"{$server}\" name=\"server\">";
	echo "<input type=hidden value=\"{$dir}\" name=\"dir\">";
	//echo "<input type=hidden value=\"{$sort}\" name=\"sort\">";
	echo "&nbsp;<input type=input name=\"streamfilter\" value=\"{$streamfilter}\" size=40 autocomplete=on>";
	echo "&nbsp;<input type=submit value=Filter name=foo2>";
	echo "</td></tr>";
	echo "</table>";
	echo "</form>";
	echo "<table summary=\"Statistics\" border=0 cellspacing=1 bgcolor=\"{$color["body"][1]}\" width=\"100%\">";

	$j = 0;

	echo "<tr bgcolor=\"{$color["sort"]}\"><td></td><td>&nbsp;<b>Stream Information</b></td></tr>";
	$j=2;
	$k=0;
	for( $i = "0"; $i < sizeOf( $server_data ); $i++ )
	{
		echo "<tr bgcolor=\"{$color["body"][$k%2]}\"><a href=$i></a><td>";
		echo "[<a title=\"Add this stream to your playlist\" target=\"playlist\" href=\"index.php?body=playlist&amp;stream=";
		echo rawurlencode( $server_data[$i]["listen_url"] );
		while( strcmp( $server_data[$i]["server_name"], $server_data[($i+1)]["server_name"] ) == "0" )
		{
			$i++;
			echo $song_seperator . rawurlencode( $server_data[$i]["listen_url"] );
		}

		if(strcmp($arg,"info") == 0 && strcmp($arg2,$i) == 0) {
			$url = "index.php?body=main&amp;streamfilter=$streamfilter&amp;sfa=$sfa&amp;dir=$dir&amp;server=$server&amp;feature=$feature#{$i}#{$i}";
		} else if (!(strcmp($arg,"info") == 0 && strcmp($arg2,"expandall") == 0)) {
			$url = "index.php?body=main&amp;streamfilter=$streamfilter&amp;sfa=$sfa&amp;dir=$dir&amp;server=$server&amp;feature=$feature&amp;arg=info&amp;arg2={$i}#{$i}";
		}
		echo "\">add</a>]</td>";
		echo "<td>&nbsp;";
		if(isset($url)) {
			echo "<a title=\"View information about this stream\" name=\"main\" href=\"$url\">" . trim( $server_data[$i]["server_name"] ) . "</a>";
		} else {
			echo trim( $server_data[$i]["server_name"] );
		}
		echo "</td></tr>";
		if((strcmp($arg,"info") == 0 && strcmp($arg2,$i) == 0) || strcmp($arg,"info") == 0 && strcmp($arg2,"expandall") == 0) {
			echo "<tr bgcolor=\"{$color["body"][$k%2]}\"><td></td><td>";
			echo "<table><tr>";
			if(!empty($server_data[$i]["bitrate"])) {
				echo "<td><b>Bitrate:</b> {$server_data[$i]["bitrate"]}</td>";
			}

			if(!empty($server_data[$i]["channels"])) {
				echo "<td><b>Channels:</b> {$server_data[$i]["channels"]}</td>";
			}

			if(!empty($server_data[$i]["rank"])) {
				echo "<td><b>Rank:</b> {$server_data[$i]["rank"]}</td>";
			}

			if(!empty($server_data[$i]["listening"])) {
				echo "<td><b>Listening:</b> {$server_data[$i]["listening"]}</td>";
			}

			if(!empty($server_data[$i]["max_listeners"])) {
				echo "<td><b>Max Listeners:</b> {$server_data[$i]["max_listeners"]}</td>";
			}

			if(!empty($server_data[$i]["samplerate"])) {
				echo "<td><b>Sample Rate:</b> {$server_data[$i]["samplerate"]}</td>";
			}
			echo "</tr>";

			if(!empty($server_data[$i]["server_type"])) {
				echo "<tr><td colspan=99><b>Server Type:</b> {$server_data[$i]["server_type"]}</td></tr>";
			}

			if(!empty($server_data[$i]["current_song"])) {
				echo "<tr><td colspan=99><b>Current Song:</b> {$server_data[$i]["current_song"]}</td></tr>";
			}

			if(!empty($server_data[$i]["genre"])) {
				echo "<tr><td colspan=99><b>Genre:</b> {$server_data[$i]["genre"]}</td></tr>";
			}

			if(!empty($server_data[$i]["stream_homepage"])) {
				if(!preg_match("/http:\/\//",$server_data[$i]["stream_homepage"])) {
					$server_data[$i]["stream_homepage"] = "http://" . $server_data[$i]["stream_homepage"];
				}

				echo "<tr><td colspan=99><b>Server Homepage:</b> ";
				echo "<a href=\"{$server_data[$i]["stream_homepage"]}\" target=\"new\" title=\"This stream's homepage\">{$server_data[$i]["stream_homepage"]}</a></td>";
				echo "</tr>";
			}
			echo "</table>";
			$previous = ($i-1);
			echo "<a name={$previous}></a>";
			echo "</td></tr>";
		}
		$k++;
	}
}

function server( $servers, $host, $port, $color )
{
	echo "<br>";
	echo "<table summary=\"Server Selection\" border=0 cellspacing=1 bgcolor=\"{$color["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Servers</b></td></tr>";
	echo "<tr bgcolor=\"{$color["body"]}\"><td>";

	// This is for those who utilize multiple MPD servers setup in phpMp's config
 	echo '<form method=post action="index.php" target=_top>';
	$sel1 = '<select name=server>';
	$sel2 = "";
	for( $x = "0"; $x < sizeof( $servers ); $x++ )
	{
		// add the server to select box, if the 3rd field is not blank use that,
		// otherwise default to the host name as the displayed server
		// If the host is the current host, place it at the top, otherwise put it under that
		if( strcmp( $servers[$x][0], $host ) == "0" && strcmp( $servers[$x][1], $port ) == "0" )
		{
			$sel1 .= "<option selected value=\"$x\">{$servers[$x][2]}</option>";
		}
		else
		{
			$sel2 .= "<option value=\"{$x}\">" . ( ( $servers[$x][2] != '' ) ? $servers[$x][2] : $servers[$x][0] ) . "</option>";
		}
	}
	$sel2.= '</select>&nbsp';
	echo $sel1 . $sel2 . '<input align="right" type=submit value="Switch Server">';
	echo "</form></td></tr></table>";
}

function search( $fp, $color, $config, $dir, $search, $find, $arg, $sort, $server, $addperm, $feature, $ordered, $tagged, $untagged, $lsinfo, $search_fields, $MPDversion )
{
	echo "<br>";
	echo "<form action=index.php? method=get>";
	echo "<table summary=\"Search\" border=0 cellspacing=1 bgcolor=\"{$color["meta"]["title"]}\" width=\"100%\">";
	echo "<tr><td><b>Search</b></td></tr>";
	echo "<tr bgcolor=\"{$color["meta"]["body"][1]}\"><td>";
	echo "<select name=search>";

	if( strcmp( $search, "filename" ) == "0" || strcmp( $find, "filename" ) == "0" )
	{
		$filename = "<option value=\"filename\" selected>Filename</option>";
	}
	else
	{
	        $filename = "<option value=\"filename\">Filename</option>";
	}


	if( $config["filenames_only"] === true )
	{
		echo $filename;
	}

	// Default to Any if 0.12.x
	if(( strcasecmp( $search, "any" ) == "0" || strcasecmp( $find, "any" ) == "0" ||
	    ( strcmp($search,'') == 0 && strcmp($find,'') == 0 )) && strcmp( $MPDversion, "0.12.0" ) == 0 )
	{
		echo "<option value=\"any\" selected>Any</option>";
	}
	else if( strcmp( $MPDversion, "0.12.0") == 0 )
	{
		echo "<option value=\"any\">Any</option>";
	}


        for( $i = "0"; $i < count( $search_fields ); $i++ )
	{
		// Don't echo 'Time' or 'Track' as they don't make sense to search for.
                if( strcmp( "Time", $search_fields[$i]) && strcmp( "Track", $search_fields[$i] ))
		{
			if( strcasecmp( $search, $search_fields[$i] ) == "0" || strcasecmp( $find, $search_fields[$i] ) == "0" )
			{
				echo "<option selected>{$search_fields[$i]}</option>";
			}
			else
			{
				echo "<option>{$search_fields[$i]}</option>";
			}
		}
	}
        
	if( $config["filenames_only"] !== true )
	{
		echo $filename;
	}
	
	echo "</select>";
	echo "<input type=hidden value=\"main\" name=body>";
	echo "<input type=hidden value=\"search\" name=feature>";
	echo "<input type=hidden value=\"$server\" name=server>";
	echo "&nbsp;&nbsp;<input name=arg value=\"$arg\" size=40 autocomplete=on>";
	echo "<input type=hidden value=\"$dir\" name=dir>";
	echo "<input type=hidden value=\"$sort\" name=sort>";
	echo "<input type=submit value=Search name=foo>";
	echo "</td></tr></table>";
	echo "</form>";

	$arg_url = rawurlencode( $arg );
	$dir_url = rawurlencode( $dir );
	$sort_array = explode( ",", $sort );

	if( empty( $search ))
	{
		$url = "index.php?body=main&amp;feature=search&amp;find=$find&amp;arg=$arg_url&amp;dir=$dir_url";
	}
	else
	{
		$url = "index.php?body=main&amp;feature=search&amp;search=$search&amp;arg=$arg_url&amp;dir=$dir_url";
	}

	if( is_array( $untagged ) || is_array( $tagged ))
	{
		list( $sort_array, $sort ) = cleanSort( $sort_array, $config["display_fields"] );
		$add_all = createAddAll( $lsinfo, $config["song_separator"] );
		$tagged_info = taginfo2musicTable( $tagged, $dir_url, $config["display_fields"], $config["unknown_string"], $color, $server, $addperm, $sort_array, $sort, $ordered, $url );
		$file_info = fileinfo2musicTable( $untagged, $dir_url, $config["display_fields"], $color, $server, $addperm );
		unset( $tagged, $untagged );

		if( ! empty( $tagged_info ) && ! empty( $untagged_info ))
		{
			if( empty( $tagged_info["print"] ))
			{
				$file_info["title"] = "Music";
			}
			if( empty( $file_info["print"] ))
			{
				$tagged_info["title"] = "Music";
			}
		}
		printMusicTable( $add_all, count( $config["display_fields"] ), $config["use_javascript"], $color["meta"], $tagged_info, $file_info["count"], $sort_array, $server, $dir, $addperm, $feature, $ordered );
		printMusicTable( $add_all, count( $config["display_fields"] ), $config["use_javascript"], $color["file"], $file_info, $tagged_info["count"], $sort_array, $server, $dir, $addperm, $feature, 0 );
	}
	else if( ! empty( $arg ))
	{
		echo "<br>&nbsp;<b>" . ucwords( $search ) . " \"$arg\" not found.</b>";
	}
}
?>