<?php

if($_POST["adc"]){
	$thisLimit = 50;
	if($_POST["limit"]){
		$thisLimit = $_POST["limit"];
	};

	include 'include.php';
	$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect");
	$sel = mysql_select_db($db, $dbh) or die("Could not select database");

	$result = mysql_query("SELECT * FROM sensors WHERE `pin`='".$_POST["adc"]."' LIMIT ".$thisLimit." ");

	$a = array();

	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		$b = array();
		$b["date"] = $row{'ts'};
		$b["value"] = $row{'value'};
		$a[] = $b;
	}
	
	print json_encode($a);
}else{
	echo "Wulle";
	return "Wulle";
}

?>
