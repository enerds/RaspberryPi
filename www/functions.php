<?php

	function getAlarm(){
		include 'include.php';
		$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect");
		$sel = mysql_select_db($db, $dbh) or die("Could not select database");

		$result = mysql_query("SELECT * FROM alarm");
		$a = array();
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$b = array();
			$b["id"] = $row{'id'};
			$b["hour"] = str_pad($row{'hour'}, 2, "0", STR_PAD_LEFT);
			$b["min"] = str_pad($row{'min'}, 2, "0", STR_PAD_LEFT);
			$b["days"] = $row{'day'};
			$b["cmd"] = $row{'cmd'};
			$a[] = $b;
		}
		mysql_close($dbh);
		return json_encode($a);
	}

	function dow($digit, $nr, $first = false){
		$pre = "";
		if($digit == 1){
			if($first){
				$pre = " ";
			}else{
				$pre = ",";
			}
			return $pre.(($nr+1) % 7);
		}else{
			return;
		}
	}

	function updateCron(){
		include 'include.php';
		$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect");
		$sel = mysql_select_db($db, $dbh) or die("Could not select database");

		// create contents of new cronfile
		//$cronfile = "# created by the web-frontend\n";
		$cronfile = "";
		$result = mysql_query("SELECT * FROM alarm");
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
			$cronfile .= str_pad($row{'min'}, 2, "0", STR_PAD_LEFT);
			$cronfile .= " ". str_pad($row{'hour'}, 2, "0", STR_PAD_LEFT);
			$cronfile .= " *";
			$cronfile .= " *";
			$cronfile .= dow($row{'day'}[0],0,true);
			$cronfile .= dow($row{'day'}[1],1);
			$cronfile .= dow($row{'day'}[2],2);
			$cronfile .= dow($row{'day'}[3],3);
			$cronfile .= dow($row{'day'}[4],4);
			$cronfile .= dow($row{'day'}[5],5);
			$cronfile .= dow($row{'day'}[6],6);
			$cronfile .= " ".$row{'cmd'};
			$cronfile .= "\n";
		}
		$cronfile .= "\n";
	
		$file = "/home/www-data/www-cron";
		$fh = fopen($file, 'w');
		fwrite($fh, $cronfile);
		fclose($fh);

		shell_exec("crontab /home/www-data/www-cron");
	
		mysql_close($dbh);
	}

	function setAlarm($hour, $min, $days, $cmd){
		include 'include.php';
		$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect");
		$sel = mysql_select_db($db, $dbh) or die("Could not select database");

		$a = "";
		if(mysql_query("INSERT INTO alarm values(' ', '".$hour."', '".$min."', '".$days."', '".$cmd."') ")){
			$a = "OK";
		}else{
			$a = "NO: ".$hour." ".$min." ".$cmd. " error: ".mysql_error();
		}

		mysql_close($dbh);
		updateCron();
		return $a;
	}

	function deleteAlarm($id){
		include 'include.php';
		$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect");
		$sel = mysql_select_db($db, $dbh) or die("Could not select database");

		$result = mysql_query("DELETE FROM alarm WHERE id='".$id."' ");

		mysql_close($dbh);
		updateCron();
	}

	if($_POST["func"]){
		switch($_POST["func"]){
			case 'getPinValue':
				if(isset($_POST["pin"])){
					echo "Calling function with pin: ".$_POST["pin"];
				}else{
					echo "Err: No Pin supplied.";
				}
				break;
			case 'setPWM':
				if(isset($_POST["pwm"])){
					exec("/usr/bin/pinset 1 1 ".$_POST["pwm"]);
				}
				break;
			case 'setPinValue':
				if(!isset($_POST["pin"])){
					echo "Err: No Pin supplied";
				}else if(!isset($_POST["onoff"])){
					echo "Err: No action specified";
				}else{
					$onoff = $_POST["onoff"];
					$pin = $_POST["pin"];
					if($pin >= 0 && $pin < 17){
						if($onoff >= 0 && $onoff <= 1){
							exec("/usr/bin/pinset 0 ".$pin." ".$onoff);
							echo "OK";
						}else{
							echo "Err: Invalid pin setting";
						}
					}else{
						echo "Err: Invalid pin nr";
					}
				}
				break;
			case 'getCPULoad':
				$output = shell_exec('cat /proc/loadavg');
				echo substr($output,0,strpos($output," ")) * 100;
				break;
			case 'getAlarm':
				print getAlarm();
				break;
			case 'setAlarm':
				print setAlarm($_POST["hour"], $_POST["min"], $_POST["days"], $_POST["cmd"]);
				break;
			case 'deleteAlarm':
				print deleteAlarm($_POST["id"]);
				break;
			default:
				echo "Err: I do not know that function.";
				break;
		}	

	}else{
		echo "Err: No Function supplied";
	}


?>
