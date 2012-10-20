<?php
include "theme.php";
$phpMpVersion="0.12.1";

function cleanSort( $sort_array, $display_fields )
{
	$sort_count = sizeof( $sort_array );
	$new_sort_array = "";

	for( $i=0; $i<$sort_count; $i++ )
	{
		if( in_array( $sort_array[$i], $display_fields ))
		{
			$new_sort_array[] = $sort_array[$i];
		}
	}

	$new_sort_count = sizeof( $new_sort_array );

	$new_sort = $new_sort_array[0];
	for( $j=1; $j<$new_sort_count; $j++ )
	{
		$new_sort .= ','.$new_sort_array[$j];
	}

	return( array( $new_sort_array, $new_sort ));
}


/***********************************************************************************************#
#												#
#	crop: deletes all but the currently playing song from the current playlist		#
#												#
#***********************************************************************************************#
#												#
#   $fp => connection										#
#   $current => value of the current song (starts at '0')					#
#   $playlistlength => value of the number of songs in the current playlst (starts at '1')	#
#												#
#***********************************************************************************************/

function crop( $fp, $current, $playlistlength )
{
        $playlistlength -= 1;

	$str = "command_list_begin\n";
	for( $playlistlength; $playlistlength >= "0"; $playlistlength-- )
	{
	        if( strcmp( $current, $playlistlength ))
		{
			$str .= "delete $playlistlength\n";
		}
	}
	fputs( $fp, $str . "command_list_end\n" );

	initialConnect( $fp );
	return;
}

function initialConnect( $fp )
{
	while( ! feof( $fp ))
	{
		$got =  fgets( $fp, "1024" );
		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
		        return preg_replace( "/^OK MPD /", "", $got );
		}
		if( strncmp( "ACK", $got, strlen( "ACK" )) == "0")
		{
			return $got;
		}
	}
}

function doCommand( $fp, $arg, $arg2, $command, $overwrite, $status )
{
	// Don't let updates collide 
 	if( isset ( $status["updating_db"] ) && strcmp( $command,"update" ) == "0" )
	{
		return 0;
	}

	// Lets cleanup the $arg first
	$arg = rawurldecode( $arg );

	// This is to facilitate for overwriting playlists, it should probably detect to see if
	// it exists first, but who cares, right?
        if( strncmp( "save", $command, strlen( "save" )) == "0" && $overwrite === true )
	{
		fputs( $fp, "rm \"$arg\"\n" );
		fgets( $fp, "1024");
	}

	// Cannot use empty() here because when the argument == 0 it returns false.
	if ( strlen( $arg2 ) > "0" )
	{
		$command.= " \"$arg\" \"$arg2\"";
	}
	else if( strlen( $arg ) > "0" )
	{
	        $command.= " \"$arg\"";
        }

        // By the time you read this the 'kill' command will hopefully be a non-default compile time 
	// option. $arg is also used here, so to make sure someone doesn't kill your MPD leave it.
	if( strncmp( "kill", $command, strlen( "kill" )) || ( ! empty( $command ) && strncmp( "kill", $arg, strlen( "kill" ))))
	{
	          fputs( $fp, "$command\n" );
	}

	while( ! feof( $fp ))
	{
	        $got = fgets( $fp, "1024" );
		if( strncmp( "OK", $got, strlen( "OK" )) == "0" ) 
		{
		        break;
		}
		str_replace( "\n", "\n<br>", $got );

		// Otherwise it will output how many times it's updated
		if ( strncmp( "update", $command, strlen( "update" )))
		{
		        echo "<a target=main><b><br>Error! $got<br></b></a><br>\n";
                }
	
		if ( strncmp( "ACK", $got, strlen( "ACK" )) == "0" ) 
		{
		        break;
		}
	}
}

