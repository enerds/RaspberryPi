<?php
function getStatusInfo( $conn )
{
	fputs( $conn, "status\n" );
	while( ! feof( $conn ))
	{
		$got = fgets( $conn, "1024" );
		$got = str_replace( "\n", "", $got );

		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}

		if ( strncmp( "ACK", $got, strlen( "ACK" )) == "0" )
		{
			echo "$got<br>";
			break;
		}

		$el = strtok( $got, ":" );
		$ret[$el] = strtok( "\0" );
		$ret[$el] = ltrim( $ret[$el] );
	}
	if( ! isset( $ret ))
	{
	        $ret = array();
	}
	return $ret;
}

function getCommandInfo( $conn, $MPDVersion )
{

	fputs( $conn, "commands\n" );
	while( ! feof( $conn ))
	{
		$got = fgets( $conn, "1024" );
		$got = str_replace( "\n", "", $got );
		
		if( strncmp( "OK", $got, strlen( "OK" )) == "0")
		{
			break;
		}
		if( strncmp( "ACK", $got, strlen( "ACK" )) == "0")
		{
			$ret = array();
			if(strncmp($MPDVersion,"0.11.",strlen("0.11.") == 0 )) {
				$ret = array(
					     // 0.11.x
					     "clearerror" => 1,
					     "deleteid" => 1,
					     "move" => 1,
					     "moveid" => 1,
					     "playid" => 1,
					     "playlistid" => 1,
					     "plchanges" => 1,
					     "seekid" => 1,
					     "swapid" => 1,
					     "update" => 1
					     );
			};
			
			// 0.10.x
			$ret = $ret + array(
					"add" => true,
					"clear" => true,
					"close" => true,
					"crossfade" => true,
					"close" => true,
					"crossfade" => true,
					"currentsong" => true,
					"delete" => true,
					"find" => true,
					"list" => true,
					"listall" => true,
					"listallinfo" => true,
					"load" => true,
					"lsinfo" => true,
					"next" => true,
					"pause" => true,
					"password" => true,
					"ping" => true,
					"play" => true,
					"playlist" => true,
					"playlistinfo" => true,
					"previous" => true,
					"random" => true,
					"repeat" => true,
					"rm" => true,
					"save" => true,
					"search" => true,
					"seek" => true,
					"setvol" => true,
					"shuffle" => true,
					"stats" => true,
					"status" => true,
					"stop" => true,
					"swap" => true,
					"volume" => true
					);
			return $ret;
		}
	
		$el = str_replace( "command: ", "", $got);
		$ret[$el] = true;
        }
	fputs( $conn, "notcommands\n" );
	while( ! feof( $conn ))
	{
		$got = fgets( $conn,1024 );
		$got = str_replace( "\n", "", $got );
		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}
		if( strncmp( "ACK", $got, strlen( "ACK" )) == "0" )
		{
			echo "$got<br>";
			break;
		}

		if( $el = str_replace( "command: ", "", $got ))
		{
			$ret["all"] = false;
			$ret[$el] = false;
		}
	}
	if( ! isset ( $ret["all"] ))
	{
		$ret["all"] = true;
	}
	return $ret;
}

function setNotSetSongFields( $song, $display_fields )
{

	if( isset( $song["Title"] ))
	{
		for( $i = "0"; $i < count( $display_fields ); $i++ )
		{
			if( ! isset( $song[ $display_fields[$i] ] ))
			{
				$song[ $display_fields[$i] ] = "";
			}
		}
	}

	return $song;
}

function getPlaylistInfo( $conn, $song, $display_fields )
{
	fputs( $conn, "playlistinfo $song\n" );
	$count = -1;
	while ( ! feof( $conn ))
	{
		$got =  fgets( $conn, "1024" );
		$got = str_replace( "\n", "", $got );
		if( strncmp( "OK", $got, strlen( "OK" )) == "0")
		{
			break;
		}
		if( strncmp( "ACK" , $got, strlen( "ACK" )) == "0")
		{
			break;
		}
		$el = strtok( $got, ":");
		if( strcmp( $el, "file" ) == "0" )
		{
			if( $count >= "0" )
			{
			        $ret[$count] = setNotSetSongFields( $ret[$count], $display_fields );
			}
			$count++;
		}
		$ret[$count][$el] = strtok( "\0" );
		$ret[$count][$el] = ltrim( $ret[$count][$el] );
	}
	if ( ! isset( $ret ))
	{
	    $ret = array();
	}
	return $ret;
}

