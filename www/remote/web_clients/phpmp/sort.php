<?php
function fsort( $a, $b )
{
		return strcasecmp( basename( $a["file"] ), basename( $b["file"] ) );
}

function msort( $a, $b )
{
	global $sort_array, $ordered;
	$i = "0";
	$ret = "0";

	// While not filenames_only, while in the first 7 sort_arrays and if ret is 0
	while( $i < count( $sort_array ) && $ret == "0" )
	{
		if( ! isset( $a[ ($sort_array[$i]) ] ) && isset( $b[ ($sort_array[$i]) ] ))
		{
			$ret = -1;
		}
		else if( ! isset( $b[ ($sort_array[$i]) ] ))
		{
			$ret = 1;
		}
		else if( strcmp( $sort_array[$i], "Track" ) == "0" || strcmp( $sort_array[$i], "Time" ) == "0" )
		{
			if( strcmp( $ordered, "yes" ))
			{
				$ret = strnatcmp( $a[ ($sort_array[$i]) ], $b[ ($sort_array[$i]) ] );
			}
			else
			{
				$ret = strnatcmp( $b[ ($sort_array[$i]) ], $a[ ($sort_array[$i]) ] );
			}

		}
		else
		{
			if( strcmp( $ordered, "yes" ))
			{
				$ret = strcasecmp( $a[ ($sort_array[$i]) ], $b[ ($sort_array[$i]) ] );
			}
			else
			{
				$ret = strcasecmp( $b[ ($sort_array[$i]) ], $a[ ($sort_array[$i]) ] );
			}
		}
		$i++;
	}
	return $ret;
}

/***********************************************************************************************************************#
#															#
#	pickSort(): Simply this takes $pick and makes it the first value in a string containing array $sort_array	#
#															#
#***********************************************************************************************************************/
function pickSort( $pick )
{
	global $sort_array;

	$ret = $pick;
	foreach( $sort_array as $value )
	{
		if( strncmp( $pick, $value, strlen( $pick )))
		{
			$ret .= "," . $value;
		}
	}
	return $ret;
}
?>