function displayDirectory( $dir, $dir_url, $sort, $title, $mfcount, $mtcount, $pcount, $dcount, $has_password, $commands, $color, $server, $servers, $fp, $passarg, $ordered, $feature, $arg, $search_bar, $javascript )
{
	echo "<!-- Begin displayDirectory  -->";
	// The next line needs a cellspacing value of 2 since the other tables have 2 tables, and this one only has one
	echo "<table summary=\"Directory\" cellspacing=1 bgcolor=\"{$color["title"]}\">";
	echo "<tr><td>";
	echo "<b>$title</b>&nbsp;";

	if( $dcount > "0" )
	{
		if( $mfcount > "0" && $mtcount > "0" )
		{
 		        echo "<small>(<a title=\"Jump to the tagged music menu\" href=\"#Tagged Music\">Tagged</a>)</small>&nbsp;";
			echo "<small>(<a title=\"Jump to the untagged music menu\" href=\"#Untagged Music\">Untagged</a>)</small>&nbsp;";
		}
		else if( $mfcount > "0" )
		{
			echo "<small>(<a title=\"Jump to the music menu\" href=\"#Untagged Music\">Music</a>)</small>&nbsp;";
		}
		else if( $mtcount > "0" )
		{
			echo "<small>(<a title=\"Jump to the music menu\" href=\"#Tagged Music\">Music</a>)</small>&nbsp;";
		}
	}
	else if( $mfcount > "0" && $mtcount > "0" )
	{
		echo "<small>(<a title=\"Jump to the untagged music menu\" href=\"#Untagged Music\">Untagged</a>)</small>&nbsp;";
	}

	if ( $pcount > "0" )
	{
	        echo "<small>(<a title=\"Jump to the saved playlists menu\" href=\"#playlists\">Saved Playlists</a>)</small>&nbsp;";
	}

	echo "</td><td align=right><small>";

	$feature_bar = "";

	// If we have all commands available and we don't have an active password don't show the feature bar.
	if( $commands["all"] === false || strlen( $passarg ) > "0" )
	{
		if( $has_password === true )
		{
			$feature_bar .= "<a title=\"Logout of MPD Server\" target=_top href=\"index.php?server=$server&amp;dir=$dir_url&amp;sort=$sort&amp;logout=1\">Logout</a>";
		}
		else
		{
			$feature_bar .= "<a title=\"Login to MPD Server\" target=main href=\"index.php?body=main&amp;feature=login&amp;server=$server&amp;dir=$dir_url&amp;sort=$sort\">Login</a>";
		}
	}

	if( $commands["outputs"] === true )
	{
		if( ! empty( $feature_bar ))
		{
			$feature_bar .= "&nbsp;|&nbsp;";
		}
		$feature_bar .= "<a title=\"View the Sound Outputs\" href=\"index.php?body=main&amp;server=$server&amp;dir=$dir_url&amp;sort=$sort&amp;feature=outputs&amp;dir=$dir_url\">Outputs</a>";
	}

	if( $commands["search"] === true )
	{
		if( ! empty( $feature_bar ))
		{
			$feature_bar .= "&nbsp;|&nbsp;";
		}
		$feature_bar .= "<a title=\"Search the MPD Database\" href=\"index.php?body=main&amp;server=$server&amp;dir=$dir_url&amp;sort=$sort&amp;feature=search\">Search</a>";
	}

	if( sizeof( $servers ) > 1 )
	{
		if( ! empty( $feature_bar ))
		{
			$feature_bar .= "&nbsp;|&nbsp;";
		}
		$feature_bar .= "<a title=\"Change the MPD Server\" href=\"index.php?body=main&amp;server=$server&amp;sort=$sort&amp;dir=$dir_url&amp;feature=server\">Servers</a>";
	}

	if( $commands["stats"] === true )
	{
		if( ! empty( $feature_bar ))
		{
			$feature_bar .= "&nbsp;|&nbsp;";
		}
		$feature_bar .= "<a title=\"View MPD/phpMp Statistics\" target=main href=\"index.php?body=main&amp;server=$server&amp;dir=$dir_url&amp;sort=$sort&amp;feature=stats\">Stats</a>";
	}

	if( $commands["load"] === true )
	{
		if( ! empty( $feature_bar ))
		{
			$feature_bar .= "&nbsp;|&nbsp;";
		}
		$feature_bar .= "<a title=\"Add a Stream or Playlist of Streams to the Active Playlist\" ";
		$feature_bar .= "href=\"index.php?body=main&amp;server=$server&amp;dir=$dir_url&amp;sort=$sort&amp;feature=stream\">Stream</a>";
	}

	if( ! empty( $feature_bar ))
	{
		echo $feature_bar;
	}

	echo "&nbsp;</small></td></tr><tr><td colspan=2>";
	echo "<table style=\"background-color: {$color["body"][0]}\"\">";
	echo "<tr bgcolor=\"{$color["body"][0]}\">";
	if( $search_bar ===  true && empty($feature)) {
		echo "<td>";
	} else {
		echo "<td colspan=2>";
	}
	$dirs = explode( "/", $dir );
	echo "<a title=\"Back to the Root Music Directory\" href=\"index.php?body=main&amp;server=$server&amp;sort=$sort&amp;ordered=$ordered\">Music</a>";

	$build_dir = "";
	for( $i=0; $i < ( count( $dirs ) - 1 ); $i++ )
	{
		if ($i > "0" && $i < ( count( $dirs ) - 1 ))
		{
		        $build_dir.="/";
		}
		$dirs[$i] = stripslashes( $dirs[$i] );
		echo " / ";

		// A dirty fucking hack because for some reason the '&' doesn't get properly encoded.
		// To see this bug in action goto a directory with '&' in the name, then go to a child directory, then click on the 
		// directory with the '&' in the name again.
		$build_dir .= str_replace('&','%26',$dirs[$i]);

		// This is a workaround to prevent letters that shouldn't be in the title from getting there.
		$nice = str_replace(array("\"","\'"), '', $dirs[$i]);

		echo "<a title=\"Jump to '".$nice."'\" href=\"index.php?body=main&amp;server=$server&amp;sort=$sort&amp;ordered=$ordered&amp;dir=$build_dir\">$dirs[$i]</a>";
	}

	if ( $i > "0" )
	{
	        $build_dir.="/";
	}

	if ( ! empty( $dir ))
	{
		$dirs[$i] = stripslashes( $dirs[$i] );
		$build_dir.=$dirs[$i];
		$build_dir = rawurlencode( $build_dir );
		echo " / ";

		// This is a workaround to prevent letters that shouldn't be in the title from getting there.
		$nice = str_replace(array("\"","\'"), '', $dirs[$i]);

		echo "<a title=\"Refresh the Current Directory '".$nice."'\" href=\"index.php?body=main&amp;server=$server&amp;sort=$sort&amp;ordered=$ordered&amp;dir=$build_dir\">$dirs[$i]</a>";
	}

        // We don't allow update during search or find because 
        // the search will not be re-submitted without some javascript magic.
        // It would probably be best to compensate for the stream browsers too.
        $status = getStatusInfo( $fp );
	if( isset( $status["updating_db"] ))
	{
		echo "&nbsp;&nbsp;<small>(db updating...)</small>";
        }
	else if( strcmp( $title, "Current Directory" ) == "0" && $commands["update"] === true &&
                !( strcmp( $feature, "search" ) == "0"  || strcmp( $feature, "find" ) == "0" ))
	{
		$dirs[$i] = stripslashes( $dirs[$i] );
		$build_dir = rawurlencode( $build_dir );
		echo "&nbsp;&nbsp;<small>(<a href=\"index.php?body=main&amp;feature=$feature&amp;server=$server&amp;dir=$build_dir&amp;sort=$sort&amp;ordered=$ordered&amp;command=update&amp;arg=$build_dir/\"";
		echo "target=\"main\" title=\"Update the Current Directory\">db update</a>)</small>";
	}
	if( $search_bar === true && empty($feature)) {
		echo "<form action=index.php? method=get>";
		echo "<td align=right>";
		echo "<input type=hidden value=\"main\" name=body>";
		echo "<input type=hidden value=\"search\" name=feature>";
		echo "<input type=hidden value=\"any\" name=search>";
		echo "<input type=hidden value=\"$dir\" name=dir>";
		echo "<input type=hidden value=\"$server\" name=server>";
		echo "<input type=hidden value=\"$sort\" name=sort>";
		echo "&nbsp;&nbsp;<input name=arg size=25 id=\"f\" autocomplete=on>";
		echo "<input type=submit value=Go name=foo>";

		if(strcmp($javascript,"yes") == 0) {
			echo "<script type=\"text/javascript\">";
				echo "document.getElementById('f').focus();";
			echo "</script>";
		}
		echo "</td></form>";
	}

	echo "</td></tr></table></td></tr></table>";
	echo "<!-- End displayDirectory -->";
}

