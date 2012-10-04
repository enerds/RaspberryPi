<?php
function msort($a,$b) {
	global $sort_array,$filenames_only;
	$i=0;
	$ret = 0;
	while($filenames_only!="yes" && $i<4 && $ret==0) {
		if(!isset($a[$sort_array[$i]])) {
			if(isset($b[$sort_array[$i]])) {
				$ret = -1;
			}
		}
		else if(!isset($b[$sort_array[$i]])) {
			$ret = 1;
		}
		else if(strcmp($sort_array[$i],"Track")==0) {
			$ret = strnatcmp($a[$sort_array[$i]],$b[$sort_array[$i]]);
		}
		else {
			$ret = strcasecmp($a[$sort_array[$i]],$b[$sort_array[$i]]);
		}
		$i++;
	}
	if($ret==0)
		$ret = strcasecmp($a["file"],$b["file"]);
	return $ret;
}

function picksort($pick) {
	global $sort_array;
	if(0==strcmp($pick,$sort_array[0])) {
		return "$sort_array[0],$sort_array[1],$sort_array[2],$sort_array[3]";
	}
	else if(0==strcmp($pick,$sort_array[1])) {
		return "$pick,$sort_array[0],$sort_array[2],$sort_array[3]";
	}
	else if(0==strcmp($pick,$sort_array[2])) {
		return "$pick,$sort_array[0],$sort_array[1],$sort_array[3]";
	}
	else if(0==strcmp($pick,$sort_array[3])) {
		return "$pick,$sort_array[0],$sort_array[1],$sort_array[2]";
	}
}
?>