function printPlaylistInfo( $conn, $num, $hide, $show_options, $length, $commands, $arg, $color, $server, $config )
{
	function local( $count, $start, $filenames_only, $ret, $num, $color, $server, $hide, $show_options, $commands, $config, $length )
	{
		$goto = $count > $start ? $count - 1 : $count;
		$time = time();

		if( $filenames_only !== true && isset($ret["Name"]) && $ret["Name"] > "0")
		{
			$display = $ret["Name"];
		}
		else 
		{
			$display = songInfo2Display( $ret, $config["playlist_display_conf"], $config["playlist_display_conf_separator"], $config["filenames_only"], $config["regex"], $config["wordwrap"] );
		}

		$id = $ret["Id"];
		if( isset( $num ) && ( $num == $count ))
		{
			echo "<tr bgcolor=\"{$color["current"]}\">";
		}
		else
		{
		        echo "<tr bgcolor=\"{$color["body"][ ( $count % 2 ) ]}\">";
		}

		if( $config["enable_swap"] === true )
		{
			echo "<td valign=middle><a name=$count></a><small>";
		}
		else
		{
			echo "<td valign=top><a name=$count></a><small>";
		}

		if( $commands["move"] === false || ($config["enable_swap"] === true && $ret["Pos"] == "0" ))
		{
			echo "<small>^</small><br>";
		}
		else if( $config["enable_swap"] === true )
		{
			echo "<small><a title=\"Move song up one position in the playlist\" ";
			echo "href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=move&amp;arg=";
			echo $ret["Pos"] . "&amp;arg2=" . ( $ret["Pos"] - 1 ) . "&amp;time=$time#$goto\">^</a></small><br>";
		}

		if( $commands["delete"] === false )
		{
			echo "<small>d</small>";
		}
		else
		{
			echo "<small><a title=\"Remove song from the playlist\" ";
			echo "href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=deleteid&amp;arg=$id&amp;time=$time#$goto\">";
			echo "d</a></small>";
		}

		if( ( $commands["swap"] === false || $length == ( $ret["Pos"] + 1 )) && $config["enable_swap"] === true )
		{
			echo "<br><small>v</small>";
		}
		else if( $config["enable_swap"] === true )
		{
			echo "<br><small><a title=\"Move song down one position in the playlist\" ";
			echo "href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=move&amp;arg=";
			echo $ret["Pos"] . "&amp;arg2=" . ( $ret["Pos"] + 1 ) . "&amp;time=$time#$goto\">";
			echo "v</a></small>";
		}

		if( $commands["play"] === false )
		{
			echo "</td><td width=\"100%\">$display";
		}
		else
		{
			echo "</td><td width=\"100%\"><a title=\"Play this song\" ";
			echo "href=\"index.php?body=playlist&amp;server=$server&amp;hide=$hide&amp;show_options=$show_options&amp;command=playid&amp;arg=$id\">$display</a>";
		}
		echo "</td></tr>";
	}

	/*
		$count => $start-1
		$num => The actual MPD playlist number
	*/

	$spread = $config["hide_threshold"];
	$filenames_only = $config["filenames_only"];
	$start = "0";
	$end = ( $length - 1 );
	$spread *= "2";
	echo "<!-- Begin printPlaylistInfo Here -->";
	if ( $hide == "1" )
	{
		// $start is playlist length minus the spread divided by two
		$start = $num - $spread / 2;
		
		// $end is just the length-1
		$end = $num + $spread / 2;

		/*
		   If $start is less than 0 go ahead and make it 0 and 
		   the $end will be $end - start, we don't need to show 
		   the beginning hide marks
		*/ 
		if( $start < "0" )
		{
			$end -= $start;
			$start = "0";
		}

		/*
		   Else if $end>=$length we don't need to worry about
		   the ending ...
		*/
		if( $end >= $length )
		{
			$start -= $end - $length+1;
			if( $start < 0 )
			{
				$start = 0;
				$end = $length-1;
			}
		}

		//  Else show the beginning ... string
		if( $start > "0" )
		{
			echo "<tr bgcolor=\"{$color["body"][($start-1)%2]}\">";
			echo "<td colspan=2 align=center><small>";
			echo "<a title=\"Unhide the playlist\"  href=\"index.php?body=playlist&amp;server=$server&amp;hide=0&amp;show_options=$show_options\">...</a>";
			echo "</small></td></tr>";
		}
                
		$str = "command_list_begin\n";
		for( ($i = $start); ($i <= $end); $i++)
		{
			$str .= "playlistinfo $i\n";
		}
		fputs( $conn, $str . "command_list_end\n" );
	}
	else
	{
		if( $length > ( $spread + 1 ))
		{
			echo "<tr bgcolor=\"{$color["body"][1]}\">";
			echo "<td colspan=2 align=center><small>";
			echo "(<a title=\"Hide the playlist\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=1&amp;show_options=$show_options\">condense</a>)";
			echo "</small></td></tr>";
		}
		fputs( $conn, "playlistinfo -1\n" );
	}
	$count = $start-1;
	while( ! feof( $conn ))
	{
		$got = fgets( $conn, "1024" );
		$got = str_replace( "\n", "", $got );

		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}
		else if( strncmp( "ACK", $got, strlen( "ACK" )) == "0" )
		{
			break;
		}
		$el = strtok( $got, ":" );
		if ( strcmp( $el, "file" ) == "0" )
		{
			if ( $count >= $start )
			{
				local( $count, $start, $filenames_only, $ret, $num, $color, $server, $hide, $show_options, $commands, $config, $length );
				unset ( $ret );
			}
			$count++;
		}
		$ret[$el] = strtok( "\0" );
		$ret[$el] = ltrim( $ret[$el] );
	}
	if ( $count >= $start )
	{
		local( $count, $start, $filenames_only, $ret, $num, $color, $server, $hide, $show_options, $commands, $config, $length );
	}
	if ( $hide > 0 && $end < ( $length - 1) )
	{
		echo "<tr bgcolor=\"{$color["body"][ ( ( $end + 1 ) %2 ) ]}\">";
		echo "<td colspan=2 align=center><small>";
		echo "<a title=\"Unhide the playlist\" href=\"index.php?body=playlist&amp;server=$server&amp;hide=0&amp;show_options=$show_options\">...</a>";
		echo "</small></td></tr>";
	}
	echo "<!-- End printPlaylistInfo Here -->";
}