function mbFirstChar( $str )
{
	$i = 1;
	if( isset( $str[0] ))
	{
		$ret = $str[0];
	}
	while ($i < strlen( $str ) && ord( $str[$i] ) >= 128  && ord( $str[$i] ) < 192)
	{
		$ret .= $str[$i];
		$i++;
	}
	if( isset( $ret ))
	{
		return $ret;
	}
}

function readFileOverHTTP( $fp, $stream )
{
	$stream = rawurldecode( $stream );
	$pointer = fopen( $stream, "r" ) or die ( "<H3><b>If you want phpMp to download your stream, you have to change 'allow_url_open' to On in your php.ini</b></H3>");
	$contents = fread( $pointer, "1024" );
	if( preg_match( "/.pls$/", $stream ))
	{
		preg_match_all( "/File[0-9]*=(.*?)\n/", $contents, $out );
		$streams = $out[1];
	}
	else
	{
		if( strcmp( $contents, " " ))
		{
			$streams = explode( " ", $contents );
		}
		else
		{
			$streams = $contents;
		}
	}
	return($streams);	
}

function postStream( $fp, $filetype )
{
	$add = array();
	$i = 0;
        
	while ( ! feof( $fp ))
	{
		$url = fgets( $fp, 4096 );
		if( strcmp( $filetype, "pls" ) && preg_match( "/File[0-9]*=/", $url ))
		{
			$url = preg_replace( "/^File[0-9]*=/", "", $url );
                }
		$url = preg_replace( "/\n$/", "", $url );
		if ( preg_match( "/^[a-z]*:\/\//", $url ))
		{
			$add[$i] = $url;
			$i++;
		}
	}
	return $add;
}

function hostname($url) {
	$i = parse_url($url);
	return "{$i["scheme"]}://{$i["host"]}";
}

/* This function was taken from php.net examples*/
function x_array_merge($arr1,$arr2) {
    for($i=0;$i<count($arr1);$i++) {
        $arr[$i]=($arr1[$i] == '')?$arr2[$i]:$arr1[$i];
    }
    return $arr;
 }

/* This is where the file downloading is done */
/* This code was taken from php.net examples */
function get_links($url) {
    if( !($body = @file_get_contents($url)) ) return FALSE;
    //Pattern building across multiple lines to avoid page distortion.
    $pattern  = "/((@import\s+[\"'`]([\w:?=@&\/#._;-]+)[\"'`];)|";
    $pattern .= "(:\s*url\s*\([\s\"'`]*([\w:?=@&\/#._;-]+)";
    $pattern .= "([\s\"'`]*\))|<[^>]*\s+(src|href|url)\=[\s\"'`]*";
    $pattern .= "([\w:?=@&\/#._;-]+)[\s\"'`]*[^>]*>))/i";
    //End pattern building.
    preg_match_all ($pattern, $body, $matches);
    return (is_array($matches)) ? $matches:FALSE;
 }
?>
