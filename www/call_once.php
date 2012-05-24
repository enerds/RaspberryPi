<?php
	include 'include.php';

	/* call once */
	$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect to mysql");
	$sel = mysql_select_db($db, $dbh) or die("Could not select database");
	mysql_query("CREATE TABLE IF NOT EXISTS alarm(
		`id` int(10) NOT NULL auto_increment,
		`hour` int(2),
		`min` int(2),
		`day` varchar(255),
		`cmd` varchar(255),
		PRIMARY KEY (`id`)
	)");
	mysql_close($dbh);
?>
