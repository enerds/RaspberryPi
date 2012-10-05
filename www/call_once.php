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

	mysql_query("CREATE TABLE IF NOT EXISTS atmega(
		`pin` varchar(4),
		`nr` int(2),
		`dir` varchar(3),
		`activefunc` varchar(255),
		`availablefunc` varchar(255),
		`desc` varchar(255),
		`interval` int(255) NOT NULL DEFAULT '60',
		PRIMARY KEY (`pin`)
	)");

	mysql_query("CREATE TALE IF NOT EXISTS sensors(
		`ts` TIMESTAMP DEFAULT NOW(),
		`pin` varchar(4),
		`value` int(8)
	)");

	// check if there is already data in there,
	// else insert
	$result = mysql_query("SELECT * FROM atmega");
	$num_rows = mysql_num_rows($result);
	if($num_rows == 0){
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD0',2,'in','uart','dig','used as uart') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD1',3,'out','uart','dig','used as uart') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD2',4,'out','radio','dig','used as radio sender') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD3',5,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD4',6,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD5',11,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD6',12,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PD7',13,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB0',14,'n/a','n/a','dig|pwm','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB1',15,'out','pwm','dig|pwm','used as pulsing status light') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB2',16,'in','prog','dig|pwm','used for programming') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB3',17,'in','prog','dig','used for programming') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB4',18,'in','prog','dig','used for programming') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB5',19,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB6',9,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PB7',10,'n/a','n/a','dig','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC0',23,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC1',24,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC2',25,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC3',26,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC4',27,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC5',28,'n/a','n/a','dig|adc','n/a') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('PC6',1,'in','prog','dig|adc','used for programming') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('VCC',7,'n/a','pwr','pwr','power') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('GND',8,'n/a','pwr','pwr','power') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('AGND',22,'n/a','pwr','pwr','power') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('AVCC',20,'n/a','pwr','pwr','power') ");
		mysql_query("INSERT INTO atmega (`pin`,`nr`,`dir`,`activefunc`,`availablefunc`,`desc`) VALUES ('AREF',21,'n/a','pwr','pwr','power') ");
	}

	mysql_close($dbh);
?>