function getLsInfo( $conn, $command, $display_fields )
{
	$dir = array();
	$music = array();
	$playlist = array();

	$mcount = -1;
	$dcount = -1;
	$pcount = -1;

	$type = "";
	fputs( $conn, $command );
	while( ! feof( $conn ))
	{
		$got = fgets( $conn, "1024" );
		$got = str_replace( "\n", "", $got );

		if( strncmp( "OK", $got, strlen( "OK" )) == "0" )
		{
			break;
		}
		if( strncmp( "ACK", $got, strlen("ACK")) == "0" )
		{
			echo "$got<br>";
			break;
		}
		$el = strtok( $got, ":" );
		
		switch ($el){
			// add a new entry depending on the element type
			case "directory":
							$type = "directory";
							$dcount++;
							$dir[$dcount] = str_replace( "$el: " , "", $got );
							break;
			case "playlist":
							$type = "playlist";
							$pcount++;
							$playlist[$pcount] = str_replace( "$el: " , "", $got );
							break;
			case "file":
							$type = "file";
							$mcount++;
							$music[$mcount]["$el"] = str_replace( "$el: ", "", $got);
							//$music[$mcount] = setNotSetSongFields( $filename, $display_fields );
							break;			
							
			default:	
				// add additional information depending on last item type
				switch ($type){
					case "directory":
							$dir[$dcount]["$el"] = str_replace( "$el: ", "", $got);
							break;
					case "playlist":
							//$playlist[$pcount]["$el"] = str_replace( "$el: ", "", $got);
							break;
					case "file":
					        //
							$music[$mcount]["$el"] = str_replace( "$el: ", "", $got);
							break;
									 
					
				}			
				
		}
/*		
		if( strcmp( $el, "directory" ) == "0" )
		{
			$dir[$dcount] = str_replace( "$el: " , "", $got );
			$dcount++;
			continue;
		}
		if( strcmp( $el, "playlist" ) == "0" )
		{
			$playlist[$pcount] = str_replace( "$el: " , "", $got );
			$pcount++;
			continue;
		}
		if( strcmp( $el, "file" ) == "0" )
		{
			if ( $mcount >= 0 )
			{
			        $music[$mcount] = setNotSetSongFields( $music[$mcount], $display_fields );
			}
			$mcount++;
		}
		
		This crashes when playlists/directories come with additional information:
		$music[$mcount]["$el"] = str_replace( "$el: ", "", $got);
	*/

	} // end while
	
	$ret["dir"] = $dir;
	$ret["music"] = $music;
	$ret["playlist"] = $playlist;
	
	return $ret;
}
?>